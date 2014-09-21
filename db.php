<?php
/**
 * Класс модели работы с БД
*/
class DB 
{
	private $host = "localhost";
	private $dbname = "users";
	private $user = "root";
	private $pass = "";	
	
	private static $DBH;
	
	private function __construct () {
		// MySQL через PDO_MYSQL  
		try {
            self::$DBH = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbname, $this->user, $this->pass);  
            self::$DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION ); 
            self::$DBH->query('SET NAMES utf8');
        } catch(Exception $e) { 
            throw new Exception ("[DB PDO] Ошибка БД - " . $e->getMessage());
        }
	}
	
	public static function GetDBH () {
		if ( empty ( self::$DBH )) {
			new DB();
		}
		return self::$DBH; 
	}
}