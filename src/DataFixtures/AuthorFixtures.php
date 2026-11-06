<?php

namespace App\DataFixtures;

use App\Entity\Author;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AuthorFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('pt_BR');
        
        for ($i = 0; $i <= 100; $i++) {
            $author = new Author();
            $author->setName($faker->name());
            $manager->persist($author);
        }

        $manager->flush();
    }

}
