<?php

namespace Platformsh\Client\Model;

use GuzzleHttp\ClientInterface;

/**
 * A class wrapping the result of an API call.
 */
class Result extends Resource
{
    protected $resourceClass;

    /**
     * {@inheritdoc}
     *
     * @param string $className
     */
    public function __construct(array $data, $baseUrl, ClientInterface $client, $className)
    {
        parent::__construct($data, $baseUrl, $client);
        $this->setResourceClass($className);
    }

    /**
     * @param string $className
     *
     * @internal
     */
    public function setResourceClass($className)
    {
        if (!class_exists($className)) {
            throw new \InvalidArgumentException("Class not found: $className");
        }

        $this->resourceClass = $className;
    }

    /**
     * Count the activities embedded in the result.
     *
     * @return int
     */
    public function countActivities()
    {
        if (!isset($this->data['_embedded']['activities'])) {
            return 0;
        }

        return count($this->data['_embedded']['activities']);
    }

    /**
     * Get activities embedded in the result.
     *
     * A result could embed 0, 1, or many activities.
     *
     * @return Activity[]
     */
    public function getActivities()
    {
        if (!isset($this->data['_embedded']['activities'])) {
            return [];
        }

        $activities = [];
        foreach ($this->data['_embedded']['activities'] as $data) {
            $activities[] = new Activity($data, $this->baseUrl, $this->client);
        }

        return $activities;
    }

    /**
     * Get the entity embedded in the result.
     *
     * @throws \Exception If no entity was embedded.
     *
     * @return Resource
     *   An instance of Resource.
     */
    public function getEntity()
    {
        if (!isset($this->data['_embedded']['entity']) || !isset($this->resourceClass)) {
            throw new \Exception("No entity found in result");
        }

        $data = $this->data['_embedded']['entity'];
        $resourceClass = $this->resourceClass;
        return new $resourceClass($data, $this->baseUrl, $this->client);
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $values)
    {
       throw new \BadMethodCallException("Cannot update() a Result instance directly. Perhaps use getEntity().");
    }

    /**
     * {@inheritdoc}
     */
    public function delete()
    {
        throw new \BadMethodCallException("Cannot delete() a Result instance directly. Perhaps use getEntity().");
    }
}
