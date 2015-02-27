<?php

namespace Junior\SiteinterneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Junior\SiteinterneBundle\Validator as JuniorAssert;


/**
 * Document
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Junior\SiteinterneBundle\Entity\DocumentRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="document")
 * @JuniorAssert\Surveilledates
 */
class Document
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
     * @var string
     *
     * @ORM\Column(name="type_de_document", type="string", length=255)
     */
    private $typeDeDocument;
	
    /**
     * @var string
     *
     * @ORM\Column(name="extension", type="string", length=255)
     */
    private $extension;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="frozen", type="boolean", nullable=true)
     */
    private $frozen;

	/**
     * @var boolean
     *
     * @ORM\Column(name="up_une_fois", type="boolean", nullable=true)
     */
    private $upUneFois;

   /**
   * @ORM\ManyToOne(targetEntity="Junior\SiteinterneBundle\Entity\User")
   */
    private $ajoutePar;

   /**
   * @ORM\ManyToOne(targetEntity="Junior\SiteinterneBundle\Entity\User")
   */
    private $intervenant;

   /**
   * @ORM\ManyToOne(targetEntity="Junior\SiteinterneBundle\Entity\Mission")
   */
    private $mission;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ajoute_le", type="datetime", nullable=true)
     */
    private $ajouteLe;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="signe_le", type="datetime", nullable=true)
     */
    private $signeLe;

	private $file;

  
    /**
     * Constructor
     */
    public function __construct()
    {
		$this->ajouteLe = new \Datetime();
    }
	
	/**
   * @ORM\PreUpdate
   */
  public function up()
  {
    $this->setUpUneFois(true);
  }

  public function getFile()
  {
    return $this->file;
  }


  public function setFile(UploadedFile $file)
  {
    $this->file = $file;
  }


  public function upload()
  {
    if (null === $this->file) {
      return;
    }
	
	$this->setExtension('.' . $this->file->guessExtension());

    $this->file->move(
      $this->getUploadRootDir(),
      $this->getId().$this->getExtension()
    );
  }
  public function getUploadDir()
  {
    return 'uploads/docsmissions/' . $this->getDirName();
  }

  protected function getUploadRootDir()
  {
    return __DIR__.'/../../../../web/'.$this->getUploadDir();
  }
  
  public function getDirName()
  {
	if($this->typeDeDocument == "Devis"){
		return "devis";
	}
	if($this->typeDeDocument == "Facture à accompte"){
		return "factureaaccompte";
	}
	if($this->typeDeDocument == "Propale"){
		return "propale";
	}
	if($this->typeDeDocument == "Convention client"){
		return "conventionclient";
	}
	if($this->typeDeDocument == "Récapitulatif mission"){
		return "recapitulatifmission";
	}
	if($this->typeDeDocument == "PVRI"){
		return "pvri";
	}
	if($this->typeDeDocument == "Avenant à la convention client"){
		return "avenantalacc";
	}
	if($this->typeDeDocument == "Avenant au récapitulatif mission"){
		return "avenantaurm";
	}
	if($this->typeDeDocument == "Avenant de rupture à la convention"){
		return "avenantruptureconv";
	}
	if($this->typeDeDocument == "PVRF"){
		return "pvrf";
	}
	if($this->typeDeDocument == "Facture"){
		return "facture";
	}
	if($this->typeDeDocument == "Rapport pédagogique"){
		return "rapportpedagogique";
	}
	if($this->typeDeDocument == "Bulletin de versement"){
		return "bulletindeversement";
	}
	if($this->typeDeDocument == "Questionnaire de satisfaction"){
		return "questionnairedesatisfaction";
	}
	return null;
  }

  public function getWebPath()
  {
    return $this->getUploadDir().'/'.$this->getId().$this->getExtension();
  }
  
  /**
   * @ORM\PrePersist
   */
   public function xmlParDefault(){
	$this->setExtension('.xml');
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
     * Set typeDeDocument
     *
     * @param string $typeDeDocument
     * @return Document
     */
    public function setTypeDeDocument($typeDeDocument)
    {
        $this->typeDeDocument = $typeDeDocument;

        return $this;
    }

    /**
     * Get typeDeDocument
     *
     * @return string 
     */
    public function getTypeDeDocument()
    {
        return $this->typeDeDocument;
    }

    /**
     * Set frozen
     *
     * @param boolean $frozen
     * @return Document
     */
    public function setFrozen($frozen)
    {
        $this->frozen = $frozen;

        return $this;
    }

    /**
     * Get frozen
     *
     * @return boolean 
     */
    public function getFrozen()
    {
        return $this->frozen;
    }

    /**
     * Set ajouteLe
     *
     * @param \DateTime $ajouteLe
     * @return Document
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
     * Set ajoutePar
     *
     * @param \Junior\SiteinterneBundle\Entity\User $ajoutePar
     * @return Document
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
     * Set mission
     *
     * @param \Junior\SiteinterneBundle\Entity\Mission $mission
     * @return Document
     */
    public function setMission(\Junior\SiteinterneBundle\Entity\Mission $mission = null)
    {
        $this->mission = $mission;

        return $this;
    }

    /**
     * Get mission
     *
     * @return \Junior\SiteinterneBundle\Entity\Mission 
     */
    public function getMission()
    {
        return $this->mission;
    }
	

    /**
     * Set upUneFois
     *
     * @param boolean $upUneFois
     * @return Document
     */
    public function setUpUneFois($upUneFois)
    {
        $this->upUneFois = $upUneFois;

        return $this;
    }

    /**
     * Get upUneFois
     *
     * @return boolean 
     */
    public function getUpUneFois()
    {
        return $this->upUneFois;
    }

    /**
     * Set signeLe
     *
     * @param \DateTime $signeLe
     * @return Document
     */
    public function setSigneLe($signeLe)
    {
        $this->signeLe = $signeLe;

        return $this;
    }

    /**
     * Get signeLe
     *
     * @return \DateTime 
     */
    public function getSigneLe()
    {
        return $this->signeLe;
    }

    /**
     * Set intervenant
     *
     * @param \Junior\SiteinterneBundle\Entity\User $intervenant
     * @return Document
     */
    public function setIntervenant(\Junior\SiteinterneBundle\Entity\User $intervenant = null)
    {
        $this->intervenant = $intervenant;

        return $this;
    }

    /**
     * Get intervenant
     *
     * @return \Junior\SiteinterneBundle\Entity\User 
     */
    public function getIntervenant()
    {
        return $this->intervenant;
    }

    /**
     * Set extension
     *
     * @param string $extension
     * @return Document
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension
     *
     * @return string 
     */
    public function getExtension()
    {
        return $this->extension;
    }
}
