<?php

namespace App\Tests\Controller;

use App\Tests\logIn;
use App\Tests\ObtainUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    use logIn;
    use ObtainUser;

    public function testAccessToLoginPage()
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertResponseStatusCodeSame(200);
    }

    public function testRedirectionAfterLogout()
    {
        $client = static::createClient();

        $kernel = self::bootKernel();
        $user = $this->obtainUser($kernel, ['email' => 'admin@mail.fr']);
        $this->logIn($client, $kernel, $user);

        $client->request('GET', '/logout');

        $this->assertResponseStatusCodeSame(302);
    }
}