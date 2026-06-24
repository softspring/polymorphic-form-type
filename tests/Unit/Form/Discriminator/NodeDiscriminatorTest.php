<?php

namespace Softspring\Component\PolymorphicFormType\Tests\Unit\Form\Discriminator;

use PHPUnit\Framework\TestCase;
use Softspring\Component\PolymorphicFormType\Form\Discriminator\NodeDiscriminator;
use Softspring\Component\PolymorphicFormType\Form\Exception\MissingClassDiscriminatorException;
use Softspring\Component\PolymorphicFormType\Form\Exception\MissingFormTypeException;
use Softspring\Component\PolymorphicFormType\Tests\Example1\Model\Properties\Size;
use Softspring\Component\PolymorphicFormType\Tests\Example1\Model\Properties\Weight;
use stdClass;

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

    public function testExposesConfiguredMapsAndOptions(): void
    {
        $discriminator = new NodeDiscriminator([
            'size' => Size::class,
        ], [
            'size' => 'size_form',
        ], [
            'size' => ['label' => 'Size'],
        ], '_node_discr');

        self::assertSame(['size' => 'size_form'], $discriminator->getFormTypeDiscriminatorMap());
        self::assertSame(Size::class, $discriminator->getClassNameForDiscriminator('size'));
        self::assertSame('size_form', $discriminator->getFormTypeFromDiscriminator('size'));
        self::assertSame(['label' => 'Size'], $discriminator->getFormTypeOptionsFromDiscriminator('size'));
        self::assertSame([], $discriminator->getFormTypeOptionsFromDiscriminator('unknown'));
        self::assertNull($discriminator->getIdFieldForObject(new Size()));
        self::assertNull($discriminator->findObjectById(Size::class, 1));
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

    public function testGetDiscriminatorForUnknownObjectFailsWithComponentException(): void
    {
        $discriminator = new NodeDiscriminator([
            'size' => Size::class,
        ], [
            'size' => 'size_form',
        ], [], '_node_discr');

        $this->expectException(MissingClassDiscriminatorException::class);
        $this->expectExceptionMessage('There is not form type for "stdClass" discriminator');

        $discriminator->getDiscriminatorForObject(new stdClass());
    }

    public function testGetDiscriminatorForUnknownFormTypeFailsWithComponentException(): void
    {
        $discriminator = new NodeDiscriminator([
            'size' => Size::class,
        ], [], [], '_node_discr');

        $this->expectException(MissingFormTypeException::class);
        $this->expectExceptionMessage('There is not form type for "size" discriminator');

        $discriminator->getDiscriminatorForObject(new Size());
    }

    public function testMissingClassDiscriminatorExceptionExposesClass(): void
    {
        $exception = new MissingClassDiscriminatorException(Size::class);

        self::assertSame(Size::class, $exception->getClass());
        self::assertSame(sprintf('There is not form type for "%s" discriminator', Size::class), $exception->getMessage());
    }

    public function testMissingFormTypeExceptionExposesDiscriminator(): void
    {
        $exception = new MissingFormTypeException('size');

        self::assertSame('size', $exception->getDiscriminator());
        self::assertSame('There is not form type for "size" discriminator', $exception->getMessage());
    }
}
