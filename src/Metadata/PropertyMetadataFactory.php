<?php

namespace Fixrel\Metadata;

use Fixrel\Exception\UndefinedAssociationException;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;

final class PropertyMetadataFactory
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
            $inverseClassMetadata = $this->metadataFactory->getMetadataFor($classMetadata->getAssociationTargetClass($propertyName));
        } catch (\InvalidArgumentException $ex) {
            throw new UndefinedAssociationException($className, $propertyName, $ex);
        }

        if ($classMetadata->isAssociationInverseSide($propertyName)) {
            $inverseProperty = $this->buildProperty($inverseClassMetadata, $classMetadata->getAssociationMappedByTargetField($propertyName));
        } else {
            foreach ($inverseClassMetadata->getAssociationNames() as $inversePropertyName) {
                if ($inverseClassMetadata->getAssociationMappedByTargetField($inversePropertyName) === $propertyName) {
                    $inverseProperty = $this->buildProperty($inverseClassMetadata, $inversePropertyName);

                    break;
                }
            }

            if (!isset($inverseProperty)) {
                $inverseProperty = new NullProperty();
            }
        }

        $property = $this->buildProperty($classMetadata, $propertyName);

        $inverseProperty->setInverseProperty($property);
        $property->setInverseProperty($inverseProperty);

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
        $ref->setAccessible(true);

        return new Property($ref, $this->loader, $classMetadata->isCollectionValuedAssociation($propertyName));
    }
}
