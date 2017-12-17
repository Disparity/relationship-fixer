<?php

namespace Fixrel;

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

        $property->getInversedProperty()->setValue($oldValue, null);
        $property->setValue($property->getInversedProperty()->getValue($value), null);

        $property->setValue($_this, $value);
        $property->getInversedProperty()->setValue($value, $_this);

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

        $property->getInversedProperty()->getValue($oldValue)->removeElement($_this);

        $property->setValue($_this, $value);
        $property->getInversedProperty()->getValue($value)->add($_this);

        return true;
    }

    /**
     * @param object $_this
     * @param string $propertyName
     * @param mixed $value
     * @return bool
     * @throws \Exception
     */
    public function assign($_this, $propertyName, $value)
    {
        $property = $this->propertyMetadataFactory->getMetadataFor(get_class($_this), $propertyName);

        if ($property->isCollection()) {
            throw new \Exception(''); // @todo fix exception message
        }

        return $property->getInversedProperty()->isCollection() ?
            $this->assignManyToOne($property, $_this, $value) :
            $this->assignOneToOne($property, $_this, $value)
        ;
    }

    /**
     * @param object $_this
     * @param string $propertyName
     * @param mixed $value
     * @return bool
     */
    public function collectionAdd($_this, $propertyName, $value)
    {
        $property = $this->propertyMetadataFactory->getMetadataFor(get_class($_this), $propertyName);

        if (!$property->getInversedProperty()->isCollection()) {
            return $this->assignManyToOne($property->getInversedProperty(), $value, $_this);
        }

        if ($property->getValue($_this)->contains($value)) {
            return false;
        }

        $property->getValue($_this)->add($value);
        $property->getInversedProperty()->getValue($value)->add($_this);

        return true;
    }

    /**
     * @param object $_this
     * @param string $propertyName
     * @param mixed $value
     * @return bool
     */
    public function collectionRemove($_this, $propertyName, $value)
    {
        $property = $this->propertyMetadataFactory->getMetadataFor(get_class($_this), $propertyName);

        if (!$property->getInversedProperty()->isCollection()) {
            return $this->assignManyToOne($property->getInversedProperty(), $value, null);
        }

        if (!$property->getValue($_this)->contains($value)) {
            return false;
        }

        $property->getValue($_this)->removeElement($value);
        $property->getInversedProperty()->getValue($value)->removeElement($_this);

        return true;
    }
}
