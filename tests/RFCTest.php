<?php

namespace Angle\CFDI\Tests;

use Angle\CFDI\Utility\RFC;
use PHPUnit\Framework\TestCase;

final class RFCTest extends TestCase
{
    public function testRfc(): void
    {
        // 1. Generic National
        $raw = "XAXX010101000";
        $rfc = RFC::createFromString($raw);

        $this->assertInstanceOf(RFC::class, $rfc);
        $this->assertEquals(true, $rfc->isGenericNational());
        $this->assertEquals(false, $rfc->isGenericForeign());
        $this->assertEquals(false, $rfc->isNaturalPerson());
        $this->assertEquals(false, $rfc->isLegalEntity());


        // 2. Generic Foreign
        $raw = "XEXX010101000";
        $rfc = RFC::createFromString($raw);

        $this->assertInstanceOf(RFC::class, $rfc);
        $this->assertEquals(false, $rfc->isGenericNational());
        $this->assertEquals(true, $rfc->isGenericForeign());
        $this->assertEquals(false, $rfc->isNaturalPerson());
        $this->assertEquals(false, $rfc->isLegalEntity());


        // 3. Natural Person
        $raw = "VECJ880326180";
        $rfc = RFC::createFromString($raw);

        $this->assertInstanceOf(RFC::class, $rfc);
        $this->assertEquals(false, $rfc->isGenericNational());
        $this->assertEquals(false, $rfc->isGenericForeign());
        $this->assertEquals(true, $rfc->isNaturalPerson());
        $this->assertEquals(false, $rfc->isLegalEntity());

        $raw = "vecj-880326-qt0";
        $rfc = RFC::createFromString($raw);

        $this->assertInstanceOf(RFC::class, $rfc);
        $this->assertEquals(false, $rfc->isGenericNational());
        $this->assertEquals(false, $rfc->isGenericForeign());
        $this->assertEquals(true, $rfc->isNaturalPerson());
        $this->assertEquals(false, $rfc->isLegalEntity());


        // 4. Legal Entity
        $raw = "ABC680524P76";
        $rfc = RFC::createFromString($raw);

        $this->assertInstanceOf(RFC::class, $rfc);
        $this->assertEquals(false, $rfc->isGenericNational());
        $this->assertEquals(false, $rfc->isGenericForeign());
        $this->assertEquals(false, $rfc->isNaturalPerson());
        $this->assertEquals(true, $rfc->isLegalEntity());

        $raw = "abc680524p76";
        $rfc = RFC::createFromString($raw);

        $this->assertInstanceOf(RFC::class, $rfc);
        $this->assertEquals(false, $rfc->isGenericNational());
        $this->assertEquals(false, $rfc->isGenericForeign());
        $this->assertEquals(false, $rfc->isNaturalPerson());
        $this->assertEquals(true, $rfc->isLegalEntity());

        // 5. Invalid RFCs
        $raw = "a1c680524p76";
        $rfc = RFC::createFromString($raw);
        $this->assertEquals(null, $rfc);

        $raw = "";
        $rfc = RFC::createFromString($raw);
        $this->assertEquals(null, $rfc);


        $raw = "a1c6805241231p76";
        $rfc = RFC::createFromString($raw);
        $this->assertEquals(null, $rfc);
    }
}