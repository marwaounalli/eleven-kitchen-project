<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {}
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

        for ($i = 0; $i < 20; $i++) {
            $ingredient = new Ingredient();
            $ingredient->setName('Ingrédient de la recette ' . $i);
            $ingredient->setQuantity(12);
            $ingredient->setMeasurmentUnit('gramme');
            $manager->persist($ingredient);

            $recipe = new Recipe();
            $recipe->setTitle('Recette ' . $i);
            $recipe->setDescription('Recette numéro : ' . $i);
            $recipe->setPublicationDate(new \DateTime());
            $recipe->addIngredient($ingredient);
            $recipe->setUser($user);
            $manager->persist($recipe);
        }

        $manager->flush();
    }
}
