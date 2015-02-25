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
				"Récapitulatif mission",
				"Avenant et PVRI",
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
				$dernierReferent=$this->getDoctrine()
									->getRepository('JuniorSiteinterneBundle:Mission')
									->getDernierReferent();
				if($dernierReferent != null){
					$referentMission = $this->getDoctrine()
								  ->getRepository('JuniorSiteinterneBundle:User')
								  ->getProchainReferent($dernierReferent);
					$mission1->setReferent($referentMission);
				}
				$em->persist($mission1);
				
				$message_referent = \Swift_Message::newInstance()
					->setSubject('[XProjets] Nouvelle mission postée')
					->setFrom('contact@xprojets.com')
					//->setTo($this->getReferent()->getEmail())
					->setTo('remi.delbouys@laposte.net')
					->setBody($this->renderView('JuniorSiteinterneBundle:Mails:mail-referent-mission-ajoutee.html.twig', array(
					'mission' => $mission1
					)))
				;
				$message_recruteur = \Swift_Message::newInstance()
					->setSubject('[XProjets] Nouvelle mission postée')
					->setFrom('contact@xprojets.com')
					//->setTo('mailreferent')
					->setTo('remi.delbouys@laposte.net')
					->setBody($this->renderView('JuniorSiteinterneBundle:Mails:mail-recruteur-mission-ajoutee.html.twig', array(
					'mission' => $mission1
					)))
				;
	
				$this->get('mailer')->send($message_referent);
				$this->get('mailer')->send($message_recruteur);
				
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
				$dernierReferent=$this->getDoctrine()
									->getRepository('JuniorSiteinterneBundle:Mission')
									->getDernierReferent();
				if($dernierReferent != null){
					$referentMission = $this->getDoctrine()
								  ->getRepository('JuniorSiteinterneBundle:User')
								  ->getProchainReferent($dernierReferent);
					$mission2->setReferent($referentMission);
				}
				$em->persist($mission2);
				$message_referent = \Swift_Message::newInstance()
					->setSubject('[XProjets] Nouvelle mission postée')
					->setFrom('contact@xprojets.com')
					//->setTo($this->getReferent()->getEmail())
					->setTo('remi.delbouys@laposte.net')
					->setBody($this->renderView('JuniorSiteinterneBundle:Mails:mail-referent-mission-ajoutee.html.twig', array(
					'mission' => $mission2
					)))
				;
				$message_recruteur = \Swift_Message::newInstance()
					->setSubject('[XProjets] Nouvelle mission postée')
					->setFrom('contact@xprojets.com')
					//->setTo('mailreferent')
					->setTo('remi.delbouys@laposte.net')
					->setBody($this->renderView('JuniorSiteinterneBundle:Mails:mail-recruteur-mission-ajoutee.html.twig', array(
					'mission' => $mission2
					)))
				;
	
				$this->get('mailer')->send($message_referent);
				$this->get('mailer')->send($message_recruteur);
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
public function userAction($id, Request $request){
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
		->add('inscritLe', 'date')
		->add('save', 'submit')
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
			} elseif ($refIntCDP== 2 ){
				$mission->addIntervenant($user);
			} elseif ($refIntCDP== 3 ){
				$mission->setChefDeProjet($user);
			}
			$em = $this->getDoctrine()->getManager();
			$em->flush();
			return $this->redirect($this->generateUrl('junior_siteinterne_mission', array('id' => $mission->getId())));
		}
		
		$listeUsers = $this->getDoctrine()
							  ->getRepository('JuniorSiteinterneBundle:User')
							  ->findAll();
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
					->remove('file')
					->remove('signeLe');
		}


		if ($form2->handleRequest($request)->isValid()) {
			$user = $this->getUser();
			if (null !== $user) {
				$em = $this->getDoctrine()->getManager();
				$doc->upload();
				$doc->setAjoutePar($user);
				$doc->setAjouteLe(new \Datetime());
				$em->flush();
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
	
	public function docviergeAction($id, $typedoc, Request $request)
	{

		$manager = $this
						->getDoctrine()
						->getManager();
		$repository1 = $manager
						->getRepository('JuniorSiteinterneBundle:Mission');
		$mission = $repository1->find($id);
		
		$name = $typedoc.'.xml'; // nom du fichier à ouvrir
		$myFile = file_get_contents( __DIR__.'/../../../../web/downloads/docsvierges/'.$name);
		 
		$searchReplace = array(
			'refdoc' => $mission->getNom(),
			'titredossier' => 'swagmission'
			);
		 
		$search  = array_keys($searchReplace);
		$replace = array_values($searchReplace);

		
		$response = new Response();
		$response->setContent(str_replace($search, $replace, $myFile));
		$response->headers->set('Content-Type', 'application/msword; name="'.$name.'"');
		$response->headers->set('Content-Transfer-Encoding', 'binary');
		$response->headers->set('Content-Disposition', 'attachment; filename="'.$name.'"');

		$response->send();
	}
	
  /**
   * @Security("has_role('ROLE_MBJE')")
   */
	public function modifAction($idmission, $iduser, $supprInt, $changerEtat, $changerPublique, $modifMission, $modifDates, $modifRaisonEchec, Request $request)
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
			$intervenant = $repository2->find($iduser);
			
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
		11 : afficher les users pr ajouter admin
		12 : afficher les users pr ajouter mbje
		13 : afficher les users pr ajouter polemission
		14 : afficher les users pr ajouter rh*/
		
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
		}
		
		$manager->flush();

		$admin = array();
		$mbje = array();
		$polemission = array();
		$rh = array();
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
				}
			}
		}
		
		return $this->render('JuniorSiteinterneBundle:Siteinterne:config.html.twig', array(
			'admin' => $admin,
			'mbje' => $mbje,
			'polemission' => $polemission,
			'rh' => $rh,
			'action' => $action
		));		
	}
	
}
