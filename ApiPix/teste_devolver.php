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
	
	
	//Devolver Pix
	
		
	
	$valor = "0.50";
	$txid  = "";	
	
	
	$fetch_cred = "SELECT * FROM `tb_transacao_pix` WHERE `txid`=:txid";
	$query_stmt = $conn->prepare($fetch_cred);
	$query_stmt->bindValue(':txid', $txid,PDO::PARAM_STR);
	$query_stmt->execute();			

	if($query_stmt->rowCount()){
		$rows = $query_stmt->fetchAll(PDO::FETCH_ASSOC);		
		foreach ($rows as $row => $data) {
	
			$response = $obj_api_pix->devolver_pix($data["e2eid"],uniqId(),$valor);
			var_dump($response);	
			if ($response["statusCode"] != "200" ){
				echo "Erro ao gerar Devolução, tente novamente";
				http_response_code(400);
				return;
			}else{
				$set_credit = "UPDATE `tb_transacao_pix` 
								  SET valorDevolv = valorDevolv + :valorDevolv
								WHERE `txid`=:txid";
				$query_stmt = $conn->prepare($set_credit);
				$query_stmt->execute(array( 				
						':txid'        => $txid,
						':valorDevolv' => $valor,
				));		
			}
		}			
	}else{
		echo 'Não identificado transações PIX para o txid!';
	}				
			
	
		
}catch(Exception  $e){
	echo "Erro: ". $e->getMessage();
}



?>

