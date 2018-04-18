<?php

namespace Softspring\PolymorphicFormType\Form\Type\Node;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AbstractNodeType
 */
abstract class AbstractNodeType extends AbstractType
{
    /**
     * Declare as final to force to define some defaults
     *
     * If needed override configureChildOptions method
     *
     * {@inheritdoc}
     */
    final public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'prototype_button_label' => null,
            'prototype_button_attr' => [],
            'node_data_transformer' => null,
            'error_bubbling' => false,
        ]);

        $this->configureChildOptions($resolver);
    }

    /**
     * @param OptionsResolver $resolver
     */
    protected function configureChildOptions(OptionsResolver $resolver)
    {
        // nothing to do on default, override method if needed
    }

    /**
     * Declare as final to force to define some defaults
     *
     * If needed override buildChildForm method
     *
     * {@inheritdoc}
     */
    final public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!empty($options['data_class'])) {
            throw new InvalidConfigurationException('AbstractNodeType requires no data_class to be defined in child types');
        }

        $builder->add('_node_id', HiddenType::class);
        $builder->add('_node_discr', HiddenType::class);

        $this->buildChildForm($builder, $options);

        if ($options['node_data_transformer'] instanceof DataTransformerInterface) {
            $builder->addModelTransformer($options['node_data_transformer']);
        }
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    abstract protected function buildChildForm(FormBuilderInterface $builder, array $options);

    /**
     * Declare as final to force to define some defaults
     *
     * If needed override buildChildView method
     *
     * {@inheritdoc}
     */
    final public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $this->buildChildView($view, $form, $options);
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    protected function buildChildView(FormView $view, FormInterface $form, array $options)
    {
        // nothing to do on default, override method if needed
    }

    /**
     * Declare as final to force to define some defaults
     *
     * If needed override finishChildView method
     *
     * {@inheritdoc}
     */
    final public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $this->finishChildView($view, $form, $options);
        $view->vars['attr']['data-index'] = $view->vars['name'];
        $view->vars['prototype_button_label'] = $options['prototype_button_label'];
        $view->vars['prototype_button_attr'] = $options['prototype_button_attr'];
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    protected function finishChildView(FormView $view, FormInterface $form, array $options)
    {
        // nothing to do on default, override method if needed
    }
}