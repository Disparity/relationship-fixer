<?php

namespace Fixrel\Metadata;

use Fixrel\NullCollection;

class NullProperty implements PropertyInterface
{
    /**
     * @var PropertyInterface
     */
    private $inverseProperty;


    /**
     * @internal
     * @param PropertyInterface $inverseProperty
     */
    public function setInverseProperty(PropertyInterface $inverseProperty)
    {
        $this->inverseProperty = $inverseProperty;
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
    public function getInverseProperty()
    {
        return $this->inverseProperty;
    }

    /**
     * @inheritdoc
     */
    public function isCollection()
    {
        return true;
    }
}
