# SAT CFDI
A pure PHP library to simplify the handling and processing of CFDIs. Creates new CFDIs and parses and validates existing CFDI files.

A CFDI is a "**C**omprobante **F**iscal **D**igital por **I**nternet" of Mexico's SAT "Servicio de Administración Tributaria" (Tax Administration Service).

## Features
- Parse XML representation of CFDIs
- Write XML files from a CFDI
- Validate cryptographic signatures
- Validate CFDI status online


## Installation

Requires PHP 7.2+ and Composer.

```bash
composer require anglemx/sat-cfdi
```

## Usage

```php
<?

use Angle\CFDI\CFDI33;
use Angle\CFDI\XmlLoader;

// ...
// ...
// ...

$xmlFile = 'invoice.xml';

$loader = new XmlLoader();

$cfdi = $loader->fileToCFDI($xmlFile);

if (!$cfdi) {
    // Parsing failed
    // All the helper classes and utilities keep a log of errors that are meant for internal debugging
    echo "Errors:" . PHP_EOL;
    echo print_r($loader->getErrors(), true) . PHP_EOL;

    // And the utilities also keep a log of validations, which are user-friendly and meant for public display
    echo "Validations:" . PHP_EOL;
    echo print_r($loader->getValidations(), true) . PHP_EOL;
}

// Parsing success!
print_r($cfdi);

// The validations log is also available upon success
echo "Validations:" . PHP_EOL;
echo print_r($loader->getValidations(), true) . PHP_EOL;
```

For more implementation examples, check the Test files.


## Dependencies
In order to make this library as robust as possible, we're using many libraries that are included by default on most environments.
- Implements XSD schema files to validate XML. (`ext-dom`, `ext-libxml`)
- Implements XSLT stylesheets to generate the Original Chain Sequence for signature validations. (`ext-xsl`)
- Implements OpenSSL to operate and verify X.509 Certificates. ( `ext-openssl`)


## Translations
This library is written in english to maintain code consistency.  However, some keywords are very specific to this domain.

| Español | English |
| ------------- | ------------- |
| Comprobante | Invoice |
| Relacionados | Related |
| Emisor | Issuer |
| Concepto | Item |
| Impuesto | Tax |
| Impuestos Trasladados | Transferred Tax |
| Impuestos Retenidos | Retained Tax |
| Información Aduanera | Customs Information |
| Predial | Property Tax |
_pending.._


## Resources
This library bundles some resources to simplify the installation process on production servers, and to allow for offline processing. All of these files are published by SAT and are made available completely free through [SAT's official website](http://www.sat.gob.mx).

| Document | Date | URL |
|----------|------|-----|
| Standard (pdf) | 2017-07-28 | http://omawww.sat.gob.mx/tramitesyservicios/Paginas/documentos/cfdv33.pdf |
| Schema (xsd) | 2017-07-28 | http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd |
| Original Chain Sequence (xslt) | 2021-12-01 | http://www.sat.gob.mx/sitio_internet/cfd/3/cadenaoriginal_3_3/cadenaoriginal_3_3.xslt |
| Data Catalog (xsd) | 2020-12-29 | http://www.sat.gob.mx/sitio_internet/cfd/catalogos/catCFDI.xsd |
| Data Types & Patterns (xsd) | 2017-12-14 | http://www.sat.gob.mx/sitio_internet/cfd/tipoDatos/tdCFDI/tdCFDI.xsd |
| Fiscal Digital Signature, Schema v1.1 (xsd) | 2017-04-12 | http://www.sat.gob.mx/sitio_internet/cfd/timbrefiscaldigital/TimbreFiscalDigitalv11.xsd
| Fiscal Digital Signature, Original Chain Sequence (xslt) | 2017-05-29 | http://www.sat.gob.mx/sitio_internet/cfd/timbrefiscaldigital/cadenaoriginal_TFD_1_1.xslt |
| Error Catalog (xls) | 2017-04-12 | http://omawww.sat.gob.mx/tramitesyservicios/Paginas/documentos/MatrizDeErrores_CFDI_v33.xls |
_This list is not complete, but any reference to the complementary XSD and XSLT resources are included in the root definition schemas._


### Certificates

We've bundled the production root X.509 Certificates (CA) in a convenient PEM file. If you'd prefer, you can download them from the official website and install those files locally: http://omawww.sat.gob.mx/tramitesyservicios/Paginas/certificado_sello_digital.htm

SAT publishes a list of all the certificates for every single registered and active taxpayer. You can consult any specific certificate with [SAT's official tool "Recuperación de Certificados"](https://portalsat.plataforma.sat.gob.mx/RecuperacionDeCertificados/faces/recuperaRFC.xhtml).


## References
The official publication regarding CFDI is called "Anexo 20" and we are currently on CFDI version 3.3. [View online](http://omawww.sat.gob.mx/tramitesyservicios/Paginas/anexo_20_version3-3.htm).



## Web Services
This employs some public web services to validate the status of CFDI.

_pending.._


## Validations

See `tests/ValidatorTest.php` for a sample implementation of the Validation Steps

- STEP 1: Parse XML into an Invoice
- STEP 2: Validate properties
- STEP 3: Validate CFDI signature
- STEP 4: Validate Fiscal Stamp (TFD)
- STEP 5: Validate CFDI's UUID against SAT

## Testing
To run tests with PHPUnit, simply install the dev dependencies
```bash
composer install
```

Create a `/test-data/` directory in this library's root path, and place all the XML files inside that you would like to test.

Finally, run the tests:
```bash
php vendor/bin/phpunit tests
```

## TO DO
- Validate properties when parsing
- Validate basic business rules
- Implement cache for X.509 Certificates
- Check for Certificate Revocation status before validating
- Implement cache for XSLT 2.0 transpilation
- Documentation and more samples


## TO FIX
- Duplicated Namespace declarations on `CFDI::toXML()` when we have any child (even if not on the root node) with a different namespace than the default for the Document. We should clean this up when pretty printing our XMLs.
- Replace all instances of `Node::NODE_NS_NAME` with `Node::NODE_NS_URI_NAME` inside `setChildrenFromDOMNodes()`. See `CFDI40\Complements` for reference. 

### Other notes

```php
echo 'Node class: ' . get_class($node) . PHP_EOL;
echo 'Node Name: ' . $node->nodeName . PHP_EOL;
echo 'Node Prefix ' . $node->prefix . PHP_EOL;
echo 'Node Local Name: ' . $node->localName . PHP_EOL;
echo 'Node Base URI: ' . $node->baseURI . PHP_EOL;
echo 'Node Namespace URI: ' . $node->namespaceURI . PHP_EOL;
echo PHP_EOL;
```

```
Node class: DOMElement
Node Name: tfd:TimbreFiscalDigital
Node Prefix tfd
Node Local Name: TimbreFiscalDigital
Node Base URI: /Users/mundofr/GitHub/Angle/sat-cfdi/test-data/4D75D60D-48CF-434C-96A6-26DABB3AC5AF.xml
Node Namespace URI: http://www.sat.gob.mx/TimbreFiscalDigital

Node class: DOMElement
Node Name: Pagos20:Pagos
Node Prefix Pagos20
Node Local Name: Pagos
Node Base URI: /Users/mundofr/GitHub/Angle/sat-cfdi/test-data/4D75D60D-48CF-434C-96A6-26DABB3AC5AF.xml
Node Namespace URI: http://www.sat.gob.mx/Pagos20
```