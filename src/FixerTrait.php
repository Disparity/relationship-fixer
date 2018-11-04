<?php

namespace Fixrel;

use Fixrel\Exception\RuntimeException;
use Fixrel\Exception\UndefinedAssociationException;
use Fixrel\Exception\UnexpectedAssociationTypeException;

trait FixerTrait
{
    /**
     * @return Fixer
     */
    private function getFixer()
    {
        $fixer = FixerStaticProxy::getFixer();

        if (!$fixer instanceof Fixer) {
            throw new RuntimeException('StaticProxy doesn\'t initialized.');
        }

        return $fixer;
    }

    /**
     * @param string $propertyName
     * @param mixed $value
     * @return bool
     * @throws UndefinedAssociationException
     * @throws UnexpectedAssociationTypeException
     */
    protected function assign($propertyName, $value)
    {
        return $this->getFixer()->assign($this, $propertyName, $value);
    }

    /**
     * @param string $propertyName
     * @param mixed $value
     * @return bool
     * @throws UndefinedAssociationException
     */
    protected function collectionAdd($propertyName, $value)
    {
        return $this->getFixer()->collectionAdd($this, $propertyName, $value);
    }

    /**
     * @param string $propertyName
     * @param mixed $value
     * @return bool
     * @throws Exception\UndefinedAssociationException
     */
    protected function collectionRemove($propertyName, $value)
    {
        return $this->getFixer()->collectionAdd($this, $propertyName, $value);
    }
}
