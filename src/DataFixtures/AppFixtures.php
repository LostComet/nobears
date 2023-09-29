<?php

namespace App\DataFixtures;

use App\Entity\Bear;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use League\Csv\Reader;
use Symfony\Component\Finder\Finder;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createOne([
            'email' => 'donald.duck@gmail.com',
            'password' => 'nobears'
        ]);

        $this->importBears($manager);
    }

    private function importBears(ObjectManager $manager): void
    {
        $finder = new Finder();

        $finder->files()->in('var/import');

        if (!$finder->hasResults()) {
            return;
        }

        foreach ($finder as $file) {
            $csv = Reader::createFromPath($file->getRealPath(), 'r');

            foreach($csv->getRecords() as $row) {
                $bear = new Bear();
                $bear->setName($row[0]);
                $bear->setCity($row[1]);
                $bear->setProvince($row[2]);
                $bear->setLatitude($row[3]);
                $bear->setLongitude($row[4]);

                $manager->persist($bear);
            }

            $manager->flush();
        }
    }
}
