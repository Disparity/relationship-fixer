<?php

namespace Fixrel\Metadata;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;

interface ClassMetadataFactoryInterface
{
    /**
     * @param string $className
     * @return ClassMetadata
     */
    public function getMetadataFor($className);
}
