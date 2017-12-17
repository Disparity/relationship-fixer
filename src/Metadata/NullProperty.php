<?php

namespace Fixrel\Metadata;

use Fixrel\NullCollection;

class NullProperty implements PropertyInterface
{
    /**
     * @var Property
     */
    private $inversedProperty;


    /**
     * @internal
     * @param PropertyInterface $inversedProperty
     */
    public function setInversedProperty(PropertyInterface $inversedProperty)
    {
        $this->inversedProperty = $inversedProperty;
    }

    /**
     * @inheritdoc
     */
    public function getValue($entity)
    {
        return new NullCollection();
    }

    /**
     * @inheritdoc
     */
    public function setValue($entity, $value)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getInversedProperty()
    {
        return $this->inversedProperty;
    }

    /**
     * @inheritdoc
     */
    public function isCollection()
    {
        return true;
    }
}
