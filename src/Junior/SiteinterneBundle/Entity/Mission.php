<?php

namespace Junior\SiteinterneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Mission
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Junior\SiteinterneBundle\Entity\MissionRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="mission")
 */
class Mission
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="num_siaje", type="integer", unique=true)
     */
    private $numSiaje;

    /**
     * @var boolean
     *
     * @ORM\Column(name="publique", type="integer", nullable=true)
     */
    private $publique;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="descriptif", type="text")
     */
    private $descriptif;


    /**
     * @var string
     *
     * @ORM\Column(name="etat", type="string", length=255, nullable=true)
     */
    private $etat;

    /**
     * @var string
     *
     * @ORM\Column(name="raison_echec", type="text", nullable=true)
     */
    private $raisonEchec;

    /**
     * @var string
     *
     * @ORM\Column(name="detail_avancement", type="text", nullable=true)
     */
    private $detailAvancement;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_debut", type="datetime", nullable=true)
     */
    private $dateDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_fin", type="datetime", nullable=true)
     */
    private $dateFin;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_semaines", type="integer", nullable=true)
     */
    private $nbSemaines;
	
   /**
    * @ORM\Column(name="phases", type="array", nullable=true)
    */
    private $phases = array();

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_jeh", type="integer", nullable=true)
     */
    private $nbJeh;

    /**
     * @var integer
     *
     * @ORM\Column(name="taux_tva", type="integer", nullable=true)
     */
    private $tauxTva;

    /**
     * @var integer
     *
     * @ORM\Column(name="frais", type="integer", nullable=true)
     */
    private $frais;

    /**
     * @var integer
     *
     * @ORM\Column(name="accompte", type="integer", nullable=true)
     */
    private $accompte;

  /**
   * @ORM\ManyToMany(targetEntity="Junior\SiteinterneBundle\Entity\Category", cascade={"persist"})
   */
   private $categories;

   /**
   * @ORM\ManyToMany(targetEntity="Junior\SiteinterneBundle\Entity\Competence", cascade={"persist"})
   */
   private $competences;
   
   /**
   * @ORM\ManyToOne(targetEntity="Junior\SiteinterneBundle\Entity\Client", cascade={"persist"})
   */
   private $client;
   
    /**
     * @var boolean
     *
     * @ORM\Column(name="montrer_client_au_cdp", type="boolean", nullable=true)
     */
    private $montrerClientAuCdp;

   /**
   * @ORM\ManyToOne(targetEntity="Junior\SiteinterneBundle\Entity\User")
   */
    private $referent;

     /**
     * @var string
     *
     * @ORM\Column(name="appel_offre_contact_client", type="string", length=255, nullable=false)
     */
    private $appelOffreContactClient;
  
   /**
   * @ORM\ManyToOne(targetEntity="Junior\SiteinterneBundle\Entity\User")
   */
    private $chefDeProjet;

   /**
   * @ORM\ManyToOne(targetEntity="Junior\SiteinterneBundle\Entity\User")
   */
    private $ajoutePar;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ajoute_le", type="datetime", nullable=true)
     */
    private $ajouteLe;

   /**
   * @ORM\ManyToMany(targetEntity="Junior\SiteinterneBundle\Entity\User")
   */
    private $intervenants;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->competences = new \Doctrine\Common\Collections\ArrayCollection();
        $this->intervenants = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ajouteLe = new \Datetime();
		$this->etat = 'A pourvoir';
    }
	

    public function getChaineDeCaractere()
    {
		if($this->getAppelOffreContactClient() == "cc"){
			return "Conctact client";
		}
		if($this->getAppelOffreContactClient() == "ao"){
			return "Appel d'offre";
		}
		return null;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
	
    /**
     * Set numSiaje
     *
     * @param integer $numSiaje
     * @return Mission
     */
    public function setNumSiaje($numSiaje)
    {
        $this->numSiaje = $numSiaje;

        return $this;
    }

    /**
     * Get numSiaje
     *
     * @return integer 
     */
    public function getNumSiaje()
    {
        return $this->numSiaje;
    }

    /**
     * Set publique
     *
     * @param integer $publique
     * @return Mission
     */
    public function setPublique($publique)
    {
        $this->publique = $publique;

        return $this;
    }

    /**
     * Get publique
     *
     * @return integer 
     */
    public function getPublique()
    {
        return $this->publique;
    }

    /**
     * Set nom
     *
     * @param string $nom
     * @return Mission
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set descriptif
     *
     * @param string $descriptif
     * @return Mission
     */
    public function setDescriptif($descriptif)
    {
        $this->descriptif = $descriptif;

        return $this;
    }

    /**
     * Get descriptif
     *
     * @return string 
     */
    public function getDescriptif()
    {
        return $this->descriptif;
    }

    /**
     * Set etat
     *
     * @param string $etat
     * @return Mission
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return string 
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * Set raisonEchec
     *
     * @param string $raisonEchec
     * @return Mission
     */
    public function setRaisonEchec($raisonEchec)
    {
        $this->raisonEchec = $raisonEchec;

        return $this;
    }

    /**
     * Get raisonEchec
     *
     * @return string 
     */
    public function getRaisonEchec()
    {
        return $this->raisonEchec;
    }

    /**
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     * @return Mission
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateDebut
     *
     * @return \DateTime 
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set dateFin
     *
     * @param \DateTime $dateFin
     * @return Mission
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Get dateFin
     *
     * @return \DateTime 
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set nbJeh
     *
     * @param integer $nbJeh
     * @return Mission
     */
    public function setNbJeh($nbJeh)
    {
        $this->nbJeh = $nbJeh;

        return $this;
    }

    /**
     * Get nbJeh
     *
     * @return integer 
     */
    public function getNbJeh()
    {
        return $this->nbJeh;
    }

    /**
     * Set appelOffreContactClient
     *
     * @param string $appelOffreContactClient
     * @return Mission
     */
    public function setAppelOffreContactClient($appelOffreContactClient)
    {
        $this->appelOffreContactClient = $appelOffreContactClient;

        return $this;
    }

    /**
     * Get appelOffreContactClient
     *
     * @return string 
     */
    public function getAppelOffreContactClient()
    {
        return $this->appelOffreContactClient;
    }

    /**
     * Set ajouteLe
     *
     * @param \DateTime $ajouteLe
     * @return Mission
     */
    public function setAjouteLe($ajouteLe)
    {
        $this->ajouteLe = $ajouteLe;

        return $this;
    }

    /**
     * Get ajouteLe
     *
     * @return \DateTime 
     */
    public function getAjouteLe()
    {
        return $this->ajouteLe;
    }

    /**
     * Add categories
     *
     * @param \Junior\SiteinterneBundle\Entity\Category $categories
     * @return Mission
     */
    public function addCategory(\Junior\SiteinterneBundle\Entity\Category $categories)
    {
        $this->categories[] = $categories;

        return $this;
    }

    /**
     * Remove categories
     *
     * @param \Junior\SiteinterneBundle\Entity\Category $categories
     */
    public function removeCategory(\Junior\SiteinterneBundle\Entity\Category $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add competences
     *
     * @param \Junior\SiteinterneBundle\Entity\Competence $competences
     * @return Mission
     */
    public function addCompetence(\Junior\SiteinterneBundle\Entity\Competence $competences)
    {
        $this->competences[] = $competences;

        return $this;
    }

    /**
     * Remove competences
     *
     * @param \Junior\SiteinterneBundle\Entity\Competence $competences
     */
    public function removeCompetence(\Junior\SiteinterneBundle\Entity\Competence $competences)
    {
        $this->competences->removeElement($competences);
    }

    /**
     * Get competences
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCompetences()
    {
        return $this->competences;
    }

    /**
     * Set client
     *
     * @param \Junior\SiteinterneBundle\Entity\Client $client
     * @return Mission
     */
    public function setClient(\Junior\SiteinterneBundle\Entity\Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \Junior\SiteinterneBundle\Entity\Client 
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set referent
     *
     * @param \Junior\SiteinterneBundle\Entity\User $referent
     * @return Mission
     */
    public function setReferent(\Junior\SiteinterneBundle\Entity\User $referent = null)
    {
        $this->referent = $referent;

        return $this;
    }

    /**
     * Get referent
     *
     * @return \Junior\SiteinterneBundle\Entity\User 
     */
    public function getReferent()
    {
        return $this->referent;
    }

    /**
     * Set chefDeProjet
     *
     * @param \Junior\SiteinterneBundle\Entity\User $chefDeProjet
     * @return Mission
     */
    public function setChefDeProjet(\Junior\SiteinterneBundle\Entity\User $chefDeProjet = null)
    {
        $this->chefDeProjet = $chefDeProjet;

        return $this;
    }

    /**
     * Get chefDeProjet
     *
     * @return \Junior\SiteinterneBundle\Entity\User 
     */
    public function getChefDeProjet()
    {
        return $this->chefDeProjet;
    }

    /**
     * Set ajoutePar
     *
     * @param \Junior\SiteinterneBundle\Entity\User $ajoutePar
     * @return Mission
     */
    public function setAjoutePar(\Junior\SiteinterneBundle\Entity\User $ajoutePar = null)
    {
        $this->ajoutePar = $ajoutePar;

        return $this;
    }

    /**
     * Get ajoutePar
     *
     * @return \Junior\SiteinterneBundle\Entity\User 
     */
    public function getAjoutePar()
    {
        return $this->ajoutePar;
    }

    /**
     * Add intervenants
     *
     * @param \Junior\SiteinterneBundle\Entity\User $intervenants
     * @return Mission
     */
    public function addIntervenant(\Junior\SiteinterneBundle\Entity\User $intervenants)
    {
        $this->intervenants[] = $intervenants;

        return $this;
    }

    /**
     * Remove intervenants
     *
     * @param \Junior\SiteinterneBundle\Entity\User $intervenants
     */
    public function removeIntervenant(\Junior\SiteinterneBundle\Entity\User $intervenants)
    {
        $this->intervenants->removeElement($intervenants);
    }

    /**
     * Get intervenants
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getIntervenants()
    {
        return $this->intervenants;
    }

    /**
     * Set montrerClientAuCdp
     *
     * @param integer $montrerClientAuCdp
     * @return Mission
     */
    public function setMontrerClientAuCdp($montrerClientAuCdp)
    {
        $this->montrerClientAuCdp = $montrerClientAuCdp;

        return $this;
    }

    /**
     * Get montrerClientAuCdp
     *
     * @return integer 
     */
    public function getMontrerClientAuCdp()
    {
        return $this->montrerClientAuCdp;
    }

    /**
     * Set nbSemaines
     *
     * @param integer $nbSemaines
     * @return Mission
     */
    public function setNbSemaines($nbSemaines)
    {
        $this->nbSemaines = $nbSemaines;

        return $this;
    }

    /**
     * Get nbSemaines
     *
     * @return integer 
     */
    public function getNbSemaines()
    {
        return $this->nbSemaines;
    }

    /**
     * Set frais
     *
     * @param integer $frais
     * @return Mission
     */
    public function setFrais($frais)
    {
        $this->frais = $frais;

        return $this;
    }

    /**
     * Get frais
     *
     * @return integer 
     */
    public function getFrais()
    {
        return $this->frais;
    }

    /**
     * Set accompte
     *
     * @param integer $accompte
     * @return Mission
     */
    public function setAccompte($accompte)
    {
        $this->accompte = $accompte;

        return $this;
    }

    /**
     * Get accompte
     *
     * @return integer 
     */
    public function getAccompte()
    {
        return $this->accompte;
    }

    /**
     * Set tauxTva
     *
     * @param integer $tauxTva
     * @return Mission
     */
    public function setTauxTva($tauxTva)
    {
        $this->tauxTva = $tauxTva;

        return $this;
    }

    /**
     * Get tauxTva
     *
     * @return integer 
     */
    public function getTauxTva()
    {
        return $this->tauxTva;
    }

    /**
     * Set phases
     *
     * @param array $phases
     * @return Mission
     */
    public function setPhases($phases)
    {
        $this->phases = $phases;

        return $this;
    }

    /**
     * Get phases
     *
     * @return array 
     */
    public function getPhases()
    {
        return $this->phases;
    }

    /**
     * Set detailAvancement
     *
     * @param string $detailAvancement
     * @return Mission
     */
    public function setDetailAvancement($detailAvancement)
    {
        $this->detailAvancement = $detailAvancement;

        return $this;
    }

    /**
     * Get detailAvancement
     *
     * @return string 
     */
    public function getDetailAvancement()
    {
        return $this->detailAvancement;
    }
}
