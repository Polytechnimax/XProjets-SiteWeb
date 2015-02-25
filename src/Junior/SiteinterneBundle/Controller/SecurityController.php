<?php
/*
namespace Junior\SiteinterneBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Junior\SiteinterneBundle\Entity\User;
use Junior\SiteinterneBundle\Form\MissionType;
use Symfony\Component\Security\Core\SecurityContext;

class SecurityController extends Controller
{
    public function loginAction(Request $request)
    {
		// $logger = $this->get('logger');
		// $logger->info($request->query->get('hruid'));
		// Si le visiteur est déjà identifié, on le redirige vers l'accueil
		if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
		  return $this->redirect($this->generateUrl('junior_siteinterne_index'));
		}
		
		
		if(null === $request->query->get('response')){
			return $this->redirect('http://albi-gite.fr/fkz.php');
		}
		
		$reponse = json_decode($request->query->get('response'),true);
		
		$em = $this->getDoctrine()->getManager();
		$repository = $this
						->getDoctrine()
						->getManager()
						->getRepository('JuniorSiteinterneBundle:User');
		$utilisateur = $repository->findOneByUsername($reponse['hruid']);
		if($utilisateur == null){
			$utilisateur = new User();
			$utilisateur->setUsername($reponse['hruid']);
			$utilisateur->setFirstName($reponse['firstname']);
			$utilisateur->setLastName($reponse['lastname']);
			$utilisateur->setEmail($reponse['email']);
			$utilisateur->setPromo($reponse['promo']);
			$utilisateur->setSalt('');
			$utilisateur->setPassword("1z8X6dS54");
			$utilisateur->setHash('');
			$utilisateur->setRoles(array('ROLE_USER'));
			$em->persist($utilisateur);
			$em->flush();
		}
		
		

		$session = $request->getSession();

		// On vérifie s'il y a des erreurs d'une précédente soumission du formulaire
		if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
		  $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
		} else {
		  $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
		  $session->remove(SecurityContext::AUTHENTICATION_ERROR);
		}

		return $this->render('JuniorSiteinterneBundle:Security:login.html.twig', array(
		  // Valeur du précédent nom d'utilisateur entré par l'internaute
		  'last_username' => $session->get(SecurityContext::LAST_USERNAME),
		  'error'         => $error,
		  'utilisateur' => $utilisateur
		));
    }
	
    public function redirigeAction(Request $request)
    {
		return $this->render('JuniorSiteinterneBundle:Security:fkz.html.php');
    }
}*/


namespace Junior\SiteinterneBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Junior\SiteinterneBundle\Entity\User;
use Junior\SiteinterneBundle\Form\MissionType;
use Symfony\Component\Security\Core\SecurityContext;

class SecurityController extends Controller
{
    public function loginAction(Request $request)
    {
		// Si le visiteur est déjà identifié, on le redirige vers l'accueil
		if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
		  return $this->redirect($this->generateUrl('junior_siteinterne_index'));
		}

		$session = $request->getSession();

		// On vérifie s'il y a des erreurs d'une précédente soumission du formulaire
		if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
		  $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
		} else {
		  $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
		  $session->remove(SecurityContext::AUTHENTICATION_ERROR);
		}

		return $this->render('JuniorSiteinterneBundle:Security:login.html.twig', array(
		  // Valeur du précédent nom d'utilisateur entré par l'internaute
		  'last_username' => $session->get(SecurityContext::LAST_USERNAME),
		  'error'         => $error,
		));
    }
}
