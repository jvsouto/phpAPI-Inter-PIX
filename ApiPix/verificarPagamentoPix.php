<?php
date_default_timezone_set("America/Sao_Paulo");

require __DIR__."/Database.php";
require __DIR__."/Pix.php";
require __DIR__."/Auth.php";

//este php é executado pelo servidor para verificar se as transações pendentes foram pagas
try{		
	$db_connection = new Database();
	$conn = $db_connection->dbConnection();
	$auth = new Auth($conn);
    
	$query = "SELECT * FROM tb_transacao_pix WHERE status = 'Criado'";
	$query_stmt = $conn->prepare($query);
	$query_stmt->execute();

	
	if($query_stmt->rowCount()){	
    	echo "\n<br/>".date("Y-m-d H:i:s");
	
		$rows = $query_stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach ($rows as $row => $data) {		
			
			echo "\n<br/>Iniciando consulta ".$data["txid"]." :";
			$obj_api_pix = new Pix();		
			
			$token = $auth->getTokenPix();
			if ($token == null){
				$res = $obj_api_pix->get_access_token();
				$auth->setTokenPix($res["access_token"],$res["expires_in"]);
				$token = $res["access_token"];
			}
					
			$obj_api_pix->setAccessToken($token);			
			
			$response = $obj_api_pix->consult_cob($data["txid"]);					
			if ($response["statusCode"] != "200" ){
				echo "Erro ao consultar Pix, tente novamente";
			}else{				
				if ( array_key_exists("pix",$response["content"]) && $response["content"]["status"] == "CONCLUIDA" ){ 
					echo "Pagamento efetivado.";
					$return = $auth->confirmaPix($data["txid"], $response["content"]["pix"][0]["endToEndId"]);		
				}else{
					$now = date("Y-m-d H:i:s");
					if ($now > $data["dthr_exp"]){
						echo "Transação Expirada!";					
						$return = $auth->expiraPix($data["txid"]);		
					}else{
						echo "Aguardando pagt.";
					}
				}
			}
		}
	}else{
    	echo "\n<br/>".date("Y-m-d H:i:s");		
	}
}catch(Exception  $e){
	echo "Erro: ". $e->getMessage();
}
?>

