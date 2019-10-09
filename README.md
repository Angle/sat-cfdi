# SAT CFDI


## Translations
La librería está escrita en inglés para mantener consistencia en código.
A continuación la lista de Keywords que fueron utilizados:

| Español | Ingles |
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



## PHPUnit

Install dev dependencies
```bash
compose install
```

Run tests
```bash
php vendor/bin/phpunit tests
```

## Certificates (CA)
http://omawww.sat.gob.mx/tramitesyservicios/Paginas/certificado_sello_digital.htm



## Official

http://omawww.sat.gob.mx/tramitesyservicios/Paginas/anexo_20_version3-3.htm

### Resources

| Document | Date | URL |
|----------|------|-----|
| Standard (pdf) | 2017-07-28 | http://omawww.sat.gob.mx/tramitesyservicios/Paginas/documentos/cfdv33.pdf |
| Schema (xsd) | 2017-07-28 | http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd |
| Original Chain Sequence (xslt) | 2018-09-28 | http://www.sat.gob.mx/sitio_internet/cfd/3/cadenaoriginal_3_3/cadenaoriginal_3_3.xslt |
| Data Catalog (xsd) | 2018-06-04 | http://www.sat.gob.mx/sitio_internet/cfd/catalogos/catCFDI.xsd |
| Data Patterns (xsd) | 2017-12-14 | http://www.sat.gob.mx/sitio_internet/cfd/tipoDatos/tdCFDI/tdCFDI.xsd |
| Fiscal Digital Signature, Schema v1.1 (xsd) | 2017-04-12 | http://www.sat.gob.mx/sitio_internet/cfd/timbrefiscaldigital/TimbreFiscalDigitalv11.xsd
| Fiscal Digital Signature, Original Chain Sequence (xslt) | 2017-05-29 | http://www.sat.gob.mx/sitio_internet/cfd/timbrefiscaldigital/cadenaoriginal_TFD_1_1.xslt |
| Error Catalog (xls) | 2017-04-12 | http://omawww.sat.gob.mx/tramitesyservicios/Paginas/documentos/MatrizDeErrores_CFDI_v33.xls |


http://www.sat.gob.mx/sitio_internet/cfd/Pagos/Pagos10.xsd

http://www.sat.gob.mx/sitio_internet/cfd/catalogos/Pagos/catPagos.xsd


Download CSD
https://portalsat.plataforma.sat.gob.mx/RecuperacionDeCertificados/faces/recuperaRFC.xhtml


To solve:
- how can we pull new CSDs from SAT to validate the TFD?
- Expired certificates / revoked

Validations

- STEP 1: Parse (XML to Invoice)
- STEP 2: Validate properties
- STEP 3: Validate Signature and FiscalStamp match
- STEP 4: Validate Signature cryptographic integrity
- STEP 5: Validate FiscalStamp cryptographic integrity
- STEP 6: Validate UUID against SAT <- optional ?