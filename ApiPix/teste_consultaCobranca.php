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
	
	
	//Consultar transação específica
	
	
	$txid = "3bd2384e6a328ce0948b73be014f46";
	
	
	
	$response = $obj_api_pix->consult_cob($txid);
	
	if ( isset($response["content"]["pix"][0]) && $response["content"]["status"] == "CONCLUIDA" ){ 
		$response["resultado"] = "Pagamento efetivado.";
	}else{
		$now = date("Y-m-d H:i:s");
		
		$calendario = $response["content"]["calendario"];
		$criacao  = $calendario["criacao"];
		$expiracao  = $calendario["expiracao"];
					
		$dateinsec = strtotime($criacao);
		$newdate = $dateinsec + $expiracao;
		
		$exp = date("Y-m-d H:i:s",$newdate);
		$dthr = date("Y-m-d H:i:s",$dateinsec);
		
		$response["expiracao"] = $exp;
		
		if ($now > $exp){
			$response["resultado"] = "Transação Expirada!";						
		}else{
			$response["resultado"] = "Aguardando pagt.";
			
		////Código QR para pagamento PIX
		 $payload_qr_code = $response["content"]["pixCopiaECola"];
			
		 //Instância do QR Code
		 $obj_qr_code = new QrCode($payload_qr_code);
		 
		 $qr_code_image = (new Output\Png)->output($obj_qr_code, 250); 
		 
		 echo '<img src="data:image/png;base64, '. base64_encode($qr_code_image) .'" alt="">';

		}
	}
	
	echo json_encode($response);
	
	
		
}catch(Exception  $e){
	echo "Erro: ". $e->getMessage();
}


?>

