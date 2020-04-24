<?php

namespace Angle\CFDI\Tests;

use Angle\CFDI\Catalog\RegimeType;
use Angle\CFDI\CFDI;
use Angle\CFDI\Catalog\CFDIType;
use Angle\CFDI\Catalog\CFDIUse;
use Angle\CFDI\Catalog\TaxType;
use Angle\CFDI\Catalog\TaxFactorType;
use Angle\CFDI\Catalog\PaymentType;
use Angle\CFDI\Catalog\PaymentMethod;

use PHPUnit\Framework\TestCase;

final class InvoiceTest extends TestCase
{
    public function testInvoiceCreationEnglish(): void
    {
        $data = [
            'version'           => CFDI::VERSION_3_3,
            'series'            => 'TEST',
            'folio'             => '12345',
            'date'              => new \DateTime('now'),
            'paymentMethod'     => PaymentMethod::TRANSFER,
            'paymentConditions' => null,
            'subTotal'          => 100.0,
            'discount'          => null,
            'currency'          => 'MXN', // todo: make this as a catalog
            'exchangeRate'      => null,
            'total'             => 116.0,
            'cfdiType'          => CFDIType::INCOME,
            'paymentType'       => PaymentType::SINGLE,
            'postalCode'        => '00200'
        ];

        // We're building an unsigned CFDI, but we cannot pass the fields as null, we'll write some filler data
        $data['signature']          = 'unsigned';
        $data['certificateNumber']  = 'unsigned';
        $data['certificate']        = 'unsigned';

        // Create the children nodes
        $data['issuer'] = [
            'rfc'       => 'XAXX010101000',
            'name'      => 'Test Issuer',
            'regime'    => RegimeType::SIN_OBLIGACIONES_FISCALES,
        ];

        $data['recipient'] = [
            'rfc'               => 'XAXX010101000',
            'name'              => 'Test Recipient',
            'foreignCountry'    => null,
            'foreignTaxCode'    => null,
            'cfdiUse'           => CFDIUse::GENERAL_EXPENSE,
        ];

        $data['relatedCFDIList'] = [
            'type' => '04',
            'related' => [
                [
                    'uuid' => '1234',
                ],
            ],
        ];

        $data['itemList'] = [
            'items' => [
                [
                    'code'          => '1234',
                    'quantity'      => 1.0,
                    'unitCode'      => 'XNA',
                    'description'   => 'Test item / product',
                    'unitPrice'     => 100.0,
                    'amount'        => 100.0,
                    'taxes' => [
                        'transferredList' => [
                            'transfers' => [
                                [
                                    'base' => '100.0',
                                    'tax' => TaxType::IVA,
                                    'factorType' => TaxFactorType::RATE,
                                    'rate' => '0.16',
                                    'amount' => '16',
                                ],
                            ]
                        ],
                        'retainedList' => [
                            'retentions' => []
                        ]
                    ]
                ],
            ]
        ];

        $data['complements'] = [
            [
                'localTaxes' => [
                    [
                        'transfers' => [
                            [
                                'tax' => 'DSA',
                                'rate' => '0.05',
                                'amount' => '5.00',
                            ],
                        ],
                        'retentions' => [
                            [
                                'tax' => 'ISH',
                                'rate' => '0.15',
                                'amount' => '15.00',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        echo "## Input Data Array ## " . PHP_EOL . PHP_EOL;
        print_r($data);
        echo PHP_EOL . PHP_EOL;

        try {
            $cfdi = new CFDI($data);
            $cfdi->autoCalculate();

        } catch (\Exception $e) {
            $this->fail($e->getMessage());
            return;
        }

        $this->assertInstanceOf(CFDI::class, $cfdi);

        echo "## Parsed CFDI Object ## " . PHP_EOL . PHP_EOL;
        print_r($cfdi);
        echo PHP_EOL . PHP_EOL;

        echo "## Output XML (reproduced) ## " . PHP_EOL . PHP_EOL;
        echo $cfdi->toXML();
        echo PHP_EOL . PHP_EOL;
    }
}