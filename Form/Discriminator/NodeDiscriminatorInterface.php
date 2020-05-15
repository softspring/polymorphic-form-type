<?php

namespace Softspring\PolymorphicFormType\Form\Discriminator;

interface NodeDiscriminatorInterface
{
    /**
     * @return array
     */
    public function getFormTypeDiscriminatorMap();

    /**
     * @param object $object
     *
     * @return string
     */
    public function getDiscriminatorForObject($object);

    /**
     * @param string $discriminator
     *
     * @return string
     */
    public function getClassNameForDiscriminator($discriminator);

    /**
     * @param string $discriminator
     *
     * @return string
     */
    public function getFormTypeFromDiscriminator($discriminator);

    /**
     * @param object $object
     *
     * @return mixed|null
     */
    public function getIdFieldForObject($object);

    /**
     * @param string $className
     * @param mixed $id
     *
     * @return object
     */
    public function findObjectById($className, $id);
}