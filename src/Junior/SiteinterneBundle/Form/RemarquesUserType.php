<?php

namespace Junior\SiteinterneBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RemarquesUserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contenu', 'textarea')
			->add('save', 'submit')
		;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Junior\SiteinterneBundle\Entity\RemarquesUser'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'junior_siteinternebundle_remarquesuser';
    }
}
