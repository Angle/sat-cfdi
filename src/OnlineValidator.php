<?php

namespace Angle\CFDI;

use Angle\CFDI\Invoice\Invoice;

use SimpleXMLElement;

class OnlineValidator
{
    // Enable to debug the HTTP connection
    const DEBUG_CURL = false;

    const RESULT_VALID      = 1;
    const RESULT_NOT_VALID  = 0;
    const RESULT_ERROR      = -1;

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

    private static $errors = [];

    public static function lastErrors()
    {
        return self::$errors;
    }


    /**
     * Query the central SAT webservice to check the validity of a CFDI
     * Returns 1 on success, 0 on failure, -1 on error
     * @param Invoice $invoice
     * @return int
     */
    public static function validate(Invoice $invoice): int
    {
        // This WebService is forked from the QR validation method that is found on printed representations of the CFDI
        // so the implemention is a bit funky.
        // We query the service with a WS SOAP payload that contains the "URL Query" that would be used in a QR code, this
        // Query is passed inside the field <tem:expresionImpresa> as a CDATA blob. Why? Because México.

        // Clear any previous errors
        self::$errors = [];

        // Build the Query parameters
        $query = [
            'id' => strtolower($invoice->getUuid()),
            're' => $invoice->getIssuer()->getRfc(),
            'rr' => $invoice->getRecipient()->getRfc(),
            'tt' => $invoice->getTotal(),
        ];

        // Insert the Query parameters in the special CDATA field blob
        $cdata = '<![CDATA[' . '?' . http_build_query($query) . ']]>';

        // Replace the CDATA value in the preset payload XML
        // TODO: Implement a valid SOAPClient instance with WSDLs
        $payload = str_replace('##CDATA##', $cdata, self::PAYLOAD);

        // SOAP Headers
        $customHeaders = array(
            'Content-Type: text/xml;charset="utf-8"',
            'SOAPAction: http://tempuri.org/IConsultaCFDIService/Consulta'
        );

        // Execute the HTTP POST request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::ENDPOINT);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $customHeaders);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if (self::DEBUG_CURL) {
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
        }

        $response = curl_exec($ch);

        if (self::DEBUG_CURL) {
            echo "ENDPOINT: " . self::ENDPOINT . PHP_EOL;
            echo "REQUEST: " . PHP_EOL . $payload . PHP_EOL;
            echo "RESPONSE: " . PHP_EOL . $response . PHP_EOL;
        }

        if ($response === false) {
            self::$errors[] = "cURL exception: " . curl_errno($ch) . ": " . curl_error($ch);
            return self::RESULT_ERROR;
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        // Check the HTTP Response
        if ($httpCode != 200) {
            // Invalid HTTP response
            self::$errors[] = "Invalid HTTP Status code: " . $httpCode;
            return self::RESULT_ERROR;
        }

        ## PARSE OUTPUT RESPONSE
        if (!$response) {
            // Missing response body
            self::$errors[] = 'Missing response body';
            return self::RESULT_ERROR;
        }


        // The response is a XML SOAP Envelope, so we'll parse it with SimpleXML
        // TODO: Implement SOAPClient WSDLs to validate the response schema

        libxml_use_internal_errors(true);
        libxml_clear_errors(); // clear any previous errors in the buffer


        try {
            $xml = simplexml_load_string( (string)$response );
        } catch (\Exception $e) {
            // we'll catch the error and display it below
            $xml = false;
        }

        if ($xml === false) {
            self::$errors[] = 'Error parsing response XML';
            foreach( libxml_get_errors() as $error ) {
                self::$errors[] = sprintf("LibXML: Error %d (Line:%d): %s", $error->code, $error->line, trim($error->message));
            }
            return self::RESULT_ERROR;
        }

        // Open up the XML response, get the important values
        try {
            $resultNodes = $xml->children('s', true)->Body->children('', true)->ConsultaResponse->ConsultaResult->children('a', true);
            //$statusDescription    = (string)$resultNodes->CodigoEstatus;
            //$statusCancellable    = (string)$resultNodes->EsCancelable;
            //$statusCancellation   = (string)$resultNodes->EstatusCancelacion;
            $statusValid            = (string)$resultNodes->Estado;
        } catch (\Exception $e) {
            // Invalid XML structure
            self::$errors[] = "Invalid XML Response structure (" . $e->getMessage() . ")" ;
            return self::RESULT_ERROR;
        }

        // Possible status:
        // - Vigente -> Success
        // - Cancelado -> Invalid

        if ($statusValid == 'Vigente') {
            return self::RESULT_VALID;
        } else {
            return self::RESULT_NOT_VALID;
        }
    }
}