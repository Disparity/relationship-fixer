<?php

namespace Fixrel\Tests;

use Fixrel\Fixer;
use Fixrel\Metadata\ClassMetadataFactoryInterface;
use Fixrel\Metadata\DoctrineProxyLoader;
use Fixrel\Metadata\PropertyMetadataFactory;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Fixrel\Tests\Fixtures\ManyToMany1;
use Fixrel\Tests\Fixtures\ManyToMany2;
use Fixrel\Tests\Fixtures\ManyToMany3;
use Fixrel\Tests\Fixtures\ManyToMany4;
use PHPUnit\Framework\TestCase;

class ManyToManyTest extends TestCase
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
    public function testBidirectionalAdd(PropertyMetadataFactory $metadataFactory)
    {
        $fixer = new Fixer($metadataFactory);

        $entity1 = new ManyToMany1();
        $entity2 = new ManyToMany2();
        $entity3 = new ManyToMany1();
        $entity4 = new ManyToMany2();

        $entity1->var->add($entity2);
        $entity2->var->add($entity1);
        $entity3->var->add($entity4);
        $entity4->var->add($entity3);

        $fixer->collectionAdd($entity3, 'var', $entity2);

        $this->assertContains($entity4, $entity3->var);
        $this->assertContains($entity2, $entity3->var);
        $this->assertContains($entity3, $entity2->var);
        $this->assertContains($entity1, $entity2->var);
    }

    /**
     * @dataProvider metadataProvider
     * @param PropertyMetadataFactory $metadataFactory
     */
    public function testBidirectionalInverseAdd(PropertyMetadataFactory $metadataFactory)
    {
        $fixer = new Fixer($metadataFactory);

        $entity1 = new ManyToMany1();
        $entity2 = new ManyToMany2();
        $entity3 = new ManyToMany1();
        $entity4 = new ManyToMany2();

        $entity1->var->add($entity2);
        $entity2->var->add($entity1);
        $entity3->var->add($entity4);
        $entity4->var->add($entity3);

        $fixer->collectionAdd($entity2, 'var', $entity3);

        $this->assertContains($entity4, $entity3->var);
        $this->assertContains($entity2, $entity3->var);
        $this->assertContains($entity3, $entity2->var);
        $this->assertContains($entity1, $entity2->var);
    }

    /**
     * @dataProvider metadataProvider
     * @param PropertyMetadataFactory $metadataFactory
     */
    public function testBidirectionalRemove(PropertyMetadataFactory $metadataFactory)
    {
        $fixer = new Fixer($metadataFactory);

        $entity1 = new ManyToMany1();
        $entity2 = new ManyToMany2();

        $entity1->var->add($entity2);
        $entity2->var->add($entity1);

        $fixer->collectionRemove($entity1, 'var', $entity2);

        $this->assertNotContains($entity1, $entity2->var);
        $this->assertNotContains($entity2, $entity1->var);
    }

    /**
     * @dataProvider metadataProvider
     * @param PropertyMetadataFactory $metadataFactory
     */
    public function testBidirectionalRemoveInverse(PropertyMetadataFactory $metadataFactory)
    {
        $fixer = new Fixer($metadataFactory);

        $entity1 = new ManyToMany1();
        $entity2 = new ManyToMany2();

        $entity1->var->add($entity2);
        $entity2->var->add($entity1);

        $fixer->collectionRemove($entity2, 'var', $entity1);

        $this->assertNotContains($entity1, $entity2->var);
        $this->assertNotContains($entity2, $entity1->var);
    }

    /**
     * @dataProvider metadataProvider
     * @param PropertyMetadataFactory $metadataFactory
     */
    public function testUnidirectionalAdd(PropertyMetadataFactory $metadataFactory)
    {
        $fixer = new Fixer($metadataFactory);

        $entity1 = new ManyToMany3();
        $entity2 = new ManyToMany4();
        $entity3 = new ManyToMany3();
        $entity4 = new ManyToMany4();

        $entity1->var->add($entity2);
        $entity3->var->add($entity4);

        $fixer->collectionAdd($entity3, 'var', $entity2);

        $this->assertContains($entity4, $entity3->var);
        $this->assertContains($entity2, $entity3->var);
        $this->assertContains($entity2, $entity1->var);
    }

    /**
     * @dataProvider metadataProvider
     * @param PropertyMetadataFactory $metadataFactory
     */
    public function testUnidirectionalRemove(PropertyMetadataFactory $metadataFactory)
    {
        $fixer = new Fixer($metadataFactory);

        $entity1 = new ManyToMany3();
        $entity2 = new ManyToMany4();

        $entity1->var->add($entity2);

        $fixer->collectionRemove($entity1, 'var', $entity2);

        $this->assertNotContains($entity2, $entity1->var);
    }

    /**
     * @dataProvider metadataProvider
     * @param PropertyMetadataFactory $metadataFactory
     */
    public function testBidirectionalAddTwice(PropertyMetadataFactory $metadataFactory)
    {
        $fixer = new Fixer($metadataFactory);

        $entity1 = new ManyToMany1();
        $entity2 = new ManyToMany2();

        $fixer->collectionAdd($entity1, 'var', $entity2);
        $result = $fixer->collectionAdd($entity2, 'var', $entity1);

        $this->assertContains($entity1, $entity2->var);
        $this->assertContains($entity2, $entity1->var);
        $this->assertFalse($result);
    }

    /**
     * @dataProvider metadataProvider
     * @param PropertyMetadataFactory $metadataFactory
     */
    public function testBidirectionalRemoveTwice(PropertyMetadataFactory $metadataFactory)
    {
        $fixer = new Fixer($metadataFactory);

        $entity1 = new ManyToMany1();
        $entity2 = new ManyToMany2();

        $entity1->var->add($entity2);
        $entity2->var->add($entity1);

        $fixer->collectionRemove($entity2, 'var', $entity1);
        $result = $fixer->collectionRemove($entity2, 'var', $entity1);

        $this->assertNotContains($entity1, $entity2->var);
        $this->assertNotContains($entity2, $entity1->var);
        $this->assertFalse($result);
    }
}
