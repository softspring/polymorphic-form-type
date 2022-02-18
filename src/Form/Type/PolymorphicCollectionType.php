<?php

namespace Softspring\PolymorphicFormType\Form\Type;

use Softspring\PolymorphicFormType\Form\DataTransformer\NodeDataTransformer;
use Softspring\PolymorphicFormType\Form\Discriminator\NodeDiscriminator;
use Softspring\PolymorphicFormType\Form\EventListener\NodesResizeFormListener;
use Softspring\PolymorphicFormType\Form\Type\Node\AbstractNodeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PolymorphicCollectionType extends AbstractType
{
    protected FormFactory $formFactory;

    public function __construct(FormFactory $formFactory = null)
    {
        $this->formFactory = $formFactory;
    }

    public function getParent(): string
    {
        return CollectionType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'types_map' => [],
            'types_options' => [],
            'discriminator_map' => [],
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'error_bubbling' => false,
            'prototype_name' => '__node__',
            'form_factory' => null,
            'discriminator_field' => '_node_discr',
            'id_field' => null,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'polymorphic_collection';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (empty($options['types_map'])) {
            throw new RuntimeException('types_map must be set');
        }

        $this->configureResizeEventSubscriber($builder, $options);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (!$options['allow_add'] || !$options['prototype']) {
            return;
        }

        $prototypes = [];

        foreach ($options['types_map'] as $discr => $formClass) {
            /* @var AbstractNodeType $formType */
            if (is_object($formClass)) {
                $formType = $formClass;
            } elseif (class_exists($formClass)) {
                $formType = $formClass;
            } else {
                $formType = $formClass;
            }

            $formOptions = $options['types_options'][$discr] ?? [];
            $formOptions['discriminator_field'] = $options['discriminator_field'];

            $prototypeForm = $this->getFormFactory($options)->createNamedBuilder($options['prototype_name'], $formType, null, $formOptions)->getForm();
            $prototypeForm->get($options['discriminator_field'])->setData($discr);
            $prototypes[$discr] = $prototypeForm->createView($view);
        }

        $view->vars['prototypes'] = $prototypes;
    }

    /**
     * @throws RuntimeException
     */
    protected function getFormFactory(array $options): FormFactory
    {
        if ($options['form_factory']) {
            if (!$options['form_factory'] instanceof FormFactory) {
                throw new RuntimeException('form_factory option must contain an instance of FormFactory');
            }

            return $options['form_factory'];
        } elseif ($this->formFactory) {
            return $this->formFactory;
        }

        throw new RuntimeException('Form factory is required for PolymorphicCollectionType, check documentation.');
    }

    /**
     * Configure event subscriber for resizing with data transformer.
     */
    protected function configureResizeEventSubscriber(FormBuilderInterface $builder, array $options)
    {
        if (empty($options['discriminator_map'])) {
            throw new RuntimeException('discriminator_map must be set');
        }

        $discriminator = new NodeDiscriminator($options['discriminator_map'], $options['types_map'], $options['types_options']);
        $transformer = new NodeDataTransformer($discriminator, $options['discriminator_field'], $options['id_field']);
        $builder->addEventSubscriber(new NodesResizeFormListener($discriminator, $transformer, $options['discriminator_field'], $options['id_field']));
    }
}
