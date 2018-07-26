<?php

namespace App\Entity\Advert;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Advert\BannerRepository")
 */
class Banner
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
     * @ORM\Column(type="string", length=255)
     * @Assert\Url(
     *    protocols={"http", "https"},
     *    message = "The url '{{ value }}' is not a valid url",
     * )
     */
    private $src;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Image(
     *     maxWidth="240",
     *     maxHeight="400",
     * )
     */
    private $bannerImg;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private $status;

    /**
     * @ORM\Column(type="integer")
     */
    private $views;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="banners")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    public function addView()
    {
        $this->views--;
    }

    public function isWait()
    {
        return $this->status === self::STATUS_WAIT;
    }

    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isReadyToPay()
    {
        return $this->status === self::STATUS_READY_TO_PAY;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getSrc(): ?string
    {
        return $this->src;
    }

    public function setSrc(string $src): self
    {
        $this->src = $src;

        return $this;
    }

    public function getBannerImg(): ?string
    {
        return $this->bannerImg;
    }

    public function setBannerImg(string $bannerImg): self
    {
        $this->bannerImg = $bannerImg;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getViews(): ?int
    {
        return $this->views;
    }

    public function setViews(int $views): self
    {
        $this->views = $views;

        return $this;
    }
}
