<?php

namespace Fixrel\Metadata;

use Doctrine\ORM\Proxy\Proxy;

class DoctrineProxyLoader
{
    /**
     * @param mixed $entity
     * @return mixed
     */
    public function load($entity)
    {
        if ($entity instanceof \Traversable) {
            foreach ($entity as $item) {
                $this->load($item);
            }

            return $entity;
        }

        if ($entity instanceof Proxy && !$entity->__isInitialized()) {
            $entity->__load();
        }

        return $entity;
    }
}
