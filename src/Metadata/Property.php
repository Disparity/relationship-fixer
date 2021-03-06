<?php

namespace Fixrel\Metadata;

use Fixrel\NullCollection;

class Property implements PropertyInterface
{
    /**
     * @var PropertyInterface
     */
    private $inverseProperty;

    /**
     * @var \ReflectionProperty
     */
    private $refProperty;

    /**
     * @var DoctrineProxyLoader
     */
    private $loader;

    /**
     * @var bool
     */
    private $toMany;


    /**
     * @internal
     * @param \ReflectionProperty $refProperty
     * @param DoctrineProxyLoader $loader
     * @param bool $toMany
     */
    public function __construct(\ReflectionProperty $refProperty, DoctrineProxyLoader $loader, $toMany)
    {
        $this->refProperty = $refProperty;
        $this->loader = $loader;
        $this->toMany = $toMany;
    }

    /**
     * @inheritdoc
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
        if ($entity === null) {
            return $this->isCollection() ? new NullCollection() : null;
        }

        return $this->loader->load($this->refProperty->getValue($this->loader->load($entity)));
    }

    /**
     * @inheritdoc
     */
    public function setValue($entity, $value)
    {
        if ($entity === null) {
            return null;
        }

        if ($value instanceof NullCollection) { // fix for unidirectional MANY_TO_* relation
            return $this;
        }

        $this->refProperty->setValue($this->loader->load($entity), $value);

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
        return $this->toMany;
    }
}
