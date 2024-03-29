<?php

namespace Softspring\Component\PolymorphicFormType\Form\Type\Node;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AbstractNodeType.
 */
abstract class AbstractNodeType extends AbstractType
{
    /**
     * Declare as final to force to define some defaults.
     *
     * If needed override configureChildOptions method
     *
     * {@inheritdoc}
     */
    final public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'prototype_button_label' => null,
            'prototype_button_attr' => [],
            'node_data_transformer' => null,
            'error_bubbling' => false,
            'discriminator_field' => '_node_discr',
            'id_field' => null,
        ]);

        $this->configureChildOptions($resolver);
    }

    protected function configureChildOptions(OptionsResolver $resolver): void
    {
        // nothing to do on default, override method if needed
    }

    /**
     * Declare as final to force to define some defaults.
     *
     * If needed override buildChildForm method
     *
     * {@inheritdoc}
     */
    final public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!empty($options['data_class'])) {
            throw new InvalidConfigurationException('AbstractNodeType requires no data_class to be defined in child types');
        }

        if ($options['id_field']) {
            $builder->add($options['id_field'], HiddenType::class);
        }

        $builder->add($options['discriminator_field'], HiddenType::class);

        $this->buildChildForm($builder, $options);

        if ($options['node_data_transformer'] instanceof DataTransformerInterface) {
            $builder->addModelTransformer($options['node_data_transformer']);
        }
    }

    abstract protected function buildChildForm(FormBuilderInterface $builder, array $options): void;

    /**
     * Declare as final to force to define some defaults.
     *
     * If needed override buildChildView method
     *
     * {@inheritdoc}
     */
    final public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $this->buildChildView($view, $form, $options);
    }

    protected function buildChildView(FormView $view, FormInterface $form, array $options): void
    {
        // nothing to do on default, override method if needed
    }

    /**
     * Declare as final to force to define some defaults.
     *
     * If needed override finishChildView method
     *
     * {@inheritdoc}
     */
    final public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $this->finishChildView($view, $form, $options);
        $view->vars['attr']['data-index'] = $view->vars['name'];
        $view->vars['attr']['data-full-name'] = $view->vars['full_name'];
        $view->vars['prototype_button_label'] = $options['prototype_button_label'];
        $view->vars['prototype_button_attr'] = $options['prototype_button_attr'];
    }

    protected function finishChildView(FormView $view, FormInterface $form, array $options): void
    {
        // nothing to do on default, override method if needed
    }
}
