<?php

namespace Fixrel\Tests\Fixtures;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class OneToMany2
{
    /**
     * @ORM\ManyToOne(targetEntity="Fixrel\Tests\Fixtures\OneToMany1", inversedBy="var")
     */
    public $var;
}
