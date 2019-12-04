<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EvenementRepository")
 */
class Evenement
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $photo;

    /**
     * @ORM\Column(type="boolean")
     */
    private $disponible;

    /**
     * @ORM\Column(type="integer")
     */
    private $nombrePlaces;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Categorie", inversedBy="evenements")
     */
    private $categorie;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="decimal", precision=8, scale=2, nullable=true)
     */
    private $prix;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Place", mappedBy="evenement")
     */
    private $places;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PanierPlace", mappedBy="evenement")
     */
    private $panierPlaces;

    public function __construct()
    {
        $this->places = new ArrayCollection();
        $this->panierPlaces = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getDisponible(): ?bool
    {
        return $this->disponible;
    }

    public function setDisponible(bool $disponible): self
    {
        $this->disponible = $disponible;

        return $this;
    }

    public function getNombrePlaces(): ?int
    {
        return $this->nombrePlaces;
    }

    public function setNombrePlaces(int $nombrePlaces): self
    {
        $this->nombrePlaces = $nombrePlaces;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function __toString()
    {
        return $this->nom . " - " . $this->prix . " € - " . $this->categorie;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(?string $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * @return Collection|Place[]
     */
    public function getPlaces(): Collection
    {
        return $this->places;
    }

    public function addPlace(Place $place): self
    {
        if (!$this->places->contains($place)) {
            $this->places[] = $place;
            $place->setEvenement($this);
        }

        return $this;
    }

    public function removePlace(Place $place): self
    {
        if ($this->places->contains($place)) {
            $this->places->removeElement($place);
            // set the owning side to null (unless already changed)
            if ($place->getEvenement() === $this) {
                $place->setEvenement(null);
            }
        }

        return $this;
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
            $panierPlace->setEvenement($this);
        }

        return $this;
    }

    public function removePanierPlace(PanierPlace $panierPlace): self
    {
        if ($this->panierPlaces->contains($panierPlace)) {
            $this->panierPlaces->removeElement($panierPlace);
            // set the owning side to null (unless already changed)
            if ($panierPlace->getEvenement() === $this) {
                $panierPlace->setEvenement(null);
            }
        }

        return $this;
    }
}
