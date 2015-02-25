<?php

namespace Junior\SiteinterneBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Junior\SiteinterneBundle\Form\CategoryType;


class MissionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('publique', 'checkbox', array(
								'required' => false
					))
            ->add('appelOffreContactClient', 'choice', array(
					'choices' => array(
									   'ao' =>'Appel d\'offre',
									   'cc' => 'Contact client'
									   )
								))
            ->add('nom', 'text')
            ->add('descriptif', 'textarea')
            ->add('categories', 'entity', array(
					  'class'    => 'JuniorSiteinterneBundle:Category',
					  'property' => 'name',
					  'multiple' => true,
					  'required' => false
					  ))
            ->add('competences', 'entity', array(
					  'class'    => 'JuniorSiteinterneBundle:Competence',
					  'property' => 'nom',
					  'multiple' => true,
					  'required' => false
					  ))
			->add('save', 'submit')
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
        return 'junior_siteinternebundle_mission';
    }
}
