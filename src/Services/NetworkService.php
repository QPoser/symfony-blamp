<?php
/**
 * Created by PhpStorm.
 * User: qposer
 * Date: 22.07.18
 * Time: 22:11
 */

namespace App\Services;


use App\Controller\NetworkController;
use App\Entity\Review\Network;
use App\Entity\User;
use App\Repository\NetworkRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Client;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class NetworkService
{
    /**
     * @var EntityManager
     */
    private $manager;
    /**
     * @var TokenStorage
     */
    private $storage;
    /**
     * @var UserRepository
     */
    private $users;
    /**
     * @var NetworkRepository
     */
    private $networks;

    public function __construct(EntityManager $manager, TokenStorage $storage, UserRepository $users, NetworkRepository $networks)
    {
        $this->manager = $manager;
        $this->storage = $storage;
        $this->users = $users;
        $this->networks = $networks;
    }

    /**
     * @param string $code
     * @return Network
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getNetworkVkByCode(string $code, $safe = false)
    {
        $identity = $this->getIdentityVkByCode($code);

        if ($safe) {
            if ($findedUser = $this->users->findUserByNetworkIdentity($identity)) {
                throw new \DomainException('Пользователь с идентификатором ' . $identity . ' уже есть в системе.');
            }
        }

        if ($network = $this->networks->findByIdentity(Network::NETWORK_VK, $identity)) {
            return $network;
        }

        $network = new Network();
        $network->setIdentity($identity);
        $network->setNetwork(Network::NETWORK_VK);

        /** @var Network $network */
        return $network;
    }

    /**
     * @param User $user
     * @param string $code
     * @return User|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Doctrine\ORM\ORMException
     */
    public function connectVk(User $user, string $code, $safe = false, $add_url = false)
    {
        $identity = $this->getIdentityVkByCode($code, $add_url);

        $network = new Network();
        $network->setIdentity($identity);
        $network->setNetwork(Network::NETWORK_VK);

        if ($safe) {

            $findedUser = $this->users->findUserByNetworkIdentity($identity);

            if ($findedUser) {
                if ($findedUser->getId() != $user->getId()) {
                    throw new \DomainException('Пользователь с идентификатором ' . $identity . ' уже есть в системе.');
                }
                throw new \DomainException('Вы уже подключили данную социальную сеть к своему профилю.');
            }

        }

        $user->addNetwork($network);

        $this->manager->persist($network);
        $this->manager->flush();

        return $user;
    }

    public function connectNetwork(User $user, Network $network)
    {
        $user->addNetwork($network);

        $this->manager->persist($user);
        $this->manager->persist($network);
        $this->manager->flush();

        return $user;
    }

    /**
     * @param string $code
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getIdentityVkByCode(string $code, $add_url = false)
    {
        $client = new Client();
        if ($add_url) {
            $res = $client->request('GET',
                'https://oauth.vk.com/access_token?client_id=' . getenv('VK_CLIENT_ID')
                . '&client_secret=' . getenv('VK_SECRET_KEY')
                . '&redirect_uri=https://localhost:8080/network/add/vk&code=' . $code);
        } else {
            $res = $client->request('GET',
                'https://oauth.vk.com/access_token?client_id=' . getenv('VK_CLIENT_ID')
                . '&client_secret=' . getenv('VK_SECRET_KEY')
                . '&redirect_uri=https://localhost:8080/login/check-vkontakte&code=' . $code);
        }
        $response = json_decode($res->getBody());

        return $response->user_id;
    }
}