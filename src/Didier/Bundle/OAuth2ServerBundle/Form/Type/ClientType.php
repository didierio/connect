<?php

namespace Didier\Bundle\OAuth2ServerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', [
                'constraints' => [new NotBlank()],
            ])
            ->add('redirectUri', 'text', [
                'required' => false,
            ])
            ->add('allowedGrants', 'text')
        ;
    }

    public function getName()
    {
        return 'client';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Didier\Bundle\OAuth2ServerBundle\Entity\Client',
        ));
    }
}
