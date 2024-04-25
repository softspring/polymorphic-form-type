<?php

namespace Softspring\Component\PolymorphicFormType\Form\Exception;

use Throwable;

class MissingClassDiscriminatorException extends PolymorphicFormTypeException
{
    protected string $class;

    public function __construct(string $class, int $code = 0, ?Throwable $previous = null)
    {
        $this->class = $class;
        parent::__construct(sprintf('There is not form type for "%s" discriminator', $class), $code, $previous);
    }

    public function getClass(): string
    {
        return $this->class;
    }
}
