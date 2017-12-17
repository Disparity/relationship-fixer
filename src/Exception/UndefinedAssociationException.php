<?php

namespace Fixrel\Exception;

class UndefinedAssociationException extends FixerException
{
    /**
     * @param string $className
     * @param string $associationName
     * @param \Exception $ex
     */
    public function __construct($className, $associationName, \Exception $ex)
    {
        parent::__construct("Association name expected, '{$className}::{$associationName}' is not an association.", 0, $ex);
    }
}
