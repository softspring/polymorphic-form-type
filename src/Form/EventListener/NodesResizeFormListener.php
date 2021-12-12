<?php

namespace Softspring\PolymorphicFormType\Form\EventListener;

use Softspring\PolymorphicFormType\Form\Discriminator\NodeDiscriminatorInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class NodesResizeFormListener
 */
class NodesResizeFormListener implements EventSubscriberInterface
{
    /**
     * @var NodeDiscriminatorInterface
     */
    protected $nodeDiscriminator;

    /**
     * @var DataTransformerInterface
     */
    protected $transformer;

    /**
     * NodesResizeFormListener constructor.
     *
     * @param NodeDiscriminatorInterface $nodeDiscriminator
     * @param DataTransformerInterface   $transformer
     */
    public function __construct(NodeDiscriminatorInterface $nodeDiscriminator, DataTransformerInterface $transformer)
    {
        $this->nodeDiscriminator = $nodeDiscriminator;
        $this->transformer = $transformer;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }

    /**
     * Before data is set, add the existing collection subforms
     *
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $nodes = $event->getData() ?? [];
        $form = $event->getForm();

        foreach ($nodes as $i => $node) {
            // get form class for entity
            $discr = $this->nodeDiscriminator->getDiscriminatorForObject($node);

            $this->addSubform($i, $discr, $form);
        }
    }

    /**
     * After submitted data is set (and before validation), add the new collection subforms
     *
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $nodes = $event->getData();
        $form = $event->getForm();

        if (empty($nodes)) {
            return;
        }

        foreach ($nodes as $i => $node) {
            if ($node['_node_id']) {
                continue;
            }

            $this->addSubform($i, $node['_node_discr'], $form);
        }
    }

    /**
     * @param mixed         $name
     * @param string        $discr
     * @param FormInterface $form
     */
    protected function addSubform($name, $discr, FormInterface $form)
    {
        $formClass = $this->nodeDiscriminator->getFormTypeFromDiscriminator($discr);

        if (empty($formClass)) {
            throw new RuntimeException(sprintf('No form type was found for %s discriminator value', $discr));
        }

        $formOptions = [
            'node_data_transformer' => $this->transformer,
            'error_bubbling' => false,
        ];

        /** @var AbstractType $formType */
        if (is_object($formClass)) {
            $formType = $formClass;
        } elseif (is_string($formClass) && class_exists($formClass)) {
            $formType = $formClass;
        } else {
            $formType = $formClass;
        }
        $form->add($name, $formType, $formOptions);
    }
}