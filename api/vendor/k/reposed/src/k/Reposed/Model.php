<?php namespace k\Reposed;

use Illuminate\Database\Eloquent;
use DateTime;
use Illuminate\Support\Contracts\ArrayableInterface;
use k\Reposed\Database\QueryBuilder;

class Model extends Eloquent\Model {

	protected $serializables = array('*');

	protected static $serializerCache = array();

	protected static $dbFields = array();

	protected $allowedExtraFields = array();

	/**
	 * Some static methods that provide access to model's repositories
	 * via the reposed facade so that stuff remains mockable
	 */
	public static function repo()
	{
		return Facade::make(static::klass());
	}

	public static function table()
	{
		return static::repo()->getTable();
	}

	public static function c($field = null)
	{
		return static::repo()->c($field);
	}

	public static function klass()
	{
		return get_called_class();
	}


	/**
	 * Get the extra fields allowed on the model
	 */
	public function getAllowedExtraFields()
	{
		return $this->allowedExtraFields;
	}

	public static function getDbFields()
	{
		return static::$dbFields;
	}

	public static function setDbFields(array $fields)
	{
		return static::$dbFields = $fields;
	}

	/**
	 * override boot to call more setups
	 * @return void
	 */
	protected static function boot()
	{
		parent::boot();

		static::setupSerializerCache();
	}

	/**
	 * setup serializer cache
	 * @return  void
	 */
	protected static function setupSerializerCache()
	{
		$class = get_called_class();

		static::$serializerCache[$class] = array();

		// Now we extract methods which serialize different fields
		foreach (get_class_methods($class) as $method)
		{
			if (preg_match('/^serialize(.+)$/', $method, $matches))
			{
				if (static::$snakeAttributes) $matches[1] = snake_case($matches[1]);

				static::$serializerCache[$class][] = lcfirst($matches[1]);
			}
		}
	}

	/**
	 * provide a method to expose serialized fields
	 * @return  array
	 */
	public function getSerializedFields()
	{
		$class = get_class($this);

		if (isset(static::$serializerCache[$class]))
		{
			return static::$serializerCache[$class];
		}

		return array();
	}


	/**
	 * get serializable fields of current model
	 */
	public function getSerializables()
	{
		if($this->serializables == array('*'))
		{
			return $this->getDefaultSerializables();
		}

		return $this->serializables;
	}

	/**
	 * get default serializable fields so that all fields need
	 * not be mentioned for the serializer
	 */
	public function getDefaultSerializables()
	{
		$serializables = array_keys(array_merge(
			array_flip(array_keys($this->attributes)),
			array_flip(array_keys($this->relations))
		));

		if(count($this->getVisible()) > 0)
		{
			$serializables = array_values(array_intersect($serializables, $this->getVisible()));
		}
		
		$serializables = array_values(array_diff($serializables, $this->getHidden()));

		return array_merge($serializables, $this->getSerializedFields());
	}

	/**
	 * Return a new Repository Instance
	 */
	public function newRepository()
	{
		return new Repository($this);
	}

	/**
	 * Provide an attributes to object map
	 *
	 * @return Model
	 */
	public function mapData(array $attributes)
	{
		return $this->newInstance();
	}

	/**
	 * Create a new model instance that is existing.
	 *
	 * @param  array  $attributes
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public function newExisting($attributes = array())
	{
		$instance = $this->newInstance(array(), true);

		$instance->setRawAttributes($attributes, true);

		return $instance;
	}

	/**
	 * Get a new query builder instance for the connection.
	 * Overriding this method in order to properly implement
	 * aggregates on queries with groups
	 *
	 * @return \Illuminate\Database\Query\Builder
	 */
	protected function newBaseQueryBuilder()
	{
		$conn = $this->getConnection();

		$grammar = $conn->getQueryGrammar();

		return new QueryBuilder($conn, $grammar, $conn->getPostProcessor());
	}

	/**
	 * Create a new model instance requested by the builder.
	 *
	 * @param  array  $attributes
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public function newFromBuilder($attributes = array())
	{
		$m = $this->mapData((array) $attributes)->newInstance(array(), true);
		$m->setRawAttributes((array) $attributes);
		return $m;
	}

	/**
	 * Create a new Eloquent Collection instance.
	 *
	 * @param  array  $models
	 * @return Reposed\Collection
	 */
	public function newCollection(array $models = array())
	{
		return new Collection($models);
	}

	/**
	 * adding a getter for "with", so it can be worked on by
	 * various traits/sub-types
	 */
	public function getWith()
	{
		return $this->with;
	}

	/**
	 * Create a new Eloquent query builder for the model.
	 *
	 * @param  \Illuminate\Database\Query\Builder $query
	 * @return \Illuminate\Database\Eloquent\Builder|static
	 */
	public function newEloquentBuilder($query)
	{
		return new Builder($query);
	}

	
	/**
	 * Get a new query builder for the model's table.
	 *
	 * @return Reposed\Builder
	 */
	public function newRawQuery()
	{
		$builder = $this->newEloquentBuilder($this->newBaseQueryBuilder());

		// Once we have the query builders, we will set the model instances so the
		// builder can easily access any information it may need from the model
		// while it is constructing and executing various queries against it.
		$builder->setModel($this)->with($this->getWith());

		return $builder;
	}

	/**
	 * Get a new query builder for the model.
	 * set any type of scope you want on this builder in a child class, and it'll
	 * keep applying the scope on any read-queries on this model
	 *
	 * @return Reposed\Builder
	 */
	public function newQuery($excludeDeleted = true)
	{
		$builder = $this->newRawQuery();

		if ($excludeDeleted and $this->softDelete)
		{
			$builder->whereNull($this->getQualifiedDeletedAtColumn());
		}

		return $builder;
	}

	/**
	 * Get a relationship value from a method.
	 *
	 * @param  string  $key
	 * @param  string  $camelKey
	 * @return mixed
	 */
	protected function getRelationshipFromMethod($key, $camelKey)
	{
		$relations = $this->$camelKey();

		if ( ! $relations instanceof Relation)
		{
			throw new LogicException('Relationship method must return an object of type '
				. 'Illuminate\Database\Eloquent\Relations\Relation');
		}

		$this->setRelation($key, $relations);

		return $this->getRelation($key);
	}

	/**
	 * Save the model to the database.
	 *
	 * @return bool
	 */
	public function save(array $options = array())
	{
		$query = $this->newRawQuery();

		// If the "saving" event returns false we'll bail out of the save and return
		// false, indicating that the save failed. This gives an opportunities to
		// listeners to cancel save operations if validations fail or whatever.
		if ($this->fireModelEvent('saving') === false)
		{
			return false;
		}

		// If the model already exists in the database we can just update our record
		// that is already in this database using the current IDs in this "where"
		// clause to only update this model. Otherwise, we'll just insert them.
		if ($this->exists)
		{
			$saved = $this->performUpdate($query);
		}

		// If the model is brand new, we'll insert it into our database and set the
		// ID attribute on the model to the value of the newly inserted row's ID
		// which is typically an auto-increment value managed by the database.
		else
		{
			$saved = $this->performInsert($query);
		}

		if ($saved) $this->finishSave($options);

		return $saved;
	}

	/**
	 * Get the date fields of model
	 *
	 * @return array
	 */
	public function getDates()
	{
		if($this->timestamps)
			return array_unique(array_merge($this->dates, array(static::CREATED_AT, static::UPDATED_AT)));
		else
			return $this->dates;
	}

	/**
	 * Get the gaurded fields of a model
	 *
	 * @return array
	 */
	public function getGaurded()
	{
		return $this->gaurded;
	}

	/**
	 * Get the visible fields of a model
	 *
	 * @return array
	 */
	public function getVisible()
	{
		return $this->visible;
	}

	/**
	 * Convert the model instance to an array.
	 *
	 * @return array
	 */
	public function toArray()
	{
		$attributes = array_merge(
			$this->attributesToArray(),
			$this->relationsToArray()
		);

		return array_merge($attributes, $this->extraSerializedFieldsToArray($attributes));
	}

	/**
	 * Convert the model's attributes to an array.
	 *
	 * @return array
	 */
	public function attributesToArray()
	{
		$attributes = $this->getArrayableAttributes();

		// We want to spin through all the mutated attributes for this model and call
		// the mutator for the attribute. We cache off every mutated attributes so
		// we don't have to constantly check on attributes that actually change.
		foreach ($this->getMutatedAttributes() as $key)
		{
			if(in_array($key, $this->getSerializedFields()))
			{
				$attributes[$key] = $this->{'serialize'.studly_case($key)}();
				continue;
			}

			if ( ! array_key_exists($key, $attributes)) continue;

			$attributes[$key] = $this->mutateAttribute($key, $attributes[$key]);
		}

		foreach($attributes as $key => $value)
		{
			if(in_array($key, $this->getSerializedFields()))
			{
				$attributes[$key] = $this->{'serialize'.studly_case($key)}($value);
			}
		}

		return $attributes;
	}

	/**
	 * Get an attribute array of all arrayable attributes.
	 *
	 * @return array
	 */
	protected function getArrayableAttributes()
	{
		return array_intersect_key($this->attributes, array_flip($this->getSerializables()));
	}

	/**
	 * Get the model's relationships in array form.
	 *
	 * @return array
	 */
	public function relationsToArray()
	{
		$attributes = array();

		$relations = $this->getArrayableRelations();

		foreach ($relations as $key => $value)
		{
			if (! in_array($key, $this->getSerializables())) continue;

			// We check if relation is in our serializerCache and just let
			// the serializer handle the field
			if(in_array($key, $this->getSerializedFields()))
			{
				$value = $this->{'serialize'.studly_case($key)}($value);
			}


			// If the values implements the Arrayable interface we can just call this
			// toArray method on the instances which will convert both models and
			// collections to their proper array form and we'll set the values.
			if ($value instanceof ArrayableInterface)
			{
				$relation = $value->toArray();
			}
			// Otherwise we just assign relation to value and be done
			else
			{
				$relation = $value;
			}

			// If the relationships snake-casing is enabled, we will snake case this
			// key so that the relation attribute is snake cased in this returned
			// array to the developer, making this consisntent with attributes.
			if (static::$snakeAttributes)
			{
				$key = snake_case($key);
			}

			// If the relation value has been set, we will set it on this attributes
			// list for returning. If it was not arrayable or null, we'll not set
			// the value on the array because it is some type of invalid value.
			if (isset($relation))
			{
				$attributes[$key] = $relation;
			}
		}

		return $attributes;
	}

	/**
	 * Get all arrayable relations
	 */
	public function getArrayableRelations()
	{
		return array_intersect_key($this->relations, array_flip($this->getSerializables()));
	}

	/**
	 * Get an array of extra serializedfields which are not raw attributes or relations
	 * @return array
	 */
	public function extraSerializedFieldsToArray(array $existing = array())
	{
		$fields = $this->getExtraSerializableFields($existing);

		$attributes = array();

		foreach ($fields as $field)
		{
			$attributes[$field] = $this->{'serialize' . studly_case($field)}();
		}

		return $attributes;
	}

	/**
	 * Get an array of names of extra serialized fields
	 * @return  array
	 */
	public function getExtraSerializableFields(array $existing = array())
	{
		$allowedSerializedFields = array_values(array_intersect(
			$this->getSerializedFields(), $this->getSerializables()
		));

		$extraSerializableFields = array_values(
			array_diff($allowedSerializedFields, array_keys($existing))
		);

		return $extraSerializableFields;
	}

	/**
	 * Perform a model update operation.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder
	 * @return bool
	 */
	protected function performUpdate(Eloquent\Builder $query)
	{
		$dirty = $this->getDirty();

		if (count($dirty) > 0)
		{
			// If the updating event returns false, we will cancel the update operation so
			// developers can hook Validation systems into their models and cancel this
			// operation if the model does not pass validation. Otherwise, we update.
			if ($this->fireModelEvent('updating') === false)
			{
				return false;
			}

			// First we need to create a fresh query instance and touch the creation and
			// update timestamp on the model which are maintained by us for developer
			// convenience. Then we will just continue saving the model instances.
			if ($this->timestamps)
			{
				$this->updateTimestamps();

				$dirty = $this->getDirty();
			}

			// once we have the dirty ones, we remove any extra fields that they might have
			// so that persistence goes smoothly
			$dirty = array_diff_key($dirty, array_flip($this->getAllowedExtraFields()));

			// Once we have run the update operation, we will fire the "updated" event for
			// this model instance. This will allow developers to hook into these after
			// models are updated, giving them a chance to do any special processing.
			$this->setKeysForSaveQuery($query)->update($dirty);

			$this->fireModelEvent('updated', false);
		}

		return true;
	}

	/**
	 * Perform a model insert operation.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder
	 * @return bool
	 */
	protected function performInsert(Eloquent\Builder $query)
	{
		if ($this->fireModelEvent('creating') === false) return false;

		// First we'll need to create a fresh query instance and touch the creation and
		// update timestamps on this model, which are maintained by us for developer
		// convenience. After, we will just continue saving these model instances.
		if ($this->timestamps)
		{
			$this->updateTimestamps();
		}

		// If the model has an incrementing key, we can use the "insertGetId" method on
		// the query builder, which will give us back the final inserted ID for this
		// table from the database. Not all tables have to be incrementing though.
		$attributes = $this->attributes;

		// once we have the attributes, we remove any extra fields that they might have
		// so that persistence goes smoothly
		$attributes = array_diff_key($attributes, array_flip($this->allowedExtraFields));

		if ($this->incrementing)
		{
			$this->insertAndSetId($query, $attributes);
		}

		// If the table is not incrementing we'll simply insert this attributes as they
		// are, as this attributes arrays must contain an "id" column already placed
		// there by the developer as the manually determined key for these models.
		else
		{
			$query->insert($attributes);
		}

		// We will go ahead and set the exists property to true, so that it is set when
		// the created event is fired, just in case the developer tries to update it
		// during the event. This will allow them to do so and run an update here.
		$this->exists = true;

		$this->fireModelEvent('created', false);

		return true;
	}


}