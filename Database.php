 <?php
 
define('DB_TYPE', 'mysql');
define('DB_HOST', 'localhost');
define('DB_NAME', 'popcornstocks');
define('DB_USER', 'root');
define('DB_PASS', '');



class Database {    

    public $db;

    public function __construct() {
        
        $dboptions = array(
            PDO::ATTR_PERSISTENT            => true,
            PDO::MYSQL_ATTR_INIT_COMMAND    => "SET NAMES utf8",
            PDO::ATTR_ERRMODE               => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE    => PDO::FETCH_ASSOC); 
     
    
        try 
        {        
            $this->db = new PDO(DB_TYPE.':host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS, $dboptions);
        } 
       
            
        catch(PDOException $e){
            echo $e->getMessage();
        }
    }
}
    