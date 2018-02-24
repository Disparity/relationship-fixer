<?php

namespace Fixrel\Tests\Fixtures;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class OneToMany1
{
    /**
     * @ORM\OneToMany(targetEntity="Fixrel\Tests\Fixtures\OneToMany2", mappedBy="var")
     */
    public $var;

    public function __construct()
    {
        $this->var = new ArrayCollection();
    }
}
