<?php

namespace dto;

use ReflectionClass;
use yii\helpers\Inflector;

abstract class AbstractDto
{
    private array $unspecifiedProperties = [];

    public function __construct(mixed $data = null)
    {
        if ($data) {
            $this->populate($data);
        }
    }

    /**
     * Fill DTO model properties
     */
    public function populate(mixed $data): AbstractDto
    {
        $object = is_object($data) ? $data : (object)$data;
        foreach (array_keys($this->getProperties()) as $property) {
            if ($field = $this->searchCorrespondingField($property, $object)) {
                $this->populateProperty($property, $object->$field);
                unset($this->unspecifiedProperties[$property]);
            } else {
                $this->unspecifiedProperties[$property] = true;
            }
        }

        return $this;
    }

    public function getUnspecifiedProperties(): array
    {
        return array_keys($this->unspecifiedProperties);
    }

    public function asUnderscoreArray($isNotNullOnly = false): array
    {
        $array = [];
        foreach (array_keys($this->getProperties()) as $property) {
            if (!$isNotNullOnly || !is_null($this->$property)) {
                $array[Inflector::underscore($property)] = $this->$property;
            }
        }

        return $array;
    }

    public function asArray(): array
    {
        $array = [];
        foreach (array_keys($this->getProperties()) as $property) {
            $array[$property] = $this->$property;
        }

        return $array;
    }

    private function getProperties(): array
    {
        $properties = [];
        foreach ((new ReflectionClass($this))->getProperties() as $property) {
            $properties[$property->getName()] = null;
        }
        foreach (array_keys(get_class_vars(__CLASS__)) as $property) {
            unset($properties[$property]);
        }

        return $properties;
    }

    private function searchCorrespondingField(string $property, object $object): ?string
    {
        if (isset($object->$property)) {
            return $property;
        } else {
            $underscored = Inflector::underscore($property);
            if (isset($object->$underscored)) {
                return $underscored;
            }
        }

        return null;
    }

    private function populateProperty(string $name, mixed $value): void
    {
        $setterName = 'set' . Inflector::camelize($name);
        if (method_exists(get_class($this), $setterName)) {
            $this->$setterName($value);
        } else {
            $this->$name = $value;
        }
    }
}
