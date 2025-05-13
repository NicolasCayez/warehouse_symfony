<?php

namespace App\DataFixtures;

use App\Entity\Cours;
use App\Entity\Langages;
use App\Entity\Niveaux;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

use function Symfony\Component\Clock\now;

class UserFixtures extends Fixture
{
    public const USER_REFERENCE_TAG = 'user-';
    public const NB_USER = 10;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        //admin
        $i = 0;
        $user = new User();
        $user->setUsername('Nicolas');
        $user->setRoles(["ROLE_USER","ROLE_ADMIN"]);
        $plainPassword = '123456';
        $user->setPassword(password_hash($plainPassword, PASSWORD_BCRYPT));
        $user->setUserLastName('Cayez');
        $user->setUserFirstName('Nicolas');
        $manager->persist($user);
        $this->addReference(self::USER_REFERENCE_TAG . $i, $user);
        // $manager->flush();

        //classic user
        $i = 1;
        $user = new User();
        $user->setUsername('NicoManager');
        $user->setRoles(["ROLE_USER","ROLE_MANAGER"]);
        $plainPassword = '123456';
        $user->setPassword(password_hash($plainPassword, PASSWORD_BCRYPT));
        $user->setUserLastName('Cayez');
        $user->setUserFirstName('NicoManager');
        $manager->persist($user);
        $this->addReference(self::USER_REFERENCE_TAG . $i, $user);
        // $manager->flush();

        //manager
        $i = 2;
        $user = new User();
        $user->setUsername('Nico');
        $user->setRoles(["ROLE_USER"]);
        $plainPassword = '123456';
        $user->setPassword(password_hash($plainPassword, PASSWORD_BCRYPT));
        $user->setUserLastName('Cayez');
        $user->setUserFirstName('Nico');
        $manager->persist($user);
        $this->addReference(self::USER_REFERENCE_TAG . $i, $user);
        // $manager->flush();

        //other users
        for ($i = 3; $i < self::NB_USER; $i++) {
            $user = new User();
            $user->setUsername($faker->userName());
            $user->setRoles(["ROLE_USER"]);
            $plainPassword = $faker->password(10);
            $user->setPassword(password_hash($plainPassword, PASSWORD_BCRYPT));
            $user->setUserLastName($faker->lastName());
            $user->setUserFirstName($faker->firstName());

            $manager->persist($user);
            $this->addReference(self::USER_REFERENCE_TAG . $i, $user);
        }
        $manager->flush();
    }
}

