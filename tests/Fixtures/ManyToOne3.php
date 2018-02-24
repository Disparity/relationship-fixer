<?php

namespace Fixrel\Tests\Fixtures;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class ManyToOne3
{
    /**
     * @ORM\ManyToOne(targetEntity="Fixrel\Tests\Fixtures\ManyToOne4")
     */
    public $var;
}
