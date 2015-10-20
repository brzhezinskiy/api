<?php
/**
 * Реализация роутинга API + авторизация
 * Реализация UserList0
 * Автор: freecod@mail.ru
*/

require_once ("auth.php");
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
                         
    private $httpError = array(
       404 => '404 Не найдено - Запрашиваемая страница не найдена на сервере.',
       500 => '500 Внутренняя ошибка сервера - Запрос не может быть обработан из-за внутренней ошибки сервера.'
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
        // проверяем ридирект с ошибки
        $rdStatus = isset($_SERVER['REDIRECT_STATUS'])?$_SERVER['REDIRECT_STATUS']:0;
        if (array_key_exists($rdStatus, $this->httpError)) {
            $this->makeError("[" . __CLASS__ . "] " . $this->httpError[$rdStatus] );
        }
        
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


