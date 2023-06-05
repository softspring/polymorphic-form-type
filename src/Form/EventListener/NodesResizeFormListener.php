<?php

namespace Softspring\Component\PolymorphicFormType\Form\EventListener;

use Softspring\Component\PolymorphicFormType\Form\Discriminator\NodeDiscriminatorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class NodesResizeFormListener implements EventSubscriberInterface
{
    protected NodeDiscriminatorInterface $nodeDiscriminator;

    protected DataTransformerInterface $transformer;

    protected string $discriminatorField;

    protected ?string $idField;

    public function __construct(NodeDiscriminatorInterface $nodeDiscriminator, DataTransformerInterface $transformer, string $discriminatorField, ?string $idField)
    {
        $this->nodeDiscriminator = $nodeDiscriminator;
        $this->transformer = $transformer;
        $this->discriminatorField = $discriminatorField;
        $this->idField = $idField;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    /**
     * Before data is set, add the existing collection subforms.
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
     * After submitted data is set (and before validation), add the new collection subforms.
     */
    public function preSubmit(FormEvent $event)
    {
        $nodes = $event->getData();
        $form = $event->getForm();

        if (empty($nodes)) {
            return;
        }

        foreach ($nodes as $i => $node) {
            if ($this->idField && $node[$this->idField]) {
                continue;
            }

            $this->addSubform($i, $node[$this->discriminatorField], $form);
        }
    }

    protected function addSubform($name, string $discr, FormInterface $form)
    {
        $formClass = $this->nodeDiscriminator->getFormTypeFromDiscriminator($discr);

        if (empty($formClass)) {
            throw new RuntimeException(sprintf('No form type was found for %s discriminator value', $discr));
        }

        $formOptions = $this->nodeDiscriminator->getFormTypeOptionsFromDiscriminator($discr);

        $formOptions += [
            'node_data_transformer' => $this->transformer,
            'error_bubbling' => false,
            'discriminator_field' => $this->discriminatorField,
            'id_field' => $this->idField,
        ];

        $form->add($name, $formClass, $formOptions);
    }
}
