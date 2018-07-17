<?php
/**
 * Created by PhpStorm.
 * User: qposer
 * Date: 16.07.18
 * Time: 18:12
 */

namespace App\Entity;

use App\Entity\Review;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @UniqueEntity(fields="email", message="Email already taken")
 * @UniqueEntity(fields="username", message="Username already taken")
 */
class User implements UserInterface, \Serializable
{
    const ROLE_USER = 'ROLE_USER';
    const ROLE_BUSINESS = 'ROLE_BUSINESS';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     */
    private $emailToken;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     */
    private $passwordResetToken;

    /**
     * @ORM\Column(type="array")
     */
    private $roles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Review", mappedBy="user")
     */
    private $reviews;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Like", mappedBy="user")
     */
    private $likes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ReviewComment", mappedBy="user")
     */
    private $comments;

    /**
     * Many Users have many Users.
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="subscribers")
     * @ORM\JoinTable(name="subs",
     *      joinColumns={@ORM\JoinColumn(name="subs_user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="subscription_user_id", referencedColumnName="id")}
     *      )
     */
    private $subscriptions;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="subscriptions")
     */
    private $subscribers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ReviewPhoto", mappedBy="uploadedBy")
     */
    private $uploads;



    public function __construct()
    {
        $this->roles = $this->getRoles();
        $this->reviews = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->subscribers = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
        $this->uploads = new ArrayCollection();
    }

    // Password Reset

    public function setResetPasswordToken()
    {
        $this->passwordResetToken = uniqid();
    }

    public function resetPassword($newPassword)
    {
        $this->password = $newPassword;
        $this->passwordResetToken = null;
    }

    // Email Token

    public function verify()
    {
        $this->setEmailToken(null);
    }

    public function isActive()
    {
        return $this->emailToken == null;
    }

    public function getRoles()
    {
        return [
            self::ROLE_USER,
            self::ROLE_BUSINESS,
            self::ROLE_ADMIN,
        ];
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function eraseCredentials()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->email,
            $this->password,
        ]);
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->email,
            $this->password
        ) = unserialize($serialized, ['allowed_classes' => false]);
    }

    public function getEmailToken(): ?string
    {
        return $this->emailToken;
    }

    public function setEmailToken(?string $emailToken): self
    {
        $this->emailToken = $emailToken;

        return $this;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return Collection|Review[]
     */
    public function getReview(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setUser($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->contains($review)) {
            $this->reviews->removeElement($review);
            // set the owning side to null (unless already changed)
            if ($review->getUser() === $this) {
                $review->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Like[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setUser($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->contains($like)) {
            $this->likes->removeElement($like);
            // set the owning side to null (unless already changed)
            if ($like->getUser() === $this) {
                $like->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ReviewComment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(ReviewComment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(ReviewComment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|\App\Entity\Review[]
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    /**
     * @return Collection|User[]
     */
    public function getSubscribers(): Collection
    {
        return $this->subscribers;
    }

    public function addSubscriber(User $subscriber): self
    {
        if (!$this->subscribers->contains($subscriber)) {
            $this->subscribers[] = $subscriber;
            $subscriber->addSubscription($this);
        }

        return $this;
    }

    public function removeSubscriber(User $subscriber): self
    {
        if ($this->subscribers->contains($subscriber)) {
            $this->subscribers->removeElement($subscriber);
            $subscriber->removeSubscription($this);
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function addSubscription(User $subscription): self
    {
        if (!$this->subscriptions->contains($subscription)) {
            $this->subscriptions[] = $subscription;
        }

        return $this;
    }

    public function removeSubscription(User $subscription): self
    {
        if ($this->subscriptions->contains($subscription)) {
            $this->subscriptions->removeElement($subscription);
        }

        return $this;
    }

    /**
     * @return Collection|ReviewPhoto[]
     */
    public function getUploads(): Collection
    {
        return $this->uploads;
    }

    public function addUpload(ReviewPhoto $upload): self
    {
        if (!$this->uploads->contains($upload)) {
            $this->uploads[] = $upload;
            $upload->setUploadedBy($this);
        }

        return $this;
    }

    public function removeUpload(ReviewPhoto $upload): self
    {
        if ($this->uploads->contains($upload)) {
            $this->uploads->removeElement($upload);
            // set the owning side to null (unless already changed)
            if ($upload->getUploadedBy() === $this) {
                $upload->setUploadedBy(null);
            }
        }

        return $this;
    }
}