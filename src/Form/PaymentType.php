<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Currency;
use App\Entity\Account;

class PaymentType extends AbstractType
{

    /**
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        // TODO replace accountRepository to Entity
        $builder->add('amount')
            ->add('accountId', EntityType::class, array(
            'class' => Account::class,
            'choices' => $options['accRep']->findByUserId($options['userId'])
        ))
            ->add('currencyId', EntityType::class, array(
            'class' => Currency::class,
            'choice_label' => 'short'
        ));
    }

    /**
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Payment',
            'userId' => false,
            'accRep' => false,
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'almo_walletbundle_payments';
    }
}
