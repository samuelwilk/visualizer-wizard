<?php

namespace App\DataFixtures;

use App\Story\UserStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class DevFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        UserStory::load();
    }

    public static function getGroups(): array
    {
        return ['dev'];
    }
}
