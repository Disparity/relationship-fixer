<?php

namespace Fixrel\Tests\Fixtures;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class ManyToMany3
{
    /**
     * @ORM\ManyToMany(targetEntity="Fixrel\Tests\Fixtures\ManyToMany4")
     */
    public $var;

    public function __construct()
    {
        $this->var = new ArrayCollection();
    }
}
