<?php

namespace Softspring\PolymorphicFormType\Tests\Example1\Form\Type;

use Softspring\PolymorphicFormType\Form\Type\Node\AbstractNodeType;
use Symfony\Component\Form\FormBuilderInterface;

class CategoryPropertyType extends AbstractNodeType
{
    public function buildChildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('categoryName');
    }
}