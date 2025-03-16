<?php

namespace App\Story;

use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class UserStory extends Story
{
    public function build(): void
    {
        UserFactory::createOne([
            'email' => 'sam@zu.com',
        ]);
    }
}
