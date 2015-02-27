<?php

namespace Junior\SiteinterneBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ClientType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', 'text', array('required' => false))
            ->add('particulierEntreprise', 'choice', array(
					'choices' => array(
									   'particulier' =>'Particulier',
									   'entreprise' => 'Entreprise',
									)
								))
            ->add('taille', 'choice', array(
					'choices' => array(
									   'administration' =>'Administration',
									   'association' => 'Association',
									   'ecoleuniversité' => 'Ecole/université',
									   'grand_groupe' => 'Grand groupe',
									   'je' => 'JE',
									   'particulier' => 'Particulier',
									   'pme_eti' => 'PME/ETI',
									   'tpe' => 'TPE'
									),
					'required' => false)
				)
            ->add('nomContact', 'text')
            ->add('prenomContact', 'text')
            ->add('fonctionContact', 'text')
            ->add('civiliteContact', 'choice', array(
					'choices' => array(
									   'm' =>'M.',
									   'mme' => 'Mme.',
									),
					'required' => false)
				)
            ->add('email', 'text')
            ->add('telephone', 'text')
            ->add('adresse1', 'text')
            ->add('adresse2', 'text', array('required' => false))
            ->add('adresse3', 'text', array('required' => false))
            ->add('ville', 'text')
            ->add('codePostal', 'text')
            ->add('pays', 'text')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Junior\SiteinterneBundle\Entity\Client'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'junior_siteinternebundle_client';
    }
}
