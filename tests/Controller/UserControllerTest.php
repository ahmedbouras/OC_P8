<?php

namespace App\Tests\Controller;

use App\Tests\logIn;
use App\Tests\ObtainUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    use ObtainUser;
    use logIn;

    public function testAccessListAsVisitor()
    {
        $client = static::createClient();
        $client->request('GET', '/users');

        $this->assertResponseStatusCodeSame(302);
    }

    public function testAccessListAsUser()
    {
        $client = static::createClient();
        
        $kernel = self::bootKernel();
        $user = $this->obtainUser($kernel, ['email' => 'user2@mail.fr']);
        $this->logIn($client, $kernel, $user);

        $client->request('GET', '/users');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testAccessListAsAdmin()
    {
        $client = static::createClient();

        $kernel = self::bootKernel();
        $user = $this->obtainUser($kernel, ['email' => 'admin@mail.fr']);
        $this->logIn($client, $kernel, $user);

        $client->request('GET', '/users');

        $this->assertResponseStatusCodeSame(200);
    }
}