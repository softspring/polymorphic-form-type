<?php

namespace Softspring\Component\PolymorphicFormType\Tests\Unit\Form\Type;

use PHPUnit\Framework\TestCase;
use Softspring\Component\PolymorphicFormType\Form\Type\PolymorphicCollectionType;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;

class PolymorphicCollectionTypeTest extends TestCase
{
    public function testGetFormFactoryAcceptsFormFactoryInterfaceInstances(): void
    {
        $formFactory = $this->createStub(FormFactoryInterface::class);
        $type = new TestablePolymorphicCollectionType();

        $this->assertSame($formFactory, $type->publicGetFormFactory([
            'form_factory' => $formFactory,
        ]));
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
}

class TestablePolymorphicCollectionType extends PolymorphicCollectionType
{
    public function publicGetFormFactory(array $options): FormFactoryInterface
    {
        return $this->getFormFactory($options);
    }
}
