<?php

namespace Softspring\PolymorphicFormType\Tests\Example1\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Softspring\PolymorphicFormType\Tests\Example1\Model\Properties\AbstractProperty;

class Entity
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var AbstractProperty[]
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
