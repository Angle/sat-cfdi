<?php

namespace Angle\CFDI\Tests;

use Angle\CFDI\XmlValidator;
use PHPUnit\Framework\TestCase;

final class ValidatorTest extends TestCase
{
    public function testValidate(): void
    {
        $validator = new XmlValidator();

        //$xml = self::PATH . '/test-data/ONO9507278T4-ENH-314617.xml';
        $xml = __DIR__ . '/../test-data/invalid.xml';

        $validated = $validator->validateXmlFile($xml);

        if (!$validated) {
            $this->assertEquals($validated, false);
        } else {
            $this->fail('Expecting a negative validation');
        }


        $xml = __DIR__ . '/../test-data/QCS931209G49-A-94231073.xml';

        $validated = $validator->validateXmlFile($xml);

        if ($validated) {
            $this->assertEquals(
                $validated,
                true
            );
        } else {
            print_r($validator->getErrors());
            $this->fail('Expecting a positive validation');
            return;
        }
    }
}