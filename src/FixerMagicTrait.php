<?php

namespace Fixrel;

use Fixrel\Exception\BadMethodCallException;
use Fixrel\Exception\UndefinedAssociationException;

trait FixerMagicTrait
{
    use FixerTrait;


    /**
     * @param string $method
     * @param array $arguments
     * @return bool
     */
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

            throw new BadMethodCallException('Call to undefined method ' . get_class($this) . '::' . $method . '()');
        } catch (UndefinedAssociationException $ex) {
            throw new BadMethodCallException('Call to undefined method ' . get_class($this) . '::' . $method . '()', 0, $ex);
        }
    }
}
