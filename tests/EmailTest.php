<?php

namespace Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Snow\StuWeb\unit\Email;

final class EmailTest extends TestCase
{
    public function testCanBeCreatedFromValidEmailAddress(): void
    {
        $this->assertInstanceOf(Email::class, Email::fromString('user@example.com'));
    }

    public function testCannotBeCreatedFromValidEmailAddress(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Email::fromString('invalid');
    }

    public function testCanBeUsedAsString(): void
    {
        $this->assertEquals('user@example.com', Email::fromString('user@example.com'));
    }
}