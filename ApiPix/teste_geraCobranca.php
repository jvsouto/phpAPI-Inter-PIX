<?php
date_default_timezone_set("America/Sao_Paulo");

require __DIR__."/Database.php";
require __DIR__."/Pix.php";
require __DIR__."/Auth.php";
require __DIR__."/vendor/autoload.php";
    
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;    

try{		
	$db_connection = new Database();
	$conn = $db_connection->dbConnection();
	$auth = new Auth($conn);
    
	$obj_api_pix = new Pix();		
	
	$token = $auth->getTokenPix();
	if ($token == null){
		$res = $obj_api_pix->get_access_token();
		$auth->setTokenPix($res["access_token"],$res["date"],$res["expires_in"]);
		$token = $res["access_token"];
	}
			
	$obj_api_pix->setAccessToken($token);		
	
	//criar cobrança
	
	
	
	
	$valor = "0.50";
	
	
	
	
	
	////$number = rand(100,5000);
	////$valor = number_format((float)$number, 2, ".", "");
	
	$response = $obj_api_pix->create_cob($valor, "teste pedido 1");	
	var_dump($response);
	echo "<BR>";
	echo "<BR>";
	if ($response["statusCode"] != "200" ){
		echo "Erro ao gerar Pix, tente novamente";
		http_response_code(400);
		return;
	}
	
	if (!array_key_exists("txid",$response["content"]) || !array_key_exists("pixCopiaECola",$response["content"]) ){			
		echo "Erro ao gerar Pix, tente novamente.";
		http_response_code(400);
		return;		
	}else{
		$calendario = $response["content"]["calendario"];
		$criacao  = $calendario["criacao"];
		$expiracao  = $calendario["expiracao"];
					
		$dateinsec = strtotime($criacao);
		$newdate = $dateinsec + $expiracao;
		
		$exp = date("Y-m-d H:i:s",$newdate);
		$dthr = date("Y-m-d H:i:s",$dateinsec);
		
		$response["expiracao"] = $exp;
					
		$return = $auth->setTransacaoPix('1',$valor,$response["content"]["txid"],$response["content"]["pixCopiaECola"],$dthr,$exp);		
		if ($return == "Sucesso!"){
			echo json_encode($response);
		}else{
			echo $return;
			http_response_code(400);
		}
	}	
	
	////Código QR para pagamento PIX
	 $payload_qr_code = $response["content"]["pixCopiaECola"];
	 	
	 //Instância do QR Code
	 $obj_qr_code = new QrCode($payload_qr_code);
	 
	 $qr_code_image = (new Output\Png)->output($obj_qr_code, 250); 
	 
	 echo '<img src="data:image/png;base64, '. base64_encode($qr_code_image) .'" alt="">';
	
	
	
		
}catch(Exception  $e){
	echo "Erro: ". $e->getMessage();
}


?>

