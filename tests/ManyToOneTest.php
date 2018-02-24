<?php

namespace Fixrel\Tests;

use Fixrel\Exception\UnexpectedAssociationTypeException;
use Fixrel\Fixer;
use Fixrel\Metadata\ClassMetadataFactoryInterface;
use Fixrel\Metadata\DoctrineProxyLoader;
use Fixrel\Metadata\PropertyMetadataFactory;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Fixrel\Tests\Fixtures\ManyToOne1;
use Fixrel\Tests\Fixtures\ManyToOne2;
use Fixrel\Tests\Fixtures\ManyToOne3;
use Fixrel\Tests\Fixtures\ManyToOne4;
use PHPUnit\Framework\TestCase;

class ManyToOneTest extends TestCase
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

        $entity1 = new ManyToOne1();
        $entity2 = new ManyToOne2();
        $entity3 = new ManyToOne2();

        $entity1->var = $entity2;
        $entity2->var->add($entity1);

        $fixer->assign($entity1, 'var', $entity3);

        $this->assertContains($entity1, $entity3->var);
        $this->assertNotContains($entity1, $entity2->var);
        $this->assertEquals($entity3, $entity1->var);
    }

    /**
     * @dataProvider metadataProvider
     * @param PropertyMetadataFactory $metadataFactory
     */
    public function testBidirectionalWithNull(PropertyMetadataFactory $metadataFactory)
    {
        $fixer = new Fixer($metadataFactory);

        $entity1 = new ManyToOne1();
        $entity2 = new ManyToOne2();

        $fixer->assign($entity1, 'var', $entity2);

        $this->assertContains($entity1, $entity2->var);
        $this->assertEquals($entity2, $entity1->var);
    }

    /**
     * @dataProvider metadataProvider
     * @param PropertyMetadataFactory $metadataFactory
     */
    public function testUnidirectional(PropertyMetadataFactory $metadataFactory)
    {
        $fixer = new Fixer($metadataFactory);

        $entity1 = new ManyToOne3();
        $entity2 = new ManyToOne4();
        $entity3 = new ManyToOne4();

        $entity1->var = $entity2;

        $fixer->assign($entity1, 'var', $entity3);

        $this->assertEquals($entity3, $entity1->var);
    }

    /**
     * @dataProvider metadataProvider
     * @param PropertyMetadataFactory $metadataFactory
     */
    public function testUnidirectionalWithNull(PropertyMetadataFactory $metadataFactory)
    {
        $fixer = new Fixer($metadataFactory);

        $entity1 = new ManyToOne3();
        $entity2 = new ManyToOne4();

        $fixer->assign($entity1, 'var', $entity2);

        $this->assertEquals($entity2, $entity1->var);
    }

    /**
     * @dataProvider metadataProvider
     * @param PropertyMetadataFactory $metadataFactory
     */
    public function testBidirectionalWithEqualValues(PropertyMetadataFactory $metadataFactory)
    {
        $fixer = new Fixer($metadataFactory);

        $entity1 = new ManyToOne1();
        $entity2 = new ManyToOne2();

        $mockedCollection = $this->getMockBuilder(ArrayCollection::class)->setConstructorArgs([[$entity1]])->setMethods(['add', 'removeElement'])->getMock();
        $entity2->var = $mockedCollection;
        $entity1->var = $entity2;

        $mockedCollection->expects($this->never())->method('add');
        $mockedCollection->expects($this->never())->method('removeElement');

        $fixer->assign($entity1, 'var', $entity2);
    }

    /**
     * @dataProvider metadataProvider
     * @param PropertyMetadataFactory $metadataFactory
     */
    public function testBidirectionalAssignCollection(PropertyMetadataFactory $metadataFactory)
    {
        $fixer = new Fixer($metadataFactory);

        $entity1 = new ManyToOne1();
        $entity2 = new ManyToOne2();

        $this->expectException(UnexpectedAssociationTypeException::class);
        $fixer->assign($entity2, 'var', new ArrayCollection([$entity1]));
    }
}
