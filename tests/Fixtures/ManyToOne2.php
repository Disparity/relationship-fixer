<?php

namespace Fixrel\Tests\Fixtures;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class ManyToOne2
{
    /**
     * @ORM\OneToMany(targetEntity="Fixrel\Tests\Fixtures\ManyToOne1", mappedBy="var")
     */
    public $var;

    public function __construct()
    {
        $this->var = new ArrayCollection();
    }
}
