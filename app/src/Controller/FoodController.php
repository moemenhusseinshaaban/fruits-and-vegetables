<?php

declare(strict_types=1);

namespace App\Controller;

use App\Enum\Unit;
use App\Factory\FoodFactory;
use App\Service\Collection\FoodCollectionService;
use App\Service\Resolver\CollectionResolver;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class FoodController extends AbstractController
{
    public function __construct(
        private readonly FoodCollectionService $foodCollectionService,
        private readonly CollectionResolver $collectionResolver,
        private readonly SerializerInterface $serializer,
        private readonly LoggerInterface $logger
    ) {
    }

    #[Route('/api/food/{type}', name: 'list_food', methods: ['GET'], defaults: ['type' => null])]
    public function list(?string $type, Request $request): JsonResponse
    {
        try {
            $collectionService = $type ? $this->collectionResolver->resolve($type) : $this->foodCollectionService;

            $filters = $this->createFiltersFromRequest($request);

            $unit = $request->query->get('unit', Unit::GRAM->value);
            if (!in_array($unit, array_column(Unit::cases(), 'value'))) {
                throw new BadRequestHttpException('Invalid unit provided. Allowed units are g and kg.');
            }

            $food = $collectionService->list($filters);
        } catch (BadRequestHttpException $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            $this->logger->error('Error listing food items: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Internal server error'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        $jsonData = $this->serializer->serialize($food, 'json', [
            'groups' => [
                'food:read',
                "food:read:unit:$unit"
            ],
        ]);

        return new JsonResponse($jsonData, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/api/food', name: 'create_food', methods: ['POST'])]
    public function create(Request $request, FoodFactory $foodFactory): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        try {
            $food = $foodFactory->create($data);
        } catch (ValidationFailedException $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        } catch (UniqueConstraintViolationException $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            $this->logger->error('Error creating food item: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Internal server error'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse([
            'success' => 'Food added successfully',
            'data' => json_decode($this->serializer->serialize($food, 'json', [
                'groups' => [
                    'food:read',
                    "food:read:unit:g"
                ],
            ]))
        ], JsonResponse::HTTP_CREATED);
    }

    private function createFiltersFromRequest(Request $request): array
    {
        return [
            'name' => $request->query->get('name'),
            'minQuantityInGrams' => $request->query->get('minQuantityInGrams'),
            'maxQuantityInGrams' => $request->query->get('maxQuantityInGrams')
        ];
    }
}
