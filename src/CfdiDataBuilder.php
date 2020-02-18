<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf;

use CfdiUtils\CadenaOrigen\DOMBuilder;
use CfdiUtils\CadenaOrigen\XsltBuilderInterface;
use CfdiUtils\ConsultaCfdiSat\RequestParameters;
use CfdiUtils\Nodes\NodeInterface;
use CfdiUtils\Nodes\XmlNodeUtils;
use CfdiUtils\TimbreFiscalDigital\TfdCadenaDeOrigen;
use CfdiUtils\XmlResolver\XmlResolver;

class CfdiDataBuilder
{
    /** @var XmlResolver */
    private $xmlResolver;

    /** @var XsltBuilderInterface */
    private $xsltBuilder;

    public function __construct()
    {
        $this->xmlResolver = new XmlResolver('');
        $this->xsltBuilder = new DOMBuilder();
    }

    public function withXmlResolver(XmlResolver $xmlResolver): self
    {
        $this->xmlResolver = $xmlResolver;
        return $this;
    }

    public function withXsltBuilder(XsltBuilderInterface $xsltBuilder): self
    {
        $this->xsltBuilder = $xsltBuilder;
        return $this;
    }

    public function xmlResolver(): XmlResolver
    {
        return $this->xmlResolver;
    }

    public function xsltBuilder(): XsltBuilderInterface
    {
        return $this->xsltBuilder;
    }

    /**
     * @param NodeInterface<NodeInterface> $comprobante
     * @return CfdiData
     */
    public function build(NodeInterface $comprobante): CfdiData
    {
        return new CfdiData(
            $comprobante,
            $this->createQrUrl($comprobante),
            $this->createTfdSourceString($comprobante)
        );
    }

    /**
     * @param NodeInterface<NodeInterface> $comprobante
     * @return string
     */
    public function createTfdSourceString(NodeInterface $comprobante): string
    {
        $tfd = $comprobante->searchNode('cfdi:Complemento', 'tfd:TimbreFiscalDigital');
        if (null === $tfd) {
            return '';
        }
        $tfdCadenaOrigen = new TfdCadenaDeOrigen($this->xmlResolver(), $this->xsltBuilder());
        return $tfdCadenaOrigen->build(XmlNodeUtils::nodeToXmlString($tfd), $tfd['Version'] ?: $tfd['version']);
    }

    /**
     * @param NodeInterface<NodeInterface> $comprobante
     * @return string
     */
    public function createQrUrl(NodeInterface $comprobante): string
    {
        $parameters = new RequestParameters(
            $comprobante['Version'],
            $comprobante->searchAttribute('cfdi:Emisor', 'Rfc'),
            $comprobante->searchAttribute('cfdi:Receptor', 'Rfc'),
            $comprobante['Total'],
            $comprobante->searchAttribute('cfdi:Complemento', 'tfd:TimbreFiscalDigital', 'UUID'),
            $comprobante['Sello']
        );
        return $parameters->expression();
    }
}
