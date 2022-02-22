<?php

namespace Softspring\PolymorphicFormType\Form\Discriminator;

interface NodeDiscriminatorInterface
{
    public function getFormTypeDiscriminatorMap(): array;

    public function getDiscriminatorForObject(object $object): string;

    public function getClassNameForDiscriminator(string $discriminator): string;

    public function getFormTypeFromDiscriminator(string $discriminator): string;

    public function getFormTypeOptionsFromDiscriminator(string $discriminator): array;

    /**
     * @return mixed|null
     */
    public function getIdFieldForObject(object $object);

    /**
     * @param mixed $id
     */
    public function findObjectById(string $className, $id): ?object;
}
