<?php

namespace Softspring\PolymorphicFormType\Form\DataTransformer;

use Softspring\PolymorphicFormType\Form\Discriminator\NodeDiscriminator;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class NodeDataTransformer
 */
class NodeDataTransformer implements DataTransformerInterface
{
    /**
     * @var NodeDiscriminator
     */
    protected $nodeDiscriminator;

    /**
     * NodeDataTransformer constructor.
     *
     * @param NodeDiscriminator $nodeDiscriminator
     */
    public function __construct(NodeDiscriminator $nodeDiscriminator)
    {
        $this->nodeDiscriminator = $nodeDiscriminator;
    }

    /**
     * @param object $value
     *
     * @return array
     */
    public function transform($value)
    {
        if ($value === null) {
            return null;
        }

        $data = [];

        try {
            $reflection = new \ReflectionClass($value);
            foreach ($reflection->getProperties() as $property) {
                $getterName = 'get' . ucfirst($property->getName());
                if ($reflection->hasMethod($getterName)) {
                    $method = $reflection->getMethod($getterName);
                    $data[lcfirst($property->getName())] = $method->invoke($value);
                } elseif ($property->isPublic()) {
                    $propertyName = $property->getName();
                    $data[lcfirst($property->getName())] = $value->$propertyName;
                }
            }

            $data['_node_id'] = $data[$this->nodeDiscriminator->getIdFieldForObject($value)] ?? null;
            $data['_node_discr'] = $this->nodeDiscriminator->getDiscriminatorForObject($value);

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
        $className = $this->nodeDiscriminator->getClassNameForDiscriminator($value['_node_discr']);

        if (!empty($value['_node_id'])) {
            $element = $this->nodeDiscriminator->findObjectById($className, $value['_node_id']);

            if (!$element) {
                throw new TransformationFailedException(sprintf('Failed transformation for class "%s" for element with id %u', $className, $value['_node_id']));
            }
        } else {
            $element = new $className();
        }

        $data = $value;
        unset($data['_node_id']);
        unset($data['_node_discr']);

        try {
            $reflection = new \ReflectionClass($element);

            foreach ($data as $field => $fieldValue) {
                $setterName = 'set' . ucfirst($field);
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