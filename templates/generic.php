<?php

/**
 * @noinspection PhpFullyQualifiedNameUsageInspection
 */

declare(strict_types=1);

/**
 * @var \League\Plates\Template\Template $this
 * @var \PhpCfdi\CfdiToPdf\CfdiData $cfdiData
 * @var \PhpCfdi\CfdiToPdf\Catalogs\CatalogsInterface|null $catalogos
 */
$comprobante = $cfdiData->comprobante();
$emisor = $cfdiData->emisor();
$receptor = $cfdiData->receptor();
$tfd = $cfdiData->timbreFiscalDigital();
$relacionados = $comprobante->searchNodes('cfdi:CfdiRelacionados');
$totalImpuestosTrasladados = $comprobante->searchAttribute('cfdi:Impuestos', 'TotalImpuestosTrasladados');
$totalImpuestosRetenidos = $comprobante->searchAttribute('cfdi:Impuestos', 'TotalImpuestosRetenidos');
$conceptos = $comprobante->searchNodes('cfdi:Conceptos', 'cfdi:Concepto');
$informacionGlobal = $comprobante->searchNode('cfdi:InformacionGlobal');
$conceptoCounter = 0;
$conceptoCount = $conceptos->count();
if (! isset($catalogos) || ! ($catalogos instanceof \PhpCfdi\CfdiToPdf\Catalogs\CatalogsInterface)) {
    $catalogos = new \PhpCfdi\CfdiToPdf\Catalogs\StaticCatalogs();
}
?>
<style>
    * {
        font-size: 8pt;
        padding: 0;
        margin: 0;
    }

    table th, table td {
        vertical-align: top;
        text-align: center;
    }

    table th {
        font-weight: bold;
    }

    table.tabular th {
        text-align: right;
    }

    table.tabular td {
        text-align: left;
    }

    div.panel {
        border: 0.2mm solid #0000;
        margin-bottom: 1mm;
    }

    div.panel div.title {
        background-color: #333333;
        color: #ffffff;
        font-weight: bold;
        padding: 1mm 2mm;
    }

    div.panel div.content {
        padding: 1mm 2mm;
    }
</style>
<!--suppress HtmlUnknownTag -->
<page backbottom="10mm">
    <page_footer>
        <p style="text-align: center">
            Este documento es una representación impresa de un Comprobante Fiscal Digital a través de Internet
            versión <?=$this->e($comprobante['Version'])?>
            <br/>UUID: <?=$this->e($tfd['UUID'])?> - Página [[page_cu]] de [[page_nb]]
        </p>
    </page_footer>
    <div class="panel">
        <div class="title" style="text-align: center">UUID <?=$this->e($tfd['UUID'])?></div>
        <table class="tabular">
            <tr>
                <td rowspan="20" style="padding-right: 4mm;">
                    <!--suppress CheckEmptyScriptTag, HtmlUnknownTag -->
                    <qrcode style="width: 45mm;" ec="M" value="<?=$this->e($cfdiData->qrUrl())?>"/>
                </td>
                <th>Tipo:</th>
                <td><?=$catalogos->catTipoComprobante($comprobante['TipoDeComprobante'])?></td>
            </tr>
            <tr>
                <th class="">Serie:</th>
                <td><?=$this->e($comprobante['Serie'])?></td>
            </tr>
            <tr>
                <th class="">Folio:</th>
                <td><?=$this->e($comprobante['Folio'])?></td>
            </tr>
            <tr>
                <th class="">Lugar de expedición:</th>
                <td><?=$this->e($comprobante['LugarExpedicion'])?></td>
            </tr>
            <tr>
                <th class="">Fecha:</th>
                <td><?=$this->e($comprobante['Fecha'])?></td>
            </tr>
            <tr>
                <th>Forma de pago:</th>
                <td><?=$catalogos->catFormaPago($comprobante['FormaPago'])?></td>
            </tr>
            <tr>
                <th>Método de pago:</th>
                <td><?=$catalogos->catMetodoPago($comprobante['MetodoPago'])?></td>
            </tr>
            <?php if ('' !== $comprobante['CondicionesDePago']) : ?>
                <tr>
                    <th>Condiciones de pago:</th>
                    <td><?=$this->e($comprobante['CondicionesDePago'])?></td>
                </tr>
            <?php endif; ?>
            <tr>
                <th>Certificado emisor:</th>
                <td><?=$this->e($comprobante['NoCertificado'])?></td>
            </tr>
            <tr>
                <th>Certificado SAT:</th>
                <td><?=$this->e($tfd['NoCertificadoSAT'])?></td>
            </tr>
            <tr>
                <th>RFC proveedor:</th>
                <td><?=$this->e($tfd['RfcProvCertif'])?></td>
            </tr>
            <tr>
                <th>Fecha de certificación:</th>
                <td><?=$this->e($tfd['FechaTimbrado'])?></td>
            </tr>
            <?php if ('' !== $comprobante['Exportacion']) : ?>
                <tr>
                    <th>Exportación:</th>
                    <td><?=$catalogos->catExportacion($comprobante['Exportacion'])?></td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
    <?php if (null !== $informacionGlobal) : ?>
        <div class="panel">
            <div class="title">Información global</div>
            <div class="content">
                <p>
                    Periodicidad: <?=$catalogos->catPeriodicidad($informacionGlobal['Periodicidad'])?>
                    <br/>Meses: <?=$catalogos->catMeses($informacionGlobal['Meses'])?>
                    <br/>Año: <?=$this->e($informacionGlobal['Año'])?>
                </p>
            </div>
        </div>
    <?php endif; ?>
    <div class="panel">
        <div class="title">Emisor</div>
        <div class="content">
            <p>
                <?=$this->e($emisor['Nombre'] ?: 'No se especificó el nombre del emisor')?>
                <br/>RFC: <?=$this->e($emisor['Rfc'])?>
                <br/>Régimen fiscal: <?=$catalogos->catRegimenFiscal($emisor['RegimenFiscal'])?>
            </p>
        </div>
    </div>
    <div class="panel">
        <div class="title">Receptor</div>
        <div class="content">
            <p>
                <?=$this->e($receptor['Nombre'] ?: '(No se especificó el nombre del receptor)')?>
                <br/>RFC: <?=$this->e($receptor['Rfc'])?>
                <br/>Uso del CFDI: <?=$catalogos->catUsoCFDI($receptor['UsoCFDI'])?>
                <?php if ('' !== $receptor['DomicilioFiscalReceptor']) : ?>
                    <br/>Domicilio fiscal receptor: <?=$this->e($receptor['DomicilioFiscalReceptor'])?>
                <?php endif; ?>
                <?php if ('' !== $receptor['RegimenFiscalReceptor']) : ?>
                    <br/>Régimen fiscal receptor: <?=$catalogos->catRegimenFiscal($receptor['RegimenFiscalReceptor'])?>
                <?php endif; ?>
                <?php if ('' !== $receptor['ResidenciaFiscal']) : ?>
                    <br/>Residencia fiscal: <?=$this->e($receptor['ResidenciaFiscal'])?>
                <?php endif; ?>
                <?php if ('' !== $receptor['NumRegIdTrib']) : ?>
                    <br/>Residencia fiscal: <?=$this->e($receptor['NumRegIdTrib'])?>
                <?php endif; ?>
            </p>
        </div>
    </div>
    <?php foreach ($relacionados as $relacionado) : ?>
        <div class="panel">
            <div class="title">CFDI Relacionados (Tipo de relación: <?=$this->e($relacionado['TipoRelacion'])?>)</div>
            <div class="content">
                <?php foreach ($relacionado->searchNodes('cfdi:CfdiRelacionado') as $cfdiRelacionado) : ?>
                    <span>UUID: <?=$cfdiRelacionado['UUID']?></span>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
    <?php foreach ($conceptos as $concepto) : ?>
        <?php
        $conceptoCounter = $conceptoCounter + 1;
        $conceptoTraslados = $concepto->searchNodes('cfdi:Impuestos', 'cfdi:Traslados', 'cfdi:Traslado');
        $conceptoRetenciones = $concepto->searchNodes('cfdi:Impuestos', 'cfdi:Retenciones', 'cfdi:Retencion');
        $cuentaTerceros = $concepto->searchNode('cfdi:ACuentaTerceros');
        $informacionAduaneras = $concepto->searchNode('cfdi:InformacionAduanera');
        $cuentaPredials = $concepto->searchNode('cfdi:CuentaPedial');
        ?>
        <div class="panel">
            <div class="title">Concepto: <?=$this->e($conceptoCounter)?> de <?=$this->e($conceptoCount)?></div>
            <div class="content">
                <p><strong>Descripcion: </strong><?=$this->e($concepto['Descripcion'])?></p>
                <p>
                    <span>No identificación: <?=$this->e($concepto['NoIdentificacion'] ?: '(ninguno)')?>,</span>
                    <span>Clave SAT: <?=$this->e($concepto['ClaveProdServ'])?>,</span>
                    <?php if ('' !== $this->e($concepto['ObjetoImp'])) : ?>
                        <span>Objeto de impuesto: <?=$catalogos->catObjetoImp($concepto['ObjetoImp'])?>,</span>
                    <?php endif; ?>
                    <span>Clave Unidad: <?=$this->e($concepto['ClaveUnidad'])?>,</span>
                    <span>Unidad: <?=$this->e($concepto['Unidad'] ?: '(ninguna)')?></span>
                </p>
                <p>
                    <strong>Cantidad: </strong><?=$this->e($concepto['Cantidad'])?>,
                    <strong>Valor unitario: </strong><?=$this->e($concepto['ValorUnitario'])?>,
                    <strong>Descuento: </strong><?=$this->e($concepto['Descuento'] ?: '(ninguno)')?>,
                    <strong>Importe: </strong><?=$this->e($concepto['Importe'])?>
                </p>
                <?php if (null !== $cuentaTerceros) : ?>
                    <p>
                        <strong>A cuenta terceros</strong>
                        Rfc a cuenta terceros: <?=$this->e($cuentaTerceros['RfcACuentaTerceros'])?>,
                        Nombre a cuenta terceros: <?=$this->e($cuentaTerceros['NombreACuentaTerceros'])?>,
                        Regimen fiscal a cuenta terceros:
                            <?=$catalogos->catRegimenFiscal($cuentaTerceros['RegimenFiscalACuentaTerceros'])?>,
                        Domicilio fiscal a cuenta terceros:
                            <?=$this->e($cuentaTerceros['DomicilioFiscalACuentaTerceros'])?>
                    </p>
                <?php endif; ?>
                <?php if (null !== $informacionAduaneras) : ?>
                    <p>
                        <strong>Informacion aduanera</strong>
                        <?php foreach ($concepto->searchNodes('cfdi:InformacionAduanera') as $informacionAduanera) : ?>
                            Pedimento: <?=$this->e($informacionAduanera['NumeroPedimento'])?>
                        <?php endforeach; ?>
                    </p>
                <?php endif; ?>
                <?php foreach ($concepto->searchNodes('cfdi:CuentaPedial') as $cuentaPredial) : ?>
                    <p>
                        <strong>Cuenta predial: </strong><?=$this->e($cuentaPredial['Numero'])?>
                    </p>
                <?php endforeach; ?>
                <?php foreach ($concepto->searchNodes('cfdi:Parte') as $parte) : ?>
                    <p style="padding-left: 5mm">
                        <strong>Parte: </strong><?=$this->e($parte['Descripcion'])?>,
                        <br/>
                        <span>Clave SAT: <?=$this->e($parte['ClaveProdServ'])?>,</span>
                        <span>No identificación: <?=$this->e($parte['NoIdentificacion'] ?: '(ninguno)')?>,</span>
                        <span>Cantidad: <?=$this->e($parte['Cantidad'])?>,</span>
                        <span>Unidad: <?=$this->e($parte['Unidad'] ?: '(ninguna)')?>,</span>
                        <span>Valor unitario: <?=$this->e($parte['ValorUnitario'] ?: '0')?></span>,
                        <span>Importe: <?=$this->e($parte['Importe'] ?: '0')?></span>
                        <?php foreach ($parte->searchNodes('cfdi:InformacionAduanera') as $informacionAduanera) : ?>
                            <br/>Pedimento: <?=$this->e($informacionAduanera['NumeroPedimento'])?>
                        <?php endforeach; ?>
                    </p>
                <?php endforeach; ?>
                <?php foreach ($conceptoTraslados as $impuesto) : ?>
                    <p>
                        <strong>Traslado</strong>
                        Impuesto: <?=$catalogos->catImpuesto($impuesto['Impuesto'])?>,
                        Base: <?=$this->e($impuesto['Base'])?>,
                        Tipo factor: <?=$this->e($impuesto['TipoFactor'])?>,
                        Tasa o cuota: <?=$this->e($impuesto['TasaOCuota'])?>,
                        <strong>Importe: <?=$this->e($impuesto['Importe'])?></strong>
                    </p>
                <?php endforeach; ?>
                <?php foreach ($conceptoRetenciones as $impuesto) : ?>
                    <p>
                        <strong>Retención</strong>
                        Impuesto: <?=$catalogos->catImpuesto($impuesto['Impuesto'])?>,
                        Base: <?=$this->e($impuesto['Base'])?>,
                        Tipo factor: <?=$this->e($impuesto['TipoFactor'])?>,
                        Tasa o cuota: <?=$this->e($impuesto['TasaOCuota'])?>,
                        <strong>Importe: <?=$this->e($impuesto['Importe'])?></strong>
                    </p>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <?php
    $pagos = $comprobante->searchNodes('cfdi:Complemento', 'pago10:Pagos', 'pago10:Pago');
    $pagoCounter = 0;
    $pagoCount = $pagos->count();
    ?>
    <?php foreach ($pagos as $pago) : ?>
        <?php
        $pagoCounter = $pagoCounter + 1;
        $doctoRelacionados = $pago->searchNodes('pago10:DoctoRelacionado');
        ?>
        <div class="panel">
            <div class="title">Pago: <?=$this->e($pagoCounter)?> de <?=$this->e($pagoCount)?></div>
            <div class="content">
                <p>
                    <span><strong>Fecha de pago:</strong> <?=$this->e($pago['FechaPago'])?>,</span>
                    <span><strong>Forma de pago:</strong> <?=$this->e($pago['FormaDePagoP'])?>,</span>
                    <span><strong>Moneda:</strong> <?=$this->e($pago['MonedaP'])?>,</span>
                    <span><strong>Monto:</strong> <?=$this->e($pago['Monto'])?></span>
                    <?php if ('' !== $pago['TipoCambioP']) : ?>
                      <span><strong>Tipo Cambio:</strong> <?=$this->e($pago['TipoCambioP'])?></span>
                    <?php endif; ?>
                    <?php if ('' !== $pago['NumOperacion']) : ?>
                      <span><strong>Número operación:</strong> <?=$this->e($pago['NumOperacion'])?></span>
                    <?php endif; ?>
                    <?php if ('' !== $pago['RfcEmisorCtaOrd']) : ?>
                      <span><strong>RFC Emisor Cta Ord:</strong> <?=$this->e($pago['RfcEmisorCtaOrd'])?></span>
                    <?php endif; ?>
                    <?php if ('' !== $pago['NomBancoOrdExt']) : ?>
                      <span><strong>Nombre Banco Ord Extranjero:</strong> <?=$this->e($pago['NomBancoOrdExt'])?></span>
                    <?php endif; ?>
                    <?php if ('' !== $pago['CtaOrdenante']) : ?>
                      <span><strong>Cuenta Ord:</strong> <?=$this->e($pago['CtaOrdenante'])?></span>
                    <?php endif; ?>
                    <?php if ('' !== $pago['RfcEmisorCtaBen']) : ?>
                      <span><strong>RFC Emisor Cta Ben:</strong> <?=$this->e($pago['RfcEmisorCtaBen'])?></span>
                    <?php endif; ?>
                    <?php if ('' !== $pago['CtaBeneficiario']) : ?>
                      <span><strong>Cuenta Ben:</strong> <?=$this->e($pago['CtaBeneficiario'])?></span>
                    <?php endif; ?>
                    <?php if ('' !== $pago['TipoCadPago']) : ?>
                      <span><strong>Tipo cadena de pago:</strong> <?=$this->e($pago['TipoCadPago'])?></span>
                    <?php endif; ?>
                </p>
                <?php if ('' !== $pago['CertPago']) : ?>
                  <p>
                    <strong>Certificado de pago:</strong>
                    <span><?=$this->e($pago['CertPago'])?></span>
                  </p>
                <?php endif; ?>
                <?php if ('' !== $pago['CadPago']) : ?>
                  <p>
                    <strong>Cadena de pago:</strong>
                    <span><?=$this->e($pago['CadPago'])?></span>
                  </p>
                <?php endif; ?>
                <?php if ('' !== $pago['SelloPago']) : ?>
                  <p>
                    <strong>Sello de pago:</strong>
                    <span><?=$this->e($pago['SelloPago'])?></span>
                  </p>
                <?php endif; ?>
                <?php if ($doctoRelacionados->count() > 0) : ?>
                    <p style="margin: 10px 0 10px 0;">
                      <strong>Documentos relacionados</strong>
                    </p>
                    <?php foreach ($doctoRelacionados as $doctoRelacionado) : ?>
                        <p style="margin-bottom: 10px;">
                          <strong>Id Documento: </strong><span><?=$this->e($doctoRelacionado['IdDocumento'])?></span>
                          <strong>Serie: </strong><span><?=$this->e($doctoRelacionado['Serie'])?></span>
                          <strong>Folio: </strong><span><?=$this->e($doctoRelacionado['Folio'])?></span>
                          <strong>Moneda DR: </strong><span><?=$this->e($doctoRelacionado['MonedaDR'])?></span>
                          <strong>Tipo de cambio DR: </strong>
                            <span><?=$this->e($doctoRelacionado['TipoCambioDR'])?></span>
                          <strong>Método de pago DR: </strong>
                            <span><?=$this->e($doctoRelacionado['MetodoDePagoDR'])?></span>
                          <strong>Número parcialidad: </strong>
                            <span><?=$this->e($doctoRelacionado['NumParcialidad'])?></span>
                          <strong>Imp pagado: </strong><span><?=$this->e($doctoRelacionado['ImpPagado'])?></span>
                          <strong>Imp saldo insoluto: </strong>
                          <span><?=$this->e($doctoRelacionado['ImpSaldoInsoluto'])?></span>
                          <strong>Imp saldo anterior: </strong>
                            <span><?=$this->e($doctoRelacionado['ImpSaldoAnt'])?></span>
                        </p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <?php
    $pagos20 = $comprobante->searchNodes('cfdi:Complemento', 'pago20:Pagos', 'pago20:Pago');
    $pago20Count = $pagos20->count();
    ?>
    <?php foreach ($pagos20 as $pago20) : ?>
        <?php
        $pagoCounter = $pagoCounter + 1;
        $doctoRelacionados = $pago20->searchNodes('pago20:DoctoRelacionado');
        ?>
        <div class="panel">
            <div class="title">Pago: <?=$this->e($pagoCounter)?> de <?=$this->e($pago20Count)?></div>
            <div class="content">
                <p>
                    <span><strong>Fecha de pago:</strong> <?=$this->e($pago20['FechaPago'])?>,</span>
                    <span><strong>Forma de pago:</strong> <?=$catalogos->catFormaPago($pago20['FormaDePagoP'])?>,</span>
                    <span><strong>Moneda:</strong> <?=$this->e($pago20['MonedaP'])?>,</span>
                    <span><strong>Monto:</strong> <?=$this->e($pago20['Monto'])?></span>
                    <?php if ('' !== $pago20['TipoCambioP']) : ?>
                        <span><strong>Tipo cambio:</strong> <?=$this->e($pago20['TipoCambioP'])?></span>
                    <?php endif; ?>
                    <?php if ('' !== $pago20['NumOperacion']) : ?>
                        <span><strong>Número operación:</strong> <?=$this->e($pago20['NumOperacion'])?></span>
                    <?php endif; ?>
                    <?php if ('' !== $pago20['RfcEmisorCtaOrd']) : ?>
                        <span><strong>RFC emisor cta ord:</strong> <?=$this->e($pago20['RfcEmisorCtaOrd'])?></span>
                    <?php endif; ?>
                    <?php if ('' !== $pago20['NomBancoOrdExt']) : ?>
                        <span><strong>Nombre banco ord extranjero:</strong>
                        <?=$this->e($pago20['NomBancoOrdExt'])?></span>
                    <?php endif; ?>
                    <?php if ('' !== $pago20['CtaOrdenante']) : ?>
                        <span><strong>Cuenta ord:</strong> <?=$this->e($pago20['CtaOrdenante'])?></span>
                    <?php endif; ?>
                    <?php if ('' !== $pago20['RfcEmisorCtaBen']) : ?>
                        <span><strong>RFC emisor cta ben:</strong> <?=$this->e($pago20['RfcEmisorCtaBen'])?></span>
                    <?php endif; ?>
                    <?php if ('' !== $pago20['CtaBeneficiario']) : ?>
                        <span><strong>Cuenta ben:</strong> <?=$this->e($pago20['CtaBeneficiario'])?></span>
                    <?php endif; ?>
                    <?php if ('' !== $pago20['TipoCadPago']) : ?>
                        <span><strong>Tipo cadena de pago:</strong> <?=$this->e($pago20['TipoCadPago'])?></span>
                    <?php endif; ?>
                </p>
                <?php if ('' !== $pago20['CertPago']) : ?>
                    <p>
                        <strong>Certificado de pago:</strong>
                        <span><?=$this->e($pago20['CertPago'])?></span>
                    </p>
                <?php endif; ?>
                <?php if ('' !== $pago20['CadPago']) : ?>
                    <p>
                        <strong>Cadena de pago:</strong>
                        <span><?=$this->e($pago20['CadPago'])?></span>
                    </p>
                <?php endif; ?>
                <?php if ('' !== $pago20['SelloPago']) : ?>
                    <p>
                        <strong>Sello de pago:</strong>
                        <span><?=$this->e($pago20['SelloPago'])?></span>
                    </p>
                <?php endif; ?>
                <?php if ($doctoRelacionados->count() > 0) : ?>
                    <p style="margin: 10px 0 5px 0;">
                        <strong>Documentos relacionados</strong>
                    </p>
                    <?php foreach ($doctoRelacionados as $doctoRelacionado) : ?>
                        <p style="margin-bottom: 10px;">
                            <strong>Id Documento: </strong><span><?=$this->e($doctoRelacionado['IdDocumento'])?></span>
                            <strong>Serie: </strong><span><?=$this->e($doctoRelacionado['Serie'])?></span>
                            <strong>Folio: </strong><span><?=$this->e($doctoRelacionado['Folio'])?></span>
                            <strong>Moneda DR: </strong><span><?=$this->e($doctoRelacionado['MonedaDR'])?></span>
                            <?php if ('' !== $doctoRelacionado['EquivalenciaDR']) : ?>
                                <strong>Equivalencia DR: </strong>
                                <span><?=$this->e($doctoRelacionado['EquivalenciaDR'])?></span>
                            <?php endif; ?>
                            <strong>Número parcialidad: </strong>
                                <span><?=$this->e($doctoRelacionado['NumParcialidad'])?></span>
                            <strong>Importe pagado: </strong><span><?=$this->e($doctoRelacionado['ImpPagado'])?></span>
                            <strong>Importe saldo insoluto: </strong>
                                <span><?=$this->e($doctoRelacionado['ImpSaldoInsoluto'])?></span>
                            <strong>Importe saldo anterior: </strong>
                                <span><?=$this->e($doctoRelacionado['ImpSaldoAnt'])?></span>
                            <strong>Objeto Imp DR: </strong>
                                <span><?=$catalogos->catObjetoImp($doctoRelacionado['ObjetoImpDR'])?></span>
                        </p>
                        <?php
                        $impuestos = $doctoRelacionado->searchNode('pago20:ImpuestosDR');
                        ?>
                        <?php if (null !== $impuestos) : ?>
                            <?php
                            $retenciones = $impuestos->searchNodes('pago20:RetencionesDR', 'pago20:RetencionDR');
                            $traslados = $impuestos->searchNodes('pago20:TrasladosDR', 'pago20:TrasladoDR');
                            ?>
                            <p style="margin: 0 10px 5px 0;">
                                <strong>Impuestos Docto Relacionado</strong>
                            </p>
                            <table style="width: 94%">
                                <tr>
                                    <th style="width: 15%">Tipo</th>
                                    <th style="width: 15%">Base</th>
                                    <th style="width: 15%">Impuesto</th>
                                    <th style="width: 15%">Tipo factor</th>
                                    <th style="width: 20%">Tasa o cuota</th>
                                    <th style="width: 20%">Importe</th>
                                </tr>
                                <?php foreach ($traslados as $impuesto) : ?>
                                    <tr>
                                        <td>Traslado</td>
                                        <td><?=$this->e($impuesto['BaseDR'])?></td>
                                        <td><?=$catalogos->catImpuesto($impuesto['ImpuestoDR'])?></td>
                                        <td><?=$this->e($impuesto['TipoFactorDR'])?></td>
                                        <td><?=$this->e($impuesto['TasaOCuotaDR'])?></td>
                                        <td>$<?=$this->e($impuesto['ImporteDR'])?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php foreach ($retenciones as $impuesto) : ?>
                                    <tr>
                                        <td>Retención</td>
                                        <td><?=$this->e($impuesto['BaseDR'])?></td>
                                        <td><?=$catalogos->catImpuesto($impuesto['ImpuestoDR'])?></td>
                                        <td><?=$this->e($impuesto['TipoFactorDR'])?></td>
                                        <td><?=$this->e($impuesto['TasaOCuotaDR'])?></td>
                                        <td>$<?=$this->e($impuesto['ImporteDR'])?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        <?php endif ?>
                    <br>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php
                $impuestos = $pago20->searchNode('pago20:ImpuestosP');
                ?>
                <?php if (null !== $impuestos) : ?>
                    <?php
                    $retenciones = $impuestos->searchNodes('pago20:RetencionesP', 'pago20:RetencionP');
                    $traslados = $impuestos->searchNodes('pago20:TrasladosP', 'pago20:TrasladoP');
                    ?>
                    <p style="margin: 10px 0 5px 0;">
                        <strong>Impuestos Pago</strong>
                    </p>
                    <table style="width: 94%">
                        <tr>
                            <th style="width: 15%">Tipo</th>
                            <th style="width: 15%">Base</th>
                            <th style="width: 15%">Impuesto</th>
                            <th style="width: 15%">Tipo factor</th>
                            <th style="width: 20%">Tasa o cuota</th>
                            <th style="width: 20%">Importe</th>
                        </tr>
                        <?php foreach ($traslados as $impuesto) : ?>
                            <tr>
                                <td>Traslado</td>
                                <td><?=$this->e($impuesto['BaseP'])?></td>
                                <td><?=$catalogos->catImpuesto($impuesto['ImpuestoP'])?></td>
                                <td><?=$this->e($impuesto['TipoFactorP'])?></td>
                                <td><?=$this->e($impuesto['TasaOCuotaP'])?></td>
                                <td>$<?=$this->e($impuesto['ImporteP'])?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php foreach ($retenciones as $impuesto) : ?>
                            <tr>
                                <td>Retención</td>
                                <td><?=$this->e($impuesto['BaseP'])?></td>
                                <td><?=$catalogos->catImpuesto($impuesto['ImpuestoP'])?></td>
                                <td><?=$this->e($impuesto['TipoFactorP'])?></td>
                                <td><?=$this->e($impuesto['TasaOCuotaP'])?></td>
                                <td>$<?=$this->e($impuesto['ImporteP'])?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
    <?php
    $pagoTotales = $comprobante->searchNode('cfdi:Complemento', 'pago20:Pagos', 'pago20:Totales');
    ?>
    <?php if (null !== $pagoTotales) : ?>
        <div class="panel">
            <div class="title">Totales del complemento de pago</div>
            <div class="content">
                <p>
                    <?php if ('' !== $pagoTotales['TotalRetencionesIVA']) : ?>
                        <span>
                            <strong>Total retenciones IVA:</strong>
                            <?=$this->e($pagoTotales['TotalRetencionesIVA'])?>
                        </span>
                    <?php endif; ?>
                    <?php if ('' !== $pagoTotales['TotalRetencionesISR']) : ?>
                        <span>
                            <strong>Total retenciones ISR:</strong>
                            <?=$this->e($pagoTotales['TotalRetencionesISR'])?>
                        </span>
                    <?php endif; ?>
                    <?php if ('' !== $pagoTotales['TotalRetencionesIEPS']) : ?>
                        <span>
                            <strong>Total retenciones IEPS:</strong>
                            <?=$this->e($pagoTotales['TotalRetencionesIEPS'])?>
                        </span>
                    <?php endif; ?>
                    <?php if ('' !== $pagoTotales['TotalTrasladosBaseIVA16']) : ?>
                        <span>
                            <strong>Total traslados base IVA 16:</strong>
                            <?=$this->e($pagoTotales['TotalTrasladosBaseIVA16'])?>
                        </span>
                    <?php endif; ?>
                    <?php if ('' !== $pagoTotales['TotalTrasladosImpuestoIVA16']) : ?>
                        <span>
                            <strong>Total traslados impuesto IVA 16:</strong>
                            <?=$this->e($pagoTotales['TotalTrasladosImpuestoIVA16'])?>
                        </span>
                    <?php endif; ?>
                    <?php if ('' !== $pagoTotales['TotalTrasladosBaseIVA8']) : ?>
                        <span>
                            <strong>Total traslados base IVA 8:</strong>
                            <?=$this->e($pagoTotales['TotalTrasladosBaseIVA8'])?>
                        </span>
                    <?php endif; ?>
                    <?php if ('' !== $pagoTotales['TotalTrasladosImpuestoIVA8']) : ?>
                        <span>
                            <strong>Total traslados impuesto IVA 8:</strong>
                            <?=$this->e($pagoTotales['TotalTrasladosImpuestoIVA8'])?>
                        </span>
                    <?php endif; ?>
                    <?php if ('' !== $pagoTotales['TotalTrasladosBaseIVA0']) : ?>
                        <span>
                            <strong>Total traslados base IVA 0:</strong>
                            <?=$this->e($pagoTotales['TotalTrasladosBaseIVA0'])?>
                        </span>
                    <?php endif; ?>
                    <?php if ('' !== $pagoTotales['TotalTrasladosImpuestoIVA0']) : ?>
                        <span>
                            <strong>Total traslados impuesto IVA 0:</strong>
                            <?=$this->e($pagoTotales['TotalTrasladosImpuestoIVA0'])?>
                        </span>
                    <?php endif; ?>
                    <?php if ('' !== $pagoTotales['TotalTrasladosBaseIVAExento']) : ?>
                        <span>
                            <strong>Total traslados base IVA exento:</strong>
                            <?=$this->e($pagoTotales['TotalTrasladosBaseIVAExento'])?>
                        </span>
                    <?php endif; ?>
                    <span>
                        <strong>Monto total pagos:</strong>
                        <?=$this->e($pagoTotales['MontoTotalPagos'])?>
                    </span>
                </p>
            </div>
        </div>
    <?php endif; ?>

    <?php
    $impuestos = $comprobante->searchNode('cfdi:Impuestos');
    ?>
    <?php if (null !== $impuestos) : ?>
        <?php
        $traslados = $impuestos->searchNodes('cfdi:Traslados', 'cfdi:Traslado');
        $retenciones = $impuestos->searchNodes('cfdi:Retenciones', 'cfdi:Retencion');
        ?>
        <div class="panel">
            <div class="title">Impuestos</div>
            <div class="content">
                <table style="width: 94%">
                    <tr>
                        <th style="width: 15%">Tipo</th>
                        <th style="width: 15%">Base</th>
                        <th style="width: 15%">Impuesto</th>
                        <th style="width: 15%">Tipo factor</th>
                        <th style="width: 20%">Tasa o cuota</th>
                        <th style="width: 20%">Importe</th>
                    </tr>
                    <?php foreach ($traslados as $impuesto) : ?>
                        <tr>
                            <th>Traslado</th>
                            <td><?=$this->e($impuesto['Base'])?></td>
                            <td><?=$catalogos->catImpuesto($impuesto['Impuesto'])?></td>
                            <td><?=$this->e($impuesto['TipoFactor'])?></td>
                            <td><?=$this->e($impuesto['TasaOCuota'])?></td>
                            <td><?=$this->e($impuesto['Importe'])?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php foreach ($retenciones as $impuesto) : ?>
                        <tr>
                            <th>Retención</th>
                            <td><?=$this->e($impuesto['Base'])?></td>
                            <td><?=$catalogos->catImpuesto($impuesto['Impuesto'])?></td>
                            <td><?=$this->e($impuesto['TipoFactor'])?></td>
                            <td><?=$this->e($impuesto['TasaOCuota'])?></td>
                            <td><?=$this->e($impuesto['Importe'])?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    <?php endif ?>
    <div class="panel">
        <div class="title">Totales</div>
        <div class="content">
            <table style="width: 97%">
                <tr>
                    <th style="width: 10%">Moneda</th>
                    <th style="width: 15%">Tipo de cambio</th>
                    <th style="width: 15%">Subtotal</th>
                    <th style="width: 15%">Descuentos</th>
                    <th style="width: 15%">Impuestos trasladados</th>
                    <th style="width: 15%">Impuestos retenidos</th>
                    <th style="width: 15%">Total</th>
                </tr>
                <tr>
                    <td><?=$this->e($comprobante['Moneda'])?></td>
                    <td><?=$this->e($comprobante['TipoCambio'])?></td>
                    <td><?=$this->e($comprobante['SubTotal'])?></td>
                    <td><?=$this->e($comprobante['Descuento'])?></td>
                    <td><?=$this->e($totalImpuestosTrasladados)?></td>
                    <td><?=$this->e($totalImpuestosRetenidos)?></td>
                    <td><?=$this->e($comprobante['Total'])?></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="panel">
        <div class="title">Datos fiscales</div>
        <div class="content">
            <table class="tabular">
                <tr>
                    <th>Sello CFDI:</th>
                    <td><?=$this->e(chunk_split($tfd['SelloCFD'], 100))?></td>
                </tr>
                <tr>
                    <th>Sello SAT:</th>
                    <td><?=$this->e(chunk_split($tfd['SelloSAT'], 100))?></td>
                </tr>
                <tr>
                    <th>Cadena TFD:</th>
                    <td><?=$this->e(chunk_split($cfdiData->tfdSourceString(), 100))?></td>
                </tr>
                <tr>
                    <th>Verificación:</th>
                    <td>
                        <a href="<?=$this->e($cfdiData->qrUrl())?>">
                            <?=$this->e(str_replace('?', "\n?", $cfdiData->qrUrl()))?>
                        </a>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</page>
