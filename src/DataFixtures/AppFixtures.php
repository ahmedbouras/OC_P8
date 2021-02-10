<?php

namespace App\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    use UserFixtures;
    use TaskFixtures;
    
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 5; $i++) {
            $author = $this->createAuthor($i);
            $manager->persist($author);

            for ($j = 0; $j < 2; $j++) {
                $task = $this->createTask($faker, $author, $j);
                $manager->persist($task);
            }
        }
        $manager->flush();
    }
}
