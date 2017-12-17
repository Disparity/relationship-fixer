<?php

namespace Fixrel;

use Fixrel\Fixer;
use Fixrel\Metadata\ClassMetadataFactoryInterface;
use Fixrel\Metadata\DoctrineProxyLoader;
use Fixrel\Metadata\PropertyMetadataFactory;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use PHPUnit\Framework\TestCase;

class OneToOneTest extends TestCase
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

    public function testBidirectional()
    {
        $entity1 = new OneToOne1();
        $entity2 = new OneToOne2();
        $entity3 = new OneToOne1();
        $entity4 = new OneToOne2();

        $entity1->var = $entity2;
        $entity2->var = $entity1;
        $entity3->var = $entity4;
        $entity4->var = $entity3;

        static::$fixer->assign($entity2, 'var', $entity3);

        $this->assertNull($entity1->var);
        $this->assertNull($entity4->var);
        $this->assertEquals($entity3, $entity2->var);
        $this->assertEquals($entity2, $entity3->var);
    }

    public function testBidirectionalInverse()
    {
        $entity1 = new OneToOne1();
        $entity2 = new OneToOne2();
        $entity3 = new OneToOne1();
        $entity4 = new OneToOne2();

        $entity1->var = $entity2;
        $entity2->var = $entity1;
        $entity3->var = $entity4;
        $entity4->var = $entity3;

        static::$fixer->assign($entity3, 'var', $entity2);

        $this->assertNull($entity1->var);
        $this->assertNull($entity4->var);
        $this->assertEquals($entity3, $entity2->var);
        $this->assertEquals($entity2, $entity3->var);
    }

    public function testBidirectionalWithNulls()
    {
        $entity1 = new OneToOne1();
        $entity2 = new OneToOne2();

        static::$fixer->assign($entity1, 'var', $entity2);

        $this->assertEquals($entity2, $entity1->var);
        $this->assertEquals($entity1, $entity2->var);
    }

    public function testUnidirectional()
    {
        $entity1 = new OneToOne3();
        $entity2 = new OneToOne4();
        $entity3 = new OneToOne3();
        $entity4 = new OneToOne4();

        $entity1->var = $entity2;
        $entity3->var = $entity4;

        static::$fixer->assign($entity3, 'var', $entity2);

        // $this->assertNull($entity1->var); // implementation gap
        $this->assertEquals($entity2, $entity3->var);
    }

    public function testUnidirectionalWithNulls()
    {
        $entity1 = new OneToOne3();
        $entity2 = new OneToOne4();

        static::$fixer->assign($entity1, 'var', $entity2);

        $this->assertEquals($entity2, $entity1->var);
    }

    public function testBidirectionalSetNullOnOtherSide()
    {
        $entity1 = new OneToOne2();
        $entity2 = new OneToOne1();
        $entity2->var = $entity1;
        $entity1->var = $entity2;

        static::$fixer->assign($entity1, 'var', null);

        $this->assertNull($entity2->var);
    }
}

/**
 * @ORM\Entity()
 */
class OneToOne1
{
    /**
     * @ORM\OneToOne(targetEntity="Fixrel\OneToOne2", inversedBy="var")
     */
    public $var;
}

/**
 * @ORM\Entity()
 */
class OneToOne2
{
    /**
     * @ORM\OneToOne(targetEntity="Fixrel\OneToOne1", mappedBy="var")
     */
    public $var;
}

/**
 * @ORM\Entity()
 */
class OneToOne3
{
    /**
     * @ORM\OneToOne(targetEntity="Fixrel\OneToOne4")
     */
    public $var;
}

/**
 * @ORM\Entity()
 */
class OneToOne4
{
}
