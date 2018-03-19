<?php
HeadTitle("Профиль");
Top();
menu();
if($_SESSION['LOG_IN']=='LOGIN') {
    $req = mysql_fetch_array(mysql_query("SELECT `password`,`email`,`type_of_user`,`admin`,`points` FROM `users` WHERE `login`='$_SESSION[login]'", $link));
    $in = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM `messages` WHERE `to`='$_SESSION[login]'", $link));
    $out = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM `messages` WHERE `from`='$_SESSION[login]'", $link));
    ?>
    <div class="prof">
    <div class="wrapper">
    <h2>Ваш профиль</h2>
    <div id="settings">
        <span> Логин: <?php echo $_SESSION['login'] ?>          </span>
        <span>  Email: <?php echo $req['email'] ?>   </span>
        <span>  Статус:<?php echo $req['type_of_user'] ?>  </span>
        <span> Руководитель: пользователь <?php
            if($req['admin']!="null")
                echo $req['admin'];
            else echo 'не назначен';

            ?></span>
        <span> Количество баллов:<?php echo $req['points'] ?></span>
        <span>  Количество входящих сообщений:<?php echo $in['COUNT(*)'] ?>   </span>
        <span>  Количество исходящих сообщений:<?php echo $out['COUNT(*)'] ?>   </span>
    </div>
        <h2>Изменить пароль</h2>
        Введите старый пароль <input name="oldpass" type="password"><br>
        Введите новый пароль <input name="newpass"><br>
        <button name="random_pass">Сгенерировать случайный</button> <button name="changepass">Изменить</button>
    <?php if ($_SESSION['TOU'] == "user" )
    {
        if($_SESSION['admin']!='null'){
        ?>
    <h2>Написать отчет о проделанной работе</h2>
    <form action="/actions/report" method="post">
        <textarea name="report" id="" cols="20" rows="7"></textarea>
        <select name="task" id="">
            <?php
            $sql = mysql_query("SELECT `id`,`name` FROM `tasks` WHERE `proccess`<100 and `author`=$_SESSION[admin]", $link);
            while ($row = mysql_fetch_array($sql)) {
                if ($row['proccess'] < 100) {
                    echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                }
            }
            ?>
        </select>
        <input type="submit" value="Отправить">
    </form>
    <? }
    else echo "<h2>Вступите в команду чтобы отправлять отчеты о работе</h2><br>";
    ?>
        <h2>Присоединится к команде</h2>
        <label for="">Логин руководителя <input name="login_manege" type="text"></label>
        <label for=""><input type="submit" name="check_login" value="Проверить"> <input type="submit" name="connect_to_team" value="Присоединиться!"></label>
<?php
    }
else if ($_SESSION['TOU'] == "admin")
{
    $sql = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM `reports` WHERE `to`= '$_SESSION[login]' AND `cheked`=0", $link));

if ($sql['COUNT(*)']>0) {
    $sql = mysql_query("SELECT * FROM `reports` WHERE `to`= '$_SESSION[login]' AND `cheked`=0", $link);
    ?>
    <h2>Последние отчеты</h2>
    <?php
    table_head("От","Отчет","Добавить баллы");
    while ($row = mysql_fetch_array($sql)) {
   $input = '<input name="'.$row['id'].'" type="text">';
   $button =   '<button class="addpoints" name="'.$row['id'].'">Добавить</button>';
    echo table_content($row['from'], $row['text'], $input, $button);

    $input_mob = '<input class="mb" name="'.$row['id'].'" type="text">';
    $button_mob =   '<button class="addpoints mb" name="'.$row['id'].'">Добавить</button>';
    echo table_content_mobile('От', 'Отчет', 'Добавить баллы', $row['from'], $row['text'], $input_mob, $button_mob);

    }
   }
   // else echo "<h2>В данный момент отчетов нет</h2><br>";
    ?>
    <h2>Добавить новое задание</h2>
    <? table_head("Название","Описание","Срок выполнения");?>
    <form action="/actions/newtask/" method="post">
        <div class="tasks-wrap">
            <div class="link cont"><input name="name" type="text"></div>
            <div class="short-description cont"><textarea name="descr"  rows="3"></textarea></div>
            <div class="time-prog cont"><input name="date" type="date"></div>
        </div>
        <input type="submit" class="tasks-wrap" value="Опубликовать">
    </form>
    <form action="/actions/newtask/" method="post">
        <div class="mob">
            <div class="short-description head">Описание</div>
            <div class="short-description cont"><textarea name="descr"  rows="3"></textarea></div>
            <div class="mob-bot">
                <div class="left">
                    <div class="link head">Название</div>
                    <div class="link cont"><input name="name" type="text"></div>
                </div>
                <div class="right">
                    <div class="time-prog head">Срок выполнения</div>
                    <div class="time-prog cont"><input name="date" type="date">
                    </div>
                </div>
            </div>
            <input type="submit" value="Опубликовать">
    </form></div>
        <?
        if(row_count("SELECT COUNT(*) FROM `users` WHERE `connect_to_team`= '$_SESSION[login]'")>0)
        {
            ?>
            <h2>Предложения вступить в команду</h2>

            <?php
        $sql=mysql_query("SELECT `login` FROM `users` WHERE `connect_to_team`= '$_SESSION[login]'",$link);
            while ($row=mysql_fetch_array($sql))
            {
                ?>
                <div id="" class="non-log wrap">

                <?
            echo "Пользователь ".$row['login']." хочет вступить в вашу команду";
            ?>
                    <button id="accept" name="<?echo $row['login']?>">Принять</button>
                    <button id="cancel" name="<?echo $row['login']?>">Отклонить</button>
                </div>
                <?
            }
        }

        if(row_count("SELECT COUNT(*) FROM `users` WHERE `admin`= '$_SESSION[login]'")>0)
        {
            echo "<h2>Ваша рабочая команда</h2>";
            $sql = mysql_query("SELECT `login`,`type_of_user` FROM `users` WHERE `admin`= '$_SESSION[login]'",$link);
            while ($row = mysql_fetch_array($sql))
            {
if($row['type_of_user']=='user')
echo "<div>Пользователь ".$row['login']." <button class='kick' name='".$row['login']."'>Исключить</button><br></div>";
else echo "<div>Пользователь ".$row['login']." (Вы)<br></div>";
            }
        }
        else echo "<h2>В вашей команде пока никого нет</h2>";
}
else if ($_SESSION['TOU'] == "SU") {
    echo "<h1>Настройки сайта</h1>";
    echo "<h2>Текст на главной странице</h2>";
    $row = mysql_fetch_array(mysql_query("SELECT `text` FROM `main` WHERE `id`=1",$link));
    ?>
    <textarea name="main_text" class="su" id="" cols="30" rows="6"><? echo $row['text']?>
    </textarea>
    <button name="change_text" >Изменить</button>
    <h2>Слайдер и новости</h2>
    Название (максимум 20 символов) <input class="su" type="text" name="title"> <br>
    Короткое описание (максимум 165 символов) <br>
    <textarea name="short" name="main_text" class="su" cols="30" rows="2"></textarea>
    Текст новости <br>
    <textarea name="long" name="main_text" class="su" cols="30" rows="6"></textarea>
    Дата <input type="date" name="date">
    <button class="new_news" name="send_news">Добавить новость на слайдер</button>
    <button class="new_news" name="send_news_simple"> Добавить новость</button>
    <? if(row_count("SELECT COUNT(*) FROM `slider`")>0) { ?>
        <h2>Удаление новости со слайдера</h2>
        <?
        $sql=mysql_query("SELECT `id`,`title` FROM `slider` WHERE `slider`=true",$link);
        while ($row=mysql_fetch_array($sql))
        {
            echo "<div>".$row['title']." <button class='del_news' name='".$row['id']."'>Удалить</button></div>";
        }
    }
    else echo "<h2>У вас нет ни одной новости</h2>";
    echo "<h2>Социальные сети</h2>";
    $sql = mysql_query("SELECT * FROM `soc`",$link);
    while ($row = mysql_fetch_array($sql)){
        $val = "";
if($row['link']!='null') $val = $row['link'];
        echo $row['soc']." <input name='".$row['id']."' type='text' value='".$val."'> <button class='soc_link' name='".$row['id']."' >Изменить</button><br>";
    }
   echo "Введите ссылку на соц. сеть например 'https://vk.com/432423456'";
}
?>
    </div></div>
    <?
}
else non_login();

//
//echo pass_crypt(123,'ADAM_SMITH').'<br>';
//echo pass_crypt(321,'JOHANN_BERNOULLI');
footer();
Bottom();

