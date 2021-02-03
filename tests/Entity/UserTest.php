<?php

namespace App\Tests\Entity;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    public function testValidUser()
    {
        $user = new User();
        $user->setUsername('toto')
             ->setPassword('totopassword')
             ->setEmail('toto@mail.fr');
        $kernel = self::bootKernel();
        $errors = $kernel->getContainer()->get('validator')->validate($user);
        $this->assertCount(0, $errors);
    }

    public function testInvalidUserPassword()
    {
        $user = new User();
        $user->setUsername('toto')
             ->setPassword('toto')
             ->setEmail('toto@mail.fr');
        $kernel = self::bootKernel();
        $errors = $kernel->getContainer()->get('validator')->validate($user);
        $this->assertCount(1, $errors);
    }

    public function testInvalidUserUsername()
    {
        $user = new User();
        $user->setUsername('admin')
             ->setPassword('totopassword')
             ->setEmail('toto@mail.fr');
        $kernel = self::bootKernel();
        $errors = $kernel->getContainer()->get('validator')->validate($user);
        $this->assertCount(1, $errors);
    }

    public function testInvalidUserEmail()
    {
        $user = new User();
        $user->setUsername('toto')
             ->setPassword('totopassword')
             ->setEmail('totomail.fr');
        $kernel = self::bootKernel();
        $errors = $kernel->getContainer()->get('validator')->validate($user);
        $this->assertCount(1, $errors);
    }
}