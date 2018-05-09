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
