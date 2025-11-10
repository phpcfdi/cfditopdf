# phpcfdi/cfditopdf

[![Source Code][badge-source]][source]
[![Packagist PHP Version Support][badge-php-version]][php-version]
[![Discord][badge-discord]][discord]
[![Latest Version][badge-release]][release]
[![Software License][badge-license]][license]
[![Build Status][badge-build]][build]
[![Reliability][badge-reliability]][reliability]
[![Maintainability][badge-maintainability]][maintainability]
[![Code Coverage][badge-coverage]][coverage]
[![Violations][badge-violations]][violations]
[![Total Downloads][badge-downloads]][downloads]
[![Docker Downloads][badge-docker]][docker]

> Create a generic PDF file from a CFDI 3.3 & 4.0

In some cases you just simply need a PDF file from a Mexican CFDI (Comprobante Fiscal Digital por Internet).
This tool help you to create a generic one. You can also use it to build your own and pretty formats.

## Installation

Use [composer](https://getcomposer.org/), so please run

```shell
composer require phpcfdi/cfditopdf
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

## Basic usage from docker

```shell
docker run -it --rm --user="$(id -u):$(id -g)" cfditopdf --help
```

See more information on the [Docker README](Docker.README.md) file.

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
[discord]: https://discord.gg/aFGYXvX
[release]: https://github.com/phpcfdi/cfditopdf/releases
[license]: https://github.com/phpcfdi/cfditopdf/blob/master/LICENSE
[build]: https://github.com/phpcfdi/cfditopdf/actions/workflows/build.yml?query=branch:master
[reliability]:https://sonarcloud.io/component_measures?id=phpcfdi_cfditopdf&metric=Reliability
[maintainability]: https://sonarcloud.io/component_measures?id=phpcfdi_cfditopdf&metric=Maintainability
[coverage]: https://sonarcloud.io/component_measures?id=phpcfdi_cfditopdf&metric=Coverage
[violations]: https://sonarcloud.io/project/issues?id=phpcfdi_cfditopdf&resolved=false
[downloads]: https://packagist.org/packages/phpcfdi/cfditopdf
[docker]: https://hub.docker.com/r/phpcfdi/cfditopdf

[badge-source]: https://img.shields.io/badge/source-phpcfdi/cfditopdf-blue?logo=github
[badge-discord]: https://img.shields.io/discord/459860554090283019?logo=discord
[badge-php-version]: https://img.shields.io/packagist/php-v/phpcfdi/cfditopdf?logo=php
[badge-release]: https://img.shields.io/github/release/phpcfdi/cfditopdf?logo=git
[badge-license]: https://img.shields.io/github/license/phpcfdi/cfditopdf?logo=open-source-initiative
[badge-build]: https://img.shields.io/github/actions/workflow/status/phpcfdi/cfditopdf/build.yml?branch=master&logo=github-actions
[badge-reliability]: https://sonarcloud.io/api/project_badges/measure?project=phpcfdi_cfditopdf&metric=reliability_rating
[badge-maintainability]: https://sonarcloud.io/api/project_badges/measure?project=phpcfdi_cfditopdf&metric=sqale_rating
[badge-coverage]: https://img.shields.io/sonar/coverage/phpcfdi_cfditopdf/master?logo=sonarqubecloud&server=https%3A%2F%2Fsonarcloud.io
[badge-violations]: https://img.shields.io/sonar/violations/phpcfdi_cfditopdf/master?format=long&logo=sonarqubecloud&server=https%3A%2F%2Fsonarcloud.io
[badge-downloads]: https://img.shields.io/packagist/dt/phpcfdi/cfditopdf?logo=packagist
[badge-docker]: https://img.shields.io/docker/pulls/phpcfdi/cfditopdf?logo=docker
