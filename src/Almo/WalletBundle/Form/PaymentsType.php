<?php

namespace Almo\WalletBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PaymentsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	
		// TODO replace accountRepository to Entity    	
        $builder
            ->add('amount')
	        ->add('accountId', 'entity', array(
	        		'class' => 'AlmoWalletBundle:Accounts',
	        		'choices' => $options['accRep']->findByUserId($options['userId']),
	        	))
            ->add('currencyId', 'entity', array('class'=>'AlmoWalletBundle:Currency', 'property'=>'short'));
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Almo\WalletBundle\Entity\Payments',
        	'userId' => false,
        	'accRep' => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'almo_walletbundle_payments';
    }
}
