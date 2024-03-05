<?php

namespace Softspring\Component\PolymorphicFormType\Form\Exception;

class MissingFormTypeException extends PolymorphicFormTypeException
{
    protected string $discriminator;

    public function __construct(string $discriminator, int $code = 0, ?\Throwable $previous = null)
    {
        $this->discriminator = $discriminator;
        parent::__construct(sprintf('There is not form type for "%s" discriminator', $discriminator), $code, $previous);
    }

    public function getDiscriminator(): string
    {
        return $this->discriminator;
    }
}
