<?php

namespace Didier\Bundle\OAuth2ServerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AccessTokenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', 'text', [
                'required' => false,
            ])
            ->add('expiresAt', 'text', [
                'required' => false,
            ])
            ->add('scope', 'text', [
                'required' => false,
            ])
        ;
    }

    public function getName()
    {
        return 'access_token';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Didier\Bundle\OAuth2ServerBundle\Entity\AccessToken',
        ));
    }
}
