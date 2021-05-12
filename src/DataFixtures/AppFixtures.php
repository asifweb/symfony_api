<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\DataFixtures;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        // $faker = Factory::create();

        for ($i = 0; $i < 50; $i++) {
            $customer = new Customer();
            $customer->setFirstName('First Name' . mt_rand(0, 100));
            $customer->setLastName('Last Name' . mt_rand(0, 100));
            $customer->setEmail(uniqid() . '@gmail.com');
            $customer->setPhoneNumber(mt_rand(0, 100000));
            $manager->persist($customer);
        }

        $manager->flush();
    }
}
