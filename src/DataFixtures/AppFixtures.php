<?php

namespace App\DataFixtures;

use App\Entity\Difficulty;
use App\Entity\Ingredient;
use App\Entity\MeasurmentUnit;
use App\Entity\Recipe;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail("user@elevenkitchen.com");
        $user->setRoles(["ROLE_USER"]);
        $user->setFirstname('firstname');
        $user->setLastname('lastname');
        $user->setPassword($this->hasher->hashPassword($user, "password"));
        $manager->persist($user);
        $manager->flush();

        for ($i = 1; $i <= 20; $i++) {
            $ingredient = new Ingredient();
            $ingredient->setName('Ingrédient de la recette ' . $i);
            $ingredient->setQuantity(12);
            $ingredient->setMeasurmentUnit(MeasurmentUnit::G);
            $manager->persist($ingredient);
            $imgFile = $this->createImage($i);

            $recipe = new Recipe();
            $recipe->setTitle('Recette ' . $i);
            $recipe->setDescription('Recette numéro : ' . $i);
            $recipe->setPublicationDate(new \DateTime());
            $recipe->addIngredient($ingredient);
            $recipe->setUser($user);
            $recipe->setCategory('party');
            $recipe->setDifficulty(Difficulty::EASY);
            $recipe->ImagePath('/images/fixtures/'. $imgFile->getFilename());
            $manager->persist($recipe);
        }

        $manager->flush();
    }

    protected function createImage(int $number): UploadedFile
    {
        $folder = __DIR__.'/../../public/images/fixtures/';
        $imgName = $number.'.jpg';
        $src = $folder.$imgName;

        return new UploadedFile(
            path: $src,
            originalName: $imgName,
            mimeType: 'image/jpeg',
            test: true
        );
    }
}
