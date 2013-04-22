<?php

namespace Herman\WebAppTestBundle\Form\ListType;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\AbstractType;

class CountryListType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'class' => 'Herman\WebAppTestBundle\Entity\Country'
        ));
    }
    
    public function getName()
    {
        return 'country_list_type';
    }
    
    public function getParent()
    {
        return 'entity';
    }
}