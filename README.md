# phpcfdi/cfditopdf

[![Source Code][badge-source]][source]
[![Packagist PHP Version Support][badge-php-version]][php-version]
[![Latest Version][badge-release]][release]
[![Software License][badge-license]][license]
[![Build Status][badge-build]][build]
[![Scrutinizer][badge-quality]][quality]
[![Coverage Status][badge-coverage]][coverage]
[![Total Downloads][badge-downloads]][downloads]

> Create a generic PDF file from a CFDI 3.3 & 4.0

In some cases you just simply need a PDF file from a Mexican CFDI (Comprobante Fiscal Digital por Internet).
This tool help you to create a generic one. You can also use it to build your own and pretty formats.

## Installation

Use [composer](https://getcomposer.org/), so please run

```shell
composer require phpcfdi/cfditopdf
```

## Run with Docker

Running as a Docker container lets you use this CLI anywhere where Docker is installed without worrying about about any dependencies, not even PHP itself.

```shell
# This will mount the current working directory
# into a the /data directory inside the container
docker run --rm \
  --volume $PWD:/data \
  --user $(id -u):$(id -g) \
  ghcr.io/phpcfdi/cfditopdf:latest \
  /data/my-cfdi.xml \
  /data/my-cfdi-output.pdf
```

You can optionally set an alias in your shell to simplify running the container (to make this alias permanent add the alias to your .bashrc fille).

```shell
alias cfditopdf='docker run --rm --volume $PWD:/data --user $(id -u):$(id -g) ghcr.io/phpcfdi/cfditopdf:latest'

# Then, execute just as `cfditopdf` (see usage in the next section)

cfditopdf --help
cfditopdf /data/my-cfdi.xml /data/my-cfdi-output.pdf
```

## Basic usage from CLI

```text
$ bin/cfditopdf [options] <cfdi-file> [<pdf-file>]
  -h, --help                Show this help
  -V, --version             Show command version
  -d, --dirty               Do not try to clean up the cfdi file
  -f, --fonts-dir           Path where TCPDF fonts are located
  -l, --resource-location   Use this path to store the xml resources locally,
                            if none then it will always download xlst resources
  cfdi-file                 Path of the XML file (input file)
  pdf-file                  Path of the PDF file (output file) if none then it will remove
                            ".xml" extension and suffix ".pdf" extension
```

## Basic usage as a PHP library

```php
<?php declare(strict_types=1);

$cfdifile = 'datafiles/cfdi.xml';
$xml = file_get_contents($cfdifile);

// clean cfdi
$xml = \PhpCfdi\CfdiCleaner\Cleaner::staticClean($xml);

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

This library is compatible with at least the oldest [PHP Supported Version](https://php.net/supported-versions.php)
with **active** support. Please, try to use PHP full potential.

We adhere to [Semantic Versioning](https://semver.org/).
We will not introduce any compatibility backwards change on major versions.

Internal classes (using `@internal` annotation) are not part of this agreement
as they must only exist inside this project. Do not use them in your project.

## Contributing

Contributions are welcome! Please read [CONTRIBUTING][] for details
and don't forget to take a look in the [TODO][] and [CHANGELOG][] files.

## Copyright and License

The `phpcfdi/cfditopdf` library is copyright © [PHPCFDI](https://www.phpcfdi.com/)
and licensed for use under the MIT License (MIT). Please see [LICENSE][] for more information.

[contributing]: https://github.com/phpcfdi/cfditopdf/blob/master/CONTRIBUTING.md
[changelog]: https://github.com/phpcfdi/cfditopdf/blob/master/docs/CHANGELOG.md
[todo]: https://github.com/phpcfdi/cfditopdf/blob/master/docs/TODO.md

[source]: https://github.com/phpcfdi/cfditopdf
[php-version]: https://packagist.org/packages/phpcfdi/cfditopdf
[release]: https://github.com/phpcfdi/cfditopdf/releases
[license]: https://github.com/phpcfdi/cfditopdf/blob/master/LICENSE
[build]: https://github.com/phpcfdi/cfditopdf/actions/workflows/build.yml?query=branch:master
[quality]: https://scrutinizer-ci.com/g/phpcfdi/cfditopdf/
[coverage]: https://scrutinizer-ci.com/g/phpcfdi/cfditopdf/code-structure/master/code-coverage/src
[downloads]: https://packagist.org/packages/phpcfdi/cfditopdf

[badge-source]: https://img.shields.io/badge/source-phpcfdi/cfditopdf-blue?style=flat-square
[badge-php-version]: https://img.shields.io/packagist/php-v/phpcfdi/cfditopdf?style=flat-square
[badge-release]: https://img.shields.io/github/release/phpcfdi/cfditopdf?style=flat-square
[badge-license]: https://img.shields.io/github/license/phpcfdi/cfditopdf?style=flat-square
[badge-build]: https://img.shields.io/github/actions/workflow/status/phpcfdi/cfditopdf/build.yml?branch=master&style=flat-square
[badge-quality]: https://img.shields.io/scrutinizer/g/phpcfdi/cfditopdf/master?style=flat-square
[badge-coverage]: https://img.shields.io/scrutinizer/coverage/g/phpcfdi/cfditopdf/master?style=flat-square
[badge-downloads]: https://img.shields.io/packagist/dt/phpcfdi/cfditopdf?style=flat-square
