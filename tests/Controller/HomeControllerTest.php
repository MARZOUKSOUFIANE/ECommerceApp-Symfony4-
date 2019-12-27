<?php


namespace App\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class HomeControllerTest extends WebTestCase
{
    public function testHomePageIsUp(){
        $client=static::createClient();
        $client->request('GET','/');

        $this->assertEquals(Response::HTTP_OK,$client->getResponse()->getStatusCode());

        //echo $client->getResponse()->getContent();
    }

    public function testHomePage(){
        $client=static::createClient();
        $crawler=$client->request('GET','/');

        $this->assertSame(1,$crawler->filter('html:contains("Les derniers biens")')->count());
    }

}