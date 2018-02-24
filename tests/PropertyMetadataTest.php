<?php

namespace Fixrel\Tests;

use Fixrel\Exception\UndefinedAssociationException;
use Fixrel\Fixer;
use Fixrel\Metadata\ClassMetadataFactoryInterface;
use Fixrel\Metadata\DoctrineProxyLoader;
use Fixrel\Metadata\NullProperty;
use Fixrel\Metadata\Property;
use Fixrel\Metadata\PropertyMetadataFactory;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Fixrel\Tests\Fixtures\ManyToMany1;
use Fixrel\Tests\Fixtures\ManyToMany2;
use Fixrel\Tests\Fixtures\ManyToMany3;
use Fixrel\Tests\Fixtures\ManyToMany4;
use Fixrel\Tests\Fixtures\ManyToOne1;
use Fixrel\Tests\Fixtures\ManyToOne2;
use Fixrel\Tests\Fixtures\ManyToOne3;
use Fixrel\Tests\Fixtures\OneToMany1;
use Fixrel\Tests\Fixtures\OneToMany2;
use Fixrel\Tests\Fixtures\OneToMany3;
use Fixrel\Tests\Fixtures\OneToOne1;
use Fixrel\Tests\Fixtures\OneToOne2;
use Fixrel\Tests\Fixtures\OneToOne3;
use PHPUnit\Framework\TestCase;

class PropertyMetadataTest extends TestCase
{
    public function classMetadataProvider()
    {
        $classMetadataFactory = new class implements ClassMetadataFactoryInterface {
            public function getMetadataFor($className)
            {
                $classMetadata = new ORM\ClassMetadata($className, new ORM\UnderscoreNamingStrategy());
                (new AnnotationDriver(new AnnotationReader()))->loadMetadataForClass($className, $classMetadata);

                return $classMetadata;
            }
        };

        return [
            [$classMetadataFactory, ManyToMany1::class, [Property::class, true], [Property::class, true]],
            [$classMetadataFactory, ManyToMany2::class, [Property::class, true], [Property::class, true]],
            [$classMetadataFactory, ManyToMany3::class, [Property::class, true], [NullProperty::class, true]],

            [$classMetadataFactory, ManyToOne1::class, [Property::class, false], [Property::class, true]],
            [$classMetadataFactory, ManyToOne2::class, [Property::class, true], [Property::class, false]],
            [$classMetadataFactory, ManyToOne3::class, [Property::class, false], [NullProperty::class, true]],

            [$classMetadataFactory, OneToMany1::class, [Property::class, true], [Property::class, false]],
            [$classMetadataFactory, OneToMany2::class, [Property::class, false], [Property::class, true]],
            [$classMetadataFactory, OneToMany3::class, [Property::class, true], [NullProperty::class, true]],

            [$classMetadataFactory, OneToOne1::class, [Property::class, false], [Property::class, false]],
            [$classMetadataFactory, OneToOne2::class, [Property::class, false], [Property::class, false]],
            [$classMetadataFactory, OneToOne3::class, [Property::class, false], [NullProperty::class, true]],
        ];
    }

    /**
     * @dataProvider classMetadataProvider
     * @param ClassMetadataFactoryInterface $classMetadataFactory
     * @param string $className
     * @param array $property [className: string, isCollection: bool]
     * @param array $inversedProperty [className: string, isCollection: bool]
     */
    public function testPropertyMetadata(ClassMetadataFactoryInterface $classMetadataFactory, $className, $property, $inversedProperty)
    {
        $propertyName = 'var';
        $metadataFactory = new PropertyMetadataFactory($classMetadataFactory, new DoctrineProxyLoader());

        $actual = $metadataFactory->getMetadataFor($className, $propertyName);

        $this->assertInstanceOf($property[0], $actual);
        $this->assertEquals($property[1], $actual->isCollection());

        $this->assertInstanceOf($inversedProperty[0], $actual->getInversedProperty());
        $this->assertEquals($inversedProperty[1], $actual->getInversedProperty()->isCollection());
    }

    public function testUndefinedPropertyMetadata()
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

        $this->expectException(UndefinedAssociationException::class);
        $metadataFactory->getMetadataFor(ManyToMany1::class, 'undefinedVar');
    }
}
