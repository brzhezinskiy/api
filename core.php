<?php
/**
 * Модуль реализации работы с Пользователями
 * Реализация UserList0
 * Автор: freecod@mail.ru
*/

/**
 * Подключаем классы работы с БД и форматированием
*/

require_once ("db.php");
require_once ("formatter.php");


/**
 * Класс модели работы с пользователями
*/
class UserModel 
{
    private $dbInctance;
    private $dbStruct = array ( 'table' => 'users',
                                'columns' => array ('id' => 'key', 
                                                    'login' => 'txt', 
                                                    'nick' => 'txt', 
                                                    'email' => 'txt'
                                                   ) ,
                                'id' => 'id',
                                'unic' => array ('login', 'email'),
                                'nochange' => array ('id', 'login'),
                                'require' => array ('login')
                              );
    
    public function __construct(PDO $dbInctance)
    {
        $this->dbInctance = $dbInctance; 
    }
    
    /**
	 * получает пользователя по ID, алиас к selectUsers с параметром.
	 */
    public function selectUserByID($id)
    {
        $ret = $this->selectUsers( array($this->dbStruct['id'] => $id))->toArray();
        
        if ( array_key_exists(0, $ret) ) {
            $ret = $ret[0];
        } else {
            $ret = array();
        }
        return new SelectUserByID($ret);
    }
    
    /**
	 * получает список пользователей с учетом $filter, с условием LIKE % если в поле указана *.
     * если указано orCond = true, ищет по условию OR
	 */
    public function selectUsers(array $filter = array(), $orCond = false)
    {
        $retArray = array();
        $sqlStr = "SELECT * FROM " . $this->dbStruct['table'];
        $condStr = "";
        
        // экранируем - необходимо, т.к. запрос сборный и мы не сможем использовать paramValue PDO
        $filter = $this->addslashesIt($filter);
        
        foreach ($filter as $name => $val) {
            $val = addslashes($val);                
            if (strpos($val, '*') !== false) {      // найдены условия, нужен LIKE
                $val = str_replace('*', '%', $val);
                $val = $name . ' LIKE ' . "'" . $val . "'";
            } else {                                // * нет, нужно сравнение
                $val = $name . ' = ' . "'" . $val . "'";
            }
            
            if (!strlen($condStr)) {   // условия пустые
                $condStr = $val;
            } else {                   // есть условия
                $condStr .= $orCond?' OR ':' AND ';
                $condStr .= $val;
            }
        }
        
        if (strlen($condStr)) { // есть условия для WHERE
            $sqlStr .= ' WHERE ' . $condStr;
        }
        
        $sth = $this->dbInctance->query($sqlStr);
        $sth->setFetchMode(PDO::FETCH_ASSOC);
        
        if ($rows = $sth->fetchAll()) {
            $retArray = $rows;
        }
        
        return new SelectUsers($retArray);
    }
       
    /**
	 * создает пользователя на основании данных полей, указанных в $data.
     * возвращает id созданного пользователя
	 */
    public function addUser(array $data)
    {
        $retArray = array();
        $sqlStr = "INSERT INTO " . $this->dbStruct['table'] . " (:col) VALUES (':val')";
        $requiredCol = $this->dbStruct['require'];
        $mustBeUnic = $this->dbStruct['unic'];
        
        // экранируем - необходимо, т.к. запрос сборный и мы не сможем использовать paramValue PDO
        $data = $this->addslashesIt($data);
        
        foreach ($requiredCol as $column) {
            if ( !array_key_exists($column, $data) || !strlen($data[$column])) {  // не найдено обязательное поле или оно пустое
                throw new Exception ( "[" . __CLASS__ . "] Не указано обязательное поле " . $column . "!");
            }
        }
        
        $keys = implode(", ", array_keys($data));
        $values = implode("', '", array_values($data));
        $sqlStr = str_replace(':col', $keys, $sqlStr);
        $sqlStr = str_replace(':val', $values, $sqlStr);
            

        // проверяем уникальность данных, если задано
        if (count($mustBeUnic)) {
            $filter = array();
            
            foreach ($mustBeUnic as $column) {
                if (array_key_exists($column, $data) && strlen($data[$column])) {  
                    $filter[$column] = $data[$column];
                }
            }

            if ( count($filter) && count($this->selectUsers($filter, true)->toArray()) ) {
                throw new Exception ("[" . __CLASS__ . "] Пользователь с такими данными уже существует!");
            }
        }
        
        $sth = $this->dbInctance->query($sqlStr);
		return new AddUser( array ('id' => $this->dbInctance->lastInsertId() ));
    }
    
    /**
	 * изменяет данные полей, указанных в $data пользователей по ID.
	 */
    public function updateUser($id, array $data)
    {
        $retArray = array();
        $sqlStr = "UPDATE " . $this->dbStruct['table'] . " SET ";
        $condStr = "WHERE " . $this->dbStruct['id'] . " = " . $this->addslashesIt($id);
        
        $mustBeUnic = $this->dbStruct['unic'];
        $cantBeChange = $this->dbStruct['nochange'];
        
        // экранируем - необходимо, т.к. запрос сборный и мы не сможем использовать paramValue PDO
        $data = $this->addslashesIt($data);
        
        $setLine = "";
            
        foreach ($data as $key => $val) {
            
            if (strlen($setLine)) {   // еще одно поле
                $setLine .= ", ";
            }
            $setLine .= $key . ' = ' .  "'" . $val . "'";
        }
           
        $sqlStr .= $setLine . $condStr;
        
        
        if (count($cantBeChange)) {
            foreach ($cantBeChange as $column) {
                if (array_key_exists($column, $data)) {  
                    throw new Exception ("[" . __CLASS__ . "] Нельзя изменить поле " . $column . "!");
                }
            }
        }
        
        // проверяем уникальность данных, если задано
        if (count($mustBeUnic)) {
            $filter = array();
            
            foreach ($mustBeUnic as $column) {
                if (array_key_exists($column, $data) && strlen($data[$column])) {  // существует и не пустой (пустое поле мб не уникальным)
                    $filter[$column] = $data[$column];
                }
            }
            // проверяем наличие пользователя, у которого есть такие данные, кроме собственно изменяемой записи  
            if ( count($filter) ) {
            
                $searchedUsers = $this->selectUsers($filter, true)->toArray();
                
                if (count ($searchedUsers) > 1) {
                    throw new Exception ("[" . __CLASS__ . "] Пользователь с такими данными уже существует - в базе есть несколько пользователей с не уникальными данными!");
                }
                
                if (count ($searchedUsers) && $searchedUsers[0][ $this->dbStruct['id'] ] !== $id) {  
                    throw new Exception ("[" . __CLASS__ . "] Пользователь с такими данными уже существует!");
                }
            }
        }
        
        $sth = $this->dbInctance->query($sqlStr);
        /* данные могут и не обновляться
        if (!$sth->rowCount()) {
            throw new Exception ("[" . __CLASS__ . "] Не удалось обновить данные!");
        }
        */
		return new UpdateUser(array ('result' => true ));        
    }   
    
    /**
	 * удаляет пользователя по id.
	 */
    public function deleteUser($id)
    {
        $retArray = array();
        $sqlStr = "DELETE FROM " . $this->dbStruct['table'] . " WHERE " . $this->dbStruct['id'] . " = " . $this->addslashesIt($id);
        
        $sth = $this->dbInctance->query($sqlStr);
        
		if (!$sth->rowCount()) {
            throw new Exception ("[" . __CLASS__ . "] Не удалось удалить запись! Возможно, она уже удалена.");
        }
		return new DeleteUser(array ('result' => true ));              
    }
    
    /**
	 * Экранирует данные. Может принимать массив и строку
     * возвращает массив \ строку с экранированными данными
	 */
    private function addslashesIt($data)
    {
        if (is_array($data)) {  // массив
            foreach ($data as $key => $val) {
                $data[$key] = addslashes($val);  
            }
        } else {                // строка
            $data = addslashes($data);
        }
        
        return $data;
    }
    
    /**
	 * Получает структуру таблицы для дальнейшей обработки \ построения запроса
     * возвращает массив данных
	 */
    public function getTableStructure()
    {
        return new GetTableStructure ($this->dbStruct);
    }
}
















