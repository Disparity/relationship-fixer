<?php

namespace Disparity\Relationship\Metadata;

use Doctrine\ORM\Mapping\ClassMetadata;

interface ClassMetadataFactoryInterface
{
    /**
     * @param string $className
     * @return ClassMetadata
     */
    public function getMetadataFor($className);
}
