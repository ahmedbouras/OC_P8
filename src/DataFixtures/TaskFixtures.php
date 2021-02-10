<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;

/**
 * @codeCoverageIgnore
 */
trait TaskFixtures
{
    public function createTask($faker, User $author, int $iterable)
    {
        $task = new Task();
        $task->setTitle($faker->word())
                ->setContent($faker->paragraph())
                ->setUser($author);
        if ($iterable % 2) {
            $task->setIsDone(true);
        }
        return $task;
    }
}