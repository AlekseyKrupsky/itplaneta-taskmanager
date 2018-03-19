<?php
if($Module=="reg")
{
if($_POST['login']&&$_POST['password']&&$_POST['mail']&&$_POST['TOU'])
    {
        //exit(message($_POST['captcha'],2));
        if($_SESSION['captcha']==md5($_POST['captcha']))
        {
            if(!mysql_fetch_array(mysql_query("SELECT `id` FROM `users` WHERE `login`='$_POST[login]'",$link)))
            {
                if($_POST['TOU']=='admin') $admin = $_POST['login'];
                else $admin = 'null';
                $pass = pass_crypt($_POST['login'],$_POST['password']);
                mysql_query("INSERT INTO `users` VALUES(null, '$_POST[login]','$pass','$_POST[mail]','$_POST[TOU]',0,0,'$admin','null')",$link);
                exit();
            }
            else exit(message("Этот логин уже используется, выберите другой",2));
           // header("Location: /");
        }
        else exit(message("Число с картинки введено неверно",2));
    }
}

if($Module=="login")
{
    if($_POST['login']&&$_POST['password'])
    {
      //  var_dump($_POST);
      $row =  mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `login` = '$_POST[login]'",$link));

      if($row['password']==pass_crypt($_POST['login'],$_POST['password']))
      {
         // echo $row['password'];
          $_SESSION['LOG_IN']='LOGIN';
          $_SESSION['ID']=$row['id'];
          $_SESSION['login']=$_POST['login'];
          $_SESSION['mail']=$row['email'];
          $_SESSION['TOU']=$row['type_of_user'];
          $_SESSION['admin']=$row['admin'];
          $_SESSION['pass']=md5($row['password']);
          //header("Location: /");
          exit(false);
      }
      else exit(message("Логин или пароль введен неправильно",3));
    }
//    if($_POST['login']&&$_POST['password'])
//    {
//        //  var_dump($_POST);
//        $row =  mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `login` = '$_POST[login]'",$link));
//        if($row['password']==$_POST['password'])
//        {
//            // echo $row['password'];
//            $_SESSION['LOG_IN']='LOGIN';
//            $_SESSION['ID']=$row['id'];
//            $_SESSION['login']=$_POST['login'];
//            $_SESSION['mail']=$row['email'];
//            $_SESSION['TOU']=$row['type_of_user'];
//            $_SESSION['admin']=$row['admin'];
//            $_SESSION['pass']=md5($_POST['password']);
//            //header("Location: /");
//            exit(false);
//        }
//        else exit(message("Логин или пароль введен неправильно",3));
//
//    }
}

if($Module=="logout")
{
    $_SESSION['LOG_IN']='LOGOUT';
    $_SESSION['ID']=null;
    $_SESSION['login']=null;
    $_SESSION['mail']=null;
    $_SESSION['TOU']=null;
    $_SESSION['admin']=null;
    $_SESSION['pass']=null;
    header("Location: /");
}

if($Module=="message")
{
    if($_POST['mess'] && $_POST['to'])
    {
        send_mess($_POST['to'],$_POST['mess'],$_SESSION['login']);
        header("Location: /messages");
    }
}

if($Module=="history")
{
    $message = "Chat you with ".$_POST['with'].". Old to new\n\n";
$result = false;
  //  echo $_POST['from'];
   // echo '<br>'.$_SESSION['login'];
    $sql=mysql_query("SELECT `from`,`text` FROM `messages` WHERE (`from`='$_SESSION[login]' AND `to`='$_POST[with]') OR (`from`='$_POST[with]' AND `to`='$_SESSION[login]') ",$link);
    while($row=mysql_fetch_array($sql))
    {
        $result = true;
       //echo $row['text'].'<br>';
       // echo $row['from'].":".$row['text']."/n";
       $message = strval($message).$row['from'].":".$row['text']."\n";
    }
    //WHERE (`from`='$_POST[from]' AND `to`='$_SESSION[login]') OR (`to`='$_POST[from]' AND `from`='$_SESSION[login]')
   if($result) {
       $to = $_SESSION['mail'];
       $from = "dachadybenci2@gmail.com";
       $subject = "Message history";
       $headers = "From: $from\r\nReply-to: $form\r\nContent-type: text/plane; charset=utf-8\r\n";
       mail($to,$subject,$message,$headers);
       exit(message("Message history has been sent on your email",1));
   }
   else exit(message("There is no such user or message history is empty",2));
}

if($Module=="report")
{
    if($_POST['report'] && $_POST['task'])
    {
        mysql_query("INSERT INTO `reports` VALUES(null,'$_SESSION[login]','$_SESSION[admin]','$_POST[report]','$_POST[task]',0 )",$link);
        $mess ="Пользователь ".$_SESSION['login']." прислал отчет о своей работе";
        send_mess($_SESSION['admin'],$mess,'system');
        header("Location: /profile");
    }
}

if($Module=="addpoints")
{
    if($_POST['name']=="add" and $_POST['idreport'] and $_POST['mark'])
    {
        $task_id = mysql_fetch_array(mysql_query("SELECT `task_id` FROM `reports` WHERE `id`='$_POST[idreport]'",$link));
        $progress = mysql_fetch_array(mysql_query("SELECT `proccess` FROM `tasks` WHERE `id`='$task_id[task_id]'",$link));
        $login = mysql_fetch_array(mysql_query("SELECT `from` FROM `reports` WHERE `id`='$_POST[idreport]'",$link));
        if($progress['proccess']+$_POST['mark']>100 or $_POST['mark']<0)
        {

            $text = "Вы ввели недопустимое значение. Диапазон допустимых значений от 0 до ".(100-$progress['proccess']);
            $ret ='{
                "text":"'.$text.'",
                "color":"#9C0600",
                "reload":"false"
                }';

            exit($ret);
        }
        else {
            mysql_query("UPDATE `reports` SET `cheked`=TRUE WHERE `id`='$_POST[idreport]'",$link);
            mysql_query("UPDATE `users` SET `points`=`points`+ '$_POST[mark]' WHERE `login`='$login[from]'",$link);
            mysql_query("UPDATE `tasks` SET `proccess`=`proccess`+ '$_POST[mark]' WHERE `id`='$task_id[task_id]'",$link);

            $row = mysql_fetch_array(mysql_query("SELECT `name` FROM `tasks` WHERE `id`='$task_id[task_id]'",$link));
            $mess ="Пользователь ".$_SESSION['login']." оценил Вашу работу и присвоил ".$_POST['mark']." баллов за задание ".$row['name'];
            send_mess($login['from'],$mess,'system');

            $ret ='{
                "text":"Баллы начислены",
                "color":"#128504",
                "reload":"true"
                }';

            exit($ret);

          //  exit()
        }
        //error
    }
}

if($Module=="newtask")
{
    if($_POST['date'] && $_POST['descr'] && $_POST['name'])
    {
        $linktotask = "/";
        mysql_query("INSERT INTO `tasks` VALUES(null,'$_POST[descr]','$_POST[date]',0,'$_SESSION[login]','$_POST[name]')",$link);
        header("location:/profile");
    }
}

if($Module=="check")
{
    if($_POST['name']=="check" && $_POST['login'])
    {
        $row=mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM `users` WHERE `login`='$_POST[login]' and `type_of_user`='admin'",$link));
        if($row['COUNT(*)']>0)
        {
           // $row=mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM `users` WHERE `login`='$_POST[login]' AND `TOU`='admin'"));
            exit(message('Вы можете вступить к нему в команду',1));
        }
        else exit(message('Такого пользователя не существует или его статус: пользователь',3));
    }
}

if($Module=="connect")
{
    if($_POST['name']=="connect" && $_POST['login'])
    {
        $row=mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM `users` WHERE `login`='$_POST[login]' and `type_of_user`='admin'",$link));
        if($row['COUNT(*)']>0)
        {
                mysql_query("UPDATE `users` SET `connect_to_team` = '$_POST[login]' WHERE `login`='$_SESSION[login]'",$link);
                $text = 'Предложения вступить в команду отправлено пользователю '.$_POST['login'];
            exit(message($text,1));
        }
        else exit(message('Такого пользователя не существует или его статус: пользователь',3));
    }
}

if($Module=="join")
{
    if($_POST['name']=='join' && $_POST['type'] && $_POST['user'])
        {
            mysql_query("UPDATE `users` SET `connect_to_team`='$_POST[type]' WHERE `login`='$_POST[user]'",$link);
            if($_POST['type']=='accept'){
                mysql_query("UPDATE `users` SET `admin`='$_SESSION[login]' WHERE `login`='$_POST[user]'",$link);

                $mess = "Пользователь ".$_SESSION['login']." принял Вас в команду";

                send_mess($_POST['user'],$mess,'system');

            exit(message("Заявка принята",1));
            }
            else if($_POST['type']=='cancel')
            {
                $mess = "Пользователь ".$_SESSION['login']." отклонил Вашу заявку";
                send_mess($_POST['user'],$mess,'system');

            exit(message("Заявка отклонена",1));
            }
        }
       // $mess =  $_POST['name'].$_POST['type'].$_POST['user'];
       exit(message("Ошибка",3));
    //exit(message($mess,3));
}

if($Module=="kick"){
    if($_POST['name']=='kick' && $_POST['user'])
    {
        $id = mysql_fetch_array(mysql_query("SELECT `id` FROM `users` WHERE `login`='$_POST[user]'",$link));
        mysql_query("UPDATE `users` SET `admin`='null' WHERE `id`=$id[id]",$link);

        $mess = "Пользователь ".$_SESSION['login']." исключил Вас из команды";
        send_mess($_POST['user'],$mess,'system');

        exit(message('Пользователь исключен',2));
    }
    exit(message("Ошибка",3));
}

if($Module=="change_text")
{
    if($_POST['name']=="change_text" and $_POST['text'])
    {
        mysql_query("UPDATE `main` SET `text`='$_POST[text]' WHERE `id`=1",$link);
        exit(message("Изменения внесены",1));
    }
}

if($Module=="news")
{
    if($_POST['type']=="new"){
        if($_POST['name']=="send_news" && $_POST['date'] && $_POST['title'] && $_POST['short'] && $_POST['long'])
        {
            if(row_count("SELECT COUNT(*) FROM `slider` WHERE `slider`=true")==6){
                exit(message("На слайдере не может быть более 6 слайдов",2));
            }
            else{
                mysql_query("INSERT INTO `slider` VALUES (null,'$_POST[title]','$_POST[short]','$_POST[long]','$_POST[date]',true)",$link);
                exit(message("Новая новость добавлена на слайдер",1));
            }

        }

       else if($_POST['name']=="send_news_simple" && $_POST['date'] && $_POST['title'] && $_POST['short'] && $_POST['long'])
        {
            mysql_query("INSERT INTO `slider` VALUES (null,'$_POST[title]','$_POST[short]','$_POST[long]','$_POST[date]',false)",$link);
            exit(message("Новая новость добавлена",1));
        }
        else exit(message("Вы указали не все параметры",2));
    }


    if($_POST['name']=='count')
    {
        exit(row_count("SELECT COUNT(*) FROM `slider` WHERE `slider`=true"));
    }

    if($_POST['name']=="delete" && $_POST['id'])
    {
        $row=mysql_fetch_array(mysql_query("SELECT `slider` FROM `slider` WHERE `id`='$_POST[id]'"));

        if($row['slider']){
            if(row_count("SELECT COUNT(*) FROM `slider` WHERE `slider`=true")==2)
                {
                    exit(message("На слайдере не может быть менее 2 слайдов",2));
                }
            else
            {
                mysql_query("DELETE FROM `slider` WHERE `id`='$_POST[id]'",$link);
                exit(message("Новость удалена",1));
            }
            }
        else
            {
            mysql_query("DELETE FROM `slider` WHERE `id`='$_POST[id]'",$link);
            exit(message("Новость удалена",1));
            }

    }

}

if($Module=="soc"){
    if($_POST['name']='change' and $_POST['link'] and $_POST['id'])
    {
    mysql_query("UPDATE `soc` SET `link`='$_POST[link]' WHERE `id`='$_POST[id]'",$link);
    exit(message("Ссылка изменена",1));
    }
}

if($Module=="more"){


    $result='';
    if($_POST['name']=='mess')
    {

       $sql = mysql_query("SELECT * FROM `messages` WHERE `to`='$_SESSION[login]' ORDER BY `id`  DESC LIMIT $_SESSION[mess],10", $link);
       while ($row = mysql_fetch_array($sql))
       {
           if(strlen($row['text'])>200){
               $text = max_lenght($row['text'],200);
               $text = $text." <div class='show-all' id='".$row['id']."'>Показать все</div>  ";
           }
           else $text = $row['text'];
           $result=$result.table_content($row['from'],$text,$row['date'],'');
           $result=$result.table_content_mobile('От', 'Сообщение', 'Дата', $row['from'], $text, $row['date'], '');
       }
       if(($_SESSION['mess']+10)>row_count("SELECT COUNT(*) FROM `messages` WHERE `to`='$_SESSION[login]' ORDER BY `id`"))
       {
           $hide = 'true';

       $_SESSION['mess'] = row_count("SELECT COUNT(*) FROM `messages` WHERE `to`='$_SESSION[login]' ORDER BY `id`"); }
       else{ $_SESSION['mess'] = $_SESSION['mess']+10; $hide = 'false';
       }
    }

    if($_POST['name']=='tasks-more')
    {

        $sql = mysql_query("SELECT * FROM `tasks`WHERE `proccess`<100 and `author`='$_SESSION[admin]' ORDER BY `date` DESC LIMIT $_SESSION[tasks],8", $link);
        while ($row = mysql_fetch_array($sql))
        {
            $proccess = "<span>Прогресс ".$row['proccess']."%</span>";
            $task = "<a href='/task/".$row['id']."'>Задание</a>";
            $result=$result.table_content($task, $row['description'], $row['date'], $proccess);
            $result=$result.table_content_mobile('Ссылка', 'Короткое описание', 'Дата и прогресс', $row['id'], $row['description'], $row['date'], $proccess);
        }
        if(($_SESSION['tasks']+5)>row_count("SELECT COUNT(*) FROM `tasks`WHERE `proccess`<100 and `author`='$_SESSION[admin]' ORDER BY `date`"))
        {
            $hide = 'true';

            $_SESSION['tasks'] = row_count("SELECT COUNT(*) FROM `tasks`WHERE `proccess`<100 and `author`='$_SESSION[admin]' ORDER BY `date`"); }
        else{ $_SESSION['tasks'] = $_SESSION['tasks']+5; $hide = 'false';
        }
    }

    if($_POST['name']=='comp-tasks-more')
    {
        $sql = mysql_query("SELECT * FROM `tasks` WHERE `proccess`=100 and `author`='$_SESSION[admin]' ORDER BY `date` DESC LIMIT $_SESSION[comp_tasks],8", $link);
        while ($row = mysql_fetch_array($sql))
        {
            $proccess = "<span>Прогресс ".$row['proccess']."%</span>";
            $task = "<a href='/task/".$row['id']."'>Задание</a>";
            $result=$result.table_content($task, $row['description'], $row['date'], $proccess);
            $result=$result.table_content_mobile('Ссылка', 'Короткое описание', 'Дата и прогресс', $row['id'], $row['description'], $row['date'], $proccess);
        }
        if(($_SESSION['comp_tasks']+5)>row_count("SELECT COUNT(*) FROM `tasks`WHERE `proccess`=100 and `author`='$_SESSION[admin]' ORDER BY `date`"))
        {
            $hide = 'true';

            $_SESSION['comp_tasks'] = row_count("SELECT COUNT(*) FROM `tasks`WHERE `proccess`=100 and `author`='$_SESSION[admin]' ORDER BY `date`"); }
        else
            {
            $_SESSION['comp_tasks'] = $_SESSION['comp_tasks']+5; $hide = 'false';
        }
    }

    $result= str_replace("\"","'",$result);
    $ret ='{
    "text":"'.$result.'",
    "hide":"'.$hide.'"
    }';
    exit($ret);
}

if($Module=="full_mess"){
    if($_POST['name']=="full_mess" and $_POST['id'])
    {
    $text = mysql_fetch_array(mysql_query("SELECT * FROM `messages` WHERE `id`='$_POST[id]'",$link));
    exit($text['text']);
    }
}

if($Module=="changepass")
{
    if($_POST['name']=="change" and $_POST['oldpass']and $_POST['newpass'])
    {
        if(strlen($_POST['newpass'])<6)
        {
            exit(message("Новый пароль слишком короткий",3));
        }
        else
            {
            $pass =  mysql_fetch_array(mysql_query("SELECT `password` FROM `users` WHERE `login` = '$_SESSION[login]'",$link));
            if($pass['password']==pass_crypt($_SESSION['login'],$_POST['oldpass']))
            {
                    $newpass = pass_crypt($_SESSION['login'],$_POST['newpass']);
                    $id = mysql_fetch_array(mysql_query("SELECT `id` FROM `users` WHERE `login` = '$_SESSION[login]'",$link));
                    mysql_query("UPDATE `users` SET `password`='$newpass' WHERE `id`='$id[id]'",$link) OR die (mysql_error());
                    exit(message("Пароль изменен, пожалуйста перезайдите",1));
            }
            else exit(message("Введеный старый пароль неверный",3));
        }
    }
}