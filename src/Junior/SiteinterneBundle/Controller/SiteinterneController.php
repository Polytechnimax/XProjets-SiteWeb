<?php

namespace Junior\SiteinterneBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use \DateTime;
use \DateInterval;
use Junior\SiteinterneBundle\Entity\Mission;
use Junior\SiteinterneBundle\Form\MissionType;
use Junior\SiteinterneBundle\Form\MissionNvClientType;
use Junior\SiteinterneBundle\Form\MissionVxClientType;
use Junior\SiteinterneBundle\Entity\Category;
use Junior\SiteinterneBundle\Form\CategoryType;
use Junior\SiteinterneBundle\Entity\User;
use Junior\SiteinterneBundle\Entity\Client;
use Junior\SiteinterneBundle\Form\ClientType;
use Junior\SiteinterneBundle\Entity\Competence;
use Junior\SiteinterneBundle\Form\CompetenceType;
use Junior\SiteinterneBundle\Entity\RemarquesMission;
use Junior\SiteinterneBundle\Form\RemarquesMissionType;
use Junior\SiteinterneBundle\Entity\Document;
use Junior\SiteinterneBundle\Form\DocumentType;
use Junior\SiteinterneBundle\Entity\RemarquesDocument;
use Junior\SiteinterneBundle\Form\RemarquesDocumentType;
use Junior\SiteinterneBundle\Entity\RemarquesUser;
use Junior\SiteinterneBundle\Form\RemarquesUserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SiteinterneController extends Controller
{
  public function indexAction()
  {
		$listeMissions = $this->getDoctrine()
							  ->getRepository('JuniorSiteinterneBundle:Mission')
							  ->getMissionAvecCategories();

		$user = $this->getUser();
		if (null === $user) {
			return $this->redirect($this->generateUrl('login'));
		}

		$listeMissionsCDP = $this->getDoctrine()
							  ->getRepository('JuniorSiteinterneBundle:Mission')
							  ->getMissionEnTantQueCDP($user);
		$listeMissionsINT = $this->getDoctrine()
							  ->getRepository('JuniorSiteinterneBundle:Mission')
							  ->getMissionEnTantQuIntervenant($user);
							  
		$argentGagne = 0;
		foreach($listeMissionsINT as $miss){
			if($miss->getNbJeh() != null && $miss->getEtat()=="Finie"){
				$argentGagne += $miss->getNbJeh()*180;
			}
		}
		return $this->render('JuniorSiteinterneBundle:Siteinterne:index.html.twig', array(
		'listeMissions' => $listeMissions,
		'listeMissionsCDP' => $listeMissionsCDP,
		'listeMissionsINT' => $listeMissionsINT,
		'argentGagne' => $argentGagne
		));
  }
		
  /**
   * @Security("has_role('ROLE_MBJE')")
   */
	public function missionsAction(){
		$listeMissions = $this->getDoctrine()
							  ->getRepository('JuniorSiteinterneBundle:Mission')
							  ->getMissionAvecCategories();
		return $this->render('JuniorSiteinterneBundle:Siteinterne:missions.html.twig', array(
		'listeMissions' => $listeMissions
		));
   }


  /**
   * @Security("has_role('ROLE_MBJE')")
   */
  public function addAction(Request $request){
		$tableau = array(
				"Devis",
				"Facture à accompte",
				"Propale",
				"Convention client",
				"PVRF",
				"Facture",
				"Rapport pédagogique",
				"Bulletin de versement",
				"Questionnaire de satisfaction",
				);
		$mission1 = new Mission();
		$form1 = $this->get('form.factory')->create(new MissionVxClientType(), $mission1);
		$mission2 = new Mission();
		$form2 = $this->get('form.factory')->create(new MissionNvClientType(), $mission2);


		if ($form1->handleRequest($request)->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$user = $this->getUser();
			if (null === $user) {
				return $this->redirect($this->generateUrl('junior_siteinterne_ajouter'));
			} else {
				$mission1->setAjoutePar($user);
				$numSiaje = $this->getDoctrine()
							  ->getRepository('JuniorSiteinterneBundle:Mission')
							  ->getProchainNum();
				$mission1->setNumSiaje($numSiaje);
				
				if($this->getDoctrine()->getRepository('JuniorSiteinterneBundle:Mission')->isThereMission()){
					$dernierReferent=$this->getDoctrine()
										->getRepository('JuniorSiteinterneBundle:Mission')
										->getDernierReferent();
					if($dernierReferent != null){
						$referentMission = $this->getDoctrine()
									  ->getRepository('JuniorSiteinterneBundle:User')
									  ->getProchainReferent($dernierReferent);
						$mission1->setReferent($referentMission);
					}
				}else{
					$referent;
					$users = $this->getDoctrine()
								  ->getRepository('JuniorSiteinterneBundle:User')
								  ->findAll();
					foreach($users as $u){
						foreach($u->getRoles() as $r){
							if($r == "ROLE_POLEMISSION"){
								$referent = $u;
							}
						}
					}
					$mission1->setReferent($referent);
				}
				$em->persist($mission1);
				
		//---Debut des mails
				//Msg au référent
				$message_referent = \Swift_Message::newInstance()
					->setSubject('[XProjets] Nouvelle mission postée')
					->setFrom('contact@xprojets.com')
					->setTo($mission1->getReferent()->getEmail())
					->setBody($this->renderView('JuniorSiteinterneBundle:Mails:mail-referent-mission-ajoutee.html.twig', array(
					'mission' => $mission1
					)))
				;
				$this->get('mailer')->send($message_referent);
				//Message au respo recrutement
				$recruteur;
				$users = $this->getDoctrine()
							  ->getRepository('JuniorSiteinterneBundle:User')
							  ->findAll();
				foreach($users as $u){
					foreach($u->getRoles() as $r){
						if($r == "ROLE_RECRUTEUR"){
							$recruteur = $u;
						}
					}
				}
				if($recruteur != null){
					$message_recruteur = \Swift_Message::newInstance()
						->setSubject('[XProjets] Nouvelle mission postée')
						->setFrom('contact@xprojets.com')
						->setTo($recruteur->getEmail())
						->setBody($this->renderView('JuniorSiteinterneBundle:Mails:mail-recruteur-mission-ajoutee.html.twig', array(
						'mission' => $mission1
						)))
					;
					$this->get('mailer')->send($message_recruteur);
				}
		//---Fin des mails
				
				foreach($tableau as $typeDoc){
					$doc = new Document();
					$doc->setTypeDeDocument($typeDoc);
					$doc->setFrozen(false);
					$doc->setMission($mission1);
					$doc->setAjoutePar($user);
					$em->persist($doc);
				}
				$em->flush();

				$request->getSession()->getFlashBag()->add('notice', 'Mission bien enregistrée.');

				return $this->redirect($this->generateUrl('junior_siteinterne_mission', array('id' => $mission1->getId())));
			}
		}
		if ($form2->handleRequest($request)->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$user = $this->getUser();
			if (null === $user) {
				return $this->redirect($this->generateUrl('junior_siteinterne_ajouter'));
			} else {
				$mission2->setAjoutePar($user);
				$numSiaje = $this->getDoctrine()
							  ->getRepository('JuniorSiteinterneBundle:Mission')
							  ->getProchainNum();
				$mission2->setNumSiaje($numSiaje);
				if($this->getDoctrine()->getRepository('JuniorSiteinterneBundle:Mission')->isThereMission()){
					$dernierReferent=$this->getDoctrine()
										->getRepository('JuniorSiteinterneBundle:Mission')
										->getDernierReferent();
					if($dernierReferent != null){
						$referentMission = $this->getDoctrine()
									  ->getRepository('JuniorSiteinterneBundle:User')
									  ->getProchainReferent($dernierReferent);
						$mission2->setReferent($referentMission);
					}
				}else{
					$referent;
					$users = $this->getDoctrine()
								  ->getRepository('JuniorSiteinterneBundle:User')
								  ->findAll();
					foreach($users as $u){
						foreach($u->getRoles() as $r){
							if($r == "ROLE_POLEMISSION"){
								$referent = $u;
							}
						}
					}
					$mission2->setReferent($referent);
				}
				$em->persist($mission2);
				
		//---Debut des mails
				//Msg au référent
				$message_referent = \Swift_Message::newInstance()
					->setSubject('[XProjets] Nouvelle mission postée')
					->setFrom('contact@xprojets.com')
					->setTo($mission2->getReferent()->getEmail())
					->setBody($this->renderView('JuniorSiteinterneBundle:Mails:mail-referent-mission-ajoutee.html.twig', array(
					'mission' => $mission2
					)))
				;
				$this->get('mailer')->send($message_referent);
				//Message au respo recrutement
				$recruteur;
				$users = $this->getDoctrine()
							  ->getRepository('JuniorSiteinterneBundle:User')
							  ->findAll();
				foreach($users as $u){
					foreach($u->getRoles() as $r){
						if($r == "ROLE_RECRUTEUR"){
							$recruteur = $u;
						}
					}
				}
				if($recruteur != null){
					$message_recruteur = \Swift_Message::newInstance()
						->setSubject('[XProjets] Nouvelle mission postée')
						->setFrom('contact@xprojets.com')
						->setTo($recruteur->getEmail())
						->setBody($this->renderView('JuniorSiteinterneBundle:Mails:mail-recruteur-mission-ajoutee.html.twig', array(
						'mission' => $mission2
						)))
					;
					$this->get('mailer')->send($message_recruteur);
				}
		//---Fin des mails
				
				
				foreach($tableau as $typeDoc){
					$doc = new Document();
					$doc->setTypeDeDocument($typeDoc);
					$doc->setFrozen(false);
					$doc->setMission($mission2);
					$doc->setAjoutePar($user);
					$em->persist($doc);
				}
				$em->flush();

				$request->getSession()->getFlashBag()->add('notice', 'Mission bien enregistrée.');

				return $this->redirect($this->generateUrl('junior_siteinterne_mission', array('id' => $mission2->getId())));
			}
		}

		$listeClients = $this->getDoctrine()
							  ->getRepository('JuniorSiteinterneBundle:Client')
							  ->findAll();

		return $this->render('JuniorSiteinterneBundle:Siteinterne:add.html.twig', array(
			'form1' => $form1->createView(),
			'form2' => $form2->createView(),
			'clients' => $listeClients
		));
	}
	
  public function missionAction($id, Request $request){
		$repository = $this
						->getDoctrine()
						->getManager()
						->getRepository('JuniorSiteinterneBundle:Mission');
		$mission = $repository->find($id);
		
		//On regarde si l'utilisateur est autorisé à voir la page, à savoir si c'est un mb de la JE ou si il est intervenant ou chef de projet
		$statut = "simple visiteur";
		$user = $this->getUser();
		if($user !== null){
			foreach($mission->getIntervenants() as $i){
				if($i == $user){
					$statut = "intervenant";
				}
			}
			if($mission->getChefDeProjet() == $user){
				$statut = "cdp";
			}
		}
		if($this->get('security.context')->isGranted('ROLE_MBJE')){
			$statut = "mbje";
		}
		if($this->get('security.context')->isGranted('ROLE_RECRUTEUR')){
			$statut = "mbje";
		}
		
		if($statut == "simple visiteur") {
			if(!$mission->getPublique()){
				throw new AccessDeniedException('Accès limité.');
			}else{//Si il n'est pas autorisé mais que la mission est publique, on le redirige vers la page de présentation
				return $this->render('JuniorSiteinterneBundle:Siteinterne:mission_simple_user.html.twig',
				 array(
					'mission' => $mission,
				 ));
			}
		}
		
		
		
		$com = new RemarquesMission();
		$form = $this->get('form.factory')->create(new RemarquesMissionType(), $com);
		$com->setMission($mission);

		if ($form->handleRequest($request)->isValid()) {
			$user = $this->getUser();
			if (null !== $user){
				$em = $this->getDoctrine()->getManager();
				$com->setAjoutePar($user);
				$em->persist($com);
				$em->flush();
			}
		}

		$repository = $this
						->getDoctrine()
						->getManager()
						->getRepository('JuniorSiteinterneBundle:RemarquesMission');
		$commentaires = $repository->findBy(array('mission' => $mission));
		
		$repository = $this
						->getDoctrine()
						->getManager()
						->getRepository('JuniorSiteinterneBundle:Document');
		$documents = $repository->findBy(array('mission' => $mission));
		
		$tauxdavancement = -1;
		if($mission->getDateDebut() && $mission->getDateFin()){
			$delai = $mission->getDateDebut()->diff($mission->getDateFin());
			$d1 = new DateTime();
			$avancement = $mission->getDateDebut()->diff($d1);
			if($delai->days>0){
				$tauxdavancement=$avancement->days/$delai->days;
			}elseif($avancement->days>0){
				$tauxdavancement=1;
			}else{
				$tauxdavancement=0;
			}
			if($tauxdavancement>1){
				$tauxdavancement = 1;
			}elseif($tauxdavancement<0||$avancement->invert==1){
				$tauxdavancement = 0;
			}
			$tauxdavancement = floor(10*$tauxdavancement);
		}
		

		return $this->render('JuniorSiteinterneBundle:Siteinterne:mission.html.twig',
		 array(
			'form' => $form->createView(),
			'mission' => $mission,
			'commentaires' => $commentaires,
			'documents' => $documents,
			'tauxdavancement' => $tauxdavancement,
			'statut' => $statut
		 ));
	}
	
  /**
   * @Security("has_role('ROLE_MBJE')")
   */
public function docsAction(){
	return $this->render('JuniorSiteinterneBundle:Siteinterne:docs.html.twig');
}
	
  /**
   * @Security("has_role('ROLE_MBJE')")
   */
public function userAction($id, $action, Request $request){
	$repository = $this
					->getDoctrine()
					->getManager()
					->getRepository('JuniorSiteinterneBundle:User');
	$utilisateur = $repository->find($id);
	
	$listeMissionsCDP = $this->getDoctrine()
						  ->getRepository('JuniorSiteinterneBundle:Mission')
						  ->getMissionEnTantQueCDP($utilisateur);
	$listeMissionsINT = $this->getDoctrine()
						  ->getRepository('JuniorSiteinterneBundle:Mission')
						  ->getMissionEnTantQuIntervenant($utilisateur);


	$com = new RemarquesUser();
	$form = $this->get('form.factory')->create(new RemarquesUserType(), $com);
	$com->setUtilisateur($utilisateur);
	
	$form2 = $this->get('form.factory')->createBuilder('form', $utilisateur)
		->add('inscrit', 'checkbox', array('required' => false))
		->add('inscritLe', 'date', array('required' => false))
		->add('phone', 'text', array('required' => false))
		->add('email', 'text', array('required' => false))
		->add('save', 'submit')
		->add('numSecu', 'text', array('required' => false))
		->add('centreSecu', 'text', array('required' => false))
		->add('ville', 'text', array('required' => false))
		->add('codeBanque', 'text', array('required' => false))
		->add('codeGuichet', 'text', array('required' => false))
		->add('compte', 'text', array('required' => false))
		->add('cle', 'text', array('required' => false))
		->add('domiciliation', 'text', array('required' => false))
		->add('titulaire', 'text', array('required' => false))
		->add('iban', 'text', array('required' => false))
		->add('bic', 'text', array('required' => false))
		->add('nationalite', 'text', array('required' => false))
		->getForm();

	if ($form->handleRequest($request)->isValid()) {
		$user = $this->getUser();
		if (null !== $user){
			$em = $this->getDoctrine()->getManager();
			$com->setAjoutePar($user);
			$em->persist($com);
			$em->flush();
		}
	}
	
	if ($form2->handleRequest($request)->isValid()) {
		$em = $this->getDoctrine()->getManager();
		$em->flush();
	}
	


	$repository = $this
					->getDoctrine()
					->getManager()
					->getRepository('JuniorSiteinterneBundle:RemarquesUser');
	$commentaires = $repository->findBy(array('utilisateur' => $utilisateur));
	
	return $this->render('JuniorSiteinterneBundle:Siteinterne:user.html.twig',
	 array(
		'form' => $form->createView(),
		'form2' => $form2->createView(),
		'u' => $utilisateur,
		'commentaires' => $commentaires,
		'listeMissionsINT' => $listeMissionsINT,
		'listeMissionsCDP' => $listeMissionsCDP
	 ));
}

  /**
   * @Security("has_role('ROLE_MBJE')")
   */
	public function usersAction($idmission, $refIntCDP, $iduser, $add)
	{
		//refIntCDP =
		//1 -> referent
		//2 -> ajouterintervenant
		//3 -> chef de projet
		
		if($add=='ok'){
			$repository1 = $this
							->getDoctrine()
							->getManager()
							->getRepository('JuniorSiteinterneBundle:Mission');
			$repository2 = $this
							->getDoctrine()
							->getManager()
							->getRepository('JuniorSiteinterneBundle:User');
			$mission = $repository1->find($idmission);
			$user = $repository2->find($iduser);
			if($refIntCDP == 1){
				$mission->setReferent($user);
				//Mail au nouveau référent
				$message_referent = \Swift_Message::newInstance()
					->setSubject('[XProjets] Tu es affecté à une nouvelle mission')
					->setFrom('contact@xprojets.com')
					->setTo($user->getEmail())
					->setBody($this->renderView('JuniorSiteinterneBundle:Mails:mail-referent-mission-ajoutee.html.twig', array(
					'mission' => $mission
					)))
				;
				$this->get('mailer')->send($message_referent);
			} elseif ($refIntCDP== 2 ){
				$mission->addIntervenant($user);
				$doc = new Document();
				$doc->setTypeDeDocument("Récapitulatif mission");
				$doc->setFrozen(false);
				$doc->setMission($mission);
				$doc->setIntervenant($user);
				$this->getDoctrine()->getManager()->persist($doc);
				//Mail au rh
				if($user->getInscrit() == false){
					$rh;
					$users = $this->getDoctrine()
								  ->getRepository('JuniorSiteinterneBundle:User')
								  ->findAll();
					foreach($users as $u){
						foreach($u->getRoles() as $r){
							if($r == "ROLE_RH"){
								$rh = $u;
							}
						}
					}
					$message = \Swift_Message::newInstance()
						->setSubject('[XProjets] Nouveau chef de projet/intervenant pas encore inscrit à la JE')
						->setFrom('contact@xprojets.com')
						->setTo($rh->getEmail())
						->setBody($this->renderView('JuniorSiteinterneBundle:Mails:mail-rh-inscription.html.twig', array(
						'user' => $user
						)))
					;
					$this->get('mailer')->send($message);
				}
				//Mail à l'intervenant pour l'inscription à la JE
				if($user->getInscrit() == false){
					$rh;
					$users = $this->getDoctrine()
								  ->getRepository('JuniorSiteinterneBundle:User')
								  ->findAll();
					foreach($users as $u){
						foreach($u->getRoles() as $r){
							if($r == "ROLE_RH"){
								$rh = $u;
							}
						}
					}
					$message = \Swift_Message::newInstance()
						->setSubject('[XProjets] Inscription JE')
						->setFrom($rh->getEmail())
						->setTo($user->getEmail())
						->setBody($this->renderView('JuniorSiteinterneBundle:Mails:mail-intcdp-inscription.html.twig'))
					;
					$this->get('mailer')->send($message);
				}
				//Mail au référent
				$message = \Swift_Message::newInstance()
					->setSubject('[XProjets] Mission "'. $mission->getNom() .'" : Nouvel intervenant')
					->setFrom('contact@xprojets.com')
					->setTo($mission->getReferent()->getEmail())
					->setBody($this->renderView('JuniorSiteinterneBundle:Mails:mail-referent-nouvellepersonneaffectee.html.twig', array(
					'mission' => $mission,
					'user' => $user,
					'statut' => 'intervenant'
					)))
				;
				$this->get('mailer')->send($message);
			} elseif ($refIntCDP== 3 ){
				$mission->setChefDeProjet($user);
				//Mail au nouveau cdp
				$message = \Swift_Message::newInstance()
					->setSubject('[XProjets] Tu es affecté à une nouvelle mission')
					->setFrom($mission->getReferent()->getEmail())
					->setTo($user->getEmail())
					->setBody($this->renderView('JuniorSiteinterneBundle:Mails:mail-cdp-affectedtomission.html.twig', array(
					'mission' => $mission,
					'user' => $user
					)))
				;
				$this->get('mailer')->send($message);
				//Mail au chef de projet pour l'inscription à la JE
				if($user->getInscrit() == false){
					$rh;
					$users = $this->getDoctrine()
								  ->getRepository('JuniorSiteinterneBundle:User')
								  ->findAll();
					foreach($users as $u){
						foreach($u->getRoles() as $r){
							if($r == "ROLE_RH"){
								$rh = $u;
							}
						}
					}
					$message = \Swift_Message::newInstance()
						->setSubject('[XProjets] Inscription JE')
						->setFrom($rh->getEmail())
						->setTo($user->getEmail())
						->setBody($this->renderView('JuniorSiteinterneBundle:Mails:mail-intcdp-inscription.html.twig'))
					;
					$this->get('mailer')->send($message);
				}
				//Mail au rh
				if($user->getInscrit() == false){
					$rh;
					$users = $this->getDoctrine()
								  ->getRepository('JuniorSiteinterneBundle:User')
								  ->findAll();
					foreach($users as $u){
						foreach($u->getRoles() as $r){
							if($r == "ROLE_RH"){
								$rh = $u;
							}
						}
					}
					$message = \Swift_Message::newInstance()
						->setSubject('[XProjets] Nouveau chef de projet/intervenant pas encore inscrit à la JE')
						->setFrom('contact@xprojets.com')
						->setTo($rh->getEmail())
						->setBody($this->renderView('JuniorSiteinterneBundle:Mails:mail-rh-inscription.html.twig', array(
						'user' => $user
						)))
					;
					$this->get('mailer')->send($message);
				}
				//Mail au référent
				$message = \Swift_Message::newInstance()
					->setSubject('[XProjets] Mission "'. $mission->getNom() .'" : Nouveau chef de projet')
					->setFrom('contact@xprojets.com')
					->setTo($mission->getReferent()->getEmail())
					->setBody($this->renderView('JuniorSiteinterneBundle:Mails:mail-referent-nouvellepersonneaffectee.html.twig', array(
					'mission' => $mission,
					'user' => $user,
					'statut' => 'chef de projet'
					)))
				;
				$this->get('mailer')->send($message);
			}
			$em = $this->getDoctrine()->getManager();
			$em->flush();
			return $this->redirect($this->generateUrl('junior_siteinterne_mission', array('id' => $mission->getId())));
		}
		
		$listeUsers = $this->getDoctrine()
							  ->getRepository('JuniorSiteinterneBundle:User')
							  ->findBy([], ['lastName' => 'ASC']);
		
		return $this->render('JuniorSiteinterneBundle:Siteinterne:users.html.twig', array(
		'listeUsers' => $listeUsers,
		'refIntCDP' => $refIntCDP,
		'idmission' => $idmission,
		'add' => $add
		));
	}
	
  /**
   * @Security("has_role('ROLE_MBJE')")
   */
	public function categoriesAction(Request $request, $suppr, $id)
	{
		if($suppr == 'supprimer'){
			$em = $this->getDoctrine()->getManager();
			$cat = $this->getDoctrine()
							  ->getRepository('JuniorSiteinterneBundle:Category')
							  ->find($id);
			if($cat){
				$em->remove($cat);
				$em->flush();
			}
			return $this->redirect($this->generateUrl('junior_siteinterne_missions_categories'));
		}
		$categorie = new Category();
		$form = $this->get('form.factory')->create(new CategoryType(), $categorie);

		if ($form->handleRequest($request)->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($categorie);
			$em->flush();
		}
		
		$listeCategories = $this->getDoctrine()
							  ->getRepository('JuniorSiteinterneBundle:Category')
							  ->findAll();

		return $this->render('JuniorSiteinterneBundle:Siteinterne:categories.html.twig', array(
			'form' => $form->createView(),
			'listeCategories' => $listeCategories
		));
		
	}
	
  /**
   * @Security("has_role('ROLE_MBJE')")
   */
	public function clientsAction(Request $request, $suppr, $id)
	{
		if($suppr == 'supprimer'){
			$em = $this->getDoctrine()->getManager();
			$client = $this->getDoctrine()
							  ->getRepository('JuniorSiteinterneBundle:Client')
							  ->find($id);
			if($client){
				$em->remove($client);
				$em->flush();
			}
			return $this->redirect($this->generateUrl('junior_siteinterne_missions_clients'));
		}
		$client = new Client();
		$form = $this->get('form.factory')->create(new ClientType(), $client)->add('save', 'submit');

		if ($form->handleRequest($request)->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($client);
			$em->flush();
		}
		
		$listeClients = $this->getDoctrine()
							  ->getRepository('JuniorSiteinterneBundle:Client')
							  ->findAll();

		return $this->render('JuniorSiteinterneBundle:Siteinterne:clients.html.twig', array(
			'form' => $form->createView(),
			'listeClients' => $listeClients
		));
		
	}

  /**
   * @Security("has_role('ROLE_MBJE')")
   */
	public function clientAction(Request $request, $edit, $id)
	{
		$em = $this->getDoctrine()->getManager();
		$client = $this->getDoctrine()
						  ->getRepository('JuniorSiteinterneBundle:Client')
						  ->find($id);
						  
		if($edit == 'ok'){
			$form = $this->get('form.factory')->create(new ClientType(), $client)->add('save', 'submit');
			if ($form->handleRequest($request)->isValid()) {
				$em = $this->getDoctrine()->getManager();
				$em->persist($client);
				$em->flush();
				return $this->redirect($this->generateUrl('junior_siteinterne_missions_clients', array('id' => $client->getId(), 'edit' => 'null')));
			}
			return $this->render('JuniorSiteinterneBundle:Siteinterne:client.html.twig', array(
				'form' => $form->createView(),
				'edit' => 1,
				
			));
		}
		

		
		return $this->render('JuniorSiteinterneBundle:Siteinterne:client.html.twig', array(
			'edit' => 0,
			'client' => $client
		));
		
	}

  /**
   * @Security("has_role('ROLE_MBJE')")
   */
	public function competencesAction(Request $request, $suppr, $id)
	{
		if($suppr == 'supprimer'){
			$em = $this->getDoctrine()->getManager();
			$competence = $this->getDoctrine()
							  ->getRepository('JuniorSiteinterneBundle:Competence')
							  ->find($id);
			if($competence){
				$em->remove($competence);
				$em->flush();
			}
			return $this->redirect($this->generateUrl('junior_siteinterne_missions_competences'));
		}
		$competence = new Competence();
		$form = $this->get('form.factory')->create(new CompetenceType(), $competence)->add('save', 'submit');

		if ($form->handleRequest($request)->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($competence);
			$em->flush();
		}
		
		$listeCompetences = $this->getDoctrine()
							  ->getRepository('JuniorSiteinterneBundle:Competence')
							  ->findAll();

		return $this->render('JuniorSiteinterneBundle:Siteinterne:competences.html.twig', array(
			'form' => $form->createView(),
			'listeCompetences' => $listeCompetences
		));
		
	}
	
	public function docAction($id, $iddoc, Request $request)
	{
		$manager = $this
						->getDoctrine()
						->getManager();
		$repository1 = $manager
						->getRepository('JuniorSiteinterneBundle:Mission');
		$repository2 = $manager
						->getRepository('JuniorSiteinterneBundle:Document');
		$mission = $repository1->find($id);
		$doc = $repository2->find($iddoc);
		
		$com = new RemarquesDocument();
		$form1 = $this->get('form.factory')->create(new RemarquesDocumentType(), $com);

		if ($form1->handleRequest($request)->isValid()) {
			$user = $this->getUser();
			if (null !== $user) {
				$em = $this->getDoctrine()->getManager();
				$com->setDocument($doc);
				$com->setAjoutePar($user);
				$em->persist($com);
				$em->flush();
				//Mail au référent et au cdp (on envoie qu'aux personnes qui n'ont pas écrit le commentaire)
				if($user != $mission->getChefDeProjet()){
					$message_cdp = \Swift_Message::newInstance()
						->setSubject('[XProjets] Nouveau message pour la mission "'. $mission->getNom() .'" à propos du document '. $doc->getTypeDeDocument())
						->setFrom('contact@xprojets.com')
						->setTo($mission->getChefDeProjet()->getEmail())
						->setBody($this->renderView('JuniorSiteinterneBundle:Mails:mail-cdp-remarqueajoutee.html.twig', array(
						'mission' => $mission,
						'doc' => $doc,
						'com' => $com
						)))
					;
					$this->get('mailer')->send($message_cdp);
				}
				if($user != $mission->getReferent()){
					$message_ref = \Swift_Message::newInstance()
						->setSubject('[XProjets] Nouveau message pour la mission "'. $mission->getNom() .'" à propos du document '. $doc->getTypeDeDocument())
						->setFrom('contact@xprojets.com')
						->setTo($mission->getReferent()->getEmail())
						->setBody($this->renderView('JuniorSiteinterneBundle:Mails:mail-cdp-remarqueajoutee.html.twig', array(
						'mission' => $mission,
						'doc' => $doc,
						'com' => $com
						)))
					;
					$this->get('mailer')->send($message_ref);
				}
			}
		}

		$form2 = $this->get('form.factory')->create(new DocumentType(), $doc);
		
		//On regarde si l'utilisateur est autorisé à voir la page, à savoir si c'est un mb de la JE ou si il est intervenant ou chef de projet
		$statut = "simple visiteur";
		$user = $this->getUser();
		if($user !== null){
			foreach($mission->getIntervenants() as $i){
				if($i == $user){
					$statut = "intervenant";
				}
			}
			if($mission->getChefDeProjet() == $user){
				$statut = "cdp";
			}
		}
		if($this->get('security.context')->isGranted('ROLE_MBJE')){
			$statut = "mbje";
		}
		if($this->get('security.context')->isGranted('ROLE_POLEMISSION')){
			$statut = "polemission";
		}
		
		if($statut == "simple visiteur") {
			if(!$mission->getPublique()){
				throw new AccessDeniedException('Accès limité.');
			}
		}
		
		if($statut == "polemission"){
			$form2->add('frozen', 'checkbox', array('required' => false))
					;
		}


		if ($form2->handleRequest($request)->isValid()) {
			$user = $this->getUser();
			if (null !== $user) {
				$em = $this->getDoctrine()->getManager();
				$doc->upload();
				$doc->setAjoutePar($user);
				$doc->setAjouteLe(new \Datetime());
				$em->flush();
				if($user != $mission->getReferent()){
					$message_ref = \Swift_Message::newInstance()
						->setSubject('[XProjets] Nouveau document pour la mission "'. $mission->getNom() .'" - type : '. $doc->getTypeDeDocument())
						->setFrom('contact@xprojets.com')
						->setTo($mission->getReferent()->getEmail())
						->setBody($this->renderView('JuniorSiteinterneBundle:Mails:mail-cdp-docajouted.html.twig', array(
						'mission' => $mission,
						'doc' => $doc
						)))
					;
					$this->get('mailer')->send($message_ref);
				}
			}
		}

		$repository3 = $manager
						->getRepository('JuniorSiteinterneBundle:RemarquesDocument');
		$commentaires = $repository3->findBy(array('document' => $doc));

		return $this->render('JuniorSiteinterneBundle:Siteinterne:document.html.twig',
		 array(
			'form1' => $form1->createView(),
			'form2' => $form2->createView(),
			'doc' => $doc,
			'commentaires' => $commentaires,
			'statut' => $statut
		 ));
	}
	
	public function docviergeAction($id, $iddoc, $typedoc, Request $request)
	{

		$manager = $this
						->getDoctrine()
						->getManager();
		$repository1 = $manager
						->getRepository('JuniorSiteinterneBundle:Mission');
		$mission = $repository1->find($id);
		$repository2 = $manager
						->getRepository('JuniorSiteinterneBundle:Document');
		
		$name = $typedoc.'.xml'; // nom du fichier à ouvrir
		$myFile = file_get_contents( __DIR__.'/../../../../web/downloads/docsvierges/'.$name);
		 
		$nbToWrd = new Numbers_Words_Locale_fr();
		
		
		$searchReplace = array(
			'{num_etude}' => $mission->getNumSiaje(),
			'{etude_titre}' => $mission->getNom(),
			'{suiveur_prenom}' => $mission->getChefDeProjet()->getFirstName(),
			'{suiveur_nom}' => $mission->getChefDeProjet()->getLastName(),
			'{suiveur_portable}' => $mission->getChefDeProjet()->getPhone(),
			'{suiveur_mail}' => $mission->getChefDeProjet()->getEmail(),
			'{client_societe}' => $mission->getClient()->getNom(),
			'{client_titre} ' => $mission->getClient()->getCiviliteContactBienEcrit(),
			'{client_nom}' => $mission->getClient()->getNomContact(),
			'{client_prenom}' => $mission->getClient()->getPrenomContact(),
			'{client_fonction}' => $mission->getClient()->getFonctionContact(),
			'{client_adresse}' => $mission->getClient()->getAdresse1() . ' ' . $mission->getClient()->getAdresse2() . ' ' . $mission->getClient()->getAdresse3(),
			'{client_code_postal}' => $mission->getClient()->getCodePostal(),
			'{client_ville}' => $mission->getClient()->getVille(),
			'{nb_semaines}' => $mission->getNbSemaines(),
			'{etude_nombre_JEH}' => $mission->getNbJeh(),
			'{etude_nombre_JEH_lettres}' => $nbToWrd->_toWords($mission->getNbJeh()),
			'{etude_montant_HT}' => $mission->getNbJeh()*300,
			'{etude_montant_TVA}' => $mission->getNbJeh()*300*$mission->getTauxTva()/100,
			'{etude_montant_HT_lettres}' => $nbToWrd->_toWords($mission->getNbJeh()*300),
			'{frais_HT}' => $mission->getFrais(),
			'{frais_HT_lettres}' => $nbToWrd->_toWords($mission->getFrais()),
			'{total_HT}' => $mission->getFrais()+ $mission->getNbJeh()*300,
			'{total_HT_lettres}' => $nbToWrd->_toWords($mission->getFrais()+ $mission->getNbJeh()*300),
			'{taux_tva}' => $mission->getTauxTva(),//
			'{total_TTC}' => ($mission->getFrais()+ $mission->getNbJeh()*300)*(1+$mission->getTauxTva()/100),
			'{total_TTC_lettres}' => $nbToWrd->_toWords(($mission->getFrais()+ $mission->getNbJeh()*300)*(1+$mission->getTauxTva()/100)),
			'{etude_acompte_HT}' => $mission->getAccompte(),
			'{etude_acompte_HT_lettres}' => $nbToWrd->_toWords($mission->getAccompte()),
			'{etude_acompte_TTC}' => $mission->getAccompte()*(1+$mission->getTauxTva()/100),
			'{etude_acompte_TTC_lettres}' => $nbToWrd->_toWords($mission->getAccompte()*(1+$mission->getTauxTva()/100))
			);
		 
		if($mission->getFrais()+ $mission->getNbJeh()*300 != 0){
			$searchReplace['{etude_acompte_pourcentage}'] =round($mission->getAccompte()/($mission->getFrais()+ $mission->getNbJeh()*300)*100);
		}
		
		if($iddoc != 0){
			$doc = $repository2->find($iddoc);
			if($doc->getTypeDeDocument() == "Récapitulatif mission" || $doc->getTypeDeDocument() == "Avenant au récapitulatif mission"){
				$searchReplace['{etudiant_nom}'] = $doc->getIntervenant()->getLastName();
				$searchReplace['{etudiant_prenom}'] = $doc->getIntervenant()->getFirstName();
			}
		}
		
		
		for($i=1;$i<8;$i++){
			$searchReplace['{nom_phase'. $i .'}'] ='';
			$searchReplace['{nb_jeh_phase'. $i .'}'] ='';
			$searchReplace['{montant_phase'. $i .'}'] ='';
		}
		$compteur = 1;
		$nbJehFactures = 0;
		foreach($mission->getPhases() as $p){
			if($request->request->get('phase'.$p['idPhase'])){
				$searchReplace['{nom_phase'. $compteur .'}'] =$p['nom'];
				$searchReplace['{nb_jeh_phase'. $compteur .'}'] ='(' . $p['nbJeh'] . ' Jour - Etude Homme)';
				$searchReplace['{montant_phase'. $compteur .'}'] =$p['nbJeh']*300 . ',00';
				$compteur++;
				$nbJehFactures+=$p['nbJeh'];
			}
		}
		if($request->request->get('emission')){
			$searchReplace['{date_emission}'] =date_format(date_create($request->request->get('emission')),'d-m-Y');
		}
		if($request->request->get('echeance')){
			$searchReplace['{date_echeance}'] =date_format(date_create($request->request->get('echeance')),'d-m-Y');
		}
		if($request->request->get('valeur_du_jeh')){
			$searchReplace['{valeur_du_jeh}'] =$request->request->get('valeur_du_jeh');
		}
		if($request->request->get('nb_jeh_bv')){
			$searchReplace['{nb_jeh_bv}'] =$request->request->get('nb_jeh_bv');
		}
		if($request->request->get('idintervenant')){
			$repository3 = $manager
				->getRepository('JuniorSiteinterneBundle:User');
			$int = $repository3->find($request->request->get('idintervenant'));
			$searchReplace['{etudiant_nom}'] = $int->getLastName();
			$searchReplace['{etudiant_prenom}'] = $int->getFirstName();
			$searchReplace['{etudiant_num_secu}'] = $int->getNumSecu();
		}
		$searchReplace['{nb_jeh_factures}'] =$nbJehFactures;
		$searchReplace['{montant_ht_facture}'] =$nbJehFactures*300 . ',00';
		$searchReplace['{montant_tva_facture}'] = $nbJehFactures*300 * $mission->getTauxTva()/100 ;
		$searchReplace['{montant_total_facture}'] = $nbJehFactures*300 * (1+$mission->getTauxTva()/100) ;
		$searchReplace['{montant_total_lettre}'] = $nbToWrd->_toWords($nbJehFactures*300 * (1+$mission->getTauxTva()/100)) . ' euros' ;

		
		$search  = array_keys($searchReplace);
		$replace = array_values($searchReplace);

			

		
		$response = new Response();
		$response->setContent(str_replace($search, $replace, $myFile));
		$response->headers->set('Content-Type', 'application/msword; name="'.$name.'"');
		$response->headers->set('Content-Transfer-Encoding', 'binary');
		$response->headers->set('Content-Disposition', 'attachment; filename="'.$name.'"');

		return $response;
	}
	
  /**
   * @Security("has_role('ROLE_MBJE')")
   */
	public function modifAction($idmission, $iduser, $idphase, $supprInt, $changerEtat, $changerPublique, $modifMission, $modifDates, $modifDocs, $modifRaisonEchec, $modifPhases, Request $request)
	{
		if($supprInt == 'ok'){
			$manager = $this
							->getDoctrine()
							->getManager();
			$repository1 = $manager
							->getRepository('JuniorSiteinterneBundle:Mission');
			$mission = $repository1->find($idmission);
			$repository2 = $manager
							->getRepository('JuniorSiteinterneBundle:User');
			$repository3 = $manager
							->getRepository('JuniorSiteinterneBundle:Document');
			$intervenant = $repository2->find($iduser);
			$documents = $repository3->findByIntervenant($intervenant);
			foreach($documents as $document){
				$manager->remove($document);
			}
			
			$mission->removeIntervenant($intervenant);
			$manager->flush();
			return $this->redirect($this->generateUrl('junior_siteinterne_mission', array('id' => $mission->getId())));
		}
		
		if($changerPublique == 'ok'){
			$manager = $this
							->getDoctrine()
							->getManager();
			$repository1 = $manager
							->getRepository('JuniorSiteinterneBundle:Mission');
			$mission = $repository1->find($idmission);
			
			$mission->setPublique(!($mission->getPublique()));
			$manager->flush();
			return $this->redirect($this->generateUrl('junior_siteinterne_mission', array('id' => $mission->getId())));
		}
		
		if($modifDocs != 'null'){
			$manager = $this
							->getDoctrine()
							->getManager();
			$repository1 = $manager
							->getRepository('JuniorSiteinterneBundle:Mission');
			$mission = $repository1->find($idmission);
			if($modifDocs=="ajoutpvri"){
				$typeDoc ="PVRI";
			}elseif($modifDocs=="ajoutavcc"){
				$typeDoc ="Avenant à la convention client";
			}elseif($modifDocs=="ajoutavrm"){
				$typeDoc ="Avenant au récapitulatif mission";
			}elseif($modifDocs=="ajoutavrcc"){
				$typeDoc ="Avenant de rupture à la convention";
			}
			$doc = new Document();
			$doc->setTypeDeDocument($typeDoc);
			$doc->setFrozen(false);
			$doc->setMission($mission);
			if($modifDocs=="ajoutavrm"){
				$doc->setIntervenant($repository1 = $manager
							->getRepository('JuniorSiteinterneBundle:User')
							->find($iduser));
			}
			$manager->persist($doc);
			$manager->flush();
			return $this->redirect($this->generateUrl('junior_siteinterne_mission', array('id' => $mission->getId())));
		}
		
		if($changerEtat != 'null'){
			$manager = $this
							->getDoctrine()
							->getManager();
			$repository1 = $manager
							->getRepository('JuniorSiteinterneBundle:Mission');
			$mission = $repository1->find($idmission);
			
			if($changerEtat == 1){
				$mission->setEtat("Echec");		
			} elseif ($changerEtat == 2){
				$mission->setEtat("Terminée");		
			} elseif ($changerEtat == 3){
				$mission->setEtat("A pourvoir");		
			} elseif ($changerEtat == 4){
				$mission->setEtat("En standby");		
			} elseif ($changerEtat == 5){
				$mission->setEtat("En cours");		
			}
			$manager->flush();
			return $this->redirect($this->generateUrl('junior_siteinterne_mission', array('id' => $mission->getId())));
		}
		
		if($modifMission != 'null'){
			$manager = $this
							->getDoctrine()
							->getManager();
			$repository1 = $manager
							->getRepository('JuniorSiteinterneBundle:Mission');
			$mission = $repository1->find($idmission);
			$mission->setPublique($mission->getPublique()==1);
			$form = $this->get('form.factory')->create(new MissionVxClientType(), $mission)
				->add('nbJeh', 'integer', array('required' => false))
				->add('tauxTva', 'number', array('required' => false))
				->add('frais', 'number', array('required' => false))
				->add('accompte', 'number', array('required' => false))
				->add('montrerClientAuCdp', 'checkbox', array('required' => false))
				;
			if ($form->handleRequest($request)->isValid()) {
				$manager->flush();
				$request->getSession()->getFlashBag()->add('notice', 'Mission bien enregistrée.');
				return $this->redirect($this->generateUrl('junior_siteinterne_mission', array('id' => $mission->getId())));
			}
			return $this->render('JuniorSiteinterneBundle:Siteinterne:modif.html.twig',
			 array(
				'form1' => $form->createView(),
				'formulaire' => 1
			 ));
		}
		
		if($modifDates != 'null'){
			$manager = $this
							->getDoctrine()
							->getManager();
			$repository1 = $manager
							->getRepository('JuniorSiteinterneBundle:Mission');
			$mission = $repository1->find($idmission);
			$form = $this->get('form.factory')->create('form', $mission)
				->add('dateDebut', 'date')
				->add('nbSemaines', 'integer')
				->add('save', 'submit')
			;
			if($form->handleRequest($request)->isValid()) {
				$manager->flush();
				$mission->setDateFin(
					$mission->getDateDebut()->add(new DateInterval('P' . $mission->getNbSemaines() . 'W'))
				);
				$manager->flush();
				$request->getSession()->getFlashBag()->add('notice', 'Mission bien enregistrée.');
				return $this->redirect($this->generateUrl('junior_siteinterne_mission', array('id' => $mission->getId())));
			}
			return $this->render('JuniorSiteinterneBundle:Siteinterne:modif.html.twig',
			 array(
				'form2' => $form->createView(),
				'formulaire' => 2
			 ));
		}
		
		if($modifRaisonEchec != 'null'){
			$manager = $this
							->getDoctrine()
							->getManager();
			$repository1 = $manager
							->getRepository('JuniorSiteinterneBundle:Mission');
			$mission = $repository1->find($idmission);
			$form = $this->get('form.factory')->create('form', $mission)
				->add('raisonEchec', 'text')
				->add('save', 'submit')
			;
			if($form->handleRequest($request)->isValid()) {
				$mission->setEtat("Echec");
				$manager->flush();
				$request->getSession()->getFlashBag()->add('notice', 'Mission bien enregistrée.');
				return $this->redirect($this->generateUrl('junior_siteinterne_mission', array('id' => $mission->getId())));
			}
			return $this->render('JuniorSiteinterneBundle:Siteinterne:modif.html.twig',
			 array(
				'form3' => $form->createView(),
				'formulaire' => 3
			 ));
		}
		
		if($modifPhases != 'null'){
			$manager = $this
							->getDoctrine()
							->getManager();
			$repository = $manager
							->getRepository('JuniorSiteinterneBundle:Mission');
			$mission = $repository->find($idmission);
			$phases = $mission->getPhases();
			if($request->request->get('nom') != null){
				$phase = array(
							'nom'=>$request->request->get('nom'),
							'nbJeh'=>$request->request->get('nb_jeh'),
							'idPhase'=>$request->request->get('id_phase')
						);
				$nvellesPhases = array();
				foreach($phases as $p){
					if($p['idPhase'] < $phase['idPhase']){
						$nvellesPhases[] = $p;
					}
				}
				$nvellesPhases[] = $phase;
				foreach($phases as $p){
					if($p['idPhase'] >= $phase['idPhase']){
						$nvellesPhases[] = $p;
					}
				}
				$mission->setPhases($nvellesPhases);
			}
			if($modifPhases == 'suppr'){
				$nvellesPhases = array();
				foreach($phases as $p){
					if($p['idPhase'] != $idphase){
						$nvellesPhases[] = $p;
					}
				}
				$mission->setPhases($nvellesPhases);
			}
			$manager->flush();
			return $this->render('JuniorSiteinterneBundle:Siteinterne:phases.html.twig',
			 array(
				'mission' => $mission
			 ));
		}
		
		return $this->redirect($this->generateUrl('junior_site_homepage'));
	}

	public function modifdetailavancementAction($idmission, Request $request)
	{
			$manager = $this
							->getDoctrine()
							->getManager();
			$repository1 = $manager
							->getRepository('JuniorSiteinterneBundle:Mission');
			$mission = $repository1->find($idmission);
			$form = $this->get('form.factory')->create('form', $mission)
				->add('detailAvancement', 'textarea')
				->add('save', 'submit')
			;
			if($form->handleRequest($request)->isValid()) {
				$manager->flush();
				return $this->redirect($this->generateUrl('junior_siteinterne_mission', array('id' => $mission->getId())));
			}
			return $this->render('JuniorSiteinterneBundle:Siteinterne:modif.html.twig',
			 array(
				'form4' => $form->createView(),
				'formulaire' => 4
			 ));

			return $this->redirect($this->generateUrl('junior_site_homepage'));
	}

  /**
 * @Security("has_role('ROLE_ADMIN')")
   */
	public function configAction($iduser,$action)
	{
		/*action
		0 : afficher les differentes listes
		1 : ajouter un admin
		2 : ajouter un mbje
		3 : ajouter un polemission
		4 : ajouter un rh 
		5 : enlever un admin
		6 : enlever un mbje
		7 : enlever un polemission
		8 : enlever un rh 
		9 : choisir le recruteur
		11 : afficher les users pr ajouter admin
		12 : afficher les users pr ajouter mbje
		13 : afficher les users pr ajouter polemission
		14 : afficher les users pr ajouter rh
		15 : afficher les users pr choisir recruteur*/
		
		$manager = $this
						->getDoctrine()
						->getManager();
		$repository = $manager
						->getRepository('JuniorSiteinterneBundle:User');
		$users = $repository->findAll();
		
		if($action >= 10){
			return $this->render('JuniorSiteinterneBundle:Siteinterne:config.html.twig', array(
				'users' => $users,
				'action' => $action
			));
		}elseif($action == 1){
			$user = $repository->findOneById($iduser);
			$user->setRoles(array('ROLE_ADMIN'));
		}elseif($action == 2){
			$user = $repository->findOneById($iduser);
			$roles = $user->getRoles();
			//On n'ajoute le role MBJE que si l'utilisateur n'a pas un role plus élevé
			if(!in_array("ROLE_ADMIN", $roles)&&!in_array("ROLE_POLEMISSION", $roles)&&!in_array("ROLE_RH", $roles)){
				$user->setRoles(array('ROLE_MBJE'));
			}
		}elseif($action == 3){
			$user = $repository->findOneById($iduser);
			$roles = $user->getRoles();
			//On n'ajoute le role MBJE que si l'utilisateur n'a pas un role plus élevé
			if(!in_array("ROLE_ADMIN", $roles)){
				$user->setRoles(array('ROLE_POLEMISSION'));
			}
		}elseif($action == 4){
			$user = $repository->findOneById($iduser);
			$roles = $user->getRoles();
			//On n'ajoute le role MBJE que si l'utilisateur n'a pas un role plus élevé
			if(!in_array("ROLE_ADMIN", $roles)){
				$user->setRoles(array('ROLE_RH'));
			}
		}elseif($action == 5){
			$user = $repository->findOneById($iduser);
			$user->setRoles(array("ROLE_MBJE"));
		}elseif($action == 6){
			$user = $repository->findOneById($iduser);
			$user->setRoles(array("ROLE_USER"));
		}elseif($action == 7){
			$user = $repository->findOneById($iduser);
			$user->setRoles(array("ROLE_MBJE"));
		}elseif($action == 8){
			$user = $repository->findOneById($iduser);
			$user->setRoles(array("ROLE_MBJE"));
		}elseif($action == 9){
			foreach($users as $u){
				foreach($u->getRoles() as $r){
					if($r == "ROLE_RECRUTEUR"){
						$u->setRoles(array("ROLE_MBJE"));
					}
				}
			}
			$user = $repository->findOneById($iduser);
			$user->setRoles(array("ROLE_RECRUTEUR"));
		}
		
		$manager->flush();

		$admin = array();
		$mbje = array();
		$polemission = array();
		$rh = array();
		$recruteur = null;
		foreach($users as $u){
			foreach($u->getRoles() as $r){
				if($r == "ROLE_ADMIN"){
					$admin[] = $u; 
					$mbje[] = $u; 
				}elseif($r == "ROLE_MBJE"){
					$mbje[] = $u; 
				}elseif($r == "ROLE_POLEMISSION"){
					$polemission[] = $u; 
					$mbje[] = $u; 
				}elseif($r == "ROLE_RH"){
					$rh[] = $u; 
					$mbje[] = $u; 
				}elseif($r == "ROLE_RECRUTEUR"){
					$recruteur = $u; 
					$mbje[] = $u; 
				}
			}
		}
		
		return $this->render('JuniorSiteinterneBundle:Siteinterne:config.html.twig', array(
			'admin' => $admin,
			'mbje' => $mbje,
			'polemission' => $polemission,
			'rh' => $rh,
			'recruteur' => $recruteur,
			'action' => $action
		));		
	}
	
}

	class Numbers_Words
{

    /**
     * Masculine gender, for languages that need it
     */
    const GENDER_MASCULINE = 0;

    /**
      * Feminine gender, for languages that need it
      */
    const GENDER_FEMININE = 1;

    /**
      * Neuter gender, for languages that need it
      */
    const GENDER_NEUTER = 2;

    /**
      * This is not an actual gender; some languages
      * have different ways of numbering actual things
      * (e.g. Romanian: "un nor, doi nori" for "one cloud, two clouds")
      * and for just counting in an abstract manner
      * (e.g. Romanian: "unu, doi" for "one, two"
      */
    const GENDER_ABSTRACT = 3;

    // }}}

    // {{{ properties

    /**
     * Default Locale name
     * @var string
     * @access public
     */
    public $locale = 'en_US';

    /**
     * Default decimal mark
     * @var string
     * @access public
     */
    public $decimalPoint = '.';

    // }}}
    // {{{ toWords()

    /**
     * Converts a number to its word representation
     *
     * @param integer $num     An integer between -infinity and infinity inclusive :)
     *                         that should be converted to a words representation
     * @param string  $locale  Language name abbreviation. Optional. Defaults to
     *                         current loaded driver or en_US if any.
     * @param array   $options Specific driver options
     *
     * @access public
     * @author Piotr Klaban <makler@man.torun.pl>
     * @since  PHP 4.2.3
     * @return string  The corresponding word representation
     */
    function toWords($num, $locale = '', $options = array())
    {
        if (empty($locale) && isset($this) && $this instanceof Numbers_Words) {
            $locale = $this->locale;
        }

        if (empty($locale)) {
            $locale = 'en_US';
        }

        $classname = self::loadLocale($locale, '_toWords');


        $obj = new $classname;


        if (!is_int($num)) {
            $num = $obj->normalizeNumber($num);

            // cast (sanitize) to int without losing precision
            $num = preg_replace('/(.*?)('.preg_quote($obj->decimalPoint).'.*?)?$/', '$1', $num);
        }

        if (empty($options)) {
            return trim($obj->_toWords($num));
        }
        return trim($obj->_toWords($num, $options));
    }
    // }}}

    // {{{ toCurrency()
    /**
     * Converts a currency value to word representation (1.02 => one dollar two cents)
     * If the number has not any fraction part, the "cents" number is omitted.
     *
     * @param float  $num      A float/integer/string number representing currency value
     *
     * @param string $locale   Language name abbreviation. Optional. Defaults to en_US.
     *
     * @param string $intCurr  International currency symbol
     *                         as defined by the ISO 4217 standard (three characters).
     *                         E.g. 'EUR', 'USD', 'PLN'. Optional.
     *                         Defaults to $def_currency defined in the language class.
     *
     * @param string $decimalPoint  Decimal mark symbol
     *                         E.g. '.', ','. Optional.
     *                         Defaults to $decimalPoint defined in the language class.
     *
     * @return string  The corresponding word representation
     *
     * @access public
     * @author Piotr Klaban <makler@man.torun.pl>
     * @since  PHP 4.2.3
     * @return string
     */
    function toCurrency($num, $locale = 'en_US', $intCurr = '', $decimalPoint = null)
    {
        $ret = $num;

        $classname = self::loadLocale($locale, 'toCurrencyWords');

        $obj = new $classname;

        if (is_null($decimalPoint)) {
            $decimalPoint = $obj->decimalPoint;
        }

        // round if a float is passed, use Math_BigInteger otherwise
        if (is_float($num)) {
            $num = round($num, 2);
        }

        $num = $obj->normalizeNumber($num, $decimalPoint);

        if (strpos($num, $decimalPoint) === false) {
            return trim($obj->toCurrencyWords($intCurr, $num));
        }

        $currency = explode($decimalPoint, $num, 2);

        $len = strlen($currency[1]);

        if ($len == 1) {
            // add leading zero
            $currency[1] .= '0';
        } elseif ($len > 2) {
            // get the 3rd digit after the comma
            $round_digit = substr($currency[1], 2, 1);
            
            // cut everything after the 2nd digit
            $currency[1] = substr($currency[1], 0, 2);
            
            if ($round_digit >= 5) {
                // round up without losing precision
                include_once "Math/BigInteger.php";

                $int = new Math_BigInteger(join($currency));
                $int = $int->add(new Math_BigInteger(1));
                $int_str = $int->toString();

                $currency[0] = substr($int_str, 0, -2);
                $currency[1] = substr($int_str, -2);

                // check if the rounded decimal part became zero
                if ($currency[1] == '00') {
                    $currency[1] = false;
                }
            }
        }

        return trim($obj->toCurrencyWords($intCurr, $currency[0], $currency[1]));
    }
    // }}}

    // {{{ getLocales()
    /**
     * Lists available locales for Numbers_Words
     *
     * @param mixed $locales string/array of strings $locale
     *                       Optional searched language name abbreviation.
     *                       Default: all available locales.
     *
     * @return array   The available locales (optionaly only the requested ones)
     * @author Piotr Klaban <makler@man.torun.pl>
     * @author Bertrand Gugger, bertrand at toggg dot com
     *
     * @return mixed[] Array of locale names ("de_DE", "en")
     */
    public static function getLocales($locales = null)
    {
        $ret = array();
        if (isset($locales) && is_string($locales)) {
            $locales = array($locales);
        }

        $dname = __DIR__ . DIRECTORY_SEPARATOR . 'Words'
            . DIRECTORY_SEPARATOR . 'Locale'
            . DIRECTORY_SEPARATOR;

        $sfiles = glob($dname . '??.php');
        foreach ($sfiles as $fname) {
            $lname = substr($fname, -6, 2);
            if (is_file($fname) && is_readable($fname)
                && (count($locales) == 0 || in_array($lname, $locales))
            ) {
                $ret[] = $lname;
            }
        }

        $mfiles = glob($dname . '??/??.php');
        foreach ($mfiles as $fname) {
            $lname = str_replace(array('/', '\\'), '_', substr($fname, -9, 5));
            if (is_file($fname) && is_readable($fname)
                && (count($locales) == 0 || in_array($lname, $locales))
            ) {
                $ret[] = $lname;
            }
        }

        sort($ret);
        return $ret;
    }
    // }}}

    /**
     * Load the given locale and return class name
     *
     * @param string $locale         Locale key, e.g. "de" or "en_US"
     * @param string $requiredMethod Method that this class needs to have
     *
     * @return string Locale class name
     *
     * @throws Numbers_Words_Exception When the class cannot be loaded
     */
    public static function loadLocale($locale, $requiredMethod)
    {
        $classname = 'Numbers_Words_Locale_' . $locale;
        if (!class_exists($classname)) {
            $file = str_replace('_', '/', $classname) . '.php';
            if (stream_resolve_include_path($file)) {
                include_once $file;
            }

            if (!class_exists($classname)) {
                throw new Numbers_Words_Exception(
                    'Unable to load locale class ' . $classname
                );
            }
        }

        $methods = get_class_methods($classname);

        if (!in_array($requiredMethod, $methods)) {
            throw new Numbers_Words_Exception(
                "Unable to find method '$requiredMethod' in class '$classname'"
            );
        }

        return $classname;
    }

    /**
     * Removes redundant spaces, thousands separators, etc.
     *
     * @param string $num            Some number
     * @param string $decimalPoint   The decimal mark, e.g. "." or ","
     *
     * @return string Number
     */
    function normalizeNumber($num, $decimalPoint = null)
    {
        if (is_null($decimalPoint)) {
            $decimalPoint = $this->decimalPoint;
        }

        return preg_replace('/[^-'.preg_quote($decimalPoint).'0-9]/', '', $num);
    }
}

// }}}

/**
 * Class for translating numbers into French.
 *
 * @category Numbers
 * @package  Numbers_Words
 * @author   Kouber Saparev <kouber@php.net>
 * @license  PHP 3.01 http://www.php.net/license/3_01.txt
 * @link     http://pear.php.net/package/Numbers_Words
 */
class Numbers_Words_Locale_fr extends Numbers_Words
{

    // {{{ properties

    /**
     * Locale name.
     * @var string
     * @access public
     */
    var $locale = 'fr';

    /**
     * Language name in English.
     * @var string
     * @access public
     */
    var $lang = 'French';

    /**
     * Native language name.
     * @var string
     * @access public
     */
    var $lang_native = 'Français';

    /**
     * The words for some numbers.
     * @var string
     * @access private
     */
    var $_misc_numbers = array(
        10=>'dix',      // 10
            'onze',     // 11
            'douze',    // 12
            'treize',   // 13
            'quatorze', // 14
            'quinze',   // 15
            'seize',    // 16
        20=>'vingt',    // 20
        30=>'trente',   // 30
        40=>'quarante', // 40
        50=>'cinquante',// 50
        60=>'soixante', // 60
       100=>'cent'      // 100
    );


    /**
     * The words for digits (except zero).
     * @var string
     * @access private
     */
    var $_digits = array(1=>"un", "deux", "trois", "quatre", "cinq", "six", "sept", "huit", "neuf");

    /**
     * The word for zero.
     * @var string
     * @access private
     */
    var $_zero = 'zéro';

    /**
     * The word for infinity.
     * @var string
     * @access private
     */
    var $_infinity = 'infini';

    /**
     * The word for the "and" language construct.
     * @var string
     * @access private
     */
    var $_and = 'et';
    
    /**
     * The word separator.
     * @var string
     * @access private
     */
    var $_sep = ' ';

    /**
     * The dash liaison.
     * @var string
     * @access private
     */
    var $_dash = '-';

    /**
     * The word for the minus sign.
     * @var string
     * @access private
     */
    var $_minus = 'moins'; // minus sign

    /**
     * The plural suffix (except for hundred).
     * @var string
     * @access private
     */
    var $_plural = 's'; // plural suffix

    /**
     * The suffixes for exponents (singular).
     * @var array
     * @access private
     */
    var $_exponent = array(
        0 => '',
        3 => 'mille',
        6 => 'million',
        9 => 'milliard',
       12 => 'trillion',
       15 => 'quadrillion',
       18 => 'quintillion',
       21 => 'sextillion',
       24 => 'septillion',
       27 => 'octillion',
       30 => 'nonillion',
       33 => 'decillion',
       36 => 'undecillion',
       39 => 'duodecillion',
       42 => 'tredecillion',
       45 => 'quattuordecillion',
       48 => 'quindecillion',
       51 => 'sexdecillion',
       54 => 'septendecillion',
       57 => 'octodecillion',
       60 => 'novemdecillion',
       63 => 'vigintillion',
       66 => 'unvigintillion',
       69 => 'duovigintillion',
       72 => 'trevigintillion',
       75 => 'quattuorvigintillion',
       78 => 'quinvigintillion',
       81 => 'sexvigintillion',
       84 => 'septenvigintillion',
       87 => 'octovigintillion',
       90 => 'novemvigintillion',
       93 => 'trigintillion',
       96 => 'untrigintillion',
       99 => 'duotrigintillion',
      102 => 'trestrigintillion',
      105 => 'quattuortrigintillion',
      108 => 'quintrigintillion',
      111 => 'sextrigintillion',
      114 => 'septentrigintillion',
      117 => 'octotrigintillion',
      120 => 'novemtrigintillion',
      123 => 'quadragintillion',
      126 => 'unquadragintillion',
      129 => 'duoquadragintillion',
      132 => 'trequadragintillion',
      135 => 'quattuorquadragintillion',
      138 => 'quinquadragintillion',
      141 => 'sexquadragintillion',
      144 => 'septenquadragintillion',
      147 => 'octoquadragintillion',
      150 => 'novemquadragintillion',
      153 => 'quinquagintillion',
      156 => 'unquinquagintillion',
      159 => 'duoquinquagintillion',
      162 => 'trequinquagintillion',
      165 => 'quattuorquinquagintillion',
      168 => 'quinquinquagintillion',
      171 => 'sexquinquagintillion',
      174 => 'septenquinquagintillion',
      177 => 'octoquinquagintillion',
      180 => 'novemquinquagintillion',
      183 => 'sexagintillion',
      186 => 'unsexagintillion',
      189 => 'duosexagintillion',
      192 => 'tresexagintillion',
      195 => 'quattuorsexagintillion',
      198 => 'quinsexagintillion',
      201 => 'sexsexagintillion',
      204 => 'septensexagintillion',
      207 => 'octosexagintillion',
      210 => 'novemsexagintillion',
      213 => 'septuagintillion',
      216 => 'unseptuagintillion',
      219 => 'duoseptuagintillion',
      222 => 'treseptuagintillion',
      225 => 'quattuorseptuagintillion',
      228 => 'quinseptuagintillion',
      231 => 'sexseptuagintillion',
      234 => 'septenseptuagintillion',
      237 => 'octoseptuagintillion',
      240 => 'novemseptuagintillion',
      243 => 'octogintillion',
      246 => 'unoctogintillion',
      249 => 'duooctogintillion',
      252 => 'treoctogintillion',
      255 => 'quattuoroctogintillion',
      258 => 'quinoctogintillion',
      261 => 'sexoctogintillion',
      264 => 'septoctogintillion',
      267 => 'octooctogintillion',
      270 => 'novemoctogintillion',
      273 => 'nonagintillion',
      276 => 'unnonagintillion',
      279 => 'duononagintillion',
      282 => 'trenonagintillion',
      285 => 'quattuornonagintillion',
      288 => 'quinnonagintillion',
      291 => 'sexnonagintillion',
      294 => 'septennonagintillion',
      297 => 'octononagintillion',
      300 => 'novemnonagintillion',
      303 => 'centillion'
        );
    // }}}

    // {{{ _splitNumber()

    /**
     * Split a number to groups of three-digit numbers.
     *
     * @param mixed $num An integer or its string representation
     *                   that need to be split
     *
     * @return array  Groups of three-digit numbers.
     * @access private
     * @author Kouber Saparev <kouber@php.net>
     * @since  PHP 4.2.3
     */
    function _splitNumber($num)
    {
        if (is_string($num)) {
            $ret    = array();
            $strlen = strlen($num);
            $first  = substr($num, 0, $strlen%3);

            preg_match_all('/\d{3}/', substr($num, $strlen%3, $strlen), $m);
            $ret =& $m[0];

            if ($first) {
                array_unshift($ret, $first);
            }

            return $ret;
        }
        return explode(' ', number_format($num, 0, '', ' ')); // a faster version for integers
    }
    // }}}

    // {{{ _showDigitsGroup()

    /**
     * Converts a three-digit number to its word representation
     * in French language.
     *
     * @param integer $num  An integer between 1 and 999 inclusive.
     * @param boolean $last A flag, that determines if it is the last group of digits -
     *                      this is used to accord the plural suffix of the "hundreds".
     *                      Example: 200 = "deux cents", but 200000 = "deux cent mille".
     *
     * @return string   The words for the given number.
     * @access private
     * @author Kouber Saparev <kouber@php.net>
     */
    function _showDigitsGroup($num, $last = false)
    {
        $ret = '';
        
        // extract the value of each digit from the three-digit number
        $e = $num%10;                  // ones
        $d = ($num-$e)%100/10;         // tens
        $s = ($num-$d*10-$e)%1000/100; // hundreds
        
        // process the "hundreds" digit.
        if ($s) {
            if ($s>1) {
                $ret .= $this->_digits[$s].$this->_sep.$this->_misc_numbers[100];
                if ($last && !$e && !$d) {
                    $ret .= $this->_plural;
                }
            } else {
                $ret .= $this->_misc_numbers[100];
            }
            $ret .= $this->_sep;
        }

        // process the "tens" digit, and optionally the "ones" digit.
        if ($d) {
            // in the case of 1, the "ones" digit also must be processed
            if ($d==1) {
                if ($e<=6) {
                    $ret .= $this->_misc_numbers[10+$e];
                } else {
                    $ret .= $this->_misc_numbers[10].'-'.$this->_digits[$e];
                }
                $e = 0;
            } elseif ($d>5) {
                if ($d<8) {
                    $ret .= $this->_misc_numbers[60];

                    $resto = $d*10+$e-60;
                    if ($e==1) {
                        $ret .= $this->_sep.$this->_and.$this->_sep;
                    } elseif ($resto) {
                        $ret .= $this->_dash;
                    }
                    
                    if ($resto) {
                        $ret .= $this->_showDigitsGroup($resto);
                    }
                    $e = 0;
                } else {
                    $ret .= $this->_digits[4].$this->_dash.$this->_misc_numbers[20];

                    $resto = $d*10+$e-80;
                    if ($resto) {
                        $ret .= $this->_dash;
                        $ret .= $this->_showDigitsGroup($resto);

                        $e = 0;
                    } else {
                        $ret .= $this->_plural;
                    }
                }
            } else {
                $ret .= $this->_misc_numbers[$d*10];
            }
        }

        // process the "ones" digit
        if ($e) {
            if ($d) {
                if ($e==1) {
                    $ret .= $this->_sep.$this->_and.$this->_sep;
                } else {
                    $ret .= $this->_dash;
                }
            }
            $ret .= $this->_digits[$e];
        }

        // strip excessive separators
        $ret = rtrim($ret, $this->_sep);

        return $ret;
    }
    // }}}

    // {{{ _toWords()

    /**
     * Converts a number to its word representation
     * in French language.
     *
     * @param integer $num An integer (or its string representation) between 9.99*-10^302
     *                        and 9.99*10^302 (999 centillions) that need to be converted to words
     *
     * @return string  The corresponding word representation
     * @access protected
     * @author Kouber Saparev <kouber@php.net>
     * @since  Numbers_Words 0.16.3
     */
    function _toWords($num = 0)
    {
        $ret = '';

        // check if $num is a valid non-zero number
        if (!$num || preg_match('/^-?0+$/', $num) || !preg_match('/^-?\d+$/', $num)) {
            return $this->_zero;
        }

        // add a minus sign
        if (substr($num, 0, 1) == '-') {
            $ret = $this->_minus . $this->_sep;
            $num = substr($num, 1);
        }

        // if the absolute value is greater than 9.99*10^302, return infinity
        if (strlen($num)>306) {
            return $ret . $this->_infinity;
        }

        // strip excessive zero signs
        $num = ltrim($num, '0');

        // split $num to groups of three-digit numbers
        $num_groups = $this->_splitNumber($num);

        $sizeof_numgroups = count($num_groups);

        foreach ($num_groups as $i=>$number) {
            // what is the corresponding exponent for the current group
            $pow = $sizeof_numgroups-$i;

            // skip processment for empty groups
            if ($number!='000') {
                if ($number!=1 || $pow!=2) {
                    $ret .= $this->_showDigitsGroup($number, $i+1==$sizeof_numgroups||$pow>2).$this->_sep;
                }
                $ret .= $this->_exponent[($pow-1)*3];
                if ($pow>2 && $number>1) {
                    $ret .= $this->_plural;
                }
                $ret .= $this->_sep;
            }
        }

        return rtrim($ret, $this->_sep);
    }
    // }}}
}