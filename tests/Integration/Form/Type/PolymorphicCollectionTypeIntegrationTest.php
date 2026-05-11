<?php

namespace Softspring\Component\PolymorphicFormType\Tests\Integration\Form\Type;

use PHPUnit\Framework\Attributes\DataProvider;
use Softspring\Component\PolymorphicFormType\Form\Type\PolymorphicCollectionType;
use Softspring\Component\PolymorphicFormType\Tests\Example1\Form\Type\CategoryPropertyType;
use Softspring\Component\PolymorphicFormType\Tests\Example1\Form\Type\SizePropertyType;
use Softspring\Component\PolymorphicFormType\Tests\Example1\Form\Type\WeightPropertyType;
use Softspring\Component\PolymorphicFormType\Tests\Example1\Model\Properties\Category;
use Softspring\Component\PolymorphicFormType\Tests\Example1\Model\Properties\Size;
use Softspring\Component\PolymorphicFormType\Tests\Example1\Model\Properties\Weight;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class PolymorphicCollectionTypeIntegrationTest extends TypeTestCase
{
    #[DataProvider('submittedNodeProvider')]
    public function testSubmitCreatesTheExpectedNodeObject(array $submittedNode, string $expectedClass, string $expectedField, string $expectedValue): void
    {
        $form = $this->factory->createBuilder(FormType::class)
            ->add('properties', PolymorphicCollectionType::class, $this->createCollectionOptions())
            ->getForm();

        $form->submit([
            'properties' => [$submittedNode],
        ]);

        $this->assertTrue($form->isSynchronized());

        $properties = $form->get('properties')->getData();

        $this->assertCount(1, $properties);
        $this->assertInstanceOf($expectedClass, $properties[0]);
        $this->assertSame($expectedValue, (string) $properties[0]->{$expectedField});
    }

    public function testCreateViewExposesOnePrototypePerConfiguredType(): void
    {
        $form = $this->factory->createBuilder(FormType::class)
            ->add('properties', PolymorphicCollectionType::class, $this->createCollectionOptions([
                'types_options' => [
                    'size' => ['prototype_button_label' => 'Add size'],
                    'weight' => ['prototype_button_label' => 'Add weight'],
                    'category' => ['prototype_button_label' => 'Add category'],
                ],
            ]))
            ->getForm();

        $view = $form->createView();
        $prototypes = $view->children['properties']->vars['prototypes'];

        $this->assertSame(['size', 'weight', 'category'], array_keys($prototypes));
        $this->assertSame('Add size', $prototypes['size']->vars['prototype_button_label']);
        $this->assertSame('size', $prototypes['size']->children['_node_discr']->vars['value']);
    }

    public static function submittedNodeProvider(): iterable
    {
        yield 'size node' => [[
            '_node_discr' => 'size',
            'length' => '10',
            'width' => '20',
            'height' => '30',
        ], Size::class, 'length', '10'];

        yield 'weight node' => [[
            '_node_discr' => 'weight',
            'weight' => '95',
        ], Weight::class, 'weight', '95'];

        yield 'category node' => [[
            '_node_discr' => 'category',
            'categoryName' => 'News',
        ], Category::class, 'categoryName', 'News'];
    }

    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([
                new PolymorphicCollectionType(),
                new SizePropertyType(),
                new WeightPropertyType(),
                new CategoryPropertyType(),
            ], []),
        ];
    }

    private function createCollectionOptions(array $overrides = []): array
    {
        return array_replace_recursive([
            'form_factory' => $this->factory,
            'types_map' => [
                'size' => SizePropertyType::class,
                'weight' => WeightPropertyType::class,
                'category' => CategoryPropertyType::class,
            ],
            'discriminator_map' => [
                'size' => Size::class,
                'weight' => Weight::class,
                'category' => Category::class,
            ],
        ], $overrides);
    }
}
