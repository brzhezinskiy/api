<?php

/**
 * Точка входа API для работы с пользователями + тесты
 * Данные запроса могут быть или в _GET, или в _POST (определяется текущий тип запроса)
 * ---
 * Структура API
 * ---
 * 'format' - задает формат вывода (html, json, xml)
 * 'authorization' - данные для авторизации, массив ('login'=>'', 'pass'=>''), 
 *     тестовые данные - test \ test
 *     куки устанавливаются на 1 час
 * 'action' - текущее действие:
 *     'gettable' - получает текущую структуру таблицы и доступных к выборке полей
 *     'get'      - получает данные пользователей в соответствии с переданными в параметрах полями
 *     'getbyid'  - получает данные пользователя по id 
 *     'add'      - добавляет пользователя с данными из data (массив вида 'поле'=>'значение') 
 *     'update'   - обновляет данные пользователя по id данными из data (массив вида 'поле'=>'значение') 
 *     'delete'   - удаляет пользователя по id
 * ---
 * формат и имена ПАРАМЕТРОВ задаются в классе Route
 * формат и имена полей ТАБЛИЦЫ задаются в классе UserModel
 * ---
 * Реализация UserList0
 * Автор: freecod@mail.ru
*/

require_once ("route.php");

setlocale(LC_ALL, 'ru_RU.utf8');
Header("Content-Type: text/html;charset=UTF-8");

//$_GET['format'] = 'html';
//$_GET['format'] = 'json';
//$_GET['format'] = 'xml';

/* gettable test
$_GET['action'] = 'gettable';
*/

/* get test
$_GET['action'] = 'get';
$_GET['id'] = '19';
$_GET['login'] = '';
$_GET['nick'] = 'userNick19 updated!';
$_GET['email'] = '';
*/

/* getbyid test
$_GET['action'] = 'getbyid';
$_GET['id'] = '286';
*/

/* add test
$_GET['action'] = 'add';
$_GET['data'] = array('login'=>'test12');
*/

/* update test
$_GET['action'] = 'update';
$_GET['id'] = '289';
$_GET['data'] = array('nick'=>'test2222');
*/

/* delete test
$_GET['action'] = 'delete';
$_GET['id'] = '289';
*/

$_GET['authorization'] = array('login'=>'test', 'pass'=>'test');

try {
    Route::init()->run();
} catch(Exception $e) { 
        echo $e->getMessage() . '<br>'; 
}
    
    
    
    