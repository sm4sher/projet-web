<?php
// src/Entity/User.php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="app_users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="email", message="cet Email existe déjà")
 * @UniqueEntity(fields="username", message="Nom déjà utilisé")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $password;


    /**
     * @ORM\Column(type="string", length=60, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;


    /**
     * @ORM\Column(name="roles", type="string", length=64)
     */
    private $roles="ROLE_USER";


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $nom = '';

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $ville = '';

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $codePostal = '';

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $adresse = '';

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $tokenMail = '';

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PanierPlace", mappedBy="user")
     */
    private $panierPlaces;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Commande", mappedBy="user")
     */
    private $commandes;

    // /////////////////

    public function __construct()
    {
        $this->isActive = true;
        $this->panierPlaces = new ArrayCollection();
        $this->commandes = new ArrayCollection();
        // may not be needed, see section on salt below
        // $this->salt = md5(uniqid('', true));
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function getPassword()
    {
        return $this->password;
    }


    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
    }


    public function getRoles()
    {
        if ($this->roles)
            return [$this->roles];
        else
            return ['ROLE_USER'];
    }
/////////////////////////////////////////////////////////

    public function setRoles($roles)
    {
        $this->roles = $roles;
        // allows for chaining
        return $this;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }


    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getisActive()
    {
        return $this->isActive;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function setNom($nom)
    {
        $this->nom = $nom;
        return $this;
    }

    public function getVille()
    {
        return $this->ville;
    }


    public function setVille($ville)
    {
        $this->ville = $ville;
        return $this;
    }


    public function getCodePostal()
    {
        return $this->codePostal;
    }


    public function setCodePostal($codePostal)
    {
        $this->codePostal = $codePostal;
        return $this;
    }


    public function getAdresse()
    {
        return $this->adresse;
    }


    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getTokenMail()
    {
        return $this->tokenMail;
    }


    public function setTokenMail($tokenMail)
    {
        $this->tokenMail = $tokenMail;
        return $this;
    }


    public function __toString()
    {
        return "username : ".$this->getUsername()." role: ".$this->getRoles()[0]." mdp:".$this->getPassword()."\n";
    }

    /**
     * @return Collection|PanierPlace[]
     */
    public function getPanierPlaces(): Collection
    {
        return $this->panierPlaces;
    }

    public function addPanierPlace(PanierPlace $panierPlace): self
    {
        if (!$this->panierPlaces->contains($panierPlace)) {
            $this->panierPlaces[] = $panierPlace;
            $panierPlace->setUser($this);
        }

        return $this;
    }

    public function removePanierPlace(PanierPlace $panierPlace): self
    {
        if ($this->panierPlaces->contains($panierPlace)) {
            $this->panierPlaces->removeElement($panierPlace);
            // set the owning side to null (unless already changed)
            if ($panierPlace->getUser() === $this) {
                $panierPlace->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Commande[]
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): self
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes[] = $commande;
            $commande->setUser($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        if ($this->commandes->contains($commande)) {
            $this->commandes->removeElement($commande);
            // set the owning side to null (unless already changed)
            if ($commande->getUser() === $this) {
                $commande->setUser(null);
            }
        }

        return $this;
    }
}