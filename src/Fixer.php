<?php

namespace Fixrel;

use Fixrel\Exception\UndefinedAssociationException;
use Fixrel\Exception\UnexpectedAssociationTypeException;
use Fixrel\Metadata\PropertyInterface;
use Fixrel\Metadata\PropertyMetadataFactory;

class Fixer
{
    /**
     * @var PropertyMetadataFactory
     */
    private $propertyMetadataFactory;


    /**
     * @param PropertyMetadataFactory $propertyMetadataFactory
     */
    public function __construct(PropertyMetadataFactory $propertyMetadataFactory)
    {
        $this->propertyMetadataFactory = $propertyMetadataFactory;
    }

    /**
     * @param PropertyInterface $property
     * @param object $_this
     * @param object $value
     * @return bool
     */
    private function assignOneToOne(PropertyInterface $property, $_this, $value)
    {
        $oldValue = $property->getValue($_this);

        if ($value === $oldValue) {
            return false;
        }

        $property->getInverseProperty()->setValue($oldValue, null);
        $property->setValue($property->getInverseProperty()->getValue($value), null);

        $property->setValue($_this, $value);
        $property->getInverseProperty()->setValue($value, $_this);

        return true;
    }

    /**
     * @param PropertyInterface $property
     * @param object $_this
     * @param object $value
     * @return bool
     */
    private function assignManyToOne(PropertyInterface $property, $_this, $value)
    {
        $oldValue = $property->getValue($_this);

        if ($value === $oldValue) {
            return false;
        }

        $property->getInverseProperty()->getValue($oldValue)->removeElement($_this);

        $property->setValue($_this, $value);
        $property->getInverseProperty()->getValue($value)->add($_this);

        return true;
    }

    /**
     * @param object $_this
     * @param string $propertyName
     * @param mixed $value
     * @return bool
     * @throws UnexpectedAssociationTypeException
     * @throws UndefinedAssociationException
     */
    public function assign($_this, $propertyName, $value)
    {
        $property = $this->propertyMetadataFactory->getMetadataFor(get_class($_this), $propertyName);

        if ($property->isCollection()) {
            throw new UnexpectedAssociationTypeException('Assign can not be applied to a collection. Use collectionAdd or collectionRemove.');
        }

        return $property->getInverseProperty()->isCollection() ?
            $this->assignManyToOne($property, $_this, $value) :
            $this->assignOneToOne($property, $_this, $value)
        ;
    }

    /**
     * @param object $_this
     * @param string $propertyName
     * @param mixed $value
     * @return bool
     * @throws UndefinedAssociationException
     */
    public function collectionAdd($_this, $propertyName, $value)
    {
        $property = $this->propertyMetadataFactory->getMetadataFor(get_class($_this), $propertyName);

        if (!$property->getInverseProperty()->isCollection()) {
            return $this->assignManyToOne($property->getInverseProperty(), $value, $_this);
        }

        if ($property->getValue($_this)->contains($value)) {
            return false;
        }

        $property->getValue($_this)->add($value);
        $property->getInverseProperty()->getValue($value)->add($_this);

        return true;
    }

    /**
     * @param object $_this
     * @param string $propertyName
     * @param mixed $value
     * @return bool
     * @throws UndefinedAssociationException
     */
    public function collectionRemove($_this, $propertyName, $value)
    {
        $property = $this->propertyMetadataFactory->getMetadataFor(get_class($_this), $propertyName);

        if (!$property->getInverseProperty()->isCollection()) {
            return $this->assignManyToOne($property->getInverseProperty(), $value, null);
        }

        if (!$property->getValue($_this)->contains($value)) {
            return false;
        }

        $property->getValue($_this)->removeElement($value);
        $property->getInverseProperty()->getValue($value)->removeElement($_this);

        return true;
    }
}
