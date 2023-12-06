<?php

namespace App\Entity;

enum MeasurmentUnit: string
{
    case G = "g";
    case Kg = "kg";
    case L = "l";
    case Ml = "ml";
    case Cl = "cl";
    case Cup = "tasse";
    case Cs = "cs";
    case Cc = "cc";
}
