<?php
/**
 * Тесты для модуля работы с Пользователями
 * Реализация UserList0
 * Автор: freecod@mail.ru
*/

require_once ("core.php");

setlocale(LC_ALL, 'ru_RU.utf8');
Header("Content-Type: text/html;charset=UTF-8");

try {

    $objUser = new UserModel(DB::GetDBH());
    
    // тест получения данных по ID
    //print_r ( $objUser->selectUserByID('30')->toArray() );

    // получаем структуру таблицы пользователей, для определения ID и доступных полей
    $arr = $objUser->getTableStructure();
    echo '<br>' . "Структура таблицы" . '<br>';
    print_r ($arr);
    echo '<br>';
    
    echo '<br>' . "тест создания пользователей" . '<br>';
    try {
        for ($i=0; $i<20; $i++) {
            $userArr = array();
            $userArr['login'] = "user" . $i;
            $userArr['nick'] = "userNick" . $i;
            $userArr['email'] = "user" . $i . "@mail.ru";
            echo "Новый пользователь создан, ID = " . $objUser->addUser($userArr) . '<br>';
        }
    } catch(Exception $e) { 
        echo $e->getMessage() . '<br>'; 
    }
    
    
    echo '<br>' . "тест получения списка пользователей" . '<br>';
    try {
        foreach($objUser->selectUsers()->toArray() as $userArray) {
            foreach ($userArray as $key => $val) {
                echo " [" . $key . " = " . $val . "] ";
            }
            echo '<br>';
        }
    } catch(Exception $e) { 
        echo $e->getMessage() . '<br>'; 
    }
    ///*
    echo '<br>' . "тест получения списка пользователей по критерию" . '<br>';
    try {
        foreach($objUser->selectUsers(array('email'=>'*5@mail.ru'))->toArray() as $userArray) {
            foreach ($userArray as $key => $val) {
                echo " [" . $key . " = " . $val . "] ";
            }
            echo '<br>';
        }
    } catch(Exception $e) { 
        echo $e->getMessage() . '<br>'; 
    }
    
    
    echo '<br>' . "тест обновления пользователей" . '<br>';
    try {
        foreach($objUser->selectUsers()->toArray() as $userArray) {
            $newUserArray = array ('nick' => $userArray['nick'] . " updated!", 'email' => '' );
            $id = $userArray[ $arr['id'] ];
            $objUser->updateUser($id, $newUserArray);

            foreach ($objUser->selectUserByID($id)->toArray() as $key => $val) {  // получаем данные по ID
                echo " [" . $key . " = " . $val . "] ";
            }
            echo '<br>';
        }
    } catch(Exception $e) { 
        echo $e->getMessage() . '<br>'; 
    }
    
    /*
    echo '<br>' . "тест удаления пользователей" . '<br>';
    try {
        foreach($objUser->selectUsers()->toArray() as $userArray) {
            $objUser->deleteUser($userArray[ $arr['id'] ]);
            echo $userArray['login'] . " удален!" . '<br>';
        }
    } catch(Exception $e) { 
        echo $e->getMessage() . '<br>'; 
    }
    */
    
    
}
catch(Exception $e) { 
    echo $e->getMessage() . '<br>'; 
}














