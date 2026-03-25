<?php

namespace Softspring\Component\PolymorphicFormType\Tests\Unit\Form\Discriminator;

use PHPUnit\Framework\TestCase;
use Softspring\Component\PolymorphicFormType\Form\Discriminator\NodeDiscriminator;
use Softspring\Component\PolymorphicFormType\Form\Exception\MissingClassDiscriminatorException;
use Softspring\Component\PolymorphicFormType\Form\Exception\MissingFormTypeException;
use Softspring\Component\PolymorphicFormType\Tests\Example1\Model\Properties\Size;
use Softspring\Component\PolymorphicFormType\Tests\Example1\Model\Properties\Weight;

class NodeDiscriminatorTest extends TestCase
{
    public function testGetDiscriminatorForObjectUsesConfiguredClassMap(): void
    {
        $discriminator = new NodeDiscriminator([
            'size' => Size::class,
            'weight' => Weight::class,
        ], [
            'size' => 'size_form',
            'weight' => 'weight_form',
        ], [], '_node_discr');

        $this->assertSame('size', $discriminator->getDiscriminatorForObject(new Size()));
    }

    public function testGetDiscriminatorForArrayUsesSubmittedDiscriminatorField(): void
    {
        $discriminator = new NodeDiscriminator([
            'size' => Size::class,
        ], [
            'size' => 'size_form',
        ], [], '_node_discr');

        $this->assertSame('size', $discriminator->getDiscriminatorForObject([
            '_node_discr' => 'size',
        ]));
    }

    public function testGetDiscriminatorForArrayWithoutDiscriminatorFailsCleanly(): void
    {
        $discriminator = new NodeDiscriminator([
            'size' => Size::class,
        ], [
            'size' => 'size_form',
        ], [], '_node_discr');

        $this->expectException(MissingClassDiscriminatorException::class);
        $this->expectExceptionMessage('There is not class mapping for "array"');

        $discriminator->getDiscriminatorForObject([
            'length' => 120,
        ]);
    }

    public function testGetFormTypeFromUnknownDiscriminatorFailsCleanly(): void
    {
        $discriminator = new NodeDiscriminator([
            'size' => Size::class,
        ], [
            'size' => 'size_form',
        ], [], '_node_discr');

        $this->expectException(MissingFormTypeException::class);
        $this->expectExceptionMessage('There is not form type for "weight" discriminator');

        $discriminator->getFormTypeFromDiscriminator('weight');
    }

    public function testGetClassNameFromUnknownDiscriminatorFailsCleanly(): void
    {
        $discriminator = new NodeDiscriminator([
            'size' => Size::class,
        ], [
            'size' => 'size_form',
        ], [], '_node_discr');

        $this->expectException(MissingClassDiscriminatorException::class);
        $this->expectExceptionMessage('There is not class mapping for "weight"');

        $discriminator->getClassNameForDiscriminator('weight');
    }
}
