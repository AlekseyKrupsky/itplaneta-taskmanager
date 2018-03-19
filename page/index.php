<?php
HeadTitle("Task Manager");
reg();
login();
Top();
menu();
?>

<?
if($_SESSION['LOG_IN']=="LOGIN") {
    ?>
    <div id="tasks">
    <div class="wrapper">
        <? $sql = mysql_fetch_array(mysql_query("SELECT count(*) FROM `tasks` WHERE `proccess`<100 and `author`='$_SESSION[admin]'", $link));

        if($sql['count(*)']) {
            ?>
            <h1>Свежие задания</h1>
            <? table_head('Ссылка', 'Короткое описание', 'Дата и прогресс'); ?>
            <?php
            $sql = mysql_query("SELECT * FROM `tasks` WHERE `proccess`<100 and `author`='$_SESSION[admin]' ORDER BY `id` DESC LIMIT 5 ", $link);
            while ($row = mysql_fetch_array($sql)) {
                $task = "<a href='/task/".$row['id']."'>Задание</a>";
                $proccess = "<span>Прогресс ".$row['proccess']."%</span>";
              echo  table_content($task, $row['description'], $row['date'], $proccess);

                echo  table_content_mobile('Ссылка', 'Короткое описание', 'Дата и прогресс', $task, $row['description'], $row['date'], $proccess);
            }
        }
        else echo "<h2>Ваш руководитель не добовил еще ни одного задания</h2>";
    ?>
    </div> </div>
    <?php
    //table_content_mobile('Ссылка','Короткое описание','Дата и прогресс',$row['id'],$row['description'],$row['date'],$row['proccess']);
}

?>
    <div id="slider">
        <div class="slide">
            <?php
            $sql = mysql_query("SELECT * FROM `slider` WHERE `slider`=true", $link);
            $i = 1;
            while ($row = mysql_fetch_array($sql)) {
                ?>

                <div id="<?php echo "slide" . $i ?>" class="slides <?php if ($i == 1) echo "active-slide"; ?> ">
                    <div class="title">
                        <?=$row['title'] ?>
                    </div>
                    <div class="text">  <?=$row['short'] ?></div>
                    <div class="slide-link"><a href=/news/<?=$row['id'] ?>>Узнать больше
                        </a>
                    </div>
                </div>
                <?php
                $i++;
            }

            ?>
        </div>
        <div class="pages">
            <div class="p active" id="p1"></div>
            <? for($j=2;$j<$i;$j++)
        echo '<div class="p" id="p'.$j.'"></div>';

            ?>
        </div>
    <div class="full-descr main">
        <h1>О нас</h1>
        <?
        $row = mysql_fetch_array(mysql_query("SELECT `text` FROM `main` WHERE `id`=1",$link));
        echo $row['text'];
        ?>
    </div>
<?
footer();
Bottom();
