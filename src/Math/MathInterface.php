<?php
namespace Haystack\Math;

interface MathInterface
{
    /**
     * Adds numeric value of every element. Non-numeric elements have a value of 0.
     */
    public function sum(): float;

    /**
     * Multiplies numeric value of every element. Non-numeric elements have a value of 0.
     */
    public function product(): float;
}
