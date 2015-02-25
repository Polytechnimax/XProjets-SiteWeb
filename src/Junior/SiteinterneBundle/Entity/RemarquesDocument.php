<?php

namespace Junior\SiteinterneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RemarquesDocument
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Junior\SiteinterneBundle\Entity\RemarquesDocumentRepository")
 * @ORM\Table(name="remarquesdocument")
 */
class RemarquesDocument
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="text")
     */
    private $contenu;

   /**
   * @ORM\ManyToOne(targetEntity="Junior\SiteinterneBundle\Entity\Document")
   */
   private $document;

   /**
   * @ORM\ManyToOne(targetEntity="Junior\SiteinterneBundle\Entity\User")
   */
    private $ajoutePar;

	
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->date = new \Datetime();
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
     * Set date
     *
     * @param \DateTime $date
     * @return RemarquesDocument
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set contenu
     *
     * @param string $contenu
     * @return RemarquesDocument
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * Get contenu
     *
     * @return string 
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * Set document
     *
     * @param \Junior\SiteinterneBundle\Entity\Document $document
     * @return RemarquesDocument
     */
    public function setDocument(\Junior\SiteinterneBundle\Entity\Document $document = null)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get document
     *
     * @return \Junior\SiteinterneBundle\Entity\Document 
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Set ajoutePar
     *
     * @param \Junior\SiteinterneBundle\Entity\User $ajoutePar
     * @return RemarquesDocument
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
}
