<?php namespace Abstracts\Traits;

trait EAVTrait {

	protected $metadataFields = array();

	protected $metdataModel = null;

	/**
	 * Get a new query builder for the model's table.
	 * We override the "with" method call to always include metadata
	 *
	 * @return Reposed\Builder
	 */
	public function newRawQuery()
	{
		return parent::newRawQuery()->with(['metadata']);
	}

	/**
	 * Get Metadata Model
	 *
	 * @return  string
	 */
	public function getMetadataModel()
	{
		if($this->metadataModel)
		{
			return $this->metadataModel;
		}
		else
		{
			return get_class($this) . '\\Metadata';
		}
	}

	/**
	 * Get a new instance of metadata model
	 * 
	 * @return Model
	 */
	public function newMetadataModel()
	{
		$class = $this->getMetadataModel();

		return new $class;
	}

	/**
	 * method for metadata relation definition
	 *
	 * @return  \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function metadata()
	{
		$class = $this->getMetadataModel();
		$foreignKey = $this->newMetadataModel()->getOwnerForeignKey();

		return $this->hasMany($class, $foreignKey);
	}

	/**
	 * Get an array of allowed metadata fields
	 *
	 * @return  array
	 */
	public function getMetadataFields()
	{
		return $this->metadataFields;
	}

	/**
	 * Metadata validation methods
	 */
	public function validateMetadata(array $metadata)
	{
		list($fields, $values) = [array_keys($metadata), array_values($metadata)];

		return $this->validateFields($fields) and $this->validateValues($data);
	}

	public function validateMetadataFields(array $fields)
	{
		$validFields = $this->getMetadataFields();

		return count(array_intersect($fields, $validFields)) === count($fields)
		   and count(array_diff($fields, $validFields)) === 0;
	}

	public function validateMetadataValues(array $values)
	{
		$validValues = array_filter($values, function ($v) { return is_string($v); });

		return count($validValues) === count($values);
	}

	/**
	 * Get an array of existing metadata hash
	 * @return array
	 */
	public function getExistingMetadataHash()
	{
		$existing = [];

		foreach($this->metadata as $property)
		{
			$existing[$property->field] = $property->value;
		}

		return $existing;
	}

	/**
	 * Metadata operation methods
	 */
	public function createMetadata(array $metadata = array())
	{
		$this->processCreated($metadata);
	}

	public function updateMetadata(array $metadata = array())
	{
		$existing = $this->getExistingMetadataHash();

		$updated = array_intersect_key($metadata, $existing);
		$created = array_diff_key($metadata, $existing);
		$removed = array_diff_key($existing, $metadata);

		$this->processUpdatedMetadata($updated);
		$this->processCreatedMetadata($created);
		$this->processRemovedMetadata($removed);
	}

	protected function processUpdatedMetadata(array $metadata = array())
	{
		foreach($metadata as $field => $val)
		{
			if($property = $this->metadata->findByField($field))
			{
				$property->value = $val;
				$property->save();
			}
		}
	}

	protected function processCreatedMetadata(array $metadata = array())
	{
		$metadataInstance = $this->newMetaDataModel();

		foreach($metadata as $field => $val)
		{
			$property = $this->metadata()->create([
				$metadataInstance->getFieldColumn() => $field,
				$metadataInstance->getValueColumn() => $val
			]);

			$this->metadata->add($property);
		}
	}

	protected function processRemovedMetadata(array $metadata = array())
	{
		foreach($metadata as $field => $val)
		{
			if($property = $this->metadata->findByField($field))
			{
				$this->metadata->remove($property);
				$property->delete();
			}
		}
	}

	/**
	 * Metadata serializer method
	 * @return array
	 */
	public function serializeMetadata()
	{
		$arr = [];

		foreach($this->getMetadataFields() as $f)
		{
			$arr[$f] = $this->metadata->getValueByField($f);
		}

		return $arr;
	}

}
