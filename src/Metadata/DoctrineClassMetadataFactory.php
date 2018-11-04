<?php

namespace Fixrel\Metadata;

use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;

final class DoctrineClassMetadataFactory implements ClassMetadataFactoryInterface
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
