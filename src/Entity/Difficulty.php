<?php

namespace App\Entity;

enum Difficulty: string
{
    case EASY = "easy";
    case MEDIUM = "medium";
    case DIFFICULT = "difficult";
}
