<?php

namespace Softspring\Component\PolymorphicFormType\Form\Discriminator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Exception\RuntimeException;

class DoctrineNodeDiscriminator extends NodeDiscriminator
{
    protected EntityManagerInterface $em;

    protected string $abstractClass;

    public function __construct(EntityManagerInterface $em, array $formTypeDiscriminatorMap, string $abstractClass, array $formTypeOptions, ?string $discriminatorField)
    {
        parent::__construct([], $formTypeDiscriminatorMap, $formTypeOptions, $discriminatorField);
        $this->em = $em;
        $this->abstractClass = $abstractClass;
    }

    public function getDiscriminatorForObject($object): string
    {
        return $this->em->getClassMetadata(get_class($object))->discriminatorValue;
    }

    public function getClassNameForDiscriminator($discriminator): string
    {
        return $this->em->getClassMetadata($this->abstractClass)->discriminatorMap[$discriminator];
    }

    public function getIdFieldForObject($object): mixed
    {
        $classMetadata = $this->em->getClassMetadata(get_class($object));

        if (count($classMetadata->identifier) > 1) {
            throw new RuntimeException('DoctrinePolymorphicCollection only supports entities with one identity field');
        }

        return $classMetadata->identifier[0] ?? null;
    }

    public function findObjectById($className, $id): ?object
    {
        return $this->em->find($className, $id);
    }
}
