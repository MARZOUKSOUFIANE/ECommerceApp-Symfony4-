<?php

namespace App\DataFixtures;

use App\Entity\Property;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;


class PropertyFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // On configure dans quelles langues nous voulons nos données
        $faker = Faker\Factory::create('fr_FR');


        // on créé 100 properties
        for ($i = 0; $i < 100; $i++) {
            $property = new Property();
            $property->setTitle($faker->words(3,true));
            $property->setDescription($faker->sentences(3,true));
            $property->setSurface($faker->numberBetween(50,400));
            $property->setRooms($faker->numberBetween(2,10));
            $property->setBedrooms($faker->numberBetween(1,9));
            $property->setFloor($faker->numberBetween(0,15));
            $property->setPrice($faker->numberBetween(10,10000));
            $property->setPostalCode($faker->postcode);
            $property->setAddress($faker->address);
            $property->setSold(false);
            $property->setHire(false);
            $property->setCity($faker->city);
            $property->setHeat($faker->numberBetween(0,count(Property::HEAT)-1));

            $manager->persist($property);
        }

        $manager->flush();
    }
}
