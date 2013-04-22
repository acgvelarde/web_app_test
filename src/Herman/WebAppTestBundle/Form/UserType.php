<?php

namespace Herman\WebAppTestBundle\Form;

use Herman\WebAppTestBundle\Form\ListType\CountryListType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array('label' => 'Email Address'))
            ->add('firstName', 'text', array('label' => 'First Name'))
            ->add('lastName', 'text', array('label' => 'Last Name'))
            ->add('country', new CountryListType())
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Herman\WebAppTestBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'herman_webapptestbundle_usertype';
    }
}
