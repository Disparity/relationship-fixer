<?php

namespace Fixrel\Tests\Fixtures;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class ManyToOne1
{
    /**
     * @ORM\ManyToOne(targetEntity="Fixrel\Tests\Fixtures\ManyToOne2", inversedBy="var")
     */
    public $var;
}
