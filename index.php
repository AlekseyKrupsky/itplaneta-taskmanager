<?php
include_once 'settings.php';
$row =  mysql_fetch_array(mysql_query("SELECT `admin` FROM `users` WHERE `login` = '$_SESSION[login]'",$link));
$_SESSION['admin']=$row['admin'];
if($_SESSION['LOG_IN']=='LOGOUT')
{
    setcookie('login', '',1);
    setcookie('password','',1);
}
else if($_SESSION['LOG_IN']=='LOGIN')
{
    setcookie('login', $_SESSION['login'],time()+(3600*24*5));
    setcookie('password',$_SESSION['pass'],time()+(3600*24*5));
}
else if($_SESSION['LOG_IN']!='LOGIN' && isset($_COOKIE['login']) && isset($_COOKIE['password']))
{
    $row =  mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `login` = '$_COOKIE[login]'",$link));
    if(md5($row['password'])==$_COOKIE['password'])
    {
        // echo $row['password'];
        $_SESSION['LOG_IN']='LOGIN';
        $_SESSION['ID']=$row['id'];
        $_SESSION['login']=$_COOKIE['login'];
        $_SESSION['mail']=$row['email'];
        $_SESSION['TOU']=$row['type_of_user'];
        $_SESSION['admin']=$row['admin'];
     //   $_SESSION['pass']=md5($_POST['password']);
        header("Location: /");
    }
}


if ($_SERVER['REQUEST_URI'] == '/') {
    $Page = 'index';
    $Module = 'index';
} else {
    $URL_Path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $URL_Parts = explode('/', trim($URL_Path, ' /'));
    $Page = array_shift($URL_Parts);
    $Module = array_shift($URL_Parts);


    if (!empty($Module)) {
        $Param = array();
        for ($i = 0; $i < count($URL_Parts); $i++) {
            $Param[$URL_Parts[$i]] = $URL_Parts[++$i];
        }
    }
}
if ($Page == 'index')  include('page/index.php');
else if ($Page == 'actions')  include('actions.php');
else if ($Page=='messages') include('page/messages.php');
else if ($Page=='profile') include('page/profile.php');
else if ($Page=='tasks') include('page/tasks.php');
else if ($Page=='task') include('page/task.php');
else if ($Page=='complited-tasks') include('page/complited-tasks.php');
else if ($Page=='news') include('page/news.php');
else if ($Page=='search') include('page/search.php');

function HeadTitle($title)
{
    ?>
    <!DOCTYPE html>
    <html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title><?php echo $title; ?></title>
     <link href="/resource/style.css" rel="stylesheet">
    <link rel="shortcut icon"  href="/resource/logo.png" type="image/x-icon">
    <script src="/jquery.js"></script>
    <link rel="shortcut icon" href="" type="image/x-icon">
</head> <body>
    <div id="main">
    <?php
}
function Top()
{
    //echo $_SESSION['LOG_IN'];?>
<div id="message"></div>
<div id="header">
<div class="wrapper">
<div id="logo">
    <a href="/">
<span id="logo-white">Task</span><span id="logo-blue">Manager</span></a>
</div>
<div id="head-buttons">
<?php if($_SESSION['LOG_IN']=="LOGIN") {?>
<div  class="HB"><a href="/profile"> Профиль</a>
<img src="/resource/profile.png" alt="">
</div>
<div  class="HB"><a href="/messages"> Сообщения
    <?php
    // echo intval(checkmess($_SESSION['login']));
    if(checkmess($_SESSION['login'])>0) echo '('.checkmess($_SESSION['login']).')';
    ?>
    </a>
<img src="/resource/mess.png" alt=""></div>

<?php
}
?>

<?php if($_SESSION['LOG_IN']=="LOGIN")
    echo '<div class="HB ">
<a href="actions/logout">Выйти</a><img src="/resource/logout.png" alt="">';
else echo '
<div class="registr HB">Регистрация</div><div class="HB login">
Вход<img src="/resource/login.png" alt="">'
?>
</div>
</div>
</div>
</div>
        <?php
}
function checkmess($user)
{
    return mysql_fetch_array(mysql_query("SELECT `new_mess` FROM `users` WHERE `login`='$user'",mysql_connect(host,user,pass)))['new_mess'];
}
function menu()
{
    $link=mysql_connect(host,user,pass);
    ?>
<div id="menu">
<div class="wrapper">
<div id="pages">
<span class="menu-link"><a href="/">Главная</a> </span>
    <span class="menu-link"><a href="/news">Новости</a></span>
    <? if($_SESSION['LOG_IN']=='LOGIN'){?>
<span class="menu-link"><li><a href="/tasks"> Все задания</a>
<?
$sql = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM `tasks` WHERE `proccess`<100 and `author`='$_SESSION[admin]' ORDER BY `date` DESC", $link));
            if($sql['COUNT(*)']>0) {

            $sql = mysql_query("SELECT * FROM `tasks` WHERE `proccess`<100 and `author`='$_SESSION[admin]' ORDER BY `date` DESC LIMIT 4", $link);
            ?>
            <ul>
            <?while ($row=mysql_fetch_array($sql)){
                echo "<li><a href='/task/".$row['id']."'>".$row['name']."</a></li>";
            }?>
      </ul>
<?} ?>

    </li></span>
<span class="menu-link"><a href="/complited-tasks">Выполненные</a> </span>
    <? }?>

</div>
    <? if($_SESSION['LOG_IN']=="LOGIN") {?>
<div class="search">
    <form action="/search" method="post">
        <input type="text" name="search" placeholder="Введите запрос...">
        <input type="image" name="search" src="/resource/search.png">
    </form>
</div>
        <?} ?>
    <div id="mob-menu">
        <img src="/resource/menu.png" alt="">
    </div>
    <div id="mobpages">
        <? if($_SESSION['LOG_IN']=="LOGIN") {?>
        <div class="search">
            <form action="/search" method="post">
                <input type="text" name="search" placeholder="Введите запрос...">
                <input type="image" name="search" src="/resource/search.png">
            </form>
        </div>
            <?
        }
        ?>
        <span class="menu-link"><a href="/">Главная</a> </span>
        <span class="menu-link"><a href="/news">Новости</a></span>
    <? if($_SESSION['LOG_IN']=='LOGIN'){?>
        <div id="mob-task"><span class="menu-link"><a href="/tasks">Задания</a></span> <img src="/resource/arrow.png" alt=""> </div>
        <div id="mob-tasks">

            <?
            $sql = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM `tasks` WHERE `proccess`<100 and `author`='$_SESSION[admin]' ORDER BY `date` DESC", $link));
            if($sql['COUNT(*)']>0) {

                $sql = mysql_query("SELECT * FROM `tasks` WHERE `proccess`<100 and `author`='$_SESSION[admin]' ORDER BY `date` DESC LIMIT 4", $link);
                ?>
                    <?while ($row=mysql_fetch_array($sql)){
                        echo "<span class='menu-link'><a href='/task/".$row['id']."'>".$row['name']."</a></span>";
                    }?>

            <?}
            else echo "<span class='menu-link'>Заданий нет</span>"
            ?>
        </div>
        <span class="menu-link"><a href="/complited-tasks">Выполненные</a> </span>
    <? }?>

    </div>
</div>
</div>
<?php
}
function footer()
{
    $link=mysql_connect(host,user,pass);
    ?>
    <div id="footer">
    <div class="wrapper">
<div id="links"><h1>Ссылки</h1>
<div id="icons">
<?
        $sql = mysql_query("SELECT * FROM `soc`",$link);
        while ($row=mysql_fetch_array($sql))
        {
            $val = "";
if($row['link']!='null') $val = $row['link'];
            echo '<div class="icon"><a align="center" href="'.$val.'" target="_blank"><img  src="/resource/'.$row['img'].'" alt="'.$row['soc'].'" ></a></div>';
        }


    ?>

<!--   <div class="icon"><img src="/resource/vk.png" alt=""></div>-->
<!--   <div class="icon"><img src="/resource/tw.png" alt=""></div>-->
<!--    <div class="icon"> <img src="/resource/yt.png" alt=""></div>-->
<!--    <div class="icon"> <img src="/resource/fb.png" alt=""></div>-->
<!--    <div class="icon"> <img src="/resource/g+.png" alt=""></div>-->
</div>

</div>
<div id="contacts">
<h1>Контакты</h1>
<p>222012, Republic of Belarus, Minsk, Minsk region, Lubimova 34/1 144</p>
<p>+375172345414</p>
<p>dachadybenci2@gmail.com</p>
</div>

</div></div>
        <?php
}
function Bottom()
{
    echo "</div><script src='/page/script.js'></script>
</body></html>";
}
function reg()
{
    if($_SESSION[LOG_IN]!='LOGIN'){
    ?>
    <div class="reg-window reg">
<div class="form form-wrap">
                <label for="">Логин <input name="login_but" type="text"></label>
                <label for=""> Пароль <input name="password" type="password"></label>
                    <label for="">  Email <input name="mail" type="email"></label>
                        <label for=""> Тип пользователя  <select name="TOU" id="">
                    <option value="user">Пользователь</option>
                    <option value="admin">Админ</option>
                </select> </label>
    <img src="captcha.php" alt="Капча">
    <p>Число с картинки<br><input type="text" name="captcha" required></p>
                <label for=""> <input type="submit" name="reg" value="Зарегистрироваться"> <button value="Cancel">Отмена</button></label>
</div>
    </div>
    <?php
    }
}
function login()
{
    if($_SESSION[LOG_IN]!='LOGIN'){
    ?>
    <div class="reg-window log">
        <div class="form form-wrap">
                <label for="">Логин <input name="login_l" type="text"></label>
                <label for=""> Пароль <input name="password_l" type="password"></label>
                <label for=""><input type="submit" name="login" value="Войти"> <button value="Cancel" >Отмена</button></label>
        </div>
    </div>
    <?php
    }
}
function max_lenght($string,$lenght)
{
    if(strlen($string)>$lenght)
    {
        return substr($string,0,$lenght)."...";
    }
    else return $string;
}
function message($text,$type)
{
    $color = "";
    if($type==1)
    {
        $color="#128504";
    }
    else if($type==2)
    {
        $color="#B0A904";
    }
    else if($type==3)
    {
        $color="#9C0600";
    }
    return '{
    "text":"'.$text.'",
    "color":"'.$color.'"
    }';
}
function table_head($c1,$c2,$c3)
{
    ?>
    <div class="tasks-wrap">
            <div class="link head"><? echo $c1?></div>
            <div class="short-description head"><? echo $c2?></div>
            <div class="time-prog head"><? echo $c3?></div>
        </div>
    <?
}
function table_content($c1,$c2,$c3,$c4) // 3 колонки даннае в каждую соответственно
{

    $result = '<div class="tasks-wrap"><div class="link cont">' . $c1 . '</div><div class="short-description cont">' . max_lenght($c2,300) . '</div>';
    if($c4) $result = $result.'<div class="time-prog cont">' . $c3 . '<br>' . $c4 . '</div>';
    else $result = $result.'<div class="time-prog cont"><span>' . $c3 . '</span></div>';
    $result = $result.'</div>';



    if($c4=='search')
    {
        $result = '<div class="tasks-wrap"><div class="link cont">' . $c1 . '</div><div class="short-description cont">' . max_lenght($c2,300) . '</div>';
        $result = $result.'<div class="time-prog cont link">' . $c3 . '</div>';
        $result = $result.'</div>';
    }
    return $result;
}
function table_content_mobile($t1,$t2,$t3,$c1,$c2,$c3,$c4)
{

    $result =
    '<div class="mob"><div class="short-description head">'.$t2.'</div><div class="short-description cont">' . max_lenght($c2,300) . '</div><div class="mob-bot">';
    $result = $result. '<div class="left"><div class="link head">'.$t1.'</div><div class="link cont">'.$c1.'</div></div><div class="right"><div class="time-prog head">'.$t3.'</div>';

    if($c4) $result = $result.'<div class="time-prog cont">' . $c3 . '<br>' . $c4 . '</div></div></div></div>';
    else $result = $result.'<div class="time-prog cont">' . $c3 . '</div></div></div></div>';

    if($c4=='search')
    {
        $result =
            '<div class="mob"><div class="short-description head">'.$t2.'</div><div class="short-description cont">' . max_lenght($c2,300) . '</div><div class="mob-bot">';
        $result = $result. '<div class="left"><div class="link head">'.$t1.'</div><div class="link cont">'.$c1.'</div></div><div class="right"><div class="time-prog head">'.$t3.'</div>';
        $result = $result.'<div class="time-prog cont link">' . $c3 . '</div></div></div></div>';
    }

    return $result;

}
function non_login()
{
?>
    <div class="non-log">
    <div class="wrapper">
    Вы вошли на эту страницу как незарегистрированный пользователь. <br>
    Войдите или зарегистрируйтесь
</div></div>
  <?
}
function row_count($request)
{
    $link=mysql_connect(host,user,pass);
    $sql = mysql_fetch_array(mysql_query($request, $link));
    return $sql['COUNT(*)'];
}
function pass_crypt($login,$pass){
   return  crypt(md5(crypt($pass,$login),$pass),$login);
}
function send_mess($to,$mess,$from)
{
    $link=mysql_connect(host,user,pass);

    $date = date('y-m-d');

    if($from =='system') {
        mysql_query("INSERT INTO `messages` VALUES (null, 'System','$to','$mess','$date')",$link);
    }

    else mysql_query("INSERT INTO `messages` VALUES (null, '$_SESSION[login]','$to','$mess','$date')",$link);

        $count=mysql_fetch_array(mysql_query("SELECT `new_mess` FROM `users` WHERE `login`='$to'",$link));
       $count= intval($count['new_mess'])+1;
       mysql_query("UPDATE `users` SET `new_mess`='$count' WHERE `login`='$to'",$link);
}
