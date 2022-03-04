<?php

namespace Angle\CFDI\Utility;

use Angle\CFDI\CFDIInterface;

abstract class SerializationUtility
{

    public static function unserialize(string $data): ?CFDIInterface
    {
        if (!$data) {
            return null;
        }

        if (strpos($data, 'O:15:"Angle\CFDI\CFDI"') === 0) {
            // This is using a previous version of the CFDI library!
            $translation = [
                // Previous => New
                'Angle\CFDI\CFDI' => 'Angle\CFDI\Node\CFDI33\CFDI33',
            ];

            $nodes = [
                'Addendum',
                'Complement',
                'Issuer',
                'Item',
                'ItemComplement',
                'ItemCustomsInformation',
                'ItemList',
                'ItemPart',
                'ItemPropertyTaxAccount',
                'ItemTaxes',
                'ItemTaxesRetained',
                'ItemTaxesRetainedList',
                'ItemTaxesTransferred',
                'ItemTaxesTransferredList',
                'Recipient',
                'RelatedCFDI',
                'RelatedCFDIList',
                'Taxes',
                'TaxesRetained',
                'TaxesRetainedList',
                'TaxesTransferred',
                'TaxesTransferredList',
            ];

            foreach ($nodes as $v) {
                $k = 'Angle\CFDI\Node\\' . $v;
                $n = 'Angle\CFDI\Node\CFDI33\\' . $v;

                $translation[$k] = $n;
            }

            $search = [];
            $replace = [];

            foreach ($translation as $p => $n) {
                $search[] = sprintf('O:%d:"%s"', strlen($p), $p);
                $replace[] = sprintf('O:%d:"%s"', strlen($n), $n);
            }

            $data = str_replace($search, $replace, $data);
        }

        $obj = unserialize($data);

        if (!($obj instanceof CFDIInterface)) {
            // this is weird, the object was not a valid CFDI instance..
            // TODO: what should we do here?
            return null;
        }

    }

}