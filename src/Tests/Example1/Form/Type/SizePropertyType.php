<?php

namespace Softspring\PolymorphicFormType\Tests\Example1\Form\Type;

use Softspring\PolymorphicFormType\Form\Type\Node\AbstractNodeType;
use Symfony\Component\Form\FormBuilderInterface;

class SizePropertyType extends AbstractNodeType
{
    public function buildChildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('length');
        $builder->add('width');
        $builder->add('height');
    }
}