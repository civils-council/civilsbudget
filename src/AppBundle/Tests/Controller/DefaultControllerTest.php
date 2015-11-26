<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/projects');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Projects', $crawler->filter('html:contains(Projects)')->text());
    }
}
