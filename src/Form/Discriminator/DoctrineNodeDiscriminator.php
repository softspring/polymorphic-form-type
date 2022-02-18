<?php

namespace Softspring\PolymorphicFormType\Form\Discriminator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Exception\RuntimeException;

class DoctrineNodeDiscriminator extends NodeDiscriminator
{
    protected EntityManagerInterface $em;

    protected string $abstractClass;

    public function __construct(EntityManagerInterface $em, array $formTypeDiscriminatorMap, string $abstractClass, array $formTypeOptions)
    {
        parent::__construct([], $formTypeDiscriminatorMap, $formTypeOptions);
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

    public function getIdFieldForObject($object)
    {
        $classMetadata = $this->em->getClassMetadata(get_class($object));

        if (sizeof($classMetadata->identifier) > 1) {
            throw new RuntimeException('DoctrinePolymorphicCollection only supports entities with one identity field');
        }

        $idField = $classMetadata->identifier[0];

        return $idField;
    }

    public function findObjectById($className, $id): ?object
    {
        return $this->em->find($className, $id);
    }
}
