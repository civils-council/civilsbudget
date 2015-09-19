<?php

namespace AppBundle\DataFixtures\ORM;

use Hautelook\AliceBundle\Alice\DataFixtureLoader;

class FixtureLoader extends DataFixtureLoader
{
    /**
     * {@inheritDoc}
     */
    protected function getFixtures()
    {
        $prefix = __DIR__ . '/../Fixture/';

        return [
            $prefix . 'user.yml',
            $prefix . 'project.yml',
        ];
    }
}