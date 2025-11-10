<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiToPdf;

use CfdiUtils\Nodes\NodeInterface;
use RuntimeException;

class CfdiData
{
    /** @var NodeInterface<NodeInterface> */
    private NodeInterface $comprobante;

    /** @var NodeInterface<NodeInterface> */
    private NodeInterface $emisor;

    /** @var NodeInterface<NodeInterface> */
    private NodeInterface $receptor;

    /** @var NodeInterface<NodeInterface> */
    private NodeInterface $timbreFiscalDigital;

    private string $qrUrl;

    private string $tfdSourceString;

    /**
     * CfdiData constructor.
     *
     * @param NodeInterface<NodeInterface> $comprobante
     */
    public function __construct(NodeInterface $comprobante, string $qrUrl, string $tfdSourceString)
    {
        $emisor = $comprobante->searchNode('cfdi:Emisor');
        if (null === $emisor) {
            throw new RuntimeException('El CFDI no contiene nodo emisor');
        }
        $receptor = $comprobante->searchNode('cfdi:Receptor');
        if (null === $receptor) {
            throw new RuntimeException('El CFDI no contiene nodo receptor');
        }
        $timbreFiscalDigital = $comprobante->searchNode('cfdi:Complemento', 'tfd:TimbreFiscalDigital');
        if (null === $timbreFiscalDigital) {
            throw new RuntimeException('El CFDI no contiene complemento de timbre fiscal digital');
        }

        $this->comprobante = $comprobante;
        $this->emisor = $emisor;
        $this->receptor = $receptor;
        $this->timbreFiscalDigital = $timbreFiscalDigital;
        $this->qrUrl = $qrUrl;
        $this->tfdSourceString = $tfdSourceString;
    }

    /**
     * @return NodeInterface<NodeInterface>
     */
    public function comprobante(): NodeInterface
    {
        return $this->comprobante;
    }

    /**
     * @return NodeInterface<NodeInterface>
     */
    public function emisor(): NodeInterface
    {
        return $this->emisor;
    }

    /**
     * @return NodeInterface<NodeInterface>
     */
    public function receptor(): NodeInterface
    {
        return $this->receptor;
    }

    /**
     * @return NodeInterface<NodeInterface>
     */
    public function timbreFiscalDigital(): NodeInterface
    {
        return $this->timbreFiscalDigital;
    }

    public function qrUrl(): string
    {
        return $this->qrUrl;
    }

    public function tfdSourceString(): string
    {
        return $this->tfdSourceString;
    }
}
