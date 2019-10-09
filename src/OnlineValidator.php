<?php

namespace Angle\CFDI;

use Angle\CFDI\Invoice\Invoice;

use SimpleXMLElement;

class OnlineValidator
{
    // Enable to debug the HTTP connection
    const DEBUG = true;

    //const ENDPOINT = 'https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx'; // Human endpoint, not longer used
    const ENDPOINT = 'https://consultaqr.facturaelectronica.sat.gob.mx/consultacfdiservice.svc';

    const PAYLOAD = <<<ENDSOAP
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
   <soapenv:Header/>
   <soapenv:Body>
      <tem:Consulta>
         <tem:expresionImpresa>
         	##CDATA##
         	</tem:expresionImpresa>
      </tem:Consulta>
   </soapenv:Body>
</soapenv:Envelope>
ENDSOAP;


    public static function validate(Invoice $invoice)
    {
        // Build the Query parameters
        $query = [
            'id' => strtolower($invoice->getUuid()),
            're' => $invoice->getIssuer()->getRfc(),
            'rr' => $invoice->getRecipient()->getRfc(),
            'tt' => $invoice->getTotal(),
        ];

        // build URL
        //$url = self::WS . '?' . http_build_query($query);

        $cdata = '<![CDATA[' . '?' . http_build_query($query) . ']]>';

        // Build the payload
        $payload = str_replace('##CDATA##', $cdata, self::PAYLOAD);

        $customHeaders = array(
            'Content-Type: text/xml;charset="utf-8"',
            'SOAPAction: http://tempuri.org/IConsultaCFDIService/Consulta'
        );

        ## EXECUTE
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::ENDPOINT);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $customHeaders);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if (self::DEBUG) {
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
        }

        $response = curl_exec($ch);

        if (self::DEBUG) {
            echo "ENDPOINT: " . self::ENDPOINT . PHP_EOL;
            echo "REQUEST: " . PHP_EOL . $payload . PHP_EOL;
            echo "RESPONSE: " . PHP_EOL . $response . PHP_EOL;
        }

        if ($response === false) {
            /*
            # Only use for serious debugging..
            # throw new RuntimeException("cURL exception: ".curl_errno($ch).": ".curl_error($ch));
            $this->errorCode = -1;
            $this->errorDesc = "cURL exception: " . curl_errno($ch) . ": " . curl_error($ch);
            */
            return false;
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        // Check the HTTP Response
        if ($httpCode != 200) {
            // Invalid HTTP response
            return false;
        }

        ## PARSE OUTPUT RESPONSE
        if (!$response) {
            // Invalid Response Body
            return false;
        }

        // The response is a XML SOAP Envelope

        // TODO: we won't be using WSDLs nor any other type of validation

        libxml_use_internal_errors(true);

        var_dump($response);
        var_dump((string)$response);

        $response = "<?xml version='1.0'?>" . $response;

        $xml = simplexml_load_string( (string)$response );
        foreach( libxml_get_errors() as $error ) {

            print_r($error);

        }
        if ($xml === false) {
            echo "XML Parse failed!!" . PHP_EOL;
        }
        var_dump($xml);

        $ns = $xml->getNamespaces(true);
        var_dump($ns);

        print_r($xml);
        $json  = json_encode($xml);
        $data = json_decode($json, true);

        print_r($data);
    }
}