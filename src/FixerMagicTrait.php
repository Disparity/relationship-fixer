<?php

namespace Fixrel;

use Fixrel\Exception\UndefinedAssociationException;

trait FixerMagicTrait
{
    use FixerTrait;


    public function __call($method, $arguments)
    {
        try {
            if (preg_match('/^(?<action>set|add|remove)(?<propertyName>.+)$/', $method, $matches) && count($arguments) !== 0) {
                switch ($matches['action']) {
                    case 'set': return $this->assign(lcfirst($matches['propertyName']), reset($arguments));
                    case 'add': return $this->collectionAdd(lcfirst($matches['propertyName']), reset($arguments));
                    case 'remove': return $this->collectionRemove(lcfirst($matches['propertyName']), reset($arguments));
                }
            }

            throw new \BadMethodCallException('Call to undefined method ' . get_class($this) . '::' . $method . '()');
        } catch (UndefinedAssociationException $ex) {
            $className = get_class($this);
            throw new \BadMethodCallException("Call to undefined method {$className}::{$method}()", 0, $ex);
        }
    }
}
