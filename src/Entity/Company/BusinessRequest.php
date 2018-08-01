<?php

namespace App\Entity\Company;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Company\BusinessRequestRepository")
 */
class BusinessRequest
{

    public const STATUS_WAIT = 'wait';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_REJECTED = 'rejected';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="businessRequests")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company\Company", inversedBy="businessRequests")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $company;

    /**
     * @ORM\Column(type="string", length=16)
     * @Assert\Regex(
     *     pattern = "^((\+7|7|8)+([0-9]){10})^",
     *     message = "Телефон '{{ value }}' не валиден. Валидный формат телефона - +79876543210."
     * )
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="Длина примечания не может быть больше 250 символов.")
     */
    private $note;

    public function isSuccess()
    {
        return $this->status == self::STATUS_SUCCESS;
    }

    public function isWait()
    {
        return $this->status == self::STATUS_WAIT;
    }

    public function isRejected()
    {
        return $this->status == self::STATUS_REJECTED;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getNormalStatus(): ?string
    {
        switch($this->status) {
            case self::STATUS_SUCCESS:
                return 'Принят';
                break;
            case self::STATUS_WAIT:
                return 'В ожидании';
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
}
