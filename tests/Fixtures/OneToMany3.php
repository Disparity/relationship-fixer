<?php

namespace Fixrel\Tests\Fixtures;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class OneToMany3
{
    /**
     * @ORM\ManyToMany(targetEntity="Fixrel\Tests\Fixtures\OneToMany4")
     */
    public $var;

    public function __construct()
    {
        $this->var = new ArrayCollection();
    }
}
