<?php
HeadTitle("Выполненные");
reg();
login();
Top();
menu();
if($_SESSION['LOG_IN']=='LOGIN') {
    ?>
    <div id="tasks">
        <div class="wrapper">
    <?
    $sql = mysql_fetch_array(mysql_query("SELECT count(*) FROM `tasks` WHERE `proccess` =100 and `author`='$_SESSION[admin]' ORDER BY `date` DESC ", $link));
    if($sql['count(*)']>0) {
        $sql = mysql_query("SELECT * FROM `tasks` WHERE `proccess` =100 and `author`='$_SESSION[admin]' ORDER BY `date` DESC LIMIT 5", $link);

        ?>

                <h1>Все завершенные задания</h1>
                <? table_head('Ссылка', 'Короткое описание', 'Дата и прогресс'); ?>
                <?php

                while ($row = mysql_fetch_array($sql)) {
                    $task = "<a href='/task/".$row['id']."'>Задание</a>";
                    $proccess = "<span>Прогресс ".$row['proccess']."%</span>";
                    echo table_content($task, $row['description'], $row['date'], $proccess);
                    echo table_content_mobile('Ссылка', 'Короткое описание', 'Дата и прогресс', $task, $row['description'], $row['date'], $proccess);
                }
    }
    else  echo '<h2>Список выполненных заданий пуст</h2>';
    if(row_count("SELECT COUNT(*) FROM `tasks` WHERE `proccess`=100 and `author`='$_SESSION[admin]' ORDER BY `date` DESC")>5) {
        echo '<div  class="more"><button id="comp-tasks-more" name="more">Загрузить еще</button></div>';
        $_SESSION['comp_tasks']=5;
    }
        ?>
            </div>
        </div>
        <?php

}
else non_login();
footer();
Bottom();
