<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\logIn;
use App\Tests\ObtainUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    use logIn;
    use ObtainUser;

    public function testHome()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseStatusCodeSame(200);
    }

    public function testCreateTaskWithoutBeingLogged()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $client->clickLink('Créer une nouvelle tâche');

        $this->assertResponseRedirects('/login');
    }

    public function testCreateTaskBeingLogged()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $kernel = self::bootKernel();
        $user = $this->obtainUser($kernel, ['email' => 'user2@mail.fr']);

        $client = $this->logIn($client, $kernel, $user);

        $client->clickLink('Créer une nouvelle tâche');

        $this->assertResponseStatusCodeSame(200);
    }

    public function testCreateUserWithoutBeingLogged()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $client->clickLink('Créer un utilisateur');

        $this->assertResponseRedirects('/login');
    }

    public function testCreateUserLoggedAsUserIsForbidden()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $kernel = self::bootKernel();
        $user = $this->obtainUser($kernel, ['email' => 'user2@mail.fr']);

        $client = $this->logIn($client, $kernel, $user);

        $client->clickLink('Créer un utilisateur');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testCreateUserLoggedAsAdmin()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $kernel = self::bootKernel();
        $user = $this->obtainUser($kernel, ['email' => 'admin@mail.fr']);

        $client = $this->logIn($client, $kernel, $user);

        $client->clickLink('Créer un utilisateur');

        $this->assertResponseStatusCodeSame(200);
    }
}
