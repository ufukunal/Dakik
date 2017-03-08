<?php

namespace KS\Dakik;
use KS\Dakik\DakikException;
use SoapClient;
use SOAPHeader;

class Dakik {

  private $url = 'http://178.210.177.194/dprod/k10service.asmx?wsdl';
  private $ns = 'http://tempuri.org/';
  private $client;
	private $funcs = array();

  function __construct($conf)
  {

    self::setFunctions();

    $options = array("soap_version" => SOAP_1_1,  "trace" => 1, "exceptions" => false);

    if(!isset($conf['username']) || !isset($conf['user_id']) || !isset($conf['password'])) {
      throw new DakikException("Dakik Teslimat Ayarları Girilmedi");
    } else {

      try {
        $this->client = new SoapClient($this->url, $options);
        $headerbody = array('UserID' => $conf['user_id'], 'Username' => $conf['username'],'Password'=> $conf['password']);
        $header = new SOAPHeader($this->ns, 'AuthHeader', $headerbody);
        $this->client->__setSoapHeaders($header);
      } catch (SoapFault $sf) {
        throw new DakikException($sf);
      }

    }

  }

  public function __call($method, $args = null) {
    $check = $this->searchForName($method,$this->funcs);
    if(!is_null($check)){
      return $this->request($check['reqName'],$check['resName'],$args);
    } else {
      return null;
    }
  }

  private function request($function,$response,$params = null){

    $resp = $this->client->__soapCall($function, (!is_null($params) ? $params : array()));

		$data = $this->respToxml($resp->$response->any);

    if(!is_null($data->diffgram->DocumentElement)){
      return $data->diffgram->DocumentElement;
    } else {
      return null;
    }


  }

  private function searchForName($id, $array) {
     foreach ($array as $key => $val) {
         if ($val['name'] === $id) {
             return $array[$key];
         }
     }
     return null;
  }

  protected function respToxml($data){
		$xml = str_replace(array("diffgr:","msdata:"),'', $data);
		$xml    = "<package>".$xml."</package>";
		$data   = simplexml_load_string($xml);
		return $data;
	}

  protected function setFunctions(){

    $this->funcs = array(
        array(
          "name" => 'GetItemStateByCode',
          "reqName" => "GetItemStateByCodeV3",
          "resName" => "GetItemStateByCodeV3Result"
        ),
        array(
          "name" => 'GetItemStateByInvoice',
          "reqName" => "GetItemStateByInvoiceV3",
          "resName" => "GetItemStateByInvoiceV3Result"
        ),
        array(
          "name" => 'GetSemtList',
          "reqName" => "GetSemtListV3",
          "resName" => "GetSemtListV3Result"
        ),
        array(
          "name" => 'GetServisList',
          "reqName" => "GetServisListV3",
          "resName" => "GetServisListV3Result"
        ),
        array(
          "name" => 'GetStatuList',
          "reqName" => "GetStatuListV3",
          "resName" => "GetStatuListV3Result"
        ),
        array(
          "name" => 'SetIptalData',
          "reqName" => "SetIptalDataV3",
          "resName" => "SetIptalDataV3Result"
        ),
        array(
          "name" => 'SetItemData',
          "reqName" => "SetItemDataV3",
          "resName" => "SetItemDataV3Result"
        )
  		);

  }
}
