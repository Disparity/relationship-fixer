<?php

namespace Disparity\Relationship\Metadata;

use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;

class DoctrineClassMetadataFactory implements ClassMetadataFactoryInterface
{
    /**
     * @var ClassMetadataFactory
     */
    private $factory;


    /**
     * @param ClassMetadataFactory $factory
     */
    public function __construct(ClassMetadataFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @inheritdoc
     */
    public function getMetadataFor($className)
    {
        return $this->factory->getMetadataFor($className);
    }
}
