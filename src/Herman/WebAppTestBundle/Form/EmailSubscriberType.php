<?php

namespace Herman\WebAppTestBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\Validator\Constraints\Email;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EmailSubscriberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('email', 'email')
            //->add('email', 'email', array('constraints' => array(new Email(), new NotBlank())))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Herman\WebAppTestBundle\Entity\EmailSubscriber'
        ));
    }

    public function getName()
    {
        return 'herman_webapptestbundle_emailsubscribertype';
    }
}
