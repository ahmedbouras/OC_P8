<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 5; $i++) { 
            $user = new User();
            $password = $this->encoder->encodePassword($user, 'pass_' . $i);

            $user->setUsername('user_' . $i)
                 ->setPassword($password)
                 ->setEmail('user_' . $i . '@mail.fr');

            $manager->persist($user);

            for ($j = 0; $j < 2; $j++) {
                $task = new Task();
                $task->setTitle($faker->word())
                     ->setContent($faker->paragraph());

                $manager->persist($task);
            }
        }

        $manager->flush();
    }
}
