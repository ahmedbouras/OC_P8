<?php

namespace App\Tests;

use App\Entity\Task;
use Symfony\Component\HttpKernel\KernelInterface;

trait ObtainTask
{
    public function obtainTask(KernelInterface $kernel, array $criteria)
    {
        $em = $kernel->getContainer()->get('doctrine')->getManager();
        return $em->getRepository(Task::class)->findOneBy($criteria);
    }
}