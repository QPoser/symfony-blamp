<?php

namespace App\Entity\Advert;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Advert\LogBannerRepository")
 */
class LogBanner
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="bannerLogs")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Advert\Banner", inversedBy="logs")
     * @ORM\JoinColumn(name="banner_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $banner;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Ip
     */
    private $ip;

    /**
     * @ORM\Column(type="datetime")
     */
    private $seenAt;

    public function getId()
    {
        return $this->id;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getSeenAt(): ?\DateTimeInterface
    {
        return $this->seenAt;
    }

    public function setSeenAt(\DateTimeInterface $seenAt): self
    {
        $this->seenAt = $seenAt;

        return $this;
    }

    public function getBanner(): ?Banner
    {
        return $this->banner;
    }

    public function setBanner(?Banner $banner): self
    {
        $this->banner = $banner;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
