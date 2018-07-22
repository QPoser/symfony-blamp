<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Auth\RegisterFormType;
use App\Form\Auth\ResetPasswordForm;
use App\Repository\UserRepository;
use App\Services\AuthService;
use App\Services\NetworkService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class NetworkController extends Controller
{

    /**
     * @var NetworkService
     */
    private $networkService;

    public function __construct(NetworkService $networkService)
    {
        $this->networkService = $networkService;
    }

    /**
     * @Route("/network/add/vk", name="networks.add.vk")
     * @param Request $request
     * @param $code
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addVkNetwork(Request $request)
    {
        $code = $request->get('code');

        if (!$code) {
            $this->addFlash('warning', 'Неизвестная ошибка, попробуйте ещё раз.');
            $this->redirectToRoute('cabinet.profile');
        }

        try {
            $this->networkService->connectVk($this->getUser(), $code, true, true);
        } catch (\DomainException $e) {
            $this->addFlash('notice', $e->getMessage());
            return $this->redirectToRoute('cabinet.profile');
        }

        $this->addFlash('notice', 'Социальная сеть была успешно привязана к вашему профилю.');
        return $this->redirectToRoute('cabinet.profile');

    }

}
