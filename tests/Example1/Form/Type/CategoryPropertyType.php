<?php

namespace Softspring\Component\PolymorphicFormType\Tests\Example1\Form\Type;

use Softspring\Component\PolymorphicFormType\Form\Type\Node\AbstractNodeType;
use Symfony\Component\Form\FormBuilderInterface;

class CategoryPropertyType extends AbstractNodeType
{
    public function buildChildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('categoryName');
    }
}
