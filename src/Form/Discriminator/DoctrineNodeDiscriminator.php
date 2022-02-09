<?php

namespace Softspring\PolymorphicFormType\Form\Discriminator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Exception\RuntimeException;

class DoctrineNodeDiscriminator extends NodeDiscriminator
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var string
     */
    protected $abstractClass;

    /**
     * DoctrineNodeDiscriminator constructor.
     */
    public function __construct(EntityManagerInterface $em, array $formTypeDiscriminatorMap, string $abstractClass)
    {
        parent::__construct([], $formTypeDiscriminatorMap);
        $this->em = $em;
        $this->abstractClass = $abstractClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscriminatorForObject($object)
    {
        return $this->em->getClassMetadata(get_class($object))->discriminatorValue;
    }

    /**
     * {@inheritdoc}
     */
    public function getClassNameForDiscriminator($discriminator)
    {
        return $this->em->getClassMetadata($this->abstractClass)->discriminatorMap[$discriminator];
    }

    /**
     * {@inheritdoc}
     */
    public function getIdFieldForObject($object)
    {
        $classMetadata = $this->em->getClassMetadata(get_class($object));

        if (sizeof($classMetadata->identifier) > 1) {
            throw new RuntimeException('DoctrinePolymorphicCollection only supports entities with one identity field');
        }

        $idField = $classMetadata->identifier[0];

        return $idField;
    }

    /**
     * {@inheritdoc}
     */
    public function findObjectById($className, $id)
    {
        return $this->em->find($className, $id);
    }
}
