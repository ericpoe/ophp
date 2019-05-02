<?php
namespace Haystack\Container;

use Haystack\HaystackInterface;

interface ContainerInterface
{
    /**
     * Determines if a $value is in the current object.
     *
     * @param mixed $value
     * @return boolean
     */
    public function contains($value): bool;

    /**
     * Finds the location of $value in the current object. If it does not exist, the user will be notified
     *
     * @param mixed $value
     * @return int|string key of $value in current object
     * @throws ElementNotFoundException
     */
    public function locate($value);

    /**
     * Concatenates two things of the same type.
     *
     * @param mixed $value
     * @return HaystackInterface
     */
    public function append($value): HaystackInterface;

    /**
     * Inserts a $value at a specified location; if no key is provided, $value will be added to the back.
     *
     * @param mixed    $value
     * @param int|string|null $key
     * @return HaystackInterface
     */
    public function insert($value, $key = null): HaystackInterface;

    /**
     * Removes the first instance of the supplied value
     *
     * @param mixed $value
     * @return HaystackInterface
     */
    public function remove($value): HaystackInterface;

    /**
     * Shows only part of the array or string.
     *
     * @param int $start - the point in the HArray or HString to start slicing. If this number is positive, start that far on the left; if this number is negative, start that far on the right
     * @param int|null $length - the amount of items to slice. If this number is null, the length will be the rest of the HArray or HString; if the length is positive, the length will be the distance forward the HArray or HString will be sliced; if the length is negative, that is the length backwards the HArray or HString will be sliced
     * @return HaystackInterface
     */
    public function slice(int $start, ?int $length = null): HaystackInterface;

    /**
     * Converts the container into a simple array
     */
    public function toArray(): array;

    /**
     * Converts the container into a string
     */
    public function __toString(): string;

    /**
     * Converts the container into a string using another string as the glue
     */
    public function toString(string $glue = ''): string;
}
