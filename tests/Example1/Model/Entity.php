<?php

namespace Softspring\Component\PolymorphicFormType\Tests\Example1\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Softspring\Component\PolymorphicFormType\Tests\Example1\Model\Properties\AbstractProperty;

class Entity
{
    public string $name;

    /**
     * @var AbstractProperty[]|ArrayCollection
     */
    public $properties;

    /**
     * Entity constructor.
     */
    public function __construct()
    {
        $this->properties = new ArrayCollection();
    }
}
