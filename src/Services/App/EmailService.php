<?php
/**
 * Created by PhpStorm.
 * User: qposer
 * Date: 29.07.18
 * Time: 9:43
 */

namespace App\Services\App;


use Swift_Mailer;
use Twig_Environment;

class EmailService
{

    /**
     * @var Twig_Environment
     */
    private $twig;
    /**
     * @var Swift_Mailer
     */
    private $mailer;
    private $mailTo;

    public function __construct(Twig_Environment $twig, Swift_Mailer $mailer, $mailTo = 'andrey.andrey3433@ya.ru')
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->mailTo = $mailTo;
    }

    public function sendSimpleMessage($subject, string $text, string $mailTo = null)
    {
        $html = $this->twig->render('email/empty.html.twig', compact('text'));

        $message = (new \Swift_Message($subject))
            ->setFrom('andrey.andrey3433@ya.ru')
            ->setTo($mailTo ?: $this->mailTo)
            ->setBody($html, 'text/html');

        $this->mailer->send($message);
        return true;
    }

    public function sendMail(string $template, string $subject, array $parameters, string $mailTo = null)
    {
        $html = $this->twig->render($template, $parameters);

        $message = (new \Swift_Message($subject))
            ->setFrom('andrey.andrey3433@ya.ru')
            ->setTo($mailTo ?: $this->mailTo)
            ->setBody($html, 'text/html');

        $this->mailer->send($message);
        return true;
    }

}