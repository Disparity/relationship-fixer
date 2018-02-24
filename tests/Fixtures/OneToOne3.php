<?php

namespace Fixrel\Tests\Fixtures;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class OneToOne3
{
    /**
     * @ORM\OneToOne(targetEntity="Fixrel\Tests\Fixtures\OneToOne4")
     */
    public $var;
}
