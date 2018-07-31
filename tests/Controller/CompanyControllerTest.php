<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class CompanyControllerTest extends WebTestCase
{
    public function testCompanies()
    {
        $client = static::createClient();

        $client->request('GET', '/company');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testCompanyShow()
    {
        $client = static::createClient();

        // Request on verified company

        $client->request('GET', '/company/1');

        $this->assertSame(200, $client->getResponse()->getStatusCode());

        // Request on not verified company

        $client->request('GET', '/company/2');

        $this->assertSame(302, $client->getResponse()->getStatusCode());
    }
}
