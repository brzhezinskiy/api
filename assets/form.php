<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="utf-8">
    <title>Тест</title>
    <meta name="description" content="Форма для тестирования приложения">

    <link rel="stylesheet" href="assets/css/styles.css?v=1.0">

    <!--[if lt IE 9]>
  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
</head>

<body>
    <form action="<?=$_SERVER['PHP_SELF']?>" id="api_form" method="post">
        <?php if (!Authorization::init()->isAuth()) : ?>
            <label><span>Логин</span>
                <input type="text" value="test" name="authorization[login]">
            </label>
            <label><span>Пароль</span>
                <input type="password" value="test" name="authorization[pass]">
            </label>
            <label>
                <input type="submit" value="Вход">
            </label>
            <?php else : ?>
                <label class="radio action" for="gettable">
                    <input type="radio" name="action" id="gettable" value="gettable">Cтруктура таблицы и доступных к выборке полей</label>

                <label class="radio action" for="get">
                    <input type="radio" name="action" id="get" value="get">Данные пользователей</label>
                <div class="subform get">
                    <label><span>Логин</span>
                        <input type="text" name="login" disabled>
                    </label>
                    <label><span>Ник</span>
                        <input type="text" name="nick" disabled>
                    </label>
                    <label><span>Email</span>
                        <input type="text" name="email" disabled>
                    </label>
                    <input type="button" class="api_submit" name="submit" value="Отправить" disabled>
                </div>

                <label class="radio action" for="getbyid">
                    <input type="radio" name="action" id="getbyid" value="getbyid">Данные пользователя по id </label>
                <div class="subform getbyid">
                    <label><span>Id</span>
                        <input type="text" name="id" disabled>
                    </label>
                    <input type="button" class="api_submit" name="submit" value="Отправить" disabled>
                </div>

                <label class="radio action" for="add">
                    <input type="radio" name="action" id="add" value="add">Добавить пользователя</label>
                <div class="subform add">
                    <label><span>Id</span>
                        <input type="text" name="data[id]" disabled>
                    </label>
                    <label><span>Логин</span>
                        <input type="text" name="data[login]" disabled>
                    </label>
                    <label><span>Ник</span>
                        <input type="text" name="data[nick]" disabled>
                    </label>
                    <label><span>Email</span>
                        <input type="text" name="data[email]" disabled>
                    </label>
                    <input type="button" class="api_submit" name="submit" value="Отправить" disabled>
                </div>

                <label class="radio action" for="update">
                    <input type="radio" name="action" id="update" value="update">Обновить данные пользователя </label>
                <div class="subform update">
                    <label><span>Укажите Id</span>
                        <input type="text" name="id" disabled>
                    </label>
                    <label><span>Логин</span>
                        <input type="text" name="data[login]" id="update_login" disabled>
                    </label>
                    <label><span>Ник</span>
                        <input type="text" name="data[nick]" id="update_nick" disabled>
                    </label>
                    <label><span>Email</span>
                        <input type="text" name="data[email]" id="update_email" disabled>
                    </label>
                    <input type="button" class="api_submit" name="submit" value="Отправить" disabled>
                </div>

                <label class="radio action" for="delete">
                    <input type="radio" name="action" id="delete" value="delete">Удалить пользователя</label>
                <div class="subform delete">
                    <label><span>Id</span>
                        <input type="text" name="id" disabled>
                    </label>
                    <input type="button" class="api_submit" name="submit" value="Отправить" disabled>
                </div>

                <hr>
                <label class="radio format" for="html">
                    <input type="radio" name="format" id="html" value="html">HTML</label>
                <label class="radio format selected" for="json">
                    <input type="radio" name="format" id="json" value="json" checked>JSON</label>
                <label class="radio format" for="xml">
                    <input type="radio" name="format" id="xml" value="xml">XML</label>
                
                <hr>
                <label class="radio method selected" for="methodget">
                    <input type="radio" name="method" id="methodget" value="GET" checked>GET</label>
                <label class="radio method" for="post">
                    <input type="radio" name="method" id="methodpost" value="POST">POST</label>


                <?php endif; ?>
    </form>
    <div id="message" class="message"></div>
    <script src="assets/js/jquery.js"></script>
    <script src="assets/js/scripts.js"></script>
</body>

</html>