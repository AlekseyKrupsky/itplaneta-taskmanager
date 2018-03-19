<?php
HeadTitle("Все задания");
reg();
login();
Top();
menu();
$row=mysql_fetch_array(mysql_query("SELECT * FROM `tasks` WHERE `id` ='$Module' ",$link));
print "<div class='non-log'><h1> $row[name] </h1>";
print "
<div class='full-descr'> 
$row[description]</div><br>";
print "<div class='date'>".$row['date'];
if($row['proccess']==100)
    print "<span>Это задание завершено</span></div>";
    else  print "<span>Прогресс ".$row['proccess']."%</span></div></div>";
footer();
Bottom();