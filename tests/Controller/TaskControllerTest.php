<?php

namespace App\Tests\Controller;

use App\Tests\logIn;
use App\Tests\ObtainTask;
use App\Tests\ObtainUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    use logIn;
    use ObtainTask;
    use ObtainUser;

    public function testListToDo()
    {
        $client = static::createClient();
        $client->request('GET', '/tasks');

        $this->assertResponseStatusCodeSame(200);
    }

    public function testListDone()
    {
        $client = static::createClient();
        $client->request('GET', '/tasks/1');

        $this->assertResponseStatusCodeSame(200);
    }

    public function testAccessToCreateTaskAsVisitor()
    {
        $client = static::createClient();
        
        $client->request('GET', '/tasks/create');

        $this->assertResponseRedirects('/login');
    }

    public function testAccessToCreateTaskAsUser()
    {
        $client = static::createClient();
        
        $kernel = self::bootKernel();
        $user = $this->obtainUser($kernel, ['email' => 'user2@mail.fr']);

        $client = $this->logIn($client, $kernel, $user);
        $client->request('GET', '/tasks/create');

        $this->assertResponseStatusCodeSame(200);
    }

    public function testCreateTask()
    {
        $client = static::createClient();
        
        $kernel = self::bootKernel();
        $user = $this->obtainUser($kernel, ['email' => 'user2@mail.fr']);

        $client = $this->logIn($client, $kernel, $user);
        $crawler = $client->request('GET', '/tasks/create');

        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]'    => 'Test',
            'task[content]' => 'Tâche de test',
        ]);
        $client->submit($form);

        $this->assertResponseRedirects('/tasks');
    }

    public function testAccessToEditTaskWithoutBeingOwner()
    {
        $client = static::createClient();
        
        $kernel = self::bootKernel();
        $user = $this->obtainUser($kernel, ['email' => 'user2@mail.fr']);
        $task = $this->obtainTask($kernel, ['user' => 80]);

        $client = $this->logIn($client, $kernel, $user);
        $client->request('GET', sprintf('/tasks/%s/edit', $task->getId()));

        $this->assertResponseStatusCodeSame(302);
    }

    public function testAccessToEditTaskBeingOwner()
    {
        $client = static::createClient();
        
        $kernel = self::bootKernel();
        $user = $this->obtainUser($kernel, ['email' => 'user2@mail.fr']);
        $task = $this->obtainTask($kernel, ['user' => $user]);

        $client = $this->logIn($client, $kernel, $user);
        $client->request('GET', sprintf('/tasks/%s/edit', $task->getId()));

        $this->assertResponseStatusCodeSame(200);
    }

    public function testEditTask()
    {
        $client = static::createClient();
        
        $kernel = self::bootKernel();
        $user = $this->obtainUser($kernel, ['email' => 'user2@mail.fr']);
        $task = $this->obtainTask($kernel, ['user' => $user]);

        $client = $this->logIn($client, $kernel, $user);
        $crawler = $client->request('GET', sprintf('/tasks/%s/edit', $task->getId()));

        $form = $crawler->selectButton('Modifier')->form([
            'task[title]'    => 'Modification',
            'task[content]' => 'Modification de la tâche.',
        ]);
        $client->submit($form);

        $this->assertResponseRedirects('/tasks');
    }

    public function testToggleTask()
    {
        $client = static::createClient();
        
        $kernel = self::bootKernel();
        $user = $this->obtainUser($kernel, ['email' => 'user2@mail.fr']);
        $task = $this->obtainTask($kernel, ['user' => $user]);

        $client = $this->logIn($client, $kernel, $user);
        $client->request('GET', sprintf('/tasks/%s/toggle', $task->getId()));

        $this->assertResponseRedirects('/tasks');
    }

    public function testDeleteTaskWithoutBeingOwner()
    {
        $client = static::createClient();
        
        $kernel = self::bootKernel();
        $user = $this->obtainUser($kernel, ['email' => 'user2@mail.fr']);
        $admin = $this->obtainUser($kernel, ['email' => 'admin@mail.fr']);
        $task = $this->obtainTask($kernel, ['user' => $admin]);

        $client = $this->logIn($client, $kernel, $user);
        $client->request('GET', sprintf('/tasks/%s/delete', $task->getId()));

        $client->followRedirect();
        $this->assertSelectorExists('.alert-danger');
    }

    public function testDeleteTaskBeingOwner()
    {
        $client = static::createClient();
        
        $kernel = self::bootKernel();
        $user = $this->obtainUser($kernel, ['email' => 'user2@mail.fr']);
        $task = $this->obtainTask($kernel, ['user' => $user]);

        $client = $this->logIn($client, $kernel, $user);
        $client->request('GET', sprintf('/tasks/%s/delete', $task->getId()));

        $client->followRedirect();
        $this->assertSelectorExists('.alert-success');
    }
}