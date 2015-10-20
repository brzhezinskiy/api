<?php
/**
 * Класс авторизации, проверяет наличие куки, производит аутентификацию
 * возвращает true \ false
 */

class Authorization 
{
    private static $obj;
    private $cookieName = 'auth';
    private $authName = 'authorization';
    private $paramArray;
    private $paramType;
    
    private $login = 'test';
    private $pass = 'test';
    
    private $isAuth = false; 
    
    private function __construct () 
    {
        $this->paramType = $_SERVER["REQUEST_METHOD"];
        if($this->paramType == 'GET') {
            $this->paramArray = $_GET;
        } else {
            $this->paramArray = $_POST;
        }
        
        if (!isset($_COOKIE[$this->cookieName])) {
            if (array_key_exists($this->authName, $this->paramArray) 
            && array_key_exists('login', $this->paramArray[$this->authName]) 
            && array_key_exists('pass', $this->paramArray[$this->authName]))
            {
                $login = $this->paramArray[$this->authName]['login'];
                $pass = $this->paramArray[$this->authName]['pass'];
                
                if ($login == $this->login && $pass == $this->pass)
                {
                    setcookie ($this->cookieName, md5($pass), time() + 3600);
                    $this->isAuth = true;
                }
            }
        } else {
            if ($_COOKIE[$this->cookieName] == md5($this->pass)) {
                $this->isAuth = true;
            }
        }
        
        
    }
    
    public static function init()
    {
        if ( empty ( self::$obj )) {
			self::$obj = new Authorization();
		}
		return self::$obj; 
    }
    
    public function isAuth ()
    {
        return $this->isAuth;
    }
}