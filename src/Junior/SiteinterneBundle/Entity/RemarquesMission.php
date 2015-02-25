<?php

namespace Junior\SiteinterneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RemarquesMission
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Junior\SiteinterneBundle\Entity\RemarquesMissionRepository")
 * @ORM\Table(name="remarquesmission")
 */
class RemarquesMission
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
   * @ORM\ManyToOne(targetEntity="Junior\SiteinterneBundle\Entity\Mission")
   */
   private $mission;
   
  /**
   * @ORM\ManyToOne(targetEntity="Junior\SiteinterneBundle\Entity\User")
   */
    private $ajoutePar;


  public function __construct()
  {
    $this->date         = new \Datetime();
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
     * @return RemarquesMission
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
     * @return RemarquesMission
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
     * Set mission
     *
     * @param \Junior\SiteinterneBundle\Entity\Mission $mission
     * @return RemarquesMission
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
     * Set ajoutePar
     *
     * @param \Junior\SiteinterneBundle\Entity\User $ajoutePar
     * @return RemarquesMission
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
