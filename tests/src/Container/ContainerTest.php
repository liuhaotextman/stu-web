<?php

namespace Tests\src\Container;

use PHPUnit\Framework\TestCase;
use Snow\StuWeb\Container\Container;

class ContainerTest extends TestCase
{
    protected Container $container;

    public function setUp(): void
    {
        parent::setUp();
        $this->container = new Container();
    }

    public function testBindObject()
    {
        $this->container->bind('container', $this->container);
        $this->assertSame($this->container->get('container'), $this->container);
    }

    public function testBindClosure()
    {
        $this->container->bind('closure', function () {
            return 'closure success';
        });
        $this->assertEquals($this->container->get('closure'), 'closure success');
    }

    public function testBindClass()
    {
        $this->container->bind('container', Container::class);
        $this->assertInstanceOf(Container::class, $this->container->get('container'));
    }

    public function testBindArray()
    {
        $this->expectExceptionMessage('concrete type error');

        $this->container->bind('container', ['container' => Container::class]);
    }

    public function testInstanceWithoutDependent()
    {
        $this->container->instance('container', $this->container);
        $this->assertSame($this->container->get('container'), $this->container);

        $this->container->instance('boo', 'foo');
        $this->assertEquals($this->container->get('boo'), 'foo');
    }

    public function testInstanceWithOneDependent()
    {

    }
}