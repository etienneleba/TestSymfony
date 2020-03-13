<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 * @coversNothing
 */
class PageControllerTest extends WebTestCase
{
    public function testHelloPage()
    {
        $client = static::createClient();
        $client->request('GET', '/hello');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testH1HelloPage()
    {
        $client = static::createClient();
        $client->request('GET', '/hello');
        $this->assertSelectorTextContains('h1', 'Bienvenue');
    }

    public function testAuthPageIsRestricted()
    {
        $client = static::createClient();
        $client->request('GET', '/auth');
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testRedirectionToLogin()
    {
        $client = static::createClient();
        $client->request('GET', '/auth');
        $this->assertResponseRedirects('/login');
    }
}
