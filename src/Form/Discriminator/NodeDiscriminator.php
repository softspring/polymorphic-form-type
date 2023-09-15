<?php

namespace Softspring\Component\PolymorphicFormType\Form\Discriminator;

use Softspring\Component\PolymorphicFormType\Form\Exception\MissingClassDiscriminatorException;
use Softspring\Component\PolymorphicFormType\Form\Exception\MissingFormTypeException;

class NodeDiscriminator implements NodeDiscriminatorInterface
{
    protected array $objectDiscriminatorMap;

    protected array $formTypeDiscriminatorMap;

    protected array $formTypeOptions;

    protected ?string $discriminatorField;

    public function __construct(array $objectDiscriminatorMap, array $formTypeDiscriminatorMap, array $formTypeOptions, ?string $discriminatorField)
    {
        $this->objectDiscriminatorMap = $objectDiscriminatorMap;
        $this->formTypeDiscriminatorMap = $formTypeDiscriminatorMap;
        $this->formTypeOptions = $formTypeOptions;
        $this->discriminatorField = $discriminatorField;
    }

    public function getFormTypeDiscriminatorMap(): array
    {
        return $this->formTypeDiscriminatorMap;
    }

    /**
     * @throws MissingClassDiscriminatorException
     * @throws MissingFormTypeException
     */
    public function getDiscriminatorForObject($object): string
    {
        // search for discriminator key in discriminators map
        if (is_object($object)) {
            $discr = array_search(get_class($object), $this->objectDiscriminatorMap);
        } elseif (is_array($object) && $this->discriminatorField) {
            $discr = $object[$this->discriminatorField];
        }

        if (empty($discr)) {
            throw new MissingClassDiscriminatorException(get_class($object));
        }

        // get plain discriminator
        $discr = is_array($discr) ? $discr[0] : $discr;

        if (!isset($this->formTypeDiscriminatorMap[$discr])) {
            throw new MissingFormTypeException($discr);
        }

        return $discr;
    }

    public function getIdFieldForObject($object)
    {
        return null;
    }

    public function findObjectById($className, $id): ?object
    {
        return null;
    }

    public function getClassNameForDiscriminator($discriminator): string
    {
        return $this->objectDiscriminatorMap[$discriminator];
    }

    public function getFormTypeFromDiscriminator($discriminator): string
    {
        return $this->formTypeDiscriminatorMap[$discriminator];
    }

    public function getFormTypeOptionsFromDiscriminator(string $discriminator): array
    {
        return $this->formTypeOptions[$discriminator] ?? [];
    }
}
