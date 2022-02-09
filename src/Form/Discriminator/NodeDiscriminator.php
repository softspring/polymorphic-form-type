<?php

namespace Softspring\PolymorphicFormType\Form\Discriminator;

use Symfony\Component\Form\Exception\RuntimeException;

class NodeDiscriminator implements NodeDiscriminatorInterface
{
    /**
     * @var array
     */
    protected $objectDiscriminatorMap;

    /**
     * @var array
     */
    protected $formTypeDiscriminatorMap;

    /**
     * NodeDiscriminator constructor.
     */
    public function __construct(array $objectDiscriminatorMap, array $formTypeDiscriminatorMap)
    {
        $this->objectDiscriminatorMap = $objectDiscriminatorMap;
        $this->formTypeDiscriminatorMap = $formTypeDiscriminatorMap;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormTypeDiscriminatorMap()
    {
        return $this->formTypeDiscriminatorMap;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscriminatorForObject($object)
    {
        // search for discriminator key in discriminators map
        $discr = array_search(get_class($object), $this->objectDiscriminatorMap);

        if (empty($discr)) {
            throw new RuntimeException(sprintf('The class "%s" is not present in discriminator map', get_class($object)));
        }

        // get plain discriminator
        $discr = is_array($discr) ? $discr[0] : $discr;

        if (!isset($this->formTypeDiscriminatorMap[$discr])) {
            throw new RuntimeException(sprintf('There is not form type for "%s" discriminator', $discr));
        }

        return $discr;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdFieldForObject($object)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function findObjectById($className, $id)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getClassNameForDiscriminator($discriminator)
    {
        return $this->objectDiscriminatorMap[$discriminator];
    }

    /**
     * {@inheritdoc}
     */
    public function getFormTypeFromDiscriminator($discriminator)
    {
        return $this->formTypeDiscriminatorMap[$discriminator];
    }
}
