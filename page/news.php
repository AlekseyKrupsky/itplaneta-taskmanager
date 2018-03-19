<?php
/**
 * Created by PhpStorm.
 * User: LORD
 * Date: 12.03.2018
 * Time: 17:28
 */

HeadTitle("Новости");
reg();
login();
Top();
menu();
if($Module)
{
$row=mysql_fetch_array(mysql_query("SELECT * FROM `slider` WHERE `id` ='$Module' ",$link));
print "<div class='non-log'><h1> $row[title] </h1>";
print "
 <div class='wrapper'>
$row[full]</div>";
print "<div class='date'>".$row['date']."</div></div>";
}

else{
    $sql=mysql_query("SELECT * FROM `slider` ORDER BY `date` DESC",$link);
while($row=mysql_fetch_array($sql))
{
    print "
<div class='news'><div class='wrap'> <h3>$row[title]</h3>
$row[short]<br>
<div class='date new'>
<span>".$row['date']."</span>
<div><a href='/news/".$row['id']."'>Читать дальше</a>";
    if($_SESSION['TOU'] == "SU"){
        echo " <button name='".$row['id']."' class='del_news page-news'>Удалить</button>";
    }


        echo "</div></div></div></div>
";
}
}

footer();
Bottom();
