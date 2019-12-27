<?php


namespace App\Tests\Controller;


use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Property;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;


class PropertyTest extends ApiTestCase
{
    // This trait provided by HautelookAliceBundle will take care of refreshing the database content to a known state before each test
    //use RefreshDatabaseTrait;

    public function testGetProperties(): void
    {
        // The client implements Symfony HttpClient's `HttpClientInterface`, and the response `ResponseInterface`
        $response = static::createClient()->request('GET', '/api/properties');

        $this->assertResponseIsSuccessful();
        // Asserts that the returned content type is JSON-LD (the default)
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        // Asserts that the returned JSON is a superset of this one
        $this->assertJsonContains([
            '@context' => '/api/contexts/Property',
            '@id' => '/api/properties',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 120,
            'hydra:view' => [
                '@id' => '/api/properties?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/api/properties?page=1',
                'hydra:last' => '/api/properties?page=4',
                'hydra:next' => '/api/properties?page=2'
            ],
        ]);

        // Because test fixtures are automatically loaded between each test, you can assert on them
        $this->assertCount(30, $response->toArray()['hydra:member']);

        // Asserts that the returned JSON is validated by the JSON Schema generated for this resource by API Platform
        // This generated JSON Schema is also used in the OpenAPI spec!
        $this->assertMatchesResourceCollectionJsonSchema(Property::class);
    }


}