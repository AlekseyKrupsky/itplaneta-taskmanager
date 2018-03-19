<?php
HeadTitle("Все задания");
reg();
login();
Top();
menu();
if($_SESSION['LOG_IN']=='LOGIN') {
    ?>
    <div id="tasks">
        <div class="wrapper">
            <? if(row_count("SELECT COUNT(*) FROM `tasks` WHERE `proccess`<100 and `author`='$_SESSION[admin]' ORDER BY `date` DESC")>0) {
                ?>
                <h1>Все открытые задания</h1>
                <? table_head('Ссылка', 'Короткое описание', 'Дата и прогресс'); ?>
                <?php
                $sql = mysql_query("SELECT * FROM `tasks` WHERE `proccess`<100 and `author`='$_SESSION[admin]' ORDER BY `date` DESC LIMIT 8", $link);
                while ($row = mysql_fetch_array($sql)) {
                    $task = "<a href='/task/".$row['id']."'>Задание</a>";
                    $proccess = "<span>Прогресс ".$row['proccess']."%</span>";
                    echo  table_content($task, $row['description'], $row['date'], $proccess);
                    echo  table_content_mobile('Ссылка', 'Короткое описание', 'Дата и прогресс', $task, $row['description'], $row['date'], $proccess);
                }
            }
            else echo "<h2>Список заданий пуст</h2>";
            if(row_count("SELECT COUNT(*) FROM `tasks` WHERE `proccess`<100 and `author`='$_SESSION[admin]' ORDER BY `date` DESC")>8) {
        echo '<div  class="more"><button id="tasks-more" name="more">Загрузить еще</button></div>';
        $_SESSION['tasks']=8;
    }
            ?>
        </div>
    </div>
    <?php
}
else
{non_login();}
footer();
Bottom();
