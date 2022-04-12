<?php

namespace App\Models;

/**
 * @name php-calcular-frete-correios
 */
class Shipment {

	const URL = "http://ws.correios.com.br/calculador/CalcPrecoPrazo.asmx/CalcPrecoPrazo?";
	private $xml;

    private $typesOfShipment = [
        'Sedex' => '40010',
        '40010' => 'Sedex',
        'Sedex a Cobrar' => '40045',
        '40045' => 'Sedex a Cobrar',
        'PAC' => '41106',
        '41106' => 'PAC',
        'Sedex 10' => '40215',
        '40215' => 'Sedex 10',
        'e-Sedex' => '81019',
        '81019' => 'e-Sedex'
    ];

	public function __construct(
		$CEPorigem,
		$CEPdestino,
		$peso,
		$comprimento,
		$altura,
		$largura,
		$valor,
        $service = 40010
	){
		if ($comprimento < 16) $comprimento = 16;

		$this->xml = simplexml_load_file(
			Shipment::URL."nCdEmpresa=&sDsSenha=&sCepOrigem=".$CEPorigem."&sCepDestino=".$CEPdestino."&nVlPeso=".$peso."&nCdFormato=1&nVlComprimento=".$comprimento."&nVlAltura=".$altura."&nVlLargura=".$largura."&sCdMaoPropria=n&nVlValorDeclarado=".$valor."&sCdAvisoRecebimento=n&nCdServico=".$service."&nVlDiametro=0&StrRetorno=xml");
		if(!$this->xml->Servicos->cServico){
	        throw new Exception("Error Processing Request", 400);
	    }

	    if ($this->xml->Servicos->cServico->Erro != '0' && !$this->xml->Servicos->cServico->Erro == '010') {
	    	throw new Exception($this->xml->Servicos->cServico->MsgErro, 400);
	    }
        dd($this->xml);
	}

	public function getValor(){

		return (float)str_replace(',', '.', $this->xml->Servicos->cServico->Valor);

	}

	public function getPrazoEntrega(){

		return (int)$this->xml->Servicos->cServico->PrazoEntrega;

	}

	public function getValorSemAdicionais(){

		return (float)str_replace(',', '.', $this->xml->Servicos->cServico->ValorSemAdicionais);

	}

	public function getValorMaoPropria(){

		return (float)str_replace(',', '.', $this->xml->Servicos->cServico->ValorMaoPropria);

	}

	public function getValorAvisoRecebimento(){

		return (float)str_replace(',', '.', $this->xml->Servicos->cServico->ValorAvisoRecebimento);

	}

	public function getValorValorDeclarado(){

		return (float)str_replace(',', '.', $this->xml->Servicos->cServico->ValorValorDeclarado);

	}

	public function getMsgErro(){

		return $this->xml->Servicos->cServico->MsgErro;

	}

	public function getObs(){

		return $this->xml->Servicos->cServico->obsFim;

	}

}