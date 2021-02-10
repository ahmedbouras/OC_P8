<?php

namespace App\DataFixtures;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

trait UserFixtures
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    
    public function createAuthor(int $iterable)
    {
        switch ($iterable) {
            case 0:
                $admin = new User();
                $admin->setUsername('admin')
                        ->setPassword($this->encoder->encodePassword($admin, 'passadmin'))
                        ->setRoles(["ROLE_ADMIN"])
                        ->setEmail('admin@mail.fr');
                return $admin;
                break;
            case 1:
                $anonym = new User();
                $anonym->setUsername('anonym')
                        ->setPassword($this->encoder->encodePassword($anonym, '0000'))
                        ->setEmail('anonym@mail.fr');
                return $anonym;
                break;
            default:
                $user = new User();
                $user->setUsername('user' . $iterable)
                    ->setPassword($this->encoder->encodePassword($user, 'password'))
                    ->setEmail('user' . $iterable . '@mail.fr');
                return $user;
                break;
        }
        
    }

}