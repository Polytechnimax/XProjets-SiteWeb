<?php

namespace Junior\SiteinterneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RemarquesUser
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\Table(name="remarquesuser")
 */
class RemarquesUser
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
     * @ORM\Column(name="contenu", type="text")
     */
    private $contenu;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ajouteLe", type="datetime")
     */
    private $ajouteLe;

    /**
     * @ORM\ManyToOne(targetEntity="Junior\SiteinterneBundle\Entity\User")
     */
    private $utilisateur;
   
    /**
     * @ORM\ManyToOne(targetEntity="Junior\SiteinterneBundle\Entity\User")
     */
    private $ajoutePar;


    public function __construct()
    {
		$this->ajouteLe = new \Datetime();
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
     * Set contenu
     *
     * @param string $contenu
     * @return RemarquesUser
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
     * Set ajouteLe
     *
     * @param \DateTime $ajouteLe
     * @return RemarquesUser
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
     * @return RemarquesUser
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
     * Set utilisateur
     *
     * @param \Junior\SiteinterneBundle\Entity\User $utilisateur
     * @return RemarquesUser
     */
    public function setUtilisateur(\Junior\SiteinterneBundle\Entity\User $utilisateur = null)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return \Junior\SiteinterneBundle\Entity\User 
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }
}
