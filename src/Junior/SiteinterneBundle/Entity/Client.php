<?php

namespace Junior\SiteinterneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Client
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Junior\SiteinterneBundle\Entity\ClientRepository")
 * @ORM\Table(name="client")
 */
class Client
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
     * @ORM\Column(name="nom", type="string", length=255, nullable=true)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="particulier_entreprise", type="string", length=255)
     */
    private $particulierEntreprise;

    /**
     * @var string
     *
     * @ORM\Column(name="taille", type="string", length=255, nullable=true)
     */
    private $taille;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_contact", type="string", length=255)
     */
    private $nomContact;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom_contact", type="string", length=255)
     */
    private $prenomContact;

    /**
     * @var string
     *
     * @ORM\Column(name="fonction_contact", type="string", length=255,nullable=true)
     */
    private $fonctionContact;

    /**
     * @var string
     *
     * @ORM\Column(name="civilite_contact", type="string", length=255,nullable=true)
     */
    private $civiliteContact;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=255)
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse1", type="string", length=255)
     */
    private $adresse1;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse2", type="string", length=255, nullable=true)
     */
    private $adresse2;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse3", type="string", length=255, nullable=true)
     */
    private $adresse3;

    /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=255)
     */
    private $ville;

    /**
     * @var string
     *
     * @ORM\Column(name="code_postal", type="string", length=255)
     */
    private $codePostal;

    /**
     * @var string
     *
     * @ORM\Column(name="pays", type="string", length=255)
     */
    private $pays;

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
     * Set nom
     *
     * @param string $nom
     * @return Client
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
     * Set taille
     *
     * @param string $taille
     * @return Client
     */
    public function setTaille($taille)
    {
        $this->taille = $taille;

        return $this;
    }

    /**
     * Get taille
     *
     * @return string 
     */
    public function getTaille()
    {
        return $this->taille;
    }

    /**
     * Set nomContact
     *
     * @param string $nomContact
     * @return Client
     */
    public function setNomContact($nomContact)
    {
        $this->nomContact = $nomContact;

        return $this;
    }

    /**
     * Get nomContact
     *
     * @return string 
     */
    public function getNomContact()
    {
        return $this->nomContact;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Client
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     * @return Client
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string 
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set adresse1
     *
     * @param string $adresse1
     * @return Client
     */
    public function setAdresse1($adresse1)
    {
        $this->adresse1 = $adresse1;

        return $this;
    }

    /**
     * Get adresse1
     *
     * @return string 
     */
    public function getAdresse1()
    {
        return $this->adresse1;
    }

    /**
     * Set adresse2
     *
     * @param string $adresse2
     * @return Client
     */
    public function setAdresse2($adresse2)
    {
        $this->adresse2 = $adresse2;

        return $this;
    }

    /**
     * Get adresse2
     *
     * @return string 
     */
    public function getAdresse2()
    {
        return $this->adresse2;
    }

    /**
     * Set adresse3
     *
     * @param string $adresse3
     * @return Client
     */
    public function setAdresse3($adresse3)
    {
        $this->adresse3 = $adresse3;

        return $this;
    }

    /**
     * Get adresse3
     *
     * @return string 
     */
    public function getAdresse3()
    {
        return $this->adresse3;
    }

    /**
     * Set particulierEntreprise
     *
     * @param string $particulierEntreprise
     * @return Client
     */
    public function setParticulierEntreprise($particulierEntreprise)
    {
        $this->particulierEntreprise = $particulierEntreprise;

        return $this;
    }

    /**
     * Get particulierEntreprise
     *
     * @return string 
     */
    public function getParticulierEntreprise()
    {
        return $this->particulierEntreprise;
    }

    /**
     * Set ville
     *
     * @param string $ville
     * @return Client
     */
    public function setVille($ville)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string 
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set codePostal
     *
     * @param string $codePostal
     * @return Client
     */
    public function setCodePostal($codePostal)
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * Get codePostal
     *
     * @return string 
     */
    public function getCodePostal()
    {
        return $this->codePostal;
    }

    /**
     * Set pays
     *
     * @param string $pays
     * @return Client
     */
    public function setPays($pays)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays
     *
     * @return string 
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set prenomContact
     *
     * @param string $prenomContact
     * @return Client
     */
    public function setPrenomContact($prenomContact)
    {
        $this->prenomContact = $prenomContact;

        return $this;
    }

    /**
     * Get prenomContact
     *
     * @return string 
     */
    public function getPrenomContact()
    {
        return $this->prenomContact;
    }

    /**
     * Set fonctionContact
     *
     * @param string $fonctionContact
     * @return Client
     */
    public function setFonctionContact($fonctionContact)
    {
        $this->fonctionContact = $fonctionContact;

        return $this;
    }

    /**
     * Get fonctionContact
     *
     * @return string 
     */
    public function getFonctionContact()
    {
        return $this->fonctionContact;
    }

    /**
     * Set civiliteContact
     *
     * @param string $civiliteContact
     * @return Client
     */
    public function setCiviliteContact($civiliteContact)
    {
        $this->civiliteContact = $civiliteContact;

        return $this;
    }

    /**
     * Get civiliteContact
     *
     * @return string 
     */
    public function getCiviliteContact()
    {
        return $this->civiliteContact;
    }

    public function getCiviliteContactBienEcrit()
    {
		if($this->getCiviliteContact() == 'm'){
			return 'M. ';
		}
		if($this->getCiviliteContact() == 'mme'){
			return 'Mme. ';
		}
        return $this->civiliteContact;
    }
}
