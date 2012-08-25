<?php
namespace Dionise\Parser\Directions;

const IN    = 1;
const INOUT = 2;
const OUT   = 3;

function TypeStringToID($type)
{
    return constant("Dionise\\Parser\\Directions\\" . strtoupper($type));
}