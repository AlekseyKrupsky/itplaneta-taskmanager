<?php
HeadTitle("Поиск");
reg();
login();
Top();
menu();
if($_SESSION['LOG_IN']=='LOGIN') {
    ?>
    <div id="tasks">
        <div class="wrapper">
            <?
            if($_POST['search'])
            {
            $query = '%' . $_POST['search'] . '%';
            $tasks_count = row_count("SELECT COUNT(*) FROM `tasks` WHERE (`description` LIKE '$query' or `name` LIKE '$query')");
            $news_count = row_count("SELECT COUNT(*) FROM `slider` WHERE (`short` LIKE '$query' or `full` LIKE '$query' or `title` LIKE '$query')");
            $count = $news_count+$tasks_count;
               if($count==0)
               {
                   echo "<h2>Совпадений не найдено</h2>";
               }
               else {
                   echo  table_head('Название', 'Описание', 'Ссылка');
                    if($count>=1)
                    {
                        if($tasks_count)
                        {
                            $sql = mysql_query("SELECT * FROM `tasks` WHERE (`description` LIKE '$query' or `name` LIKE '$query')",$link);
                            while($result = mysql_fetch_array($sql)) {
                                $task = "<a href='/task/" . $result['id'] . "'>Задание</a>";
                                echo table_content($result['name'], $result['description'], $task, 'search');
                                echo table_content_mobile('Название', 'Описание', 'Ссылка', $result['name'], $result['description'], $task, 'search');
                            }
                        }
                        if($news_count) {
                            $sql = mysql_query("SELECT * FROM `slider` WHERE (`short` LIKE '$query' or `full` LIKE '$query' or `title` LIKE '$query')",$link);
                            while ($result=mysql_fetch_array($sql))
                            {
                                $news = "<a href='/news/".$result['id']."'>Новость</a>";
                                echo  table_content($result['title'], $result['full'], $news,'search');
                                echo  table_content_mobile('Название', 'Описание', 'Ссылка', $result['title'], $result['full'], $news,'search');
                            }

                        }
                    }

               }


            }
            else echo "<h2>Вы не ввели запрос</h2>";

            ?>
        </div>
    </div>
    <?
}
else non_login();
footer();
Bottom();