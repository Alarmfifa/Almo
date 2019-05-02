<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Tags;

class OperationsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // TODO может в базе вести не 1/0, а add/pay?
        $type = ($options['act'] == 'add') ? 1 : 2;
        $builder->add('notice')
            ->add('date', null, array(
            'widget' => 'single_text',
            'format' => 'yyyy-MM-dd HH:mm',
        ))
            ->add('type', HiddenType::class, array(
            'data' => strtolower($options['act']),
        ))
            ->add('payments', CollectionType::class, array(
            'entry_type' => PaymentsType::class,
            'entry_options' => (array(
                'userId' => $options['userId'],
                'accRep' => $options['accRep'],
            )),
        ));

        if ($options['act'] == 'transfer') {
            $builder->add('title', HiddenType::class, array(
                'data' => 'Exchange',
            ))->add('tagId', HiddenType::class, array(
                'data' => null,
            ));
        } else {
            $builder->add('title')->add('tagId', EntityType::class, array(
                'class' => Tags::class,
                'choices' => $options['tagRep']->getUserTags($options['user'], $type),
            ));
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Operations',
            'tagRep' => false,
            'accRep' => false,
            'userId' => false,
            'user' => false,
            'act' => false,
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'almo_walletbundle_operations';
    }
}
