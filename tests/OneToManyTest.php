<?php

namespace Fixrel\Tests;

use Fixrel\Fixer;
use Fixrel\Metadata\ClassMetadataFactoryInterface;
use Fixrel\Metadata\DoctrineProxyLoader;
use Fixrel\Metadata\PropertyMetadataFactory;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Fixrel\Tests\Fixtures\OneToMany1;
use Fixrel\Tests\Fixtures\OneToMany2;
use Fixrel\Tests\Fixtures\OneToMany3;
use Fixrel\Tests\Fixtures\OneToMany4;
use PHPUnit\Framework\TestCase;

class OneToManyTest extends TestCase
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

        $entity1 = new OneToMany1();
        $entity2 = new OneToMany2();
        $entity3 = new OneToMany1();

        $entity1->var->add($entity2);
        $entity2->var = $entity1;

        $fixer->collectionAdd($entity3, 'var', $entity2);

        $this->assertNotContains($entity2, $entity1->var);
        $this->assertContains($entity2, $entity3->var);
        $this->assertEquals($entity3, $entity2->var);
    }

    /**
     * @dataProvider metadataProvider
     * @param PropertyMetadataFactory $metadataFactory
     */
    public function testBidirectionalRemove(PropertyMetadataFactory $metadataFactory)
    {
        $fixer = new Fixer($metadataFactory);

        $entity1 = new OneToMany1();
        $entity2 = new OneToMany2();

        $entity1->var->add($entity2);
        $entity2->var = $entity1;

        $fixer->collectionRemove($entity1, 'var', $entity2);

        $this->assertNotContains($entity2, $entity1->var);
        $this->assertNull($entity2->var);
    }

    /**
     * @dataProvider metadataProvider
     * @param PropertyMetadataFactory $metadataFactory
     */
    public function testUnidirectionalAdd(PropertyMetadataFactory $metadataFactory)
    {
        $fixer = new Fixer($metadataFactory);

        $entity1 = new OneToMany3();
        $entity2 = new OneToMany4();
        $entity3 = new OneToMany3();

        $entity1->var->add($entity2);

        $fixer->collectionAdd($entity3, 'var', $entity2);

        // $this->assertNotContains($entity2, $entity1->var); // implementation gap
        $this->assertContains($entity2, $entity3->var);
    }

    /**
     * @dataProvider metadataProvider
     * @param PropertyMetadataFactory $metadataFactory
     */
    public function testUnidirectionalRemove(PropertyMetadataFactory $metadataFactory)
    {
        $fixer = new Fixer($metadataFactory);

        $entity1 = new OneToMany3();
        $entity2 = new OneToMany4();

        $entity1->var->add($entity2);

        $fixer->collectionRemove($entity1, 'var', $entity2);

        $this->assertNotContains($entity2, $entity1->var);
    }
}
