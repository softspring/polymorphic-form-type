<?php

namespace Softspring\Component\PolymorphicFormType\Tests\Unit\Form\Type;

use PHPUnit\Framework\TestCase;
use Softspring\Component\PolymorphicFormType\Form\Type\DoctrinePolymorphicCollectionType;
use Softspring\Component\PolymorphicFormType\Form\Type\PolymorphicCollectionType;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PolymorphicCollectionTypeTest extends TestCase
{
    public function testPolymorphicCollectionTypeMetadata(): void
    {
        $type = new PolymorphicCollectionType();

        self::assertSame(CollectionType::class, $type->getParent());
        self::assertSame('polymorphic_collection', $type->getBlockPrefix());
    }

    public function testPolymorphicCollectionTypeConfiguresDefaultOptions(): void
    {
        $resolver = new OptionsResolver();

        (new PolymorphicCollectionType())->configureOptions($resolver);
        $options = $resolver->resolve();

        self::assertSame([], $options['types_map']);
        self::assertSame([], $options['types_options']);
        self::assertSame([], $options['discriminator_map']);
        self::assertTrue($options['allow_add']);
        self::assertTrue($options['allow_delete']);
        self::assertFalse($options['by_reference']);
        self::assertFalse($options['error_bubbling']);
        self::assertSame('__node__', $options['prototype_name']);
        self::assertNull($options['form_factory']);
        self::assertSame('_node_discr', $options['discriminator_field']);
        self::assertNull($options['id_field']);
    }

    public function testGetFormFactoryRequiresConcreteFormFactoryInstance(): void
    {
        $formFactory = $this->createStub(FormFactoryInterface::class);
        $type = new TestablePolymorphicCollectionType();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('form_factory option must contain an instance of FormFactory');

        $type->publicGetFormFactory([
            'form_factory' => $formFactory,
        ]);
    }

    public function testBuildFormRequiresTypesMap(): void
    {
        $type = new PolymorphicCollectionType();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('types_map must be set');

        $type->buildForm($this->createStub(FormBuilderInterface::class), [
            'types_map' => [],
        ]);
    }

    public function testGetFormFactoryUsesConstructorFormFactory(): void
    {
        $formFactory = $this->createMock(FormFactory::class);
        $type = new TestablePolymorphicCollectionType($formFactory);

        self::assertSame($formFactory, $type->publicGetFormFactory([]));
    }

    public function testGetFormFactoryRequiresAvailableFormFactory(): void
    {
        $type = new TestablePolymorphicCollectionType();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Form factory is required for PolymorphicCollectionType, check documentation.');

        $type->publicGetFormFactory([]);
    }

    public function testDoctrineCollectionTypeRequiresAbstractClass(): void
    {
        $type = new DoctrinePolymorphicCollectionType();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('abstract_class must be set');

        $type->buildForm($this->createStub(FormBuilderInterface::class), [
            'abstract_class' => null,
        ]);
    }
}

class TestablePolymorphicCollectionType extends PolymorphicCollectionType
{
    public function publicGetFormFactory(array $options): FormFactoryInterface
    {
        return $this->getFormFactory($options);
    }
}
