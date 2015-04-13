<?php

namespace Junior\SiteinterneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Junior\SiteinterneBundle\Entity\UserRepository")
 * @ORM\Table(name="user")
 */
class User implements UserInterface
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
   * @ORM\Column(name="username", type="string", length=255, unique=true)
   */
  private $username;

  /**
   * @ORM\Column(name="password", type="string", length=255)
   */
  private $password;

  /**
   * @ORM\Column(name="salt", type="string", length=255)
   */
  private $salt;

  /**
   * @ORM\Column(name="first_name", type="string", length=255)
   */
  private $firstName;

  /**
   * @ORM\Column(name="last_name", type="string", length=255)
   */
  private $lastName;

  /**
   * pourrait permettre d'utiliser l'authentification via Frankiz
   * @ORM\Column(name="hash", type="string", length=255, nullable=true)
   */
  private $hash;

   /**
    * @ORM\Column(name="roles", type="array")
    */
    private $roles = array();

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="num_secu", type="string", length=255,nullable=true)
     */
    private $numSecu;

    /**
     * @var string
     *
     * @ORM\Column(name="centre_secu", type="string", length=255,nullable=true)
     */
    private $centreSecu;

    /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=255,nullable=true)
     */
    private $ville;

    /**
     * @var string
     *
     * @ORM\Column(name="code_banque", type="string", length=255,nullable=true)
     */
    private $codeBanque;

    /**
     * @var string
     *
     * @ORM\Column(name="code_guichet", type="string", length=255,nullable=true)
     */
    private $codeGuichet;

    /**
     * @var string
     *
     * @ORM\Column(name="compte", type="string", length=255,nullable=true)
     */
    private $compte;

    /**
     * @var string
     *
     * @ORM\Column(name="cle", type="string", length=255,nullable=true)
     */
    private $cle;

    /**
     * @var string
     *
     * @ORM\Column(name="domiciliation", type="string", length=255,nullable=true)
     */
    private $domiciliation;

    /**
     * @var string
     *
     * @ORM\Column(name="titulaire", type="string", length=255,nullable=true)
     */
    private $titulaire;

    /**
     * @var string
     *
     * @ORM\Column(name="iban", type="string", length=255,nullable=true)
     */
    private $iban;

    /**
     * @var string
     *
     * @ORM\Column(name="bic", type="string", length=255,nullable=true)
     */
    private $bic;

    /**
     * @var string
     *
     * @ORM\Column(name="nationalite", type="string", length=255,nullable=true)
     */
    private $nationalite;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="promo", type="string", length=255)
     */
    private $promo;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="inscrit", type="boolean", nullable=true)
     */
    private $inscrit;
	
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="inscrit_le", type="datetime", nullable=true)
     */
    private $inscritLe;
	
	
	public function eraseCredentials()
	{
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
     * Set names
     *
     * @param string $names
     * @return User
     */
    public function setNames($names)
    {
        $this->names = $names;

        return $this;
    }

    /**
     * Get names
     *
     * @return string 
     */
    public function getNames()
    {
        return $this->names;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
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
     * Set promo
     *
     * @param string $promo
     * @return User
     */
    public function setPromo($promo)
    {
        $this->promo = $promo;

        return $this;
    }

    /**
     * Get promo
     *
     * @return string 
     */
    public function getPromo()
    {
        return $this->promo;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set roles
     *
     * @param array $roles
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return array 
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set inscrit
     *
     * @param boolean $inscrit
     * @return User
     */
    public function setInscrit($inscrit)
    {
        $this->inscrit = $inscrit;

        return $this;
    }

    /**
     * Get inscrit
     *
     * @return boolean 
     */
    public function getInscrit()
    {
        return $this->inscrit;
    }

    /**
     * Set inscritLe
     *
     * @param \DateTime $inscritLe
     * @return User
     */
    public function setInscritLe($inscritLe)
    {
        $this->inscritLe = $inscritLe;

        return $this;
    }

    /**
     * Get inscritLe
     *
     * @return \DateTime 
     */
    public function getInscritLe()
    {
        return $this->inscritLe;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set hash
     *
     * @param string $hash
     * @return User
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string 
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set numSecu
     *
     * @param string $numSecu
     * @return User
     */
    public function setNumSecu($numSecu)
    {
        $this->numSecu = $numSecu;

        return $this;
    }

    /**
     * Get numSecu
     *
     * @return string 
     */
    public function getNumSecu()
    {
        return $this->numSecu;
    }

    /**
     * Set centreSecu
     *
     * @param string $centreSecu
     * @return User
     */
    public function setCentreSecu($centreSecu)
    {
        $this->centreSecu = $centreSecu;

        return $this;
    }

    /**
     * Get centreSecu
     *
     * @return string 
     */
    public function getCentreSecu()
    {
        return $this->centreSecu;
    }

    /**
     * Set ville
     *
     * @param string $ville
     * @return User
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
     * Set codeBanque
     *
     * @param string $codeBanque
     * @return User
     */
    public function setCodeBanque($codeBanque)
    {
        $this->codeBanque = $codeBanque;

        return $this;
    }

    /**
     * Get codeBanque
     *
     * @return string 
     */
    public function getCodeBanque()
    {
        return $this->codeBanque;
    }

    /**
     * Set codeGuichet
     *
     * @param string $codeGuichet
     * @return User
     */
    public function setCodeGuichet($codeGuichet)
    {
        $this->codeGuichet = $codeGuichet;

        return $this;
    }

    /**
     * Get codeGuichet
     *
     * @return string 
     */
    public function getCodeGuichet()
    {
        return $this->codeGuichet;
    }

    /**
     * Set compte
     *
     * @param string $compte
     * @return User
     */
    public function setCompte($compte)
    {
        $this->compte = $compte;

        return $this;
    }

    /**
     * Get compte
     *
     * @return string 
     */
    public function getCompte()
    {
        return $this->compte;
    }

    /**
     * Set cle
     *
     * @param string $cle
     * @return User
     */
    public function setCle($cle)
    {
        $this->cle = $cle;

        return $this;
    }

    /**
     * Get cle
     *
     * @return string 
     */
    public function getCle()
    {
        return $this->cle;
    }

    /**
     * Set domiciliation
     *
     * @param string $domiciliation
     * @return User
     */
    public function setDomiciliation($domiciliation)
    {
        $this->domiciliation = $domiciliation;

        return $this;
    }

    /**
     * Get domiciliation
     *
     * @return string 
     */
    public function getDomiciliation()
    {
        return $this->domiciliation;
    }

    /**
     * Set titulaire
     *
     * @param string $titulaire
     * @return User
     */
    public function setTitulaire($titulaire)
    {
        $this->titulaire = $titulaire;

        return $this;
    }

    /**
     * Get titulaire
     *
     * @return string 
     */
    public function getTitulaire()
    {
        return $this->titulaire;
    }

    /**
     * Set iban
     *
     * @param string $iban
     * @return User
     */
    public function setIban($iban)
    {
        $this->iban = $iban;

        return $this;
    }

    /**
     * Get iban
     *
     * @return string 
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * Set bic
     *
     * @param string $bic
     * @return User
     */
    public function setBic($bic)
    {
        $this->bic = $bic;

        return $this;
    }

    /**
     * Get bic
     *
     * @return string 
     */
    public function getBic()
    {
        return $this->bic;
    }

    /**
     * Set nationalite
     *
     * @param string $nationalite
     * @return User
     */
    public function setNationalite($nationalite)
    {
        $this->nationalite = $nationalite;

        return $this;
    }

    /**
     * Get nationalite
     *
     * @return string 
     */
    public function getNationalite()
    {
        return $this->nationalite;
    }
}
