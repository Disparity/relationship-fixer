<?php

namespace Disparity\Relationship;

use Disparity\Relationship\Fixer;
use Disparity\Relationship\Metadata\ClassMetadataFactoryInterface;
use Disparity\Relationship\Metadata\DoctrineProxyLoader;
use Disparity\Relationship\Metadata\PropertyMetadataFactory;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

class ManyToManyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Fixer
     */
    private static $fixer;

    public static function setUpBeforeClass()
    {
        $classMetadataFactory = new class implements ClassMetadataFactoryInterface {
            public function getMetadataFor($className)
            {
                $classMetadata = new ORM\ClassMetadata($className, new ORM\UnderscoreNamingStrategy());
                (new AnnotationDriver(new AnnotationReader()))->loadMetadataForClass($className, $classMetadata);
                return $classMetadata;
            }
        };

        static::$fixer = $fixer = new Fixer(new PropertyMetadataFactory(
            $classMetadataFactory, new DoctrineProxyLoader()
        ));
    }

    public static function tearDownAfterClass()
    {
        static::$fixer = null;
    }

    public function testBidirectionalAdd()
    {
        $entity1 = new ManyToMany1();
        $entity2 = new ManyToMany2();
        $entity3 = new ManyToMany1();
        $entity4 = new ManyToMany2();

        $entity1->var->add($entity2);
        $entity2->var->add($entity1);
        $entity3->var->add($entity4);
        $entity4->var->add($entity3);

        static::$fixer->collectionAdd($entity3, 'var', $entity2);

        $this->assertContains($entity4, $entity3->var);
        $this->assertContains($entity2, $entity3->var);
        $this->assertContains($entity3, $entity2->var);
        $this->assertContains($entity1, $entity2->var);
    }

    public function testBidirectionalInverseAdd()
    {
        $entity1 = new ManyToMany1();
        $entity2 = new ManyToMany2();
        $entity3 = new ManyToMany1();
        $entity4 = new ManyToMany2();

        $entity1->var->add($entity2);
        $entity2->var->add($entity1);
        $entity3->var->add($entity4);
        $entity4->var->add($entity3);

        static::$fixer->collectionAdd($entity2, 'var', $entity3);

        $this->assertContains($entity4, $entity3->var);
        $this->assertContains($entity2, $entity3->var);
        $this->assertContains($entity3, $entity2->var);
        $this->assertContains($entity1, $entity2->var);
    }

    public function testBidirectionalRemove()
    {
        $entity1 = new ManyToMany1();
        $entity2 = new ManyToMany2();

        $entity1->var->add($entity2);
        $entity2->var->add($entity1);

        static::$fixer->collectionRemove($entity1, 'var', $entity2);

        $this->assertNotContains($entity1, $entity2->var);
        $this->assertNotContains($entity2, $entity1->var);
    }

    public function testBidirectionalRemoveInverse()
    {
        $entity1 = new ManyToMany1();
        $entity2 = new ManyToMany2();

        $entity1->var->add($entity2);
        $entity2->var->add($entity1);

        static::$fixer->collectionRemove($entity2, 'var', $entity1);

        $this->assertNotContains($entity1, $entity2->var);
        $this->assertNotContains($entity2, $entity1->var);
    }

    public function testUnidirectionalAdd()
    {
        $entity1 = new ManyToMany3();
        $entity2 = new ManyToMany4();
        $entity3 = new ManyToMany3();
        $entity4 = new ManyToMany4();

        $entity1->var->add($entity2);
        $entity3->var->add($entity4);

        static::$fixer->collectionAdd($entity3, 'var', $entity2);

        $this->assertContains($entity4, $entity3->var);
        $this->assertContains($entity2, $entity3->var);
        $this->assertContains($entity2, $entity1->var);
    }

    public function testUnidirectionalRemove()
    {
        $entity1 = new ManyToMany3();
        $entity2 = new ManyToMany4();

        $entity1->var->add($entity2);

        static::$fixer->collectionRemove($entity1, 'var', $entity2);

        $this->assertNotContains($entity2, $entity1->var);
    }
}

/**
 * @ORM\Entity()
 */
class ManyToMany1
{
    /**
     * @ORM\ManyToMany(targetEntity="Disparity\Relationship\ManyToMany2", inversedBy="var")
     */
    public $var;

    public function __construct()
    {
        $this->var = new ArrayCollection();
    }
}

/**
 * @ORM\Entity()
 */
class ManyToMany2
{
    /**
     * @ORM\ManyToMany(targetEntity="Disparity\Relationship\ManyToMany1", mappedBy="var")
     */
    public $var;

    public function __construct()
    {
        $this->var = new ArrayCollection();
    }
}

/**
 * @ORM\Entity()
 */
class ManyToMany3
{
    /**
     * @ORM\ManyToMany(targetEntity="Disparity\Relationship\ManyToMany4")
     */
    public $var;

    public function __construct()
    {
        $this->var = new ArrayCollection();
    }
}

/**
 * @ORM\Entity()
 */
class ManyToMany4
{
}
