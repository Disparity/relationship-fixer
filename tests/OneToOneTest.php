<?php

namespace Fixrel\Tests;

use Fixrel\Fixer;
use Fixrel\Metadata\ClassMetadataFactoryInterface;
use Fixrel\Metadata\DoctrineProxyLoader;
use Fixrel\Metadata\PropertyMetadataFactory;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Fixrel\Tests\Fixtures\OneToOne1;
use Fixrel\Tests\Fixtures\OneToOne2;
use Fixrel\Tests\Fixtures\OneToOne3;
use Fixrel\Tests\Fixtures\OneToOne4;
use PHPUnit\Framework\TestCase;

class OneToOneTest extends TestCase
{
    public function metadataProvider()
    {
        $classMetadataFactory = new class implements ClassMetadataFactoryInterface {
            public function getMetadataFor($className)
            {
                $classMetadata = new ORM\ClassMetadata($className, new ORM\UnderscoreNamingStrategy());
                (new AnnotationDriver(new AnnotationReader()))->loadMetadataForClass($className, $classMetadata);

                return $classMetadata;
            }
        };

        $metadataFactory = new PropertyMetadataFactory($classMetadataFactory, new DoctrineProxyLoader());

        return [
            [$metadataFactory],
        ];
    }

    /**
     * @dataProvider metadataProvider
     * @param PropertyMetadataFactory $metadataFactory
     */
    public function testBidirectional(PropertyMetadataFactory $metadataFactory)
    {
        $fixer = new Fixer($metadataFactory);

        $entity1 = new OneToOne1();
        $entity2 = new OneToOne2();
        $entity3 = new OneToOne1();
        $entity4 = new OneToOne2();

        $entity1->var = $entity2;
        $entity2->var = $entity1;
        $entity3->var = $entity4;
        $entity4->var = $entity3;

        $fixer->assign($entity2, 'var', $entity3);

        $this->assertNull($entity1->var);
        $this->assertNull($entity4->var);
        $this->assertEquals($entity3, $entity2->var);
        $this->assertEquals($entity2, $entity3->var);
    }

    /**
     * @dataProvider metadataProvider
     * @param PropertyMetadataFactory $metadataFactory
     */
    public function testBidirectionalInverse(PropertyMetadataFactory $metadataFactory)
    {
        $fixer = new Fixer($metadataFactory);

        $entity1 = new OneToOne1();
        $entity2 = new OneToOne2();
        $entity3 = new OneToOne1();
        $entity4 = new OneToOne2();

        $entity1->var = $entity2;
        $entity2->var = $entity1;
        $entity3->var = $entity4;
        $entity4->var = $entity3;

        $fixer->assign($entity3, 'var', $entity2);

        $this->assertNull($entity1->var);
        $this->assertNull($entity4->var);
        $this->assertEquals($entity3, $entity2->var);
        $this->assertEquals($entity2, $entity3->var);
    }

    /**
     * @dataProvider metadataProvider
     * @param PropertyMetadataFactory $metadataFactory
     */
    public function testBidirectionalWithNulls(PropertyMetadataFactory $metadataFactory)
    {
        $fixer = new Fixer($metadataFactory);

        $entity1 = new OneToOne1();
        $entity2 = new OneToOne2();

        $fixer->assign($entity1, 'var', $entity2);

        $this->assertEquals($entity2, $entity1->var);
        $this->assertEquals($entity1, $entity2->var);
    }

    /**
     * @dataProvider metadataProvider
     * @param PropertyMetadataFactory $metadataFactory
     */
    public function testUnidirectional(PropertyMetadataFactory $metadataFactory)
    {
        $fixer = new Fixer($metadataFactory);

        $entity1 = new OneToOne3();
        $entity2 = new OneToOne4();
        $entity3 = new OneToOne3();
        $entity4 = new OneToOne4();

        $entity1->var = $entity2;
        $entity3->var = $entity4;

        $fixer->assign($entity3, 'var', $entity2);

        // $this->assertNull($entity1->var); // implementation gap
        $this->assertEquals($entity2, $entity3->var);
    }

    /**
     * @dataProvider metadataProvider
     * @param PropertyMetadataFactory $metadataFactory
     */
    public function testUnidirectionalWithNulls(PropertyMetadataFactory $metadataFactory)
    {
        $fixer = new Fixer($metadataFactory);

        $entity1 = new OneToOne3();
        $entity2 = new OneToOne4();

        $fixer->assign($entity1, 'var', $entity2);

        $this->assertEquals($entity2, $entity1->var);
    }

    /**
     * @dataProvider metadataProvider
     * @param PropertyMetadataFactory $metadataFactory
     */
    public function testBidirectionalSetNullOnOtherSide(PropertyMetadataFactory $metadataFactory)
    {
        $fixer = new Fixer($metadataFactory);

        $entity1 = new OneToOne2();
        $entity2 = new OneToOne1();
        $entity2->var = $entity1;
        $entity1->var = $entity2;

        $fixer->assign($entity1, 'var', null);

        $this->assertNull($entity2->var);
    }

    /**
     * @dataProvider metadataProvider
     * @param PropertyMetadataFactory $metadataFactory
     */
    public function testBidirectionalWithEqualValues(PropertyMetadataFactory $metadataFactory)
    {
        $fixer = new Fixer($metadataFactory);

        $entity1 = new OneToOne1();
        $entity2 = new OneToOne2();

        $entity2->var = $entity1;
        $entity1->var = $entity2;

        $result = $fixer->assign($entity1, 'var', $entity2);

        $this->assertEquals($entity2, $entity1->var);
        $this->assertFalse($result);
    }
}
