<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use App\Utils\Base64FileExtractor;
use App\Utils\UploadedBase64File;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\User;

#[Route('/api', name: 'api_')]
class RecipeController extends AbstractController
{
    #[Route('/recipes', name: 'recipe_list', methods: ['GET'])]
    public function list(RecipeRepository $recipeRepository, SerializerInterface $serializer): JsonResponse
    {
        $recipes = $recipeRepository->findAll();

        return new JsonResponse(
            $serializer->serialize($recipes, 'json', ['groups' => 'getRecipes']),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/recipes/{id}', name: 'recipe_get', methods: ['GET'])]
    public function get(Recipe $recipe, SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse(
            $serializer->serialize($recipe, 'json', ['groups' => 'getRecipes']),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/recipes/{id}', name: 'recipe_delete', methods: ['DELETE'])]
    public function delete(Recipe $recipe, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($recipe);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/recipes', name: 'recipe_put', methods: ['POST'])]
    public function post(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        Base64FileExtractor $base64FileExtractor,
        UploaderHelper $helper
    ): JsonResponse {
        try {
            $recipe = $serializer->deserialize($request->getContent(), Recipe::class, 'json');
        } catch (\Exception $exception) {
            return new JsonResponse($serializer->serialize($exception->getMessage(), 'json'), Response::HTTP_BAD_REQUEST, [], true);
        }
        $base64 = json_decode($request->getContent())->photo;
        $base64Image = $base64FileExtractor->extractBase64String($base64);
        $imageFile = new UploadedBase64File($base64Image, __DIR__.'/../../public/images/recipe/' . $recipe->getTitle());
        $recipe->setImageFile($imageFile);
        /** @var User $user */
        $user = $this->getUser();
        $recipe->setUser($user);
        $recipe->setPublicationDate(new \DateTime());
        $entityManager->persist($recipe);
        $path = $helper->asset($recipe, 'imageFile');
        $recipe->ImagePath($path);

        $entityManager->flush();

        $jsonRecipe = $serializer->serialize($recipe, 'json', ['groups' => 'getRecipes']);
        $location = $urlGenerator->generate('api_recipe_get', ['id' => $recipe->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonRecipe, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/recipes/{id}', name: 'recipe_update', methods: ['PUT'])]
    public function update(
        Recipe $currentRecipe,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $recipe = $serializer->deserialize(
            $request->getContent(),
            Recipe::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentRecipe]
        );

        $entityManager->persist($recipe);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_OK);
    }
}
