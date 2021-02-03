<?php

namespace App\Tests\Entity;

use App\Tests\ObtainTask;
use App\Tests\ObtainUser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    use ObtainTask;
    use ObtainUser;

    public function errorValidator($valueToValidate)
    {
        $kernel = self::bootKernel();
        return $kernel->getContainer()->get('validator')->validate($valueToValidate);
    }

    public function testValidUser()
    {
        $user = $this->obtainNewUser();
        $this->assertCount(0, $this->errorValidator($user));
    }

    public function testExistingUsername()
    {
        $user = $this->obtainNewUser();
        $user->setUsername('admin');
        $this->assertCount(1, $this->errorValidator($user));
    }

    public function testExistingEmail()
    {
        $user = $this->obtainNewUser();
        $user->setEmail('admin@mail.fr');
        $this->assertCount(1, $this->errorValidator($user));
    }

    public function testInvalidPassword()
    {
        $user = $this->obtainNewUser();
        $user->setPassword('john');
        $this->assertCount(1, $this->errorValidator($user));
    }

    public function testInvalidEmail()
    {
        $user = $this->obtainNewUser();
        $user->setEmail('jdoemail.fr');
        $this->assertCount(1, $this->errorValidator($user));
    }

    public function testAddTask()
    {
        $task = $this->obtainNewTask();
        $user = $this->obtainNewUser();
        $user->addTask($task);

        $this->assertEquals($task->getUser(), $user);
    }

    public function testRemoveTask()
    {
        $task = $this->obtainNewTask();
        $user = $this->obtainNewUser();
        $user->addTask($task);
        $user->removeTask($task);

        $this->assertEquals([], $user->getTasks()->toArray());
    }
}