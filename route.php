<?php
/**
 * Реализация роутинга API + авторизация
 * Реализация UserList0
 * Автор: freecod@mail.ru
*/

require_once ("core.php");

/**
 * Класс-роутер, получает параметры и вызывает модель
 * возвращает данные в json или запрошенном формате
 */
class Route 
{
    private static $obj;
    private $paramArray;
    private $paramType;

    private $defaultFormat = 'json';
    
    private $actionName = 'action';
    private $api = array ( 'gettable' => array( 'metod' => 'getTableStructure',
                                              'param' => array () 
                                            ),
                           'get'    => array( 'metod' => 'selectUsers',
                                              'param' => array ('id', 'login', 'nick', 'email') 
                                            ),
                           'getbyid'=> array( 'metod' => 'selectUserByID',
                                              'param' => array ('id') 
                                            ),
                           'add'    => array( 'metod' => 'addUser',
                                              'param' => array ('data') 
                                            ),
                           'update' => array( 'metod' => 'updateUser',
                                              'param' => array ('id', 'data') 
                                            ),
                           'delete' => array( 'metod' => 'deleteUser',
                                              'param' => array ('id') 
                                            )
                         ); 
    
    private function __construct () 
    {
        $this->paramType = $_SERVER["REQUEST_METHOD"];
        if($this->paramType == 'GET') {
            $this->paramArray = $_GET;
        } else {
            $this->paramArray = $_POST;
        }
    }
    
    public static function init()
    {
        if ( empty ( self::$obj )) {
			self::$obj = new Route();
		}
		return self::$obj; 
    }
    
    public function run()
    {
        // проверяем авторизацию
        if (!Authorization::init()->isAuth()) {
            $this->makeError("[" . __CLASS__ . "] API доступно только авторизованным пользователям!");
        }
        
        $action = $this->getParam($this->actionName);
        
        if (!array_key_exists($action, $this->api)) {
           $this->makeError("[" . __CLASS__ . "] не найдена комманда " . $action . '!');
        }
        
        $metod = $this->api[$action]['metod'];
        $param = array();
        
        switch ($action) { // дополнительная проверка \ обработка параметров для комманд
            case 'get':
                $filter = array();
                foreach ($this->api[$action]['param'] as $val) {
                    $paramElem = $this->getParam($val, false);
                    if ($paramElem !== false) {
                        $filter[$val] = $paramElem;
                    }
                }
                $param[] = $filter;
                
                break;

            default:
                foreach ($this->api[$action]['param'] as $val) {
                    $param[] = $this->getParam($val);
                }
                break;
        }
        
        // создаем экземпляр класса
        try {
            $obj = new UserModel(DB::GetDBH());
            $userObject = call_user_func_array(array($obj, $metod), $param);
        } catch(Exception $e) { 
            $this->makeError( $e->getMessage() );
        }

        echo $this->outputFormated($userObject);
        return;
    }
    
    private function getParam ($name, $required = true)
    {
        if ( !array_key_exists($name, $this->paramArray) ) {
            if ($required) {
                $this->makeError("[" . __CLASS__ . "] Нет обязательного элемента " . $name . '!');
            } else {
                return false;
            }
        }
        return $this->paramArray[$name];
    }
    
    private function outputFormated($obj)
    {    
        $format = $this->getParam('format', false);
        
        if ($format === false) {
            $format = $this->defaultFormat;
        }
        
        if (!method_exists($obj, 'to' . $format)) {
            die("[" . __CLASS__ . "] Неверный формат вывода - " . $format . '!');
        }
        
        $obj->{'to'.$format}();
        
        return $obj->{'to'.$format}();
    }
    
    private function makeError($errMess)
    {
        die ( $this->outputFormated(new FormattedError($errMess) ));
    }
}


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
