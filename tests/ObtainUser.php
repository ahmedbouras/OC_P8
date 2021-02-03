<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Component\HttpKernel\KernelInterface;

trait ObtainUser
{
    public function obtainUser(KernelInterface $kernel, array $criteria)
    {
        $em = $kernel->getContainer()->get('doctrine')->getManager();
        return $em->getRepository(User::class)->findOneBy($criteria);
    }

    public function obtainNewUser()
    {
        $user = new User();
        $user->setUsername('john')
             ->setPassword('johnpassword')
             ->setEmail('jdoe@mail.fr');
        return $user;
    }
}