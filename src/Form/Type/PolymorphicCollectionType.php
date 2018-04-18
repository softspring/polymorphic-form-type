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

/**
 * Class PolymorphicCollectionType
 */
class PolymorphicCollectionType extends AbstractType
{
    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * PolymorphicCollectionType constructor.
     *
     * @param FormFactory $formFactory
     */
    public function __construct(FormFactory $formFactory = null)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return CollectionType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'abstract_class' => null,
            'types_map' => [],
            'discriminator_map' => [],
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'error_bubbling' => false,
            'prototype_name' => '__node__',
            'form_factory' => null,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'polymorphic_collection';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (empty($options['abstract_class'])) {
            throw new RuntimeException('abstract_class must be set');
        }

        if (empty($options['types_map'])) {
            throw new RuntimeException('types_map must be set');
        }

        $this->configureResizeEventSubscriber($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (!$options['allow_add'] || !$options['prototype']) {
            return;
        }

        $prototypes = [];

        foreach ($options['types_map'] as $discr => $formClass) {
            /** @var AbstractNodeType $formType */
            if (is_object($formClass)) {
                $formType = $formClass;
            } elseif (class_exists($formClass)) {
                $formType = $formClass;
            } else {
                $formType = $formClass;
            }

            $prototypeForm = $this->getFormFactory($options)->createNamedBuilder($options['prototype_name'], $formType, null, [])->getForm();
            $prototypeForm->get('_node_discr')->setData($discr);
            $prototypes[$discr] = $prototypeForm->createView($view);
        }

        $view->vars['prototypes'] = $prototypes;
    }

    /**
     * @param array $options
     *
     * @return FormFactory
     * @throws RuntimeException
     */
    protected function getFormFactory(array $options)
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
     * Configure event subscriber for resizing with data transformer
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    protected function configureResizeEventSubscriber(FormBuilderInterface $builder, array $options)
    {
        if (empty($options['discriminator_map'])) {
            throw new RuntimeException('discriminator_map must be set');
        }

        $discriminator = new NodeDiscriminator($options['discriminator_map'], $options['types_map']);
        $transformer = new NodeDataTransformer($discriminator);
        $builder->addEventSubscriber(new NodesResizeFormListener($discriminator, $transformer));
    }
}