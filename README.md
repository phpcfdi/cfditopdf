# phpcfdi/cfditopdf

[![Source Code][badge-source]][source]
[![Latest Version][badge-release]][release]
[![Software License][badge-license]][license]
[![Build Status][badge-build]][build]
[![Scrutinizer][badge-quality]][quality]
[![Coverage Status][badge-coverage]][coverage]
[![Total Downloads][badge-downloads]][downloads]

> Create a generic PDF file from a CFDI 3.3

In some cases you just simply need a PDF file from a Mexican CFDI (Comprobante Fiscal Digital por Internet).
This tool help you to create a generic one. You can also use it to build your own and pretty formats.

## Installation

Use [composer](https://getcomposer.org/), so please run

```shell
composer require phpcfdi/cfditopdf
```

## Basic usage from CLI

```text
$ bin/cfditopdf --help
bin/cfditopdf [options] <cfdi-file> [<pdf-file>]
  -h, --help                Show this help
  -v, --version             Show command version
  -d, --dirty               Do not try to clean up the cfdi file
  -l, --resource-location   Use this path to store the xml resources locally,
                            if none then it will always download xlst resources
  cfdi-file                 Path of the XML file (input file)
  pdf-file                  Path of the PDF file (output file) if none then it will remove
                            ".xml" extension and sufix ".pdf" extension
```

## Basic usage as a PHP library

```php
<?php declare(strict_types=1);

$cfdifile = 'datafiles/cfdi.xml';
$xml = file_get_contents($cfdifile);

// clean cfdi
$xml = \CfdiUtils\Cleaner\Cleaner::staticClean($xml);

// create the main node structure
$comprobante = \CfdiUtils\Nodes\XmlNodeUtils::nodeFromXmlString($xml);

// create the CfdiData object, it contains all the required information
$cfdiData = (new \PhpCfdi\CfdiToPdf\CfdiDataBuilder())
    ->build($comprobante);

// create the converter
$converter = new \PhpCfdi\CfdiToPdf\Converter(
    new \PhpCfdi\CfdiToPdf\Builders\Html2PdfBuilder()
);

// create the invoice as output.pdf
$converter->createPdfAs($cfdiData, 'output.pdf');
```

To change the way data is translated from `CfdiData` to HTML you could provide a specialized translator to
`Html2PdfBuilder` when the object is constructed.

In the following example is using the default HTML translator that uses Plates, only changing the directory
where templates are located and the template name. The expected result must be compatible with Html2Pdf.

```php
<?php declare(strict_types=1);
$htmlTranslator = new \PhpCfdi\CfdiToPdf\Builders\HtmlTranslators\PlatesHtmlTranslator(
    'directory_where_templates_are_located',
    'main_template_name'
);
$converter = new \PhpCfdi\CfdiToPdf\Converter(
    new \PhpCfdi\CfdiToPdf\Builders\Html2PdfBuilder($htmlTranslator)
);
```

## PHP Support

This library is compatible with PHP versions 7.0 and above.
Please, try to use the full potential of the language.

## Contributing

Contributions are welcome! Please read [CONTRIBUTING][] for details
and don't forget to take a look in the [TODO][] and [CHANGELOG][] files.

## Copyright and License

The phpcfdi/cfditopdf library is copyright © [PHPCFDI](https://www.phpcfdi.com/)
and licensed for use under the MIT License (MIT). Please see [LICENSE][] for more information.

[contributing]: https://github.com/phpcfdi/cfditopdf/blob/master/CONTRIBUTING.md
[changelog]: https://github.com/phpcfdi/cfditopdf/blob/master/docs/CHANGELOG.md
[todo]: https://github.com/phpcfdi/cfditopdf/blob/master/docs/TODO.md

[source]: https://github.com/phpcfdi/cfditopdf
[release]: https://github.com/phpcfdi/cfditopdf/releases
[license]: https://github.com/phpcfdi/cfditopdf/blob/master/LICENSE
[build]: https://travis-ci.com/phpcfdi/cfditopdf?branch=master
[quality]: https://scrutinizer-ci.com/g/phpcfdi/cfditopdf/
[coverage]: https://scrutinizer-ci.com/g/phpcfdi/cfditopdf/code-structure/master/code-coverage/src
[downloads]: https://packagist.org/packages/phpcfdi/cfditopdf

[badge-source]: https://img.shields.io/badge/source-phpcfdi/cfditopdf-blue?style=flat-square
[badge-release]: https://img.shields.io/github/release/phpcfdi/cfditopdf?style=flat-square
[badge-license]: https://img.shields.io/github/license/phpcfdi/cfditopdf?style=flat-square
[badge-build]: https://img.shields.io/travis/com/phpcfdi/cfditopdf/master?style=flat-square
[badge-quality]: https://img.shields.io/scrutinizer/g/phpcfdi/cfditopdf/master?style=flat-square
[badge-coverage]: https://img.shields.io/scrutinizer/coverage/g/phpcfdi/cfditopdf/master?style=flat-square
[badge-downloads]: https://img.shields.io/packagist/dt/phpcfdi/cfditopdf?style=flat-square
