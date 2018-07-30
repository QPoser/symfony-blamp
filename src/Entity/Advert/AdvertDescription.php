<?php

namespace App\Entity\Advert;

use App\Entity\Company\Company;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Advert\AdvertDescriptionRepository")
 */
class AdvertDescription
{
    const STATUS_ACTIVE = 'active';
    const STATUS_WAIT = 'wait';
    const STATUS_REJECTED = 'reject';
    const STATUS_READY_TO_PAY = 'readyToPay';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Company\Company", inversedBy="advertDescription")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     */
    private $company;

    /**
     * @ORM\Column(type="string", length=350)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private $status;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $endDate;

    public function isActive()
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    public function isWait()
    {
        return $this->status == self::STATUS_WAIT;
    }

    public function isReadyToPay()
    {
        return $this->status == self::STATUS_READY_TO_PAY;
    }

    public function isRejected()
    {
        return $this->status == self::STATUS_REJECTED;
    }


    public function getId()
    {
        return $this->id;
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }
}
