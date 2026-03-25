<?php

namespace Softspring\Component\PolymorphicFormType\Tests\Unit\Form\Discriminator;

use PHPUnit\Framework\TestCase;
use Softspring\Component\PolymorphicFormType\Form\Discriminator\NodeDiscriminator;
use Softspring\Component\PolymorphicFormType\Form\Exception\MissingClassDiscriminatorException;
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
}
