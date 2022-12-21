<?php

namespace Softspring\Component\PolymorphicFormType\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use Softspring\Component\PolymorphicFormType\Form\DataTransformer\NodeDataTransformer;
use Softspring\Component\PolymorphicFormType\Form\Discriminator\DoctrineNodeDiscriminator;
use Softspring\Component\PolymorphicFormType\Form\EventListener\NodesResizeFormListener;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DoctrinePolymorphicCollectionType extends PolymorphicCollectionType
{
    protected ?EntityManagerInterface $em;

    public function __construct(FormFactory $formFactory = null, EntityManagerInterface $em = null)
    {
        parent::__construct($formFactory);
        $this->em = $em;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'abstract_class' => null,
            'entity_manager' => null,
            'id_field' => '_node_id',
        ]);

        $resolver->setRequired('abstract_class');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (empty($options['abstract_class'])) {
            throw new RuntimeException('abstract_class must be set');
        }

        parent::buildForm($builder, $options);
    }

    public function getBlockPrefix(): string
    {
        return 'polymorphic_collection';
    }

    /**
     * @throws RuntimeException
     */
    protected function getEntityManager(array $options): EntityManagerInterface
    {
        if ($options['entity_manager']) {
            if (!$options['entity_manager'] instanceof EntityManagerInterface) {
                throw new RuntimeException('entity_manager option must contain a valid doctrine entity manager');
            }

            return $options['entity_manager'];
        } elseif ($this->em) {
            return $this->em;
        }

        throw new RuntimeException('Entity manager is required for DoctrinePolymorphicCollectionType, check documentation.');
    }

    protected function configureResizeEventSubscriber(FormBuilderInterface $builder, array $options)
    {
        $em = $this->getEntityManager($options);
        $discriminator = new DoctrineNodeDiscriminator($em, $options['types_map'], $options['abstract_class'], $options['types_options'], $options['discriminator_field']);
        $transformer = new NodeDataTransformer($discriminator, $options['discriminator_field'], $options['id_field']);
        $builder->addEventSubscriber(new NodesResizeFormListener($discriminator, $transformer, $options['discriminator_field'], $options['id_field']));
    }
}
