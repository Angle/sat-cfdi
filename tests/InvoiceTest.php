<?php

namespace Angle\CFDI\Tests;

use Angle\CFDI\Catalog\CFDIType;
use Angle\CFDI\Catalog\CFDIUse;
use Angle\CFDI\Catalog\PaymentMethod;
use Angle\CFDI\Catalog\PaymentType;
use Angle\CFDI\Catalog\RegimeType;
use Angle\CFDI\Catalog\TaxFactorType;
use Angle\CFDI\Catalog\TaxType;
use Angle\CFDI\Node\CFDI33\CFDI33;
use Angle\CFDI\Node\CFDI40\CFDI40;
use Angle\CFDI\Node\CFDI40\Complement;
use Angle\CFDI\Node\Complement\Payment20\Payment;
use Angle\CFDI\Node\Complement\Payment20\Payments;
use Angle\CFDI\Node\Complement\Payment20\RelatedDocument;
use Angle\CFDI\Node\Complement\Payment20\RelatedDocumentTaxes;
use Angle\CFDI\Node\Complement\Payment20\RelatedDocumentTaxesRetained;
use Angle\CFDI\Node\Complement\Payment20\RelatedDocumentTaxesRetainedList;
use Angle\CFDI\Node\Complement\Payment20\RelatedDocumentTaxesTransferred;
use Angle\CFDI\Node\Complement\Payment20\RelatedDocumentTaxesTransferredList;
use PHPUnit\Framework\TestCase;

final class InvoiceTest extends TestCase
{
    public function testInvoiceCreationEnglish(): void
    {
        $data = [
            'version' => CFDI33::VERSION_3_3,
            'series' => 'TEST',
            'folio' => '12345',
            'date' => new \DateTime('now'),
            'paymentMethod' => PaymentMethod::TRANSFER,
            'paymentConditions' => null,
            'subTotal' => 100.0,
            'discount' => null,
            'currency' => 'MXN', // todo: make this as a catalog
            'exchangeRate' => null,
            'total' => 116.0,
            'cfdiType' => CFDIType::INCOME,
            'paymentType' => PaymentType::SINGLE,
            'postalCode' => '00200'
        ];

        // We're building an unsigned CFDI, but we cannot pass the fields as null, we'll write some filler data
        $data['signature'] = 'unsigned';
        $data['certificateNumber'] = 'unsigned';
        $data['certificate'] = 'unsigned';

        // Create the children nodes
        $data['issuer'] = [
            'rfc' => 'XAXX010101000',
            'name' => 'Test Issuer',
            'regime' => RegimeType::SIN_OBLIGACIONES_FISCALES,
        ];

        $data['recipient'] = [
            'rfc' => 'XAXX010101000',
            'name' => 'Test Recipient',
            'foreignCountry' => null,
            'foreignTaxCode' => null,
            'cfdiUse' => CFDIUse::GENERAL_EXPENSE,
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
                    'code' => '1234',
                    'quantity' => 1.0,
                    'unitCode' => 'XNA',
                    'description' => 'Test item / product',
                    'unitPrice' => 100.0,
                    'amount' => 100.0,
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
            $cfdi = new CFDI33($data);
            $cfdi->autoCalculate();
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
            return;
        }

        $this->assertInstanceOf(CFDI33::class, $cfdi);

        echo "## Parsed CFDI Object ## " . PHP_EOL . PHP_EOL;
        print_r($cfdi);
        echo PHP_EOL . PHP_EOL;

        echo "## Output XML (reproduced) ## " . PHP_EOL . PHP_EOL;
        echo $cfdi->toXML();
        echo PHP_EOL . PHP_EOL;
    }

    public function testComplementPayments(): void
    {
        $data = [
            'version' => CFDI40::VERSION_4_0,
            'series' => 'TEST',
            'folio' => '1',
            'date' => new \DateTime('now', new \DateTimeZone('America/Monterrey')),
            'paymentMethod' => null,
            'paymentConditions' => null,
            'subTotal' => 0.00,
            'discount' => 0.00,
            'currency' => 'XXX',
            'exchangeRate' => null,
            'total' => 0.00,
            'cfdiType' => 'P',
            'paymentType' => null,
            'postalCode' => '06000',
            'signature' => 'unsigned',
            'certificateNumber' => 'unsigned',
            'certificate' => 'unsigned',
            'export' => '01',
            'issuer' => [
                'rfc' => 'XAXX010101000',
                'name' => 'Test Issuer',
                'regime' => RegimeType::SIN_OBLIGACIONES_FISCALES,
            ],
            'recipient' => [
                'rfc' => 'XAXX010101000',
                'name' => 'Test Recipient',
                'foreignCountry' => null,
                'foreignTaxCode' => null,
                'regime' => '601',
                'cfdiUse' => CFDIUse::PAYMENTS,
                'postalCode' => '64920',
            ],
            'itemList' => [
                'items' => [
                    [
                        'code' => 84111506,
                        'quantity' => 1.00000,
                        'unitCode' => 'ACT',
                        'description' => 'Pago',
                        'unitPrice' => 0.00000,
                        'amount' => 0.00000,
                        'operationTaxable' => "01",
                        'discount' => 0.00000,
                    ]
                ],
            ],
            Complement::NODE_NAME_EN => [
                Complement::NODE_NAME_EN => [
                    Payments::NODE_NAME_EN => [
                        Payment::NODE_NAME_EN => [
                            [
                                'date' => new \DateTime('now', new \DateTimeZone('America/Monterrey')),
                                'paymentMethod' => '01',
                                'currency' => 'MXN',
                                'exchangeRate' => '1.00000',
                                'amount' => '10000.00000',
                                'transactionNumber' => null,
                                'taxes' => [],
                                RelatedDocument::NODE_NAME_EN =>
                                [
                                    [
                                        'id' => 'XXXXXXXX-1111-2222-3333-XXXXXXXXXXXX',
                                        'series' => '2',
                                        'folio' => '1',
                                        'currency' => 'MXN',
                                        'exchangeRate' => 1,
                                        'instalmentNumber' => '1',
                                        'previousBalanceAmount' => '649.00000',
                                        'paidAmount' => '649.00000',
                                        'pendingBalanceAmount' => '0.00000',
                                        'operationTaxable' => null,
                                        RelatedDocumentTaxes::NODE_NAME_EN => [
                                            RelatedDocumentTaxesTransferredList::NODE_NAME_EN =>
                                            [
                                                RelatedDocumentTaxesTransferred::NODE_NAME_EN => [
                                                    [
                                                        'base' => '500.00000',
                                                        'tax' => '002',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.16000',
                                                        'amount' => '80.00000',
                                                    ],
                                                    [
                                                        'base' => '500.00000',
                                                        'tax' => '002',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.00000',
                                                        'amount' => '0.00000',
                                                    ],
                                                    [
                                                        'base' => '500.00000',
                                                        'tax' => '003',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.00000',
                                                        'amount' => '0.00000',
                                                    ],
                                                    [
                                                        'base' => '100.00000',
                                                        'tax' => '002',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.16000',
                                                        'amount' => '16.00000',
                                                    ],
                                                    [
                                                        'base' => '100.00000',
                                                        'tax' => '003',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.00000',
                                                        'amount' => '0.00000',
                                                    ],
                                                    [
                                                        'base' => '100.00000',
                                                        'tax' => '003',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.30000',
                                                        'amount' => '30.00000',
                                                    ],
                                                    [
                                                        'base' => '100.00000',
                                                        'tax' => '003',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.03000',
                                                        'amount' => '3.00000',
                                                    ],
                                                ]
                                            ],
                                            RelatedDocumentTaxesRetainedList::NODE_NAME_EN => [
                                                RelatedDocumentTaxesRetained::NODE_NAME_EN => [
                                                    [
                                                        'base' => '500.00000',
                                                        'tax' => '002',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.05000',
                                                        'amount' => '25.00000',
                                                    ],
                                                    [
                                                        'base' => '500.00000',
                                                        'tax' => '001',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.02000',
                                                        'amount' => '10.00000',
                                                    ],
                                                    [
                                                        'base' => '500.00000',
                                                        'tax' => '003',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.06000',
                                                        'amount' => '30.00000',
                                                    ],
                                                    [
                                                        'base' => '100.00000',
                                                        'tax' => '002',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.04000',
                                                        'amount' => '4.00000',
                                                    ],
                                                    [
                                                        'base' => '100.00000',
                                                        'tax' => '002',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.05000',
                                                        'amount' => '5.00000',
                                                    ],
                                                    [
                                                        'base' => '100.00000',
                                                        'tax' => '001',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.04000',
                                                        'amount' => '4.00000',
                                                    ],
                                                    [
                                                        'base' => '100.00000',
                                                        'tax' => '001',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.02000',
                                                        'amount' => '2.00000',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                    [
                                        'id' => 'XXXXXXXX-1111-2222-3333-XXXXXXXXXXXX',
                                        'series' => '2',
                                        'folio' => '1',
                                        'currency' => 'MXN',
                                        'exchangeRate' => 1,
                                        'instalmentNumber' => '1',
                                        'previousBalanceAmount' => '649.00000',
                                        'paidAmount' => '649.00000',
                                        'pendingBalanceAmount' => '0.00000',
                                        'operationTaxable' => null,
                                        RelatedDocumentTaxes::NODE_NAME_EN => [
                                            RelatedDocumentTaxesTransferredList::NODE_NAME_EN =>
                                            [
                                                RelatedDocumentTaxesTransferred::NODE_NAME_EN => [
                                                    [
                                                        'base' => '500.00000',
                                                        'tax' => '002',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.16000',
                                                        'amount' => '80.00000',
                                                    ],
                                                    [
                                                        'base' => '500.00000',
                                                        'tax' => '002',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.00000',
                                                        'amount' => '0.00000',
                                                    ],
                                                    [
                                                        'base' => '500.00000',
                                                        'tax' => '003',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.00000',
                                                        'amount' => '0.00000',
                                                    ],
                                                    [
                                                        'base' => '100.00000',
                                                        'tax' => '002',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.16000',
                                                        'amount' => '16.00000',
                                                    ],
                                                    [
                                                        'base' => '100.00000',
                                                        'tax' => '003',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.00000',
                                                        'amount' => '0.00000',
                                                    ],
                                                    [
                                                        'base' => '100.00000',
                                                        'tax' => '003',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.30000',
                                                        'amount' => '30.00000',
                                                    ],
                                                    [
                                                        'base' => '100.00000',
                                                        'tax' => '003',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.03000',
                                                        'amount' => '3.00000',
                                                    ],
                                                ]
                                            ],
                                            RelatedDocumentTaxesRetainedList::NODE_NAME_EN => [
                                                RelatedDocumentTaxesRetained::NODE_NAME_EN => [
                                                    [
                                                        'base' => '500.00000',
                                                        'tax' => '002',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.05000',
                                                        'amount' => '25.00000',
                                                    ],
                                                    [
                                                        'base' => '500.00000',
                                                        'tax' => '001',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.02000',
                                                        'amount' => '10.00000',
                                                    ],
                                                    [
                                                        'base' => '500.00000',
                                                        'tax' => '003',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.06000',
                                                        'amount' => '30.00000',
                                                    ],
                                                    [
                                                        'base' => '100.00000',
                                                        'tax' => '002',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.04000',
                                                        'amount' => '4.00000',
                                                    ],
                                                    [
                                                        'base' => '100.00000',
                                                        'tax' => '002',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.05000',
                                                        'amount' => '5.00000',
                                                    ],
                                                    [
                                                        'base' => '100.00000',
                                                        'tax' => '001',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.04000',
                                                        'amount' => '4.00000',
                                                    ],
                                                    [
                                                        'base' => '100.00000',
                                                        'tax' => '001',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.02000',
                                                        'amount' => '2.00000',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
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
            $cfdi = new CFDI40($data);
            $cfdi->autoCalculate();
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
            return;
        }

        $this->assertInstanceOf(CFDI40::class, $cfdi);

        echo "## Parsed CFDI Object ## " . PHP_EOL . PHP_EOL;
        print_r($cfdi);
        echo PHP_EOL . PHP_EOL;

        echo "## Output XML (reproduced) ## " . PHP_EOL . PHP_EOL;
        echo $cfdi->toXML();
        echo PHP_EOL . PHP_EOL;
    }

    public function testComplementPaymentsForeignCurrency(): void
    {
        $data = [
            'version' => CFDI40::VERSION_4_0,
            'series' => 'TEST',
            'folio' => '1',
            'date' => new \DateTime('now', new \DateTimeZone('America/Monterrey')),
            'paymentMethod' => null,
            'paymentConditions' => null,
            'subTotal' => 0.00,
            'discount' => 0.00,
            'currency' => 'XXX',
            'exchangeRate' => null,
            'total' => 0.00,
            'cfdiType' => 'P',
            'paymentType' => null,
            'postalCode' => '06000',
            'signature' => 'unsigned',
            'certificateNumber' => 'unsigned',
            'certificate' => 'unsigned',
            'export' => '01',
            'issuer' => [
                'rfc' => 'XAXX010101000',
                'name' => 'Test Issuer',
                'regime' => RegimeType::SIN_OBLIGACIONES_FISCALES,
            ],
            'recipient' => [
                'rfc' => 'XAXX010101000',
                'name' => 'Test Recipient',
                'foreignCountry' => null,
                'foreignTaxCode' => null,
                'regime' => '601',
                'cfdiUse' => CFDIUse::PAYMENTS,
                'postalCode' => '64920',
            ],
            'itemList' => [
                'items' => [
                    [
                        'code' => 84111506,
                        'quantity' => 1.00000,
                        'unitCode' => 'ACT',
                        'description' => 'Pago',
                        'unitPrice' => 0.00000,
                        'amount' => 0.00000,
                        'operationTaxable' => "01",
                        'discount' => 0.00000,
                    ]
                ],
            ],
            Complement::NODE_NAME_EN => [
                Complement::NODE_NAME_EN => [
                    Payments::NODE_NAME_EN => [
                        Payment::NODE_NAME_EN => [
                            [
                                'date' => new \DateTime('now', new \DateTimeZone('America/Monterrey')),
                                'paymentMethod' => '01',
                                'currency' => 'MXN',
                                'exchangeRate' => '1.00000',
                                'amount' => '921.23',
                                'transactionNumber' => null,
                                'taxes' => [],
                                RelatedDocument::NODE_NAME_EN =>
                                [
                                    [
                                        'id' => 'XXXXXXXX-1111-2222-3333-XXXXXXXXXXXX',
                                        'series' => '2',
                                        'folio' => '1',
                                        'currency' => 'USD',
                                        'exchangeRate' => .045331,
                                        'instalmentNumber' => '1',
                                        'previousBalanceAmount' => '41.76',
                                        'paidAmount' => '41.76',
                                        'pendingBalanceAmount' => '0.00000',
                                        'operationTaxable' => null,
                                        RelatedDocumentTaxes::NODE_NAME_EN => [
                                            RelatedDocumentTaxesTransferredList::NODE_NAME_EN =>
                                            [
                                                RelatedDocumentTaxesTransferred::NODE_NAME_EN => [
                                                    [
                                                        'base' => '36',
                                                        'tax' => '002',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.16000',
                                                        'amount' => '5.76',
                                                    ],
                                                ]
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'date' => new \DateTime('now', new \DateTimeZone('America/Monterrey')),
                                'paymentMethod' => '01',
                                'currency' => 'JPY',
                                'exchangeRate' => '.16',
                                'amount' => '123',
                                'transactionNumber' => null,
                                'taxes' => [],
                                RelatedDocument::NODE_NAME_EN =>
                                [
                                    [
                                        'id' => 'XXXXXXXX-1111-2222-3333-XXXXXXXXXXXX',
                                        'series' => '2',
                                        'folio' => '1',
                                        'currency' => 'JPY',
                                        'exchangeRate' => .008076,
                                        'instalmentNumber' => '1',
                                        'previousBalanceAmount' => '1',
                                        'paidAmount' => '1',
                                        'pendingBalanceAmount' => '0.00000',
                                        'operationTaxable' => null,
                                    ],
                                ],
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
            $cfdi = new CFDI40($data);
            $cfdi->autoCalculate();
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
            return;
        }

        $this->assertInstanceOf(CFDI40::class, $cfdi);

        echo "## Parsed CFDI Object ## " . PHP_EOL . PHP_EOL;
        print_r($cfdi);
        echo PHP_EOL . PHP_EOL;

        echo "## Output XML (reproduced) ## " . PHP_EOL . PHP_EOL;
        echo $cfdi->toXML();
        echo PHP_EOL . PHP_EOL;
    }

    public function testLocalTaxesComplement()
    {
        $data = [
            'version' => 4.0,
            'series' => 'SegmentoPruebaA',
            'folio' => 1,
            'date' => new \DateTime(),
            'paymentMethod' => '01',
            'paymentConditions' => null,
            'subTotal' => 0.00000,
            'discount' => 0.00000,
            'currency' => 'MXN',
            'exchangeRate' => null,
            'total' => 0.00000,
            'cfdiType' => 'I',
            'paymentType' => 'PUE',
            'postalCode' => '64000',
            'signature' => 'unsigned',
            'certificateNumber' => '3',
            'certificate' => 'unsigned',
            'export' => '01',
            'issuer' => [
                'rfc' => 'XAXX010101000',
                'name' => 'Test Issuer',
                'regime' => RegimeType::SIN_OBLIGACIONES_FISCALES,
            ],
            'recipient' => [
                'rfc' => 'XAXX010101000',
                'name' => 'Test Recipient',
                'foreignCountry' => null,
                'foreignTaxCode' => null,
                'regime' => '601',
                'cfdiUse' => CFDIUse::PAYMENTS,
                'postalCode' => '64920',
            ],
            'itemList' => [
                'items' => [
                    [
                        'code' => '01010101',
                        'quantity' => 1.00000,
                        'unitCode' => '18',
                        'description' => 'x',
                        'unitPrice' => 100.00000,
                        'amount' => 100.00000,
                        'operationTaxable' => '02',
                        'discount' => 0.00000,
                        'taxes' => [
                            'transferredList' => [
                                'transfers' => [
                                    [
                                        'base' => 100.00000,
                                        'tax' => '002',
                                        'factorType' => 'Tasa',
                                        'rate' => 0.16000,
                                        'amount' => 16.00000
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'complements' => [
                'complements' => [
                    'localTaxes' => [
                        'localTaxes' => [
                            'taxesTransferred' => [
                                [
                                    'tax' => 'DSA',
                                    'rate' => 0.10000,
                                    'amount' => 10.00000
                                ],
                            ],
                            'retentions' => []
                        ],

                    ]
                ]
            ]
        ];


        echo "## Input Data Array ## " . PHP_EOL . PHP_EOL;
        print_r($data);
        echo PHP_EOL . PHP_EOL;

        try {
            $cfdi = new CFDI40($data);
            $cfdi->autoCalculate();
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
            return;
        }

        $this->assertInstanceOf(CFDI40::class, $cfdi);

        echo "## Parsed CFDI Object ## " . PHP_EOL . PHP_EOL;
        print_r($cfdi);
        echo PHP_EOL . PHP_EOL;

        echo "## Output XML (reproduced) ## " . PHP_EOL . PHP_EOL;
        echo $cfdi->toXML();
        echo PHP_EOL . PHP_EOL;
    }

    public function testTotalsSum()
    {
        $data = [
            'version' => 4.0,
            'series' => 'SegmentoPruebaA',
            'folio' => 1,
            'date' => new \DateTime(),
            'paymentMethod' => '01',
            'paymentConditions' => null,
            'subTotal' => 0.00000,
            'discount' => 0.00000,
            'currency' => 'MXN',
            'exchangeRate' => null,
            'total' => 0.00000,
            'cfdiType' => 'I',
            'paymentType' => 'PUE',
            'postalCode' => '64000',
            'signature' => 'unsigned',
            'certificateNumber' => '3',
            'certificate' => 'unsigned',
            'export' => '01',
            'issuer' => [
                'rfc' => 'XAXX010101000',
                'name' => 'Test Issuer',
                'regime' => RegimeType::SIN_OBLIGACIONES_FISCALES,
            ],
            'recipient' => [
                'rfc' => 'XAXX010101000',
                'name' => 'Test Recipient',
                'foreignCountry' => null,
                'foreignTaxCode' => null,
                'regime' => '601',
                'cfdiUse' => CFDIUse::PAYMENTS,
                'postalCode' => '64920',
            ],
            'itemList' => [
                'items' => [
                    [
                        'code' => '01010101',
                        'quantity' => 1.00000,
                        'unitCode' => '18',
                        'description' => 'x',
                        'unitPrice' => 47.892720,
                        'amount' => 47.892720,
                        'operationTaxable' => '02',
                        'discount' => 0.00000,
                        'taxes' => [
                            'transferredList' => [
                                'transfers' => [
                                    [
                                        'base' => 100.00000,
                                        'tax' => '002',
                                        'factorType' => 'Tasa',
                                        'rate' => 0.16000,
                                        'amount' => 7.662835
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'code' => '01010101',
                        'quantity' => 1.00000,
                        'unitCode' => '18',
                        'description' => 'x',
                        'unitPrice' => 47.892720,
                        'amount' => 47.892720,
                        'operationTaxable' => '02',
                        'discount' => 0.00000,
                        'taxes' => [
                            'transferredList' => [
                                'transfers' => [
                                    [
                                        'base' => 100.00000,
                                        'tax' => '002',
                                        'factorType' => 'Tasa',
                                        'rate' => 0.16000,
                                        'amount' => 7.662840
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'code' => '01010101',
                        'quantity' => 2.00000,
                        'unitCode' => '18',
                        'description' => 'x',
                        'unitPrice' => 151.660280,
                        'amount' => 303.320560,
                        'operationTaxable' => '02',
                        'discount' => 0.00000,
                        'taxes' => [
                            'transferredList' => [
                                'transfers' => [
                                    [
                                        'base' => 100.00000,
                                        'tax' => '002',
                                        'factorType' => 'Tasa',
                                        'rate' => 0.16000,
                                        'amount' => 48.531290
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];



        echo "## Input Data Array ## " . PHP_EOL . PHP_EOL;
        print_r($data);
        echo PHP_EOL . PHP_EOL;

        try {
            $cfdi = new CFDI40($data);
            $cfdi->autoCalculate();
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
            return;
        }

        $this->assertEquals($cfdi->getTotalAmount(), '462.97');


        $this->assertInstanceOf(CFDI40::class, $cfdi);

        echo "## Parsed CFDI Object ## " . PHP_EOL . PHP_EOL;
        print_r($cfdi);
        echo PHP_EOL . PHP_EOL;

        echo "## Output XML (reproduced) ## " . PHP_EOL . PHP_EOL;
        echo $cfdi->toXML();
        echo PHP_EOL . PHP_EOL;
    }

    public function testComplementPaymentSum(): void
    {
        $data = [
            'version' => CFDI40::VERSION_4_0,
            'series' => 'TEST',
            'folio' => '1',
            'date' => new \DateTime('now', new \DateTimeZone('America/Monterrey')),
            'paymentMethod' => null,
            'paymentConditions' => null,
            'subTotal' => 0.00,
            'discount' => 0.00,
            'currency' => 'XXX',
            'exchangeRate' => null,
            'total' => 0.00,
            'cfdiType' => 'P',
            'paymentType' => null,
            'postalCode' => '06000',
            'signature' => 'unsigned',
            'certificateNumber' => 'unsigned',
            'certificate' => 'unsigned',
            'export' => '01',
            'issuer' => [
                'rfc' => 'XAXX010101000',
                'name' => 'Test Issuer',
                'regime' => RegimeType::SIN_OBLIGACIONES_FISCALES,
            ],
            'recipient' => [
                'rfc' => 'XAXX010101000',
                'name' => 'Test Recipient',
                'foreignCountry' => null,
                'foreignTaxCode' => null,
                'regime' => '601',
                'cfdiUse' => CFDIUse::PAYMENTS,
                'postalCode' => '64920',
            ],
            'itemList' => [
                'items' => [
                    [
                        'code' => 84111506,
                        'quantity' => 1.00000,
                        'unitCode' => 'ACT',
                        'description' => 'Pago',
                        'unitPrice' => 0.00000,
                        'amount' => 0.00000,
                        'operationTaxable' => "01",
                        'discount' => 0.00000,
                    ]
                ],
            ],
            Complement::NODE_NAME_EN => [
                Complement::NODE_NAME_EN => [
                    Payments::NODE_NAME_EN => [
                        Payment::NODE_NAME_EN => [
                            [
                                'date' => new \DateTime('now', new \DateTimeZone('America/Monterrey')),
                                'paymentMethod' => '01',
                                'currency' => 'MXN',
                                'exchangeRate' => '1.00000',
                                'amount' => '10000.00000',
                                'transactionNumber' => null,
                                'taxes' => [],
                                RelatedDocument::NODE_NAME_EN =>
                                [
                                    [
                                        'id' => 'XXXXXXXX-1111-2222-3333-XXXXXXXXXXXX',
                                        'series' => '2',
                                        'folio' => '1',
                                        'currency' => 'MXN',
                                        'exchangeRate' => 1,
                                        'instalmentNumber' => '1',
                                        'previousBalanceAmount' => '9552',
                                        'paidAmount' => '9552',
                                        'pendingBalanceAmount' => '0.00',
                                        'operationTaxable' => null,
                                        RelatedDocumentTaxes::NODE_NAME_EN => [
                                            RelatedDocumentTaxesTransferredList::NODE_NAME_EN =>
                                            [
                                                RelatedDocumentTaxesTransferred::NODE_NAME_EN => [
                                                    [
                                                        'base' => '7960.02',
                                                        'tax' => '002',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.16000',
                                                        'amount' => '1273.6',
                                                    ],

                                                ]
                                            ],
                                        ],
                                    ],
                                    [
                                        'id' => 'XXXXXXXX-1111-2222-3333-XXXXXXXXXXXX',
                                        'series' => '2',
                                        'folio' => '1',
                                        'currency' => 'MXN',
                                        'exchangeRate' => 1,
                                        'instalmentNumber' => '1',
                                        'previousBalanceAmount' => '9552',
                                        'paidAmount' => '9552',
                                        'pendingBalanceAmount' => '0.00',
                                        'operationTaxable' => null,
                                        RelatedDocumentTaxes::NODE_NAME_EN => [
                                            RelatedDocumentTaxesTransferredList::NODE_NAME_EN =>
                                            [
                                                RelatedDocumentTaxesTransferred::NODE_NAME_EN => [
                                                    [
                                                        'base' => '7960.02',
                                                        'tax' => '002',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.16000',
                                                        'amount' => '1273.6',
                                                    ],

                                                ]
                                            ],
                                        ],
                                    ],
                                    [
                                        'id' => 'XXXXXXXX-1111-2222-3333-XXXXXXXXXXXX',
                                        'series' => '2',
                                        'folio' => '1',
                                        'currency' => 'MXN',
                                        'exchangeRate' => 1,
                                        'instalmentNumber' => '1',
                                        'previousBalanceAmount' => '9552',
                                        'paidAmount' => '9552',
                                        'pendingBalanceAmount' => '0.00',
                                        'operationTaxable' => null,
                                        RelatedDocumentTaxes::NODE_NAME_EN => [
                                            RelatedDocumentTaxesTransferredList::NODE_NAME_EN =>
                                            [
                                                RelatedDocumentTaxesTransferred::NODE_NAME_EN => [
                                                    [
                                                        'base' => '7960.02',
                                                        'tax' => '002',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.16000',
                                                        'amount' => '1273.6',
                                                    ],

                                                ]
                                            ],
                                        ],
                                    ],
                                    [
                                        'id' => 'XXXXXXXX-1111-2222-3333-XXXXXXXXXXXX',
                                        'series' => '2',
                                        'folio' => '1',
                                        'currency' => 'MXN',
                                        'exchangeRate' => 1,
                                        'instalmentNumber' => '1',
                                        'previousBalanceAmount' => '1932.0',
                                        'paidAmount' => '1932.0',
                                        'pendingBalanceAmount' => '0.00',
                                        'operationTaxable' => null,
                                        RelatedDocumentTaxes::NODE_NAME_EN => [
                                            RelatedDocumentTaxesTransferredList::NODE_NAME_EN =>
                                            [
                                                RelatedDocumentTaxesTransferred::NODE_NAME_EN => [
                                                    [
                                                        'base' => '1610.00',
                                                        'tax' => '002',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.16000',
                                                        'amount' => '257.6',
                                                    ],

                                                ]
                                            ],
                                        ],
                                    ],
                                    [
                                        'id' => 'XXXXXXXX-1111-2222-3333-XXXXXXXXXXXX',
                                        'series' => '2',
                                        'folio' => '1',
                                        'currency' => 'MXN',
                                        'exchangeRate' => 1,
                                        'instalmentNumber' => '1',
                                        'previousBalanceAmount' => '1932.0',
                                        'paidAmount' => '1932.0',
                                        'pendingBalanceAmount' => '0.00',
                                        'operationTaxable' => null,
                                        RelatedDocumentTaxes::NODE_NAME_EN => [
                                            RelatedDocumentTaxesTransferredList::NODE_NAME_EN =>
                                            [
                                                RelatedDocumentTaxesTransferred::NODE_NAME_EN => [
                                                    [
                                                        'base' => '1610.00',
                                                        'tax' => '002',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.16000',
                                                        'amount' => '257.6',
                                                    ],

                                                ]
                                            ],
                                        ],
                                    ],
                                    [
                                        'id' => 'XXXXXXXX-1111-2222-3333-XXXXXXXXXXXX',
                                        'series' => '2',
                                        'folio' => '1',
                                        'currency' => 'MXN',
                                        'exchangeRate' => 1,
                                        'instalmentNumber' => '1',
                                        'previousBalanceAmount' => '4775.68',
                                        'paidAmount' => '4775.68',
                                        'pendingBalanceAmount' => '0.00',
                                        'operationTaxable' => null,
                                        RelatedDocumentTaxes::NODE_NAME_EN => [
                                            RelatedDocumentTaxesTransferredList::NODE_NAME_EN =>
                                            [
                                                RelatedDocumentTaxesTransferred::NODE_NAME_EN => [
                                                    [
                                                        'base' => '3979.74',
                                                        'tax' => '002',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.16000',
                                                        'amount' => '636.76',
                                                    ],

                                                ]
                                            ],
                                        ],
                                    ],
                                ],
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
            $cfdi = new CFDI40($data);
            $cfdi->autoCalculate();
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
            return;
        }

        $this->assertInstanceOf(CFDI40::class, $cfdi);

        echo "## Parsed CFDI Object ## " . PHP_EOL . PHP_EOL;
        print_r($cfdi);
        echo PHP_EOL . PHP_EOL;

        echo "## Output XML (reproduced) ## " . PHP_EOL . PHP_EOL;
        echo $cfdi->toXML();
        echo PHP_EOL . PHP_EOL;
    }

    public function testComplementPaymentForeignCurrencySum(): void
    {
        $data = [
            'version' => CFDI40::VERSION_4_0,
            'series' => 'TEST',
            'folio' => '1',
            'date' => new \DateTime('now', new \DateTimeZone('America/Monterrey')),
            'paymentMethod' => null,
            'paymentConditions' => null,
            'subTotal' => 0.00,
            'discount' => 0.00,
            'currency' => 'XXX',
            'exchangeRate' => null,
            'total' => 0.00,
            'cfdiType' => 'P',
            'paymentType' => null,
            'postalCode' => '06000',
            'signature' => 'unsigned',
            'certificateNumber' => 'unsigned',
            'certificate' => 'unsigned',
            'export' => '01',
            'issuer' => [
                'rfc' => 'XAXX010101000',
                'name' => 'Test Issuer',
                'regime' => RegimeType::SIN_OBLIGACIONES_FISCALES,
            ],
            'recipient' => [
                'rfc' => 'XAXX010101000',
                'name' => 'Test Recipient',
                'foreignCountry' => null,
                'foreignTaxCode' => null,
                'regime' => '601',
                'cfdiUse' => CFDIUse::PAYMENTS,
                'postalCode' => '64920',
            ],
            'itemList' => [
                'items' => [
                    [
                        'code' => 84111506,
                        'quantity' => 1.00000,
                        'unitCode' => 'ACT',
                        'description' => 'Pago',
                        'unitPrice' => 0.00000,
                        'amount' => 0.00000,
                        'operationTaxable' => "01",
                        'discount' => 0.00000,
                    ]
                ],
            ],
            Complement::NODE_NAME_EN => [
                Complement::NODE_NAME_EN => [
                    Payments::NODE_NAME_EN => [
                        Payment::NODE_NAME_EN => [
                            [
                                'date' => new \DateTime('now', new \DateTimeZone('America/Monterrey')),
                                'paymentMethod' => '01',
                                'currency' => 'MXN',
                                'exchangeRate' => '19',
                                'amount' => '10000.00000',
                                'transactionNumber' => null,
                                'taxes' => [],
                                RelatedDocument::NODE_NAME_EN =>
                                [
                                    [
                                        'id' => 'XXXXXXXX-1111-2222-3333-XXXXXXXXXXXX',
                                        'series' => '2',
                                        'folio' => '1',
                                        'currency' => 'MXN',
                                        'exchangeRate' => 19,
                                        'instalmentNumber' => '1',
                                        'previousBalanceAmount' => '7810.35',
                                        'paidAmount' => '7810.35',
                                        'pendingBalanceAmount' => '0.00',
                                        'operationTaxable' => null,
                                        RelatedDocumentTaxes::NODE_NAME_EN => [
                                            RelatedDocumentTaxesTransferredList::NODE_NAME_EN =>
                                            [
                                                RelatedDocumentTaxesTransferred::NODE_NAME_EN => [
                                                    [
                                                        'base' => '6733.06',
                                                        'tax' => '002',
                                                        'factorType' => 'Tasa',
                                                        'rate' => '0.16000',
                                                        'amount' => '1077.29',
                                                    ],

                                                ]
                                            ],
                                        ],
                                    ],
                                ],
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
            $cfdi = new CFDI40($data);
            $cfdi->autoCalculate();
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
            return;
        }

        $this->assertInstanceOf(CFDI40::class, $cfdi);

        echo "## Parsed CFDI Object ## " . PHP_EOL . PHP_EOL;
        print_r($cfdi);
        echo PHP_EOL . PHP_EOL;

        echo "## Output XML (reproduced) ## " . PHP_EOL . PHP_EOL;
        echo $cfdi->toXML();
        echo PHP_EOL . PHP_EOL;
    }
}
