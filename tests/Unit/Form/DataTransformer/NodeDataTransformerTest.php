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
    public function testTransformKeepsNullAndArraysUntouched(): void
    {
        $transformer = new NodeDataTransformer($this->createDiscriminator(), '_node_discr', null);

        self::assertNull($transformer->transform(null));
        self::assertSame(['already' => 'normalized'], $transformer->transform(['already' => 'normalized']));
    }

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

    public function testReverseTransformUpdatesExistingObjectResolvedById(): void
    {
        $existing = new Weight();
        $existing->weight = 10;
        $discriminator = new class($existing) extends NodeDiscriminator {
            public function __construct(private readonly Weight $existing)
            {
                parent::__construct([
                    'weight' => Weight::class,
                ], [
                    'weight' => 'weight_form',
                ], [], '_node_discr');
            }

            public function getIdFieldForObject($object): mixed
            {
                return 'id';
            }

            public function findObjectById($className, $id): ?object
            {
                return Weight::class === $className && 7 === $id ? $this->existing : null;
            }
        };
        $transformer = new NodeDataTransformer($discriminator, '_node_discr', '_node_id');

        $node = $transformer->reverseTransform([
            '_node_discr' => 'weight',
            '_node_id' => 7,
            'weight' => 95,
        ]);

        self::assertSame($existing, $node);
        self::assertSame(95, $existing->weight);
    }

    public function testReverseTransformFailsWhenExistingObjectIsNotFound(): void
    {
        $transformer = new NodeDataTransformer($this->createDiscriminator(), '_node_discr', '_node_id');

        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage(sprintf('Failed transformation for class "%s" for element with id 7', Weight::class));

        $transformer->reverseTransform([
            '_node_discr' => 'weight',
            '_node_id' => 7,
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
