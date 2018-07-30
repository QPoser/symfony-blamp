<?php

namespace App\Entity\Category;

use App\Entity\Company\Company;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Company\CategoryRepository")
 */
class Category
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
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Company\Company", inversedBy="categories", cascade={"persist"})
     * @ORM\OrderBy({"assessment" = "ASC"})
     */
    private $companies;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category\Category", inversedBy="childrenCategories")
     * @ORM\JoinColumn(name="parent_category_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parentCategory;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Category\Category", mappedBy="parentCategory", orphanRemoval=true, cascade={"persist"})
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $childrenCategories;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $companiesCounter;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $path;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $level;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    public $num;

    public function __construct() {
        $this->companies = new ArrayCollection();
        $this->childrenCategories = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function getId(): ?int
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

    public function getCompaniesCounter(): ?int
    {
        return $this->companiesCounter;
    }

    public function setCompaniesCounter(?int $companiesCounter): self
    {
        $this->companiesCounter = $companiesCounter;

        return $this;
    }

    public function companyCalculate()
    {
        if ($this->getChildrenCategories()) {
            foreach ($this->getChildrenCategories() as $subcategory) {
                if ($subcategory->getChildrenCategories()) {
                    $subcategory->companyCalculate();
                }
            }
        }
        $this->setCompaniesCounter(count($this->getCompanies()));
        if ($this->getParentCategory()) {
            foreach ($this->getCompanies() as $company) {
                $this->getParentCategory()->addCompany($company);
            }
        }
    }

    public function updateCompanyCounterOnAdd(Company $company)
    {
        $this->setCompaniesCounter(count($this->getCompanies()));
        if ($this->getParentCategory()) {
            $company->addCategory($this->getParentCategory());
        }
    }

    public function updateCompanyCounterOnRemove(Company $company)
    {
        $this->setCompaniesCounter(count($this->getCompanies()));
        if ($this->getParentCategory()) {
            $company->removeCategory($this->getParentCategory());
        }
    }


    public function getParentCategory(): ?self
    {
        return $this->parentCategory;
    }

    public function setParentCategory(?self $parentCategory): self
    {
        $this->parentCategory = $parentCategory;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getChildrenCategories(): Collection
    {
        return $this->childrenCategories;
    }

    public function addChildrenCategory(Category $childrenCategory): self
    {
        if (!$this->childrenCategories->contains($childrenCategory)) {
            $this->childrenCategories[] = $childrenCategory;
            $childrenCategory->setParentCategory($this);
        }

        return $this;
    }

    public function removeChildrenCategory(Category $childrenCategory): self
    {
        if ($this->childrenCategories->contains($childrenCategory)) {
            $this->childrenCategories->removeElement($childrenCategory);
            // set the owning side to null (unless already changed)
            if ($childrenCategory->getParentCategory() === $this) {
                $childrenCategory->setParentCategory(null);
            }
        }

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
            $this->updateCompanyCounterOnAdd($company);
        }
        return $this;
    }

    public function removeCompany(Company $company): self
    {
        if ($this->companies->contains($company)) {
            $this->companies->removeElement($company);
            $this->updateCompanyCounterOnRemove($company);
        }

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function generatePath()
    {
        $tab = '';
        for ($i = 0; $i < $this->getLevel(); $i++) {
            $tab = $tab . " •  •  •";
        }
        $this->setPath($tab . '└── ' . $this->getName());
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(?int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getNum(): ?int
    {
        return $this->num;
    }

    public function setNum(?int $num): self
    {
        $this->num = $num;

        return $this;
    }
}
