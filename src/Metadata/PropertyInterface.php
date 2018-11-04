<?php

namespace Fixrel\Metadata;

interface PropertyInterface
{
    /**
     * Gets the specified field's value off the given entity.
     *
     * @param object $entity
     * @return mixed
     */
    public function getValue($entity);

    /**
     * Sets the specified field to the specified value on the given entity.
     *
     * @param object $entity
     * @param mixed $value
     * @return $this
     */
    public function setValue($entity, $value);

    /**
     * @return PropertyInterface
     */
    public function getInverseProperty();

    /**
     * @return bool
     */
    public function isCollection();
}
