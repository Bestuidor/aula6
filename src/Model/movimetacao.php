<?php

namespace App\Model;

class Movimentacao
{
    private $id;
    private $idPessoa;
    private $credito;
    private $debito;
    private $dataOperacao;
    private $observacao;
    private $createdAt;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getIdPessoa()
    {
        return $this->idPessoa;
    }

    public function setIdPessoa($idPessoa)
    {
        $this->idPessoa = $idPessoa;
    }

    public function getCredito()
    {
        return $this->credito;
    }

    public function setCredito($credito)
    {
        $this->credito = $credito;
    }

    public function getDebito()
    {
        return $this->debito;
    }

    public function setDebito($debito)
    {
        $this->debito = $debito;
    }

    public function getDataOperacao()
    {
        return $this->dataOperacao;
    }

    public function setDataOperacao($dataOperacao)
    {
        $this->dataOperacao = $dataOperacao;
    }

    public function getObservacao()
    {
        return $this->observacao;
    }

    public function setObservacao($observacao)
    {
        $this->observacao = $observacao;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }
}