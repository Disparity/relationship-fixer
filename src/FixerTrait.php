<?php

namespace Disparity\Relationship;

trait FixerTrait
{
    /**
     * @return Fixer
     * @throws \Exception
     */
    private function getFixer()
    {
        $fixer = FixerStaticProxy::getFixer();

        if (!$fixer instanceof Fixer) {
            throw new \Exception('StaticProxy doesn\'t initialized.'); //@todo fix exception
        }

        return $fixer;
    }

    /**
     * @param string $propertyName
     * @param mixed $value
     * @return bool
     * @throws \Exception
     */
    protected function assign($propertyName, $value)
    {
        return $this->getFixer()->assign($this, $propertyName, $value);
    }

    /**
     * @param string $propertyName
     * @param mixed $value
     * @return bool
     * @throws \Exception
     */
    protected function collectionAdd($propertyName, $value)
    {
        return $this->getFixer()->collectionAdd($this, $propertyName, $value);
    }

    /**
     * @param string $propertyName
     * @param mixed $value
     * @return bool
     * @throws \Exception
     */
    protected function collectionRemove($propertyName, $value)
    {
        return $this->getFixer()->collectionAdd($this, $propertyName, $value);
    }
}
