<?php
date_default_timezone_set("America/Sao_Paulo");

class Pix{

  private $chavepix;
  private $client_id;
  private $client_secret;
  private $access_token;


  public function __construct(){  	  
    $dados = [
      "chavepix"      => "31602977000102", 
      "client_id"     => "d8672a33-9abe-499a-8e7a-b8981e5f5965", 
      "client_secret" => "f37b09b4-aaac-4c31-a787-d18349c5c58d", 
    ];	
	
    $this->chavepix      = $dados["chavepix"];
    $this->client_id     = $dados["client_id"];
    $this->client_secret = $dados["client_secret"];
  }

  public function genTXID(){  		
	$bytes = random_bytes(15);
	return bin2hex($bytes);
  }

  public function setAccessToken($access_token){  	
    $this->access_token   = $access_token;
  }

  //criar uma cobrança   
  public function create_cob($valor,$descricao){
	
	$txid = $this->genTXID();
	  
	$body = [
	  "calendario" => [
		"expiracao" => 180 //3 minutos
	  ],
	  "valor" => [
		"original" => $valor
	  ],
	  "chave" => $this->chavepix, 
	  "solicitacaoPagador" => $descricao
	];	  
	
    //Headers
    $headers = [
      "Authorization: Bearer ".$this->access_token,
      "Content-Type: application/json"
    ];   
	
    //Configuração do cURL
    $curl = curl_init();	
	curl_setopt($curl, CURLOPT_URL,"https://cdpj.partners.bancointer.com.br/pix/v2/cob/".$txid);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST,"PUT");
	curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_SSLCERT, getcwd() ."\certificado.crt");
	curl_setopt($curl, CURLOPT_SSLKEY, getcwd() ."\chave.key");
	curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body));
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    //Executa o cURL
    $response = curl_exec($curl);
	$error = curl_error($curl);
	$errno = curl_errno($curl);
	$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
	
	$content = (array) json_decode($response, true);	
			
	$retorno = [ 
		"statusCode" => $statusCode,
		"content" => $content,
	];
		
	if ($error != "" ){
		$retorno["error"] = $error;
		$retorno["errno"] = $errno;
	}
	
	return $retorno;
	
  }
  //devolver PIX
  public function devolver_pix($e2eid,$id,$valor){
    $body = [
	  "valor" =>  $valor
	]; 
	  
	
    //Headers
    $headers = [
      "Authorization: Bearer ".$this->access_token,
      "Content-Type: application/json"
    ];   
	
    //Configuração do cURL
    $curl = curl_init();	
	curl_setopt($curl, CURLOPT_URL,"https://cdpj.partners.bancointer.com.br/pix/v2/pix/$e2eid/devolucao/$id");
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST,"PUT");
	curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_SSLCERT, getcwd() ."\certificado.crt");
	curl_setopt($curl, CURLOPT_SSLKEY, getcwd() ."\chave.key");
	curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body));
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    //Executa o cURL
    $response = curl_exec($curl);
	$error = curl_error($curl);
	$errno = curl_errno($curl);
	$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
	
	$content = (array) json_decode($response, true);	
			
	$retorno = [ 
		"statusCode" => $statusCode,
		"content" => $content,
	];
		
	if ($error != "" ){
		$retorno["error"] = $error;
		$retorno["errno"] = $errno;
	}
	
	return $retorno;
	
  }

  //consultar uma cobrança
  public function consult_cob($txid){
		
    //Headers
    $headers = [
      "Authorization: Bearer ".$this->access_token,
      "Content-Type: application/json"
    ];   
	
    //Configuração do cURL
    $curl = curl_init();	
	curl_setopt($curl, CURLOPT_URL,"https://cdpj.partners.bancointer.com.br/pix/v2/cob/".$txid);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST,"GET");
	curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_SSLCERT, getcwd() ."\certificado.crt");
	curl_setopt($curl, CURLOPT_SSLKEY, getcwd() ."\chave.key");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    //Executa o cURL
    $response = curl_exec($curl);
	$error = curl_error($curl);
	$errno = curl_errno($curl);
	$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
	
	$content = (array) json_decode($response, true);	
			
	$retorno = [ 
		"statusCode" => $statusCode,
		"content" => $content,
	];
		
	return $retorno;
	
  }
  
  public function consult_all_pix($ini,$fim,$pag){	  
    //Headers
    $headers = [
      "Authorization: Bearer ".$this->access_token,
      "Content-Type: application/json"
    ];   
	
    //Configuração do cURL
    $curl = curl_init();	
	curl_setopt($curl, CURLOPT_URL,"https://cdpj.partners.bancointer.com.br/pix/v2/cob?inicio=$ini&fim=$fim&paginacao.paginaAtual=$pag");
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST,"GET");
	curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_SSLCERT, getcwd() ."\certificado.crt");
	curl_setopt($curl, CURLOPT_SSLKEY, getcwd() ."\chave.key");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    //Executa o cURL
    $response = curl_exec($curl);
	$error = curl_error($curl);
	$errno = curl_errno($curl);
	$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
	
	$content = (array) json_decode($response, true);	
			
	$retorno = [ 
		"statusCode" => $statusCode,
		"content" => $content,
	];
		
	return $retorno;
	
  }

  //Método responsável por obter o token de acesso às API"s Pix
  public function get_access_token(){
	     
	$date = date("Y-m-d H:i:s");

    //Configuração do cURL
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL,"https://cdpj.partners.bancointer.com.br/oauth/v2/token");
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_SSLCERT, getcwd() ."\certificado.crt");
	curl_setopt($curl, CURLOPT_SSLKEY, getcwd() ."\chave.key");
	curl_setopt($curl, CURLOPT_POSTFIELDS,
		http_build_query(array( "client_id"     => $this->client_id,
								"client_secret" => $this->client_secret,
								"scope"         => "cob.read cob.write pix.read pix.write",
								"grant_type"    => "client_credentials")));
	curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded"));

    //Executa o cURL
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

	$response = curl_exec($curl);
	$error = curl_error($curl);
	$errno = curl_errno($curl);
	$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	curl_close ($curl);
 
    $response_array = json_decode($response, true);    
	$response_array["date"] = $date;
    
	return $response_array; 

  }

}
?>
