<?php namespace k\Reposed;

class SnakeModel extends Model {

	/**
	 * Get an attribute from the model.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public function getAttribute($key)
	{
		$snake = snake_case($key);

		$inAttributes = array_key_exists($snake, $this->attributes);

		// If the key references an attribute, we can just go ahead and return the
		// plain attribute value from the model. This allows every attribute to
		// be dynamically accessed through the _get method without accessors.
		if ($inAttributes or $this->hasGetMutator($snake))
		{
			return $this->getAttributeValue($snake);
		}

		// If the key already exists in the relationships array, it just means the
		// relationship has already been loaded, so we'll just return it out of
		// here because there is no need to query within the relations twice.
		if (array_key_exists($snake, $this->relations))
		{
			return $this->relations[$snake];
		}

		// If the "attribute" exists as a method on the model, we will just assume
		// it is a relationship and will load and return results from the query
		// and hydrate the relationship's value on the "relationships" array.
		if (method_exists($this, $key))
		{
			$relations = $this->$key()->getResults();

			$this->setRelation($snake, $relations);

			return $this->getRelation($snake);
		}
	}

	/**
	 * Set a given attribute on the model.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function setAttribute($key, $value)
	{
		$key = snake_case($key);

		// First we will check for the presence of a mutator for the set operation
		// which simply lets the developers tweak the attribute as it is set on
		// the model, such as "json_encoding" an listing of data for storage.
		if ($this->hasSetMutator($key))
		{
			$method = 'set'.studly_case($key).'Attribute';

			return $this->{$method}($value);
		}

		// If an attribute is listed as a "date", we'll convert it from a DateTime
		// instance into a form proper for storage on the database tables using
		// the connection grammar's date format. We will auto set the values.
		elseif (in_array($key, $this->getDates()))
		{
			if ($value)
			{
				$value = $this->fromDateTime($value);
			}
		}

		$this->attributes[$key] = $value;
	}

	/**
	 * Get a specified relationship.
	 *
	 * @param  string  $relation
	 * @return mixed
	 */
	public function getRelation($relation)
	{
		return $this->relations[snake_case($relation)];
	}

	/**
	 * Set the specific relationship in the model.
	 *
	 * @param  string  $relation
	 * @param  mixed   $value
	 * @return void
	 */
	public function setRelation($relation, $value)
	{
		$this->relations[snake_case($relation)] = $value;
		return $this;
	}

	/**
	 * Determine if an attribute exists on the model.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function __isset($key)
	{
		return isset($this->attributes[snake_case($key)]) or isset($this->relations[snake_case($key)]);
	}

	/**
	 * Unset an attribute on the model.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function __unset($key)
	{
		unset($this->attributes[snake_case($key)]);

		unset($this->relations[snake_case($key)]);
	}
	
}