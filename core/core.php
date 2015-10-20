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
    public function selectUsers(array $filter = array(), $orCond = true)
    {
        $sql = new RequestBuilder($this->dbInctance);
        $sql->select('*')->from('^table', array('^table' => $this->dbStruct['table']));
         
        foreach ($filter as $name => $val) {             
            // проверяем, как будет составляться условие - AND или OR
            $orCond?$sql->or_():$sql->and_();
            
            if (strpos($val, '*') !== false) {      // найдены условия, нужен LIKE
                $val = str_replace('*', '%', $val);
                $sql->where('^name', array('^name' => $name))
                    ->like(':val', array(':val' => $val));
                
            } else {                                // * нет, нужно сравнение
                $sql->where('^name = :val', array('^name' => $name, ':val' => $val));
            }
        }
        
        $sth = $sql->query();
        $sth->setFetchMode(PDO::FETCH_ASSOC);
        return new SelectUsers($sth->fetchAll());
    }
       
    /**
	 * создает пользователя на основании данных полей, указанных в $data.
     * возвращает id созданного пользователя
	 */
    public function addUser(array $data)
    {
        $requiredCol = $this->dbStruct['require'];
        $mustBeUnic = $this->dbStruct['unic'];

        foreach ($requiredCol as $column) {
            if ( !array_key_exists($column, $data) || !mb_strlen($data[$column])) {  // не найдено обязательное поле или оно пустое
                throw new Exception ( "[" . __CLASS__ . "] Не указано обязательное поле " . $column . "!");
            }
        }

        // проверяем уникальность данных, если задано
        if (count($mustBeUnic)) {
            $filter = array();
            
            foreach ($mustBeUnic as $column) {
                if (array_key_exists($column, $data) && mb_strlen($data[$column])) {  
                    $filter[$column] = $data[$column];
                }
            }

            if ( count($filter) && count($this->selectUsers($filter, true)->toArray()) ) {
                throw new Exception ("[" . __CLASS__ . "] Пользователь с такими данными уже существует!");
            }
        }
        
        $sql = new RequestBuilder($this->dbInctance);
        
        $sql->insert('^table', array('^table' => $this->dbStruct['table']))
            ->values($data)
            ->query();
            
		return new AddUser( array ('id' => $sql->lastInsertId() ));
    }
    
    /**
	 * изменяет данные полей, указанных в $data пользователей по ID.
	 */
    public function updateUser($id, array $data)
    {
        $mustBeUnic = $this->dbStruct['unic'];
        $cantBeChange = $this->dbStruct['nochange'];
        
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
                if (array_key_exists($column, $data) && mb_strlen($data[$column])) {  // существует и не пустой (пустое поле мб не уникальным)
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
        
        $sql = new RequestBuilder($this->dbInctance);
        
        $sql->update('^table', array('^table' => $this->dbStruct['table']))
            ->set($data)
            ->where('^id = :val', array('^id' => $this->dbStruct['id'], ':val' => $id))
            ->query();

		return new UpdateUser(array ('result' => true ));        
    }   
    
    /**
	 * удаляет пользователя по id.
	 */
    public function deleteUser($id)
    {
        $sql = new RequestBuilder($this->dbInctance);
        
        $sql->delete()
            ->from('^table', array('^table' => $this->dbStruct['table']))
            ->where('^id = :val', array('^id' => $this->dbStruct['id'], ':val' => $id));
        
		if (!$sql->query()->rowCount()) {
            throw new Exception ("[" . __CLASS__ . "] Не удалось удалить запись! Возможно, она уже удалена.");
        }
		return new DeleteUser(array ('result' => true ));              
    }
    
    /**
	 * Экранирует данные. Может принимать массив и строку
     * возвращает массив \ строку с экранированными данными
	 */
    private function quoteUnsaveData($data)
    {
        if (is_array($data)) {  // массив
            foreach ($data as $key => $val) {
                if (is_array($val)) {
                    $data[$key] = $this->quoteUnsaveData($val);
                } else {
                    $data[$key] = addslashes($val); 
                }                
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
















