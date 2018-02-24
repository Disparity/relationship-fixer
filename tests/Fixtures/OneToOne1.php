<?php

namespace Fixrel\Tests\Fixtures;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class OneToOne1
{
    /**
     * @ORM\OneToOne(targetEntity="Fixrel\Tests\Fixtures\OneToOne2", inversedBy="var")
     */
    public $var;
}
