<?php

namespace AppBundle\DataFixtures\ORM;

use Hautelook\AliceBundle\Alice\DataFixtureLoader;
use Symfony\Component\Security\Core\User\UserInterface;

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

    /**
     * @param UserInterface $user
     * @param $plainPassword
     * @return string
     */
    public function encodePassword(UserInterface $user, $plainPassword)
    {
        return $this->container->get('security.password_encoder')->encodePassword($user, $plainPassword);
    }
}