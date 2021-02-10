<?php

namespace App\Tests\Entity;

use App\Tests\ObtainTask;
use App\Tests\ObtainUser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskTest extends KernelTestCase
{
    use ObtainTask;
    use ObtainUser;

    public function errorValidator($valueToValidate)
    {
        $kernel = self::bootKernel();
        return $kernel->getContainer()->get('validator')->validate($valueToValidate);
    }

    public function testValidTask()
    {
        $task = $this->obtainNewTask();
        $this->assertCount(0, $this->errorValidator($task));
    }

    public function testBlankTitle()
    {
        $task = $this->obtainNewTask();
        $task->setTitle('');
        $this->assertCount(1, $this->errorValidator($task));
    }

    public function testBlankContent()
    {
        $task = $this->obtainNewTask();
        $task->setContent('');
        $this->assertCount(1, $this->errorValidator($task));
    }

    public function testToggle()
    {
        $task = $this->obtainNewTask();
        $task->toggle(1);
        $this->assertEquals(1, $task->getIsDone());
        $task->setIsDone(0);
        $this->assertEquals(0, $task->getIsDone());
    }

    public function testLinkTaskToAuthor()
    {
        $task = $this->obtainNewTask();
        $user = $this->obtainNewUser();
        
        $task->setUser($user);
        $this->assertEquals($task->getUser(), $user);
    }
}