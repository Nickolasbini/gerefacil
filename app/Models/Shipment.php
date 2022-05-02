<?php

namespace App\Models;

use Exception;

/**
 * @name php-calcular-frete-correios
 */
class Shipment {

	const URL = "http://ws.correios.com.br/calculador/CalcPrecoPrazo.asmx/CalcPrecoPrazo?";
	private $xml;

    private $typesOfShipment = [
        'Sedex' => '40010',
        'PAC' => '41106'
    ];

	public function __construct(
		$CEPorigem = null,
		$CEPdestino = null,
		$peso = null,
		$comprimento = null,
		$altura = null,
		$largura = null,
		$valor = null,
        $service = 40010
	){
		if(!$CEPorigem)
			return;
		$service = ($service ? $service : 40010);
		if(!in_array($service, $this->typesOfShipment)){
			throw new Exception("Invalid type of shipment", 400);
		}
		if ($comprimento < 16) $comprimento = 16;
		$this->xml = simplexml_load_file(
			Shipment::URL."nCdEmpresa=&sDsSenha=&sCepOrigem=".$CEPorigem."&sCepDestino=".$CEPdestino."&nVlPeso=".$peso."&nCdFormato=1&nVlComprimento=".$comprimento."&nVlAltura=".$altura."&nVlLargura=".$largura."&sCdMaoPropria=n&nVlValorDeclarado=".$valor."&sCdAvisoRecebimento=n&nCdServico=".$service."&nVlDiametro=0&StrRetorno=xml");
		if(!$this->xml->Servicos->cServico){
	        throw new Exception("Error Processing Request", 400);
	    }
	    if ($this->xml->Servicos->cServico->Erro != '0' && !$this->xml->Servicos->cServico->Erro == '010') {
	    	throw new Exception($this->xml->Servicos->cServico->MsgErro, 400);
	    }
        
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

	public function getShipmentTypes(){
		return $this->typesOfShipment;
	}

	// checks if max limit was reached
	public static function isBiggerThanShipmentMaxSizeLimit($data)
	{
		$rules = [
			'maxSizeInCentimenters' => '200'
		];
		$sizeSumInCentimeters = (int)$data['length'] + (int)$data['width'] + (int)$data['height'];
		if($sizeSumInCentimeters > $rules['maxSizeInCentimenters']){
			return true;
		}
		return false;
	}

	public static function isSmallerThanShipmentMinSizeLimit($data, $position)
	{
		$rules = [
			'minSizeInCentimenters' => '26'
		];
		$sizeSumInCentimeters = $data[$position]['length'] + $data[$position]['width'] + $data[$position]['height'];
		if($sizeSumInCentimeters < $rules['minSizeInCentimenters'])
			return true;
		return false;
	}
}