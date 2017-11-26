<?php

namespace Disparity\Relationship\Metadata;

use Disparity\Relationship\Exception\UndefinedAssociationException;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;

class PropertyMetadataFactory
{
    /**
     * @var ClassMetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var DoctrineProxyLoader
     */
    private $loader;


    /**
     * @param ClassMetadataFactoryInterface $metadataFactory
     * @param DoctrineProxyLoader $loader
     */
    public function __construct(ClassMetadataFactoryInterface $metadataFactory, DoctrineProxyLoader $loader)
    {
        $this->metadataFactory = $metadataFactory;
        $this->loader = $loader;
    }

    /**
     * @param string $className
     * @param string $propertyName
     * @return Property
     * @throws UndefinedAssociationException
     */
    public function getMetadataFor($className, $propertyName)
    {
        try {
            $classMetadata = $this->metadataFactory->getMetadataFor($className);
            $inversedClassMetadata = $this->metadataFactory->getMetadataFor($classMetadata->getAssociationTargetClass($propertyName));
        } catch (\InvalidArgumentException $ex) {
            throw new UndefinedAssociationException($className, $propertyName, $ex);
        }

        if ($classMetadata->isAssociationInverseSide($propertyName)) {
            // @todo check broken relationship
            $backProperty = $this->buildProperty($inversedClassMetadata, $classMetadata->getAssociationMappedByTargetField($propertyName));
        } else {
            foreach ($inversedClassMetadata->getAssociationNames() as $inversedPropertyName) {
                if ($inversedClassMetadata->getAssociationMappedByTargetField($inversedPropertyName) === $propertyName) {
                    $backProperty = $this->buildProperty($inversedClassMetadata, $inversedPropertyName);

                    break;
                }
            }

            if (!isset($backProperty)) {
                $backProperty = new NullProperty();
            }
        }

        $property = $this->buildProperty($classMetadata, $propertyName);

        $backProperty->setInversedProperty($property);
        $property->setInversedProperty($backProperty);

        return $property;
    }

    /**
     * @param ClassMetadata $classMetadata
     * @param string $propertyName
     * @return Property
     */
    private function buildProperty(ClassMetadata $classMetadata, $propertyName)
    {
        $ref = new \ReflectionProperty($classMetadata->getName(), $propertyName);
        $ref->setAccessible(true); // @todo fix parent private property

        return new Property($ref, $this->loader, $classMetadata->isCollectionValuedAssociation($propertyName));
    }
}
