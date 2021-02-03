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

    public function testAccessCreateUserAsVisitor()
    {
        $client = static::createClient();
        $client->request('GET', '/users/create');

        $this->assertResponseStatusCodeSame(302);
    }

    public function testAccessCreateUserAsUser()
    {
        $client = static::createClient();
        
        $kernel = self::bootKernel();
        $user = $this->obtainUser($kernel, ['email' => 'user2@mail.fr']);
        $this->logIn($client, $kernel, $user);

        $client->request('GET', '/users/create');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testAccessCreateUserAsAdmin()
    {
        $client = static::createClient();

        $kernel = self::bootKernel();
        $user = $this->obtainUser($kernel, ['email' => 'admin@mail.fr']);
        $this->logIn($client, $kernel, $user);

        $client->request('GET', '/users/create');

        $this->assertResponseStatusCodeSame(200);
    }

    public function testCreateUser()
    {
        $client = static::createClient();

        $kernel = self::bootKernel();
        $user = $this->obtainUser($kernel, ['email' => 'admin@mail.fr']);
        $this->logIn($client, $kernel, $user);

        $crawler = $client->request('GET', '/users/create');

        $form = $crawler->selectButton('Ajouter')->form([
            'user[username]'    => 'userTest',
            'user[password][first]' => 'password',
            'user[password][second]' => 'password',
            'user[email]' => 'userTest@mail.fr',
        ]);
        $client->submit($form);

        $this->assertResponseRedirects('/users');
    }

    public function testAccessEditUserAsVisitor()
    {
        $client = static::createClient();

        $kernel = self::bootKernel();
        $user = $this->obtainUser($kernel, ['email' => 'user2@mail.fr']);
        $client->request('GET', sprintf('/users/%s/edit', $user->getId()));

        $this->assertResponseStatusCodeSame(302);
    }

    public function testAccessEditUserAsUser()
    {
        $client = static::createClient();
        
        $kernel = self::bootKernel();
        $user = $this->obtainUser($kernel, ['email' => 'user2@mail.fr']);
        $this->logIn($client, $kernel, $user);

        $client->request('GET', sprintf('/users/%s/edit', $user->getId()));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testAccessEditUserAsAdmin()
    {
        $client = static::createClient();

        $kernel = self::bootKernel();
        $admin = $this->obtainUser($kernel, ['email' => 'admin@mail.fr']);
        $user = $this->obtainUser($kernel, ['email' => 'user2@mail.fr']);
        $this->logIn($client, $kernel, $admin);

        $client->request('GET', sprintf('/users/%s/edit', $user->getId()));

        $this->assertResponseStatusCodeSame(200);
    }

    public function testEditUser()
    {
        $client = static::createClient();

        $kernel = self::bootKernel();
        $admin = $this->obtainUser($kernel, ['email' => 'admin@mail.fr']);
        $anonym = $this->obtainUser($kernel, ['email' => 'anonym@mail.fr']);
        $this->logIn($client, $kernel, $admin);

        $crawler = $client->request('GET', sprintf('/users/%s/edit', $anonym->getId()));

        $form = $crawler->selectButton('Modifier')->form([
            'user[username]'    => 'anonym',
            'user[password][first]' => '12345',
            'user[password][second]' => '12345',
            'user[email]' => 'anonym@mail.fr',
        ]);
        $client->submit($form);

        $this->assertResponseRedirects('/users');
    }
}