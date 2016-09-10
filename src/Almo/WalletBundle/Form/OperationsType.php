<?php

namespace Almo\WalletBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class OperationsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	// TODO может в базе вести не 1/0, а add/pay?
    	$type = ( $options['act'] == 'add' ) ? 1 : 2;
        $builder
            ->add('notice')
            ->add('date',  null, array('widget' => 'single_text', 'format' => 'yyyy-MM-dd HH:mm'))
            ->add('type', 'hidden', array('data' => strtolower($options['act'])))
			->add('payments', 'collection', array('type' => new PaymentsType(), 'options' => (array('userId' => $options['userId'], 'accRep' => $options['accRep'])) ))
        ;
        
		if ($options['act'] == 'transfer') {
			$builder
				->add('title', 'hidden', array('data' => 'Exchange'))
				->add('tagId', 'hidden', array('data' => null));
		}
		else {
			$builder
				->add('title' )
				->add('tagId', 'entity', array(
						'class' => 'AlmoWalletBundle:Tags',
						'choices' => $options['tagRep']->getUserTags($options['user'], $type),
				));
		}
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Almo\WalletBundle\Entity\Operations',
        	'tagRep' => false,
        	'accRep' => false,
        	'userId' => false,
        	'user' => false,
        	'act' => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'almo_walletbundle_operations';
    }
}
