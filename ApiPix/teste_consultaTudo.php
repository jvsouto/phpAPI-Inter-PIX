<?php
date_default_timezone_set("America/Sao_Paulo");

require __DIR__."/Database.php";
require __DIR__."/Pix.php";
require __DIR__."/Auth.php";

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
	

	////consultar todas cobranças do dia	
	
	
	$date = "2023-03-23";
	
	
	
	$response = $obj_api_pix->consult_all_pix($date."T00:00:00-03:00",$date."T23:59:59-03:00",null);
	if (array_key_exists("error",$response)){
		echo "<h2>Erro: ".$response["error"]."</h2>";
	}else{ 
		if (array_key_exists("erros",$response["content"])){
			$erro = $response["content"]["erros"][0];
			echo "<h2>".$erro["mensagem"]."</h2>";
		}else{			
			$itens = $response["content"]["cobs"];
		
			$qtd = $response["content"]["parametros"]["paginacao"]["quantidadeTotalDeItens"];
			$paginas = $response["content"]["parametros"]["paginacao"]["quantidadeDePaginas"];
			$pagina = 1;
			while( $pagina < $paginas ){
				$response2 = $obj_api_pix->consult_all_pix($date."T00:00:00-03:00",$date."T23:59:59-03:00",$pagina);
					
				$itens_temp = $itens;	
				$itens = array_merge($itens_temp, $response2["content"]["cobs"]);
				$pagina++;
			}
			
			echo '
			<table>	
			<tr>
				<th>TXID</th>
				<th>Valor</th>
				<th>Horario</th>
				<th>STATUS</th>
				<th>PAGO</th>
				<th>Devoluções</th>
			</tr>
			<tbody>';
			
			foreach($itens as $key){ 		
				if(isset($key["txid"])){	//Apenas para transações com identificador
				if(isset($key["pix"][0])){	//Apenas para transações pagas
					echo '<tr>';
					
						echo '<td>'; if(isset($key["txid"])){echo $key["txid"];} echo '</td>';
						
						echo '<td>'; echo $key["valor"]["original"]; echo '</td>';
						
						echo '<td>';
							$date = strtotime($key["calendario"]["criacao"]);
							echo date("d/M/Y H:i:s", $date); 
						echo '<td>'; echo $key["status"];echo '</td>';
						
						echo '<td>';
						if(isset($key["pix"][0]["horario"])){
							$date = strtotime($key["pix"][0]["horario"]);
							echo date("d/M/Y H:i:s", $date); 				
						}
						echo '</td>';
						
						echo '<td>'; 
						if(!empty($key["pix"][0]["devolucoes"])){
							echo build_table($key["pix"][0]["devolucoes"]);
						}  
						echo '</td>';
					echo '</tr>';
				}
				}
			}	
	
			echo '
			</tbody>
			</table>
			<br>';
		}
	}
	
	
		
}catch(Exception  $e){
	echo "Erro: ". $e->getMessage();
}

function build_table($array){
    // start table
    $html = '<table>';
    // header row
    $html .= '<tr>';
    foreach($array[0] as $key=>$value){
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
    $html .= '</tr>';

    // data rows
    foreach( $array as $key=>$value){
        $html .= '<tr>';
        foreach($value as $key2=>$value2){
            $html .= '<td>' . (is_array($value2) ? http_build_query($value2,'',', '):htmlspecialchars($value2)) . '</td>';
        }
        $html .= '</tr>';
    }

    // finish table and return it
    $html .= '</table>';
    return $html;
}


?>

