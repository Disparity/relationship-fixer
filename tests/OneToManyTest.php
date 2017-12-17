<?php

namespace Fixrel;

use Fixrel\Fixer;
use Fixrel\Metadata\ClassMetadataFactoryInterface;
use Fixrel\Metadata\DoctrineProxyLoader;
use Fixrel\Metadata\PropertyMetadataFactory;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use PHPUnit\Framework\TestCase;

class OneToManyTest extends TestCase
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
        $entity1 = new OneToMany1();
        $entity2 = new OneToMany2();
        $entity3 = new OneToMany1();

        $entity1->var->add($entity2);
        $entity2->var = $entity1;

        static::$fixer->collectionAdd($entity3, 'var', $entity2);

        $this->assertNotContains($entity2, $entity1->var);
        $this->assertContains($entity2, $entity3->var);
        $this->assertEquals($entity3, $entity2->var);
    }

    public function testBidirectionalRemove()
    {
        $entity1 = new OneToMany1();
        $entity2 = new OneToMany2();

        $entity1->var->add($entity2);
        $entity2->var = $entity1;

        static::$fixer->collectionRemove($entity1, 'var', $entity2);

        $this->assertNotContains($entity2, $entity1->var);
        $this->assertNull($entity2->var);
    }

    public function testUnidirectionalAdd()
    {
        $entity1 = new OneToMany3();
        $entity2 = new OneToMany4();
        $entity3 = new OneToMany3();

        $entity1->var->add($entity2);

        static::$fixer->collectionAdd($entity3, 'var', $entity2);

        // $this->assertNotContains($entity2, $entity1->var); // implementation gap
        $this->assertContains($entity2, $entity3->var);
    }

    public function testUnidirectionalRemove()
    {
        $entity1 = new OneToMany3();
        $entity2 = new OneToMany4();

        $entity1->var->add($entity2);

        static::$fixer->collectionRemove($entity1, 'var', $entity2);

        $this->assertNotContains($entity2, $entity1->var);
    }
}

/**
 * @ORM\Entity()
 */
class OneToMany1
{
    /**
     * @ORM\OneToMany(targetEntity="Fixrel\OneToMany2", mappedBy="var")
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
class OneToMany2
{
    /**
     * @ORM\ManyToOne(targetEntity="Fixrel\OneToMany1", inversedBy="var")
     */
    public $var;
}

/**
 * @ORM\Entity()
 */
class OneToMany3
{
    /**
     * @ORM\ManyToMany(targetEntity="Fixrel\OneToMany4")
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
class OneToMany4
{
}
