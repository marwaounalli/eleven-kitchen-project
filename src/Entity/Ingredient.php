<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: IngredientRepository::class)]
class Ingredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['getRecipes'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['getRecipes'])]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['getRecipes'])]
    private ?float $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'ingredients')]
    private ?Recipe $recipe = null;

    #[ORM\Column(length: 255, nullable: true, enumType: MeasurmentUnit::class)]
    #[Groups(['getRecipes'])]
    private ?MeasurmentUnit $measurmentUnit = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(?float $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): static
    {
        $this->recipe = $recipe;

        return $this;
    }

    public function getMeasurmentUnit(): ?MeasurmentUnit
    {
        return $this->measurmentUnit;
    }

    public function setMeasurmentUnit(?MeasurmentUnit $measurmentUnit): static
    {
        $this->measurmentUnit = $measurmentUnit;

        return $this;
    }
}
