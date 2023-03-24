<?php
class Database{
    
    private $db_host = 'localhost';
    private $db_name = 'aplicacao_pix';
    private $db_username = 'root';
    private $db_password = '';
    
    public function dbConnection(){
        
        try{
            $conn = new PDO(
				'mysql:host='.$this->db_host.';dbname='.$this->db_name.';charset=utf8',
				$this->db_username,
				$this->db_password
				);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        }
        catch(PDOException $e){
            echo "Connection error ".$e->getMessage(); 
            exit;
        }
          
    }
}
?>
