<?php

namespace App\Infrastructure\Http\Controller;

use App\Infrastructure\Persistence\Doctrine\DoctrineItineraryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ItineraryController extends AbstractController
{
    public function __construct(
        private readonly DoctrineItineraryRepository $itineraryRepository,
    ) {
    }

    #[Route('/itineraries', name: 'api_list_itineraries', methods: ['GET'])]
    public function listItineraries(): JsonResponse
    {
        $itineraries = $this->itineraryRepository->findAll();

        $data = array_map(fn ($i) => [
            'id' => $i->getId(),
            'name' => $i->getName(),
            'slug' => $i->getSlug(),
        ], $itineraries);

        return new JsonResponse($data);
    }
}
