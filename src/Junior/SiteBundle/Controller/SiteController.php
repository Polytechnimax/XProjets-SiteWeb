<?php

namespace Junior\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class SiteController extends Controller
{
    public function indexAction()
    {
        return $this->render('JuniorSiteBundle:Site:index.html.twig');
    }
	
	public function mailAction(Request $request)
	{
		$defaultData = array(
			'email' => 'Votre mail...',
			'societe' => 'Votre société...',
			'nom' => 'Votre nom...',
			'message' => 'Votre message...'
			);
			
		$form = $this->createFormBuilder($defaultData)
			->setAction($this->generateUrl('junior_site_mail_contact'))
			->setMethod('POST')
			->add('email', 'email')
			->add('societe', 'text')
			->add('nom', 'text')
			->add('message', 'textarea')
			->getForm();

		$form->handleRequest($request);
		
		if ($form->isValid()) {
				$data = $form->getData();
				
				$message_client = \Swift_Message::newInstance()
					->setSubject('Contact [Xprojets]')
					->setFrom('contact@xprojets.com')
					->setTo($data['email'])
					->setBody($this->renderView('JuniorSiteBundle:Site:mail-contact-client.html.twig', array('nom'=>$data['nom'], 'message'=>$data['message'], 'societe'=>$data['societe'], 'email'=>$data['email']) ))
				;
				
				$message_xprojets = \Swift_Message::newInstance()
					->setSubject('Contact [Xprojets]')
					->setFrom($data['email'])
					->setTo('contact@xprojets.com')
					->setBody($this->renderView('JuniorSiteBundle:Site:mail-contact-xprojets.html.twig', array('nom'=>$data['nom'], 'message'=>$data['message'], 'societe'=>$data['societe'], 'email'=>$data['email']) ))
				;
				
				$this->get('mailer')->send($message_client);
				$this->get('mailer')->send($message_xprojets);
				 
				return $this->redirect($this->generateUrl('junior_site_homepage')); 
			}
		
		return $this->render('JuniorSiteBundle:Site:formulaire-contact.html.twig', array('form' => $form->createView()));
	
	
	}
}
