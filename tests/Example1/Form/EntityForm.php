<?php

namespace Softspring\Component\PolymorphicFormType\Tests\Example1\Form;

use Softspring\Component\PolymorphicFormType\Form\Type\PolymorphicCollectionType;
use Softspring\Component\PolymorphicFormType\Tests\Example1\Form\Type\CategoryPropertyType;
use Softspring\Component\PolymorphicFormType\Tests\Example1\Form\Type\SizePropertyType;
use Softspring\Component\PolymorphicFormType\Tests\Example1\Form\Type\WeightPropertyType;
use Softspring\Component\PolymorphicFormType\Tests\Example1\Model\Entity;
use Softspring\Component\PolymorphicFormType\Tests\Example1\Model\Properties\AbstractProperty;
use Softspring\Component\PolymorphicFormType\Tests\Example1\Model\Properties\Category;
use Softspring\Component\PolymorphicFormType\Tests\Example1\Model\Properties\Size;
use Softspring\Component\PolymorphicFormType\Tests\Example1\Model\Properties\Weight;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntityForm extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Entity::class,
            'form_factory' => null,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name');

        $builder->add('properties', PolymorphicCollectionType::class, [
            'abstract_class' => AbstractProperty::class,
            'form_factory' => $options['form_factory'],
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
        ]);
    }
}
