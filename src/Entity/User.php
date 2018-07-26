<?php
/**
 * Created by PhpStorm.
 * User: qposer
 * Date: 16.07.18
 * Time: 18:12
 */

namespace App\Entity;

use App\Entity\Advert\Banner;
use App\Entity\Company\BusinessRequest;
use App\Entity\Company\Company;
use App\Entity\Review\Like;
use App\Entity\Review\Network;
use App\Entity\Review\Review;
use App\Entity\Review\ReviewComment;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @UniqueEntity(fields="email", message="Email already taken")
 * @UniqueEntity(fields="username", message="Username already taken")
 */
class User implements UserInterface, \Serializable, OAuthAwareUserProviderInterface
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
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $password;

    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
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
     * @ORM\OneToMany(targetEntity="App\Entity\Review\Network", mappedBy="user")
     */
    private $networks;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Review\Review", mappedBy="user")
     */
    private $reviews;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Review\Like", mappedBy="user", cascade={"persist"}, orphanRemoval=true)
     */
    private $likes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Review\ReviewComment", mappedBy="user")
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Event", mappedBy="user")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $events;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Event", mappedBy="senderUser")
     */
    private $sendEvent;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Company\Company", inversedBy="usersFavor")
     * @ORM\JoinTable(name="favorite_companies")
     */
    private $favoriteCompanies;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Company\Company", inversedBy="businessUsers")
     * @ORM\JoinTable(name="business_companies")
     */
    private $businessCompanies;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="subscribers")
     */
    private $subscriptions;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="friendsWithMe")
     * @ORM\JoinTable(name="subscribers",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="subscriber_user_id", referencedColumnName="id")}
     * )
     */
    private $subscribers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Advert\Banner", mappedBy="user")
     */
    private $banners;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Company\BusinessRequest", mappedBy="user")
     */
    private $businessRequests;

    public function __construct()
    {
        $this->roles = $this->getRoles();
        $this->reviews = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->networks = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->sendEvent = new ArrayCollection();
        $this->favoriteCompanies = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
        $this->subscribers = new ArrayCollection();
        $this->businessCompanies = new ArrayCollection();
        $this->businessRequests = new ArrayCollection();
        $this->banners = new ArrayCollection();
    }

    public function getNewEvents()
    {
        $newEvents = [];
        /** @var Event $event */
        foreach ($this->events as $event) {
            if (!$event->getIsSeen()) {
                $newEvents[] = $event;
            }
        }
        return $newEvents;
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

    // Roles

    public function getNormalRoleName()
    {
        if ($this->isAdmin()) {
            return 'Администратор';
        }

        if ($this->isBusiness()) {
            return 'Бизнес-пользователь';
        }

        return 'Пользователь сайта';
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function becomeUser()
    {
        $this->roles = [self::ROLE_USER];
    }

    public function becomeBusiness()
    {
        $this->roles = [self::ROLE_BUSINESS];
    }

    public function becomeAdmin()
    {
        $this->roles = [self::ROLE_ADMIN];
    }

    public function isUser()
    {
        return in_array(self::ROLE_USER, $this->roles) ||
            in_array(self::ROLE_BUSINESS, $this->roles) ||
            in_array(self::ROLE_ADMIN, $this->roles);
    }

    public function isBusiness()
    {
        return in_array(self::ROLE_BUSINESS, $this->roles) ||
            in_array(self::ROLE_ADMIN, $this->roles);
    }

    public function isAdmin()
    {
        return in_array(self::ROLE_ADMIN, $this->roles);
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
        $this->username = str_replace(' ', '', $username);

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
     * @return Collection|\App\Entity\Review\Review[]
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function getPasswordResetToken(): ?string
    {
        return $this->passwordResetToken;
    }

    public function setPasswordResetToken(?string $passwordResetToken): self
    {
        $this->passwordResetToken = $passwordResetToken;

        return $this;
    }

    /**
     * @return Collection|ReviewComment[]
     */
    public function getNetworks(): Collection
    {
        return $this->networks;
    }

    public function getNetworkVk()
    {
        /** @var Network $network */
        foreach ($this->networks as $network) {
            if ($network->getNetwork() == Network::NETWORK_VK) { return $network; }
        }

        return false;
    }

    public function addNetwork(Network $network): self
    {
        if (!$this->networks->contains($network)) {
            $this->networks[] = $network;
            $network->setUser($this);
        }

        return $this;
    }

    public function removeNetwork(Network $network): self
    {
        if ($this->networks->contains($network)) {
            $this->networks->removeElement($network);
            // set the owning side to null (unless already changed)
            if ($network->getUser() === $this) {
                $network->setUser(null);
            }
        }

        return $this;
    }

    /**
     * Loads the user by a given UserResponseInterface object.
     *
     * @param UserResponseInterface $response
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        // TODO: Implement loadUserByOAuthUserResponse() method.
    }

    /**
     * @return Collection|Event[]
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->setUser($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->contains($event)) {
            $this->events->removeElement($event);
            // set the owning side to null (unless already changed)
            if ($event->getUser() === $this) {
                $event->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Event[]
     */
    public function getSendEvent(): Collection
    {
        return $this->sendEvent;
    }

    public function addSendEvent(Event $sendEvent): self
    {
        if (!$this->sendEvent->contains($sendEvent)) {
            $this->sendEvent[] = $sendEvent;
            $sendEvent->setSenderUser($this);
        }

        return $this;
    }

    public function removeSendEvent(Event $sendEvent): self
    {
        if ($this->sendEvent->contains($sendEvent)) {
            $this->sendEvent->removeElement($sendEvent);
            // set the owning side to null (unless already changed)
            if ($sendEvent->getSenderUser() === $this) {
                $sendEvent->setSenderUser(null);
            }
        }

        return $this;
    }


    public function hasInFavoriteCompanies(Company $company)
    {
        return $this->favoriteCompanies->contains($company);
    }

    /**
     * @return Collection|Company[]
     */
    public function getFavoriteCompanies(): Collection
    {
        return $this->favoriteCompanies;
    }

    public function addFavoriteCompany(Company $favoriteCompany): self
    {
        if (!$this->favoriteCompanies->contains($favoriteCompany)) {
            $this->favoriteCompanies[] = $favoriteCompany;
        }

        return $this;
    }

    public function removeFavoriteCompany(Company $favoriteCompany): self
    {
        if ($this->favoriteCompanies->contains($favoriteCompany)) {
            $this->favoriteCompanies->removeElement($favoriteCompany);
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
            $subscription->addSubscriber($this);
        }

        return $this;
    }

    public function removeSubscription(User $subscription): self
    {
        if ($this->subscriptions->contains($subscription)) {
            $this->subscriptions->removeElement($subscription);
            $subscription->removeSubscriber($this);
        }

        return $this;
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
        }

        return $this;
    }

    public function removeSubscriber(User $subscriber): self
    {
        if ($this->subscribers->contains($subscriber)) {
            $this->subscribers->removeElement($subscriber);
        }

        return $this;
    }

    /**
     * @return Collection|Company[]
     */
    public function getBusinessCompanies(): Collection
    {
        return $this->businessCompanies;
    }

    public function addBusinessCompany(Company $businessCompany): self
    {
        if (!$this->businessCompanies->contains($businessCompany)) {
            $this->businessCompanies[] = $businessCompany;
        }

        return $this;
    }

    public function removeBusinessCompany(Company $businessCompany): self
    {
        if ($this->businessCompanies->contains($businessCompany)) {
            $this->businessCompanies->removeElement($businessCompany);
        }

        return $this;
    }

    /**
     * @return Collection|BusinessRequest[]
     */
    public function getBusinessRequests(): Collection
    {
        return $this->businessRequests;
    }

    public function addBusinessRequest(BusinessRequest $businessRequest): self
    {
        if (!$this->businessRequests->contains($businessRequest)) {
            $this->businessRequests[] = $businessRequest;
            $businessRequest->setUser($this);
        }

        return $this;
    }

    public function removeBusinessRequest(BusinessRequest $businessRequest): self
    {
        if ($this->businessRequests->contains($businessRequest)) {
            $this->businessRequests->removeElement($businessRequest);
            // set the owning side to null (unless already changed)
            if ($businessRequest->getUser() === $this) {
                $businessRequest->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Banner[]
     */
    public function getBanners(): Collection
    {
        return $this->banners;
    }

    public function addBanner(Banner $banner): self
    {
        if (!$this->banners->contains($banner)) {
            $this->banners[] = $banner;
            $banner->setUser($this);
        }

        return $this;
    }

    public function removeBanner(Banner $banner): self
    {
        if ($this->banners->contains($banner)) {
            $this->banners->removeElement($banner);
            // set the owning side to null (unless already changed)
            if ($banner->getUser() === $this) {
                $banner->setUser(null);
            }
        }

        return $this;
    }
}