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
use PHPUnit\Framework\TestCase;

class ManyToOneTest extends TestCase
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
        $entity1 = new ManyToOne1();
        $entity2 = new ManyToOne2();
        $entity3 = new ManyToOne2();

        $entity1->var = $entity2;
        $entity2->var->add($entity1);

        static::$fixer->assign($entity1, 'var', $entity3);

        $this->assertContains($entity1, $entity3->var);
        $this->assertNotContains($entity1, $entity2->var);
        $this->assertEquals($entity3, $entity1->var);
    }

    public function testBidirectionalWithNull()
    {
        $entity1 = new ManyToOne1();
        $entity2 = new ManyToOne2();

        static::$fixer->assign($entity1, 'var', $entity2);

        $this->assertContains($entity1, $entity2->var);
        $this->assertEquals($entity2, $entity1->var);
    }

    public function testUnidirectional()
    {
        $entity1 = new ManyToOne3();
        $entity2 = new ManyToOne4();
        $entity3 = new ManyToOne4();

        $entity1->var = $entity2;

        static::$fixer->assign($entity1, 'var', $entity3);

        $this->assertEquals($entity3, $entity1->var);
    }

    public function testUnidirectionalWithNull()
    {
        $entity1 = new ManyToOne3();
        $entity2 = new ManyToOne4();

        static::$fixer->assign($entity1, 'var', $entity2);

        $this->assertEquals($entity2, $entity1->var);
    }

    public function testBidirectionalWithEqualValues()
    {
        $entity1 = new ManyToOne1();
        $entity2 = new ManyToOne2();

        $mockedCollection = $this->getMockBuilder(ArrayCollection::class)->setConstructorArgs([[$entity1]])->setMethods(['add', 'removeElement'])->getMock();
        $entity2->var = $mockedCollection;
        $entity1->var = $entity2;

        $mockedCollection->expects($this->never())->method('add');
        $mockedCollection->expects($this->never())->method('removeElement');

        static::$fixer->assign($entity1, 'var', $entity2);
    }
}

/**
 * @ORM\Entity()
 */
class ManyToOne1
{
    /**
     * @ORM\ManyToOne(targetEntity="Disparity\Relationship\ManyToOne2", inversedBy="var")
     */
    public $var;
}

/**
 * @ORM\Entity()
 */
class ManyToOne2
{
    /**
     * @ORM\OneToMany(targetEntity="Disparity\Relationship\ManyToOne1", mappedBy="var")
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
class ManyToOne3
{
    /**
     * @ORM\ManyToOne(targetEntity="Disparity\Relationship\ManyToOne4")
     */
    public $var;
}

/**
 * @ORM\Entity()
 */
class ManyToOne4
{
}
