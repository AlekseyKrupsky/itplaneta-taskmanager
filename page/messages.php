<?php
mysql_query("UPDATE `users` SET `new_mess`=0 WHERE `login`='$_SESSION[login]'",$link );
HeadTitle("Сообщения");
Top();
menu();
if($_SESSION['LOG_IN']=='LOGIN') {
?>
<div id="messages">
<div class="wrapper  mess">

    <? $sql = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM `messages` WHERE `to`='$_SESSION[login]' ORDER BY `id`", $link));
   if($sql['COUNT(*)']>0){

    ?>
    <h1>Последние сообщения</h1>
    <?php
    //    if(checkmess($_SESSION['login'])==0) echo "You dont have any new messages";
    //    else {
    $sql = mysql_query("SELECT * FROM `messages` WHERE `to`='$_SESSION[login]' ORDER BY `id`   DESC LIMIT 15", $link);
    table_head("От", "Сообщение", "Дата");
    ?>
    <?php
    // $i =0;
    while ($row = mysql_fetch_array($sql)) {

        if(strlen($row['text'])>200){
        $text = max_lenght($row['text'],200);
        $text = $text." <div class='show-all' id='".$row['id']."'>Показать все</div>  ";
        }
        else $text = $row['text'];

   echo table_content($row['from'],$text,$row['date'],'');
   echo table_content_mobile('От', 'Сообщение', 'Дата', $row['from'], $text, $row['date'], '');
    // $i++;
}

}
else echo "<h2>У вас пока нет ниодного сообщения</h2>";
   if(row_count("SELECT COUNT(*) FROM `messages` WHERE `to`='$_SESSION[login]'")>15) {
       $_SESSION['mess'] = 15;
       echo '<div class="more"><button id="mess" name="more">Загрузить еще</button></div>';
   }
        ?>
    <h2>Отправить новое сообщение</h2>
    <form action="actions/message" method="post" id="send-mess">
        <label for=""><textarea name="mess" id="" cols="90" rows="3" placeholder="Введите сообщение"></textarea></label>
        <label for="">Кому &nbsp;<input type="text" name="to"> &nbsp;<input type="submit" value="Отправить"></label>
    </form>
<!--    <form action="actions/history" method="post">-->
        Отпрваить на мою почту переписку с  <input id="with" type="text">
        <button id="send-to-mail">Отправить</button>
<!--    <input type="submit">-->
<!--    </form>-->
</div>
</div>
<?php
}
else non_login();
footer();
Bottom();
