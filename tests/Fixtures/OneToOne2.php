<?php

namespace Fixrel\Tests\Fixtures;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class OneToOne2
{
    /**
     * @ORM\OneToOne(targetEntity="Fixrel\Tests\Fixtures\OneToOne1", mappedBy="var")
     */
    public $var;
}
