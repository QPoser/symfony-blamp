<?php

namespace App\Entity;

use App\Entity\Company\Company;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Company\TagRepository")
 * @UniqueEntity("name")
 */
class Tag
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Company\Company", inversedBy="categories", cascade={"persist"})
     * @ORM\OrderBy({"assessment" = "ASC"})
     */
    private $companies;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    public $companyCounter;

    public function __construct()
    {
        $this->companies = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Company[]
     */
    public function getCompanies(): Collection
    {
        return $this->companies;
    }

    public function addCompany(Company $company): self
    {
        if (!$this->companies->contains($company)) {
            $this->companies[] = $company;
        }

        return $this;
    }

    public function removeCompany(Company $company): self
    {
        if ($this->companies->contains($company)) {
            $this->companies->removeElement($company);
        }

        return $this;
    }

    public function getCompanyCounter(): ?int
    {
        return $this->companyCounter;
    }

    public function setCompanyCounter(?int $companyCounter): self
    {
        $this->companyCounter = $companyCounter;

        return $this;
    }

    public function updateCompanyCounter()
    {
        $this->companyCounter = count($this->getCompanies());
    }
}
