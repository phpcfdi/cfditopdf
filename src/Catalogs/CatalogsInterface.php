<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf\Catalogs;

interface CatalogsInterface
{
    public function catImpuesto(string $value): string;

    public function catObjetoImp(string $value): string;

    public function catUsoCFDI(string $value): string;

    public function catRegimenFiscal(string $value): string;

    public function catTipoRelacion(string $value): string;

    public function catMeses(string $value): string;

    public function catPeriodicidad(string $value): string;

    public function catExportacion(string $value): string;

    public function catMetodoPago(string $value): string;

    public function catFormaPago(string $value): string;

    public function catTipoComprobante(string $value): string;
}
