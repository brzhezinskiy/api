<?php
/**
 * Класс модели работы с БД
 * По хорошему параметры подключения нужно вынести в конфиг,
 * но сейчас это лишние сущности
*/
class DB 
{
	private $host = "localhost";
	private $dbname = "users";
	private $user = "";
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

/**
 * Маленький велосипед, аналог Конструктора запросов в Yii
 * Зачем? Возможно я неправильно понял задачу, и стоило просто 
 * сделать API на Yii, использовав AR, но раз уж начал делать все сам
 * глупо брать огромный класс AR.
 * Можно считать этот класс синтаксическим сахаром для UserModel
*/
class RequestBuilder 
{  
    private $instance;
    private $requestSQL;
    private $whereOperation = false;
    
    public function __construct (PDO $instance) 
    {
        $this->instance = $instance;
    }

    public function query () 
    {
        return $this->instance->query($this->requestSQL);
    }
    
    /* SELECT */
    public function select ($what, array $ph = array()) 
    {
        $this->requestSQL = 'SELECT ';
        $this->requestSQL .= $this->addPH($what, $ph);
        return $this;
    }
    
    /* INSERT */
    public function insert ($toTable, array $ph = array()) 
    {
        $this->requestSQL = 'INSERT INTO ';
        $this->requestSQL .= $this->addPH($toTable, $ph);
        return $this;
    }
    
    public function values (array $param = array()) 
    {
        foreach ($param as $field => $val) {
            $param[$field] = $this->instance->quote($val);
        }
        $field = implode(", ", array_keys($param));
        $values = implode(", ", array_values($param));
        $this->requestSQL .= ' (' . $field . ') VALUES (' . $values . ') ';
        return $this;
    }
    
    /* UPDATE */
    public function update ($toTable, array $ph = array()) 
    {
        $this->requestSQL = 'UPDATE ';
        $this->requestSQL .= $this->addPH($toTable, $ph);
        return $this;
    }
    
    public function set (array $param = array()) 
    {
        $this->requestSQL .= " SET ";
        foreach ($param as $field => $val) {
            $this->requestSQL .= $field . ' = ' . $this->instance->quote($val);
            $this->requestSQL .= ', ';
        }
        $this->requestSQL = mb_substr($this->requestSQL, 0, -2);
        return $this;
    }
    
    /* DELETE */
    public function delete () 
    {
        $this->requestSQL = 'DELETE ';
        return $this;
    }
    
    /* FROM */
    public function from ($fromTable, array $ph = array()) 
    {
        $this->requestSQL .= ' FROM ';
        $this->requestSQL .= $this->addPH($fromTable, $ph);
        return $this;
    }
    
    /* WHERE, AND, OR */
    public function where ($cond, array $ph = array()) 
    {
        if (!$this->whereOperation) {
            $this->requestSQL .= ' WHERE ';
            $this->whereOperation = true;
        }
        $this->requestSQL .= $this->addPH($cond, $ph);
        return $this;
    }
    
    public function and_ ($cond = "", array $ph = array()) 
    {
        if ($this->whereOperation) {
            $this->requestSQL .= ' AND ';
        }
        $this->requestSQL .= $this->addPH($cond, $ph);
        return $this;
    }
    
    public function or_ ($cond = "", array $ph = array()) 
    {
        if ($this->whereOperation) {
            $this->requestSQL .= ' OR ';
        }
        $this->requestSQL .= $this->addPH($cond, $ph);
        return $this;
    }
    
    public function like ($cond, array $ph = array()) 
    {
        $this->requestSQL .= ' LIKE ';
        $this->requestSQL .= $this->addPH($cond, $ph);
        return $this;
    }
    
    public function lastInsertId () 
    {
        return $this->instance->lastInsertId();
    }
    
    protected function addPH ($cond, array $ph) 
    {
        if (count($ph)) {
            foreach ($ph as $key => $val) {
                if ($key[0] == ':') { // это значение
                    $val = $this->instance->quote($val);
                } else {              // это поле
                    $val = addslashes($val);
                }
                $cond = str_replace($key, $val, $cond);
            }
        }
        return $cond;
    }
}