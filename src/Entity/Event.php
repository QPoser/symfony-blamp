<?php

namespace App\Entity;

use App\Entity\Company\Company;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 */
class Event
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="events")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $eventMessage;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="sendEvent")
     * @ORM\JoinColumn(name="sender_id", referencedColumnName="id", nullable=true)
     */
    private $senderUser;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company\Company", inversedBy="sendCompanies", )
     * @ORM\JoinColumn(name="company_company_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private $senderCompany;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isCompanySender;

    /**
     * @ORM\Column(name="date_upload", type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isSeen;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUser(): ?int
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getEventMessage(): ?string
    {
        return $this->eventMessage;
    }

    public function setEventMessage(string $eventMessage): self
    {
        $this->eventMessage = $eventMessage;

        return $this;
    }

    public function getIsCompanySender(): ?bool
    {
        return $this->isCompanySender;
    }

    public function setIsCompanySender(bool $isCompanySender): self
    {
        $this->isCompanySender = $isCompanySender;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getIsSeen(): ?bool
    {
        return $this->isSeen;
    }

    public function setIsSeen(bool $isSeen): self
    {
        $this->isSeen = $isSeen;

        return $this;
    }

    public function getSenderUser(): ?User
    {
        return $this->senderUser;
    }

    public function setSenderUser(?User $senderUser): self
    {
        $this->senderUser = $senderUser;

        return $this;
    }

    public function getSenderCompany(): ?Company
    {
        return $this->senderCompany;
    }

    public function setSenderCompany(?Company $senderCompany): self
    {
        $this->senderCompany = $senderCompany;

        return $this;
    }
}
