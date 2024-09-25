<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\DataFixtures\FoodFixtures;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use PHPUnit\Framework\Attributes\DataProvider;

class FoodControllerTest extends WebTestCase
{
    private string $url = '/api/food';
    private EntityManagerInterface $entityManager;
    private FoodFixtures $foodFixtures;
    private static $client;

    protected function setUp(): void
    {
        parent::setUp();

        if (self::$client === null) {
            self::$client = static::createClient();
        }
        $this->entityManager = $this->getContainer()->get(EntityManagerInterface::class);
        $this->foodFixtures = new FoodFixtures();
        $this->foodFixtures->clear($this->entityManager);
        
        $this->foodFixtures->load($this->entityManager);

    }

    protected function tearDown(): void
    {
        $this->foodFixtures->clear($this->entityManager);
    }

    #[DataProvider('foodListDataProvider')]
    public function testListWithVariousParameters(
        ?string $type,
        ?string $unit,
        array $filters,
        int $resultCount,
        int $expectedStatus,
        array $expectedError = null
    ): void {
        $client = self::$client;
        
        $url = $this->url . '?';

        if ($type) {
            $url = $this->url . '/' . $type . '?';
        }

        if ($unit) {
            $url .= "unit=$unit&";
        }

        foreach ($filters as $key => $value) {
            $url .= $key . '=' . $value;
        }

        $client->request('GET', $url);

        $response = $client->getResponse();
        $this->assertEquals($expectedStatus, $response->getStatusCode());

        if ($expectedError) {
            $this->assertJsonStringEqualsJsonString(
                json_encode($expectedError),
                $response->getContent()
            );
        } else {
            $this->assertJson($response->getContent());
            $this->assertEquals($resultCount, count(json_decode($response->getContent())));
        }
    }

    #[DataProvider('foodCreationDataProvider')]
    public function testCreateFood(array $data, int $expectedStatus, array $expectedError = null): void
    {
        $client = self::$client;
        $client->request('POST', $this->url, [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));

        $response = $client->getResponse();

        $this->assertEquals($expectedStatus, $response->getStatusCode());

        $responseBody = (array) json_decode($response->getContent());

        if ($expectedError) {
            $this->assertStringContainsString(
                $expectedError['error'],
                $responseBody['error']
            );
        } else {
            $this->assertEquals(
                'Food added successfully',
                $responseBody['success']
            );
        }
    }

    private function foodListDataProvider(): array
    {
        return [
            [null, null, [], 3, Response::HTTP_OK],
            ['fruit', 'g', [], 2, Response::HTTP_OK],
            ['fruit', 'invalid_unit', [], 0, Response::HTTP_BAD_REQUEST, ['error' => 'Invalid unit provided. Allowed units are g and kg.']],
            ['vegetable', 'kg', [], 1, Response::HTTP_OK],
            [null, null, ['name' => 'Apple'], 1, Response::HTTP_OK],
            [null, null, ['minQuantityInGrams' => 1000], 1, Response::HTTP_OK],
            [null, null, ['maxQuantityInGrams' => 400], 1, Response::HTTP_OK],
        ];
    }

    private function foodCreationDataProvider(): array
    {
        return [
            [
                [
                    'id' => 123,
                    'name' => 'Apple',
                    'type' => 'fruit',
                    'quantity' => 1,
                    'unit' => 'kg',
                ],
                Response::HTTP_CREATED
            ],
            [
                [
                    'id' => 123,
                    'name' => 'Apple',
                    'type' => 'fruit',
                    'quantity' => 1,
                    'unit' => 'invalid_unit',
                ],
                Response::HTTP_BAD_REQUEST,
                ['error' => 'Unknown unit']
            ],
            [
                [
                    'id' => 567,
                    'name' => '',
                    'type' => 'fruit',
                    'quantity' => 1,
                    'unit' => 'kg',
                ],
                Response::HTTP_BAD_REQUEST,
                ['error' => 'Name cannot be null']
            ],
            [
                [
                    'id' => 1,
                    'name' => 'Duplicate',
                    'type' => 'fruit',
                    'quantity' => 1,
                    'unit' => 'kg',
                ],
                Response::HTTP_BAD_REQUEST,
                ['error' => 'Duplicate entry']
            ],
        ];
    }
}
