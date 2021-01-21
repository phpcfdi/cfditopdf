# Version 0.3.4 2021-01-20

- Add missing `Tipo de cambio DR` field to documentos relacionados

# UNRELEASED 2020-10-11

- Testing: move `cadenaoriginal_TFD_1_1.xslt` to its correct location.
- Travis-CI: Remove allow fail on PHP 7.4.

# Version 0.3.3 2020-02-18

- Isolate the translation from CFDI to HTML into an interface `HtmlTranslatorInterface`.
- Implements `PlatesHtmlTranslator` with the current code to translate CFDI to HTML.
- Can setup the `Html2PdfBuilder` by changing the `HtmlTranslatorInterface` to use other  templates or other engine.
- Update license year.
- Update Travis-CI & Scrutinizer CI.
- Update phpstan (version 0.12, do not use phpstan-shim).
- Update todo list.

# Version 0.3.2 2019-11-14

- Add *Complemento de pagos* to the generated HTML, thanks @blacktrue
- Change license owner from *Carlos C Soto* to *PHPCFDI*
- Cleanup build and development files

# Version 0.3.1 2019-08-27

- Fix bug on `CfdiDataBuilder::createTfdSourceString`, the `tfd:TimbreFiscalDigital(Node)` does not contain
  all the requiered data (it is missing `xmlns:xsi` attribute from `cfdi:Comprobante`), as a result we need
  to provide the value of `Version | version` attribute.
- Refactor `TestCase` to add a helper `createXmlResolver` to run tests faster using local storage of external
  XSLT files.

# Version 0.3.0 2019-08-26

- Fix bug on `CfdiDataBuilder::createTfdSourceString` when *TimbreFiscalDigital* is version 1.0.
- Extract logic from `Html2PdfBuilder::build` to:
    - `Html2PdfBuilder::buildPdf`: convert from CfdiData to html to pdf
    - `Html2PdfBuilder::convertHtmlToPdf`: contains the logic of Html2Pdf
- Template `generic.php` uses alternative syntax for control structures
- Removed:
    - `PhpCfdi\CfdiToPdf\Utils\TemporaryFilename`: using `CfdiUtils`
    - `PhpCfdi\CfdiToPdf\Utils\ShellExec`: using `CfdiUtils`
    - `PhpCfdi\CfdiToPdf\PdfToText`: used only on testing environment
- Improve test coverage

# Version 0.2.3 2019-08-15

- Due GitHub API change, need to upgrade deploy section on Travis and tag a new release
  to make upload and perform a full release cicle.

# Version 0.2.2 2019-08-15

- Development improvements, code does not have significant changes.
- Remove phive, back to composer for development tools.
- Move phar construction logic to `build-phar` script.
- Update travis & scrutinizer & docs.

# Version 0.2.1 2018-08-05

- Add to travis on deploy section `skip_cleanup: tue`.

# Version 0.2.0 2018-05-08

- Depends on `phive` https://phar.io/ to handle development dependences:
  `phpcs`, `phpcbf`, `phpstan` & `phar-builder`
- Add composer custom commands:
    - `build:check-style`: Run `php-cs-fixer` and `phpcs` to check code style violations
    - `build:fix-style`: Run `php-cs-fixer` and `phpcs` to fix code style violations
    - `build:build`: Fix code style violations, run quality assurance and run test
    - `build:qa`: Run `phplint` and `phpstan`
    - `build:test`: Run `phpunit`
    - `build:coverage`: Run `phpunit` with code coverage and store the html results in `build/coverage/html/`,
    - `build:phar`: Build the phar file into `build/cfditopdf.phar` using `macfja/phar-builder`
- Build phar inside build and push it into release
- Improve messages from CLI utilily `bin/cfditopdf`
- Fix dependence on `eclipxe/cfdiutils` to explicit version instead of `master-dev`

# Version 0.1.0 2018-03-21

- Initial release, the are still many task to run to consider stable, please notice that the main dependency
  to CfdiUtils will change since that library will be moved to PhpCfdi\CfdiUtils
