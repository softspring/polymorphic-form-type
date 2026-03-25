<?php

namespace Softspring\Component\PolymorphicFormType\Tests\Unit\Form\DataTransformer;

use PHPUnit\Framework\TestCase;
use Softspring\Component\PolymorphicFormType\Form\DataTransformer\NodeDataTransformer;
use Softspring\Component\PolymorphicFormType\Form\Discriminator\NodeDiscriminator;
use Softspring\Component\PolymorphicFormType\Tests\Example1\Model\Properties\Category;
use Softspring\Component\PolymorphicFormType\Tests\Example1\Model\Properties\Size;
use Softspring\Component\PolymorphicFormType\Tests\Example1\Model\Properties\Weight;
use Symfony\Component\Form\Exception\TransformationFailedException;

class NodeDataTransformerTest extends TestCase
{
    public function testTransformExportsObjectPropertiesAndDiscriminator(): void
    {
        $size = new Size();
        $size->length = 10;
        $size->width = 20;
        $size->height = 30;

        $transformer = new NodeDataTransformer($this->createDiscriminator(), '_node_discr', null);

        $this->assertSame([
            'height' => 30,
            'width' => 20,
            'length' => 10,
            '_node_discr' => 'size',
        ], $transformer->transform($size));
    }

    public function testReverseTransformCreatesConfiguredObjectType(): void
    {
        $transformer = new NodeDataTransformer($this->createDiscriminator(), '_node_discr', null);

        $node = $transformer->reverseTransform([
            '_node_discr' => 'weight',
            'weight' => 95,
        ]);

        $this->assertInstanceOf(Weight::class, $node);
        $this->assertSame(95, $node->weight);
    }

    public function testReverseTransformCanBuildArraysWhenDiscriminatorMapsToArray(): void
    {
        $discriminator = new NodeDiscriminator([
            'category' => 'array',
        ], [
            'category' => 'category_form',
        ], [], '_node_discr');
        $transformer = new NodeDataTransformer($discriminator, '_node_discr', null);

        $node = $transformer->reverseTransform([
            '_node_discr' => 'category',
            'name' => 'News',
        ]);

        $this->assertSame([
            '_node_discr' => 'category',
            'name' => 'News',
        ], $node);
    }

    public function testReverseTransformRequiresDiscriminatorField(): void
    {
        $transformer = new NodeDataTransformer($this->createDiscriminator(), '_node_discr', null);

        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Submitted node data must contain a non-empty "_node_discr" field');

        $transformer->reverseTransform([
            'weight' => 95,
        ]);
    }

    private function createDiscriminator(): NodeDiscriminator
    {
        return new NodeDiscriminator([
            'size' => Size::class,
            'weight' => Weight::class,
            'category' => Category::class,
        ], [
            'size' => 'size_form',
            'weight' => 'weight_form',
            'category' => 'category_form',
        ], [], '_node_discr');
    }
}
