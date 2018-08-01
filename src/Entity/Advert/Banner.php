<?php

namespace App\Entity\Advert;

use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    const FORMAT_VERTICAL = 'vertical';
    const FORMAT_HORIZONTAL = 'horizontal';

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
     *    message = "Ссылка '{{ value }}' не является валидной ссылкой",
     * )
     * @Assert\Length(
     *      max=255,
     *      min=8,
     *      minMessage="Длина сайта не может быть меньше 8 символов",
     *      maxMessage="Длина сайта не может быть больше 250 символов",
     * )
     */
    private $src;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Image(
     *     maxWidth="240",
     *     groups={"vertical"},
     *     maxWidthMessage="Ширина изображения вертикального баннера не может быть больше 240px"
     * )
     * @Assert\Image(
     *     minWidth="1000",
     *     maxHeight="300",
     *     groups={"horizontal"},
     *     maxHeightMessage="Высота изображения горизонтального баннера не может быть больше 300px",
     *     minWidthMessage="Ширина изображения горизонтального баннера не может быть больше 1000px"
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
     * @ORM\Column(type="string")
     * @Assert\Choice(choices={"vertical", "horizontal"}, message="Формат баннера может быть только вертикальным или горизонтальным")
     */
    private $format;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Advert\LogBanner", mappedBy="banner")
     */
    private $logs;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="banners")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    public function __construct()
    {
        $this->logs = new ArrayCollection();
    }

    public static function formatsList()
    {
        return [
            'Вертикальный' => self::FORMAT_VERTICAL,
            'Горизонтальный' => self::FORMAT_HORIZONTAL,
        ];
    }

    public function isVertical()
    {
        return $this->format == self::FORMAT_VERTICAL;
    }

    public function isHorizontal()
    {
        return $this->format == self::FORMAT_HORIZONTAL;
    }

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

    public function getNormalStatus(): ?string
    {
        switch($this->status) {
            case self::STATUS_ACTIVE:
                return 'Активен';
                break;
            case self::STATUS_WAIT:
                return 'В ожидании';
                break;
            case self::STATUS_READY_TO_PAY:
                return 'Готов к оплате';
                break;
            case self::STATUS_REJECTED:
                return 'Отклонен';
                break;
        }

        return 'Недоступно';
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

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @return Collection|LogBanner[]
     */
    public function getLogs(): Collection
    {
        return $this->logs;
    }

    public function addLog(LogBanner $log): self
    {
        if (!$this->logs->contains($log)) {
            $this->logs[] = $log;
            $log->setBanner($this);
        }

        return $this;
    }

    public function removeLog(LogBanner $log): self
    {
        if ($this->logs->contains($log)) {
            $this->logs->removeElement($log);
            // set the owning side to null (unless already changed)
            if ($log->getBanner() === $this) {
                $log->setBanner(null);
            }
        }

        return $this;
    }
}
