<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
     }

public function load(ObjectManager $manager)
    {

        $user=new User();
        $user2=new User();

        $user->setUsername('soufiane');
        $user->setPassword($this->passwordEncoder->encodePassword($user,'soufiane'));
        $user->setEmail('soufianemarzouk.2017@gmail.com');

        $user2->setUsername('imad');
        $user2->setPassword($this->passwordEncoder->encodePassword($user2,'imad'));
        $user2->setEmail('soufianemazouk.enset@gmail.com');

        $manager->persist($user);
        $manager->persist($user2);
        $manager->flush();
    }
}
