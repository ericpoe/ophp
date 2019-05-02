<?php

namespace Haystack;

use Haystack\Container\ContainerInterface;
use Haystack\Functional\FunctionalInterface;
use Haystack\Math\MathInterface;

interface HaystackInterface extends \IteratorAggregate, \ArrayAccess, \Serializable, \Countable, ContainerInterface, FunctionalInterface, MathInterface
{

}
