<?php

namespace Junior\SiteinterneBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Junior\SiteinterneBundle\Form\CategoryType;


class MissionVxClientType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('client', 'entity', array(
					  'class'    => 'JuniorSiteinterneBundle:Client',
					  'property' => 'nom'
					  ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Junior\SiteinterneBundle\Entity\Mission'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'junior_siteinternebundle_missionvxclient';
    }
	
	  public function getParent()
  {
    return new MissionType();
  }
}
