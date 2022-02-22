<?php

namespace Softspring\PolymorphicFormType\Form\DataTransformer;

use Softspring\PolymorphicFormType\Form\Discriminator\NodeDiscriminator;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class NodeDataTransformer implements DataTransformerInterface
{
    protected NodeDiscriminator $nodeDiscriminator;
    protected string $discriminatorField;
    protected ?string $idField;

    public function __construct(NodeDiscriminator $nodeDiscriminator, string $discriminatorField, ?string $idField)
    {
        $this->nodeDiscriminator = $nodeDiscriminator;
        $this->discriminatorField = $discriminatorField;
        $this->idField = $idField;
    }

    /**
     * @param object $value
     *
     * @return array
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (is_array($value)) {
            return $value;
        }

        $data = [];

        try {
            $reflection = new \ReflectionClass($value);
            foreach ($reflection->getProperties() as $property) {
                $getterName = 'get'.ucfirst($property->getName());
                if ($reflection->hasMethod($getterName)) {
                    $method = $reflection->getMethod($getterName);
                    $data[lcfirst($property->getName())] = $method->invoke($value);
                } elseif ($property->isPublic()) {
                    $propertyName = $property->getName();
                    $data[lcfirst($property->getName())] = $value->$propertyName;
                }
            }

            if ($this->idField) {
                $data[$this->idField] = $data[$this->nodeDiscriminator->getIdFieldForObject($value)] ?? null;
            }

            $data[$this->discriminatorField] = $this->nodeDiscriminator->getDiscriminatorForObject($value);

            return $data;
        } catch (\ReflectionException $e) {
            throw new TransformationFailedException(sprintf('The "%s" class not exists', is_object($value) ? get_class($value) : $value));
        }
    }

    /**
     * @param array $value
     *
     * @return object
     */
    public function reverseTransform($value)
    {
        $className = $this->nodeDiscriminator->getClassNameForDiscriminator($value[$this->discriminatorField]);

        if (!empty($value[$this->idField])) {
            $element = $this->nodeDiscriminator->findObjectById($className, $value[$this->idField]);

            if (!$element) {
                throw new TransformationFailedException(sprintf('Failed transformation for class "%s" for element with id %u', $className, $value[$this->idField]));
            }
        } elseif ('array' == $className) {
            $element = [];
        } else {
            $element = new $className();
        }

        $data = $value;
        unset($data[$this->idField]);

        if (is_array($element)) {
            foreach ($data as $field => $fieldValue) {
                $element[$field] = $fieldValue;
            }

            return $element;
        }

        unset($data[$this->discriminatorField]);

        try {
            $reflection = new \ReflectionClass($element);

            foreach ($data as $field => $fieldValue) {
                $setterName = 'set'.ucfirst($field);
                if ($reflection->hasMethod($setterName) || $reflection->getParentClass() && $reflection->hasMethod($setterName)) {
                    $method = $reflection->getMethod($setterName);
                    $method->invoke($element, $fieldValue);
                } elseif ($reflection->hasProperty($field) && $reflection->getProperty($field)->isPublic()) {
                    $element->$field = $fieldValue;
                } else {
                    // skip field
                    // throw new TransformationFailedException(sprintf('The "%s" class has not a "%s" method', $className, $setterName));
                }
            }

            return $element;
        } catch (\ReflectionException $e) {
            throw new TransformationFailedException(sprintf('The "%s" class not exists', is_object($value) ? get_class($value) : $value));
        }
    }
}
