<?php
date_default_timezone_set("America/Sao_Paulo");

class Auth {

    protected $db;
    public function __construct($db) {
        $this->db = $db;
    }
	
    public function getTokenPix(){
        try{					
		
			$query = "DELETE FROM tb_token_pix WHERE expiracao <= CURRENT_TIMESTAMP()";
			$query_stmt = $this->db->prepare($query);
			$query_stmt->execute();
			if($query_stmt->rowCount()){
				return null;
			}
			
			$query = "SELECT * FROM tb_token_pix";
			$query_stmt = $this->db->prepare($query);
			$query_stmt->execute();
	 
			if($query_stmt->rowCount()){
				$row = $query_stmt->fetch(PDO::FETCH_ASSOC);							
				return $row["token"];
			}
			
			return null;
        }catch(PDOException $e){
				return null;
        }
    }	
	
    public function setTokenPix($token,$date,$expires){
        try{			
			$dateinsec = strtotime($date);
			$newdate = $dateinsec + $expires - 30; //diminuir 30 segundos para uma "tolerância"
			
			$dthr = date("Y-m-d H:i:s",$newdate);
				 
			$query = "INSERT INTO `tb_token_pix` (`expiracao`,`token`)
						   VALUES (:expiracao , :token ) ";						   
			$query_stmt = $this->db->prepare($query);
			
			$query_stmt->execute(array( 				
					":expiracao"    => $dthr,
					":token"  		=> $token,
				));			
				
        }catch(PDOException $e){
				return null;
        }
    }
	
	public function setTransacaoPix($id_pedido,$valor,$txid,$textoImagemQRcode,$dthr_cri,$dthr_exp){
		try{
			$set_trans = "INSERT INTO `tb_transacao_pix` (`id_pedido`,`valor`,`txid`,`textoImagemQRcode`,`status`,`dthr_cri`,`dthr_exp`)
						   VALUES (:id_pedido , :valor , :txid, :textoImagemQRcode, :status, :dthr_cri,:dthr_exp) ";						   
			$query_stmt = $this->db->prepare($set_trans);			
		    
			$query_stmt->execute(array( 				
					":id_pedido"         => $id_pedido,
					":valor"	         => $valor,
					":txid"     	     => $txid,
					":textoImagemQRcode" => $textoImagemQRcode,
					":dthr_cri"  		 => $dthr_cri,
					":dthr_exp"         => $dthr_exp,
					":status"            => "Criado",
				));			
				
			return "Sucesso!";
        }catch(PDOException $e){
            return "Erro banco de dados: ". $e->getMessage();
        }
    }	
	
    public function confirmaPix($txid,$e2eid){
        try{		
			$query = "SELECT * FROM tb_transacao_pix WHERE `txid`=:txid";
			$query_stmt = $this->db->prepare($query);
			$query_stmt->bindValue(":txid", $txid,PDO::PARAM_STR);
			$query_stmt->execute();
	 
			if($query_stmt->rowCount()){
				$row = $query_stmt->fetch(PDO::FETCH_ASSOC);	
				if ($row["status"] == "Aprovado"){
					$ret = "Sucesso!";
				}
				if ($row["status"] == "Criado"){
					//define o status do pedido como pagamento confirmado
					//$ret = $this->setStatusPedido($row["id_pedido"],"Pagt Confirmado");					
					
					//if ($ret == "Sucesso!"){
						$query = "UPDATE `tb_transacao_pix` 
							 	 	 SET `status`=:status, `e2eid`=:e2eid
								   WHERE `txid`=:txid";	
						$query_stmt = $this->db->prepare($query);
						$query_stmt->bindValue(":txid", $txid,PDO::PARAM_STR);
						$query_stmt->bindValue(":e2eid", $e2eid,PDO::PARAM_STR);
						$query_stmt->bindValue(":status", "Aprovado",PDO::PARAM_STR);
						$query_stmt->execute();		
						if($query_stmt->rowCount()){
									$ret = "Sucesso!";
						}
					//}
				}				
				return $ret;
			}
			return "Erro";
        }catch(PDOException $e){
            return "Erro banco de dados: ". $e->getMessage();
        }
    }
	
    public function expiraPix($txid){
        try{		
			$query = "SELECT * FROM tb_transacao_pix WHERE `txid`=:txid";
			$query_stmt = $this->db->prepare($query);
			$query_stmt->bindValue(":txid", $txid,PDO::PARAM_STR);
			$query_stmt->execute();
	 
			if($query_stmt->rowCount()){
				$query = "UPDATE `tb_transacao_pix` 
							 SET `status`=:status
						   WHERE `txid`=:txid";	
				$query_stmt = $this->db->prepare($query);
				$query_stmt->bindValue(":txid", $txid,PDO::PARAM_STR);
				$query_stmt->bindValue(":status", "Expirado",PDO::PARAM_STR);
				$query_stmt->execute();
			}
        }catch(PDOException $e){
            return "Erro banco de dados: ". $e->getMessage();
        }
    }		
	
}