<?php

namespace Softspring\PolymorphicFormType\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use Softspring\PolymorphicFormType\Form\DataTransformer\NodeDataTransformer;
use Softspring\PolymorphicFormType\Form\Discriminator\DoctrineNodeDiscriminator;
use Softspring\PolymorphicFormType\Form\EventListener\NodesResizeFormListener;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DoctrinePolymorphicCollectionType extends PolymorphicCollectionType
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * DoctrinePolymorphicCollectionType constructor.
     */
    public function __construct(FormFactory $formFactory = null, EntityManagerInterface $em = null)
    {
        parent::__construct($formFactory);
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'entity_manager' => null,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'polymorphic_collection';
    }

    /**
     * @return EntityManagerInterface
     *
     * @throws RuntimeException
     */
    protected function getEntityManager(array $options)
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

    /**
     * {@inheritdoc}
     */
    protected function configureResizeEventSubscriber(FormBuilderInterface $builder, array $options)
    {
        $em = $this->getEntityManager($options);
        $discriminator = new DoctrineNodeDiscriminator($em, $options['types_map'], $options['abstract_class']);
        $transformer = new NodeDataTransformer($discriminator);
        $builder->addEventSubscriber(new NodesResizeFormListener($discriminator, $transformer));
    }
}
