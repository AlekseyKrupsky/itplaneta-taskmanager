<?php
define ('host' , 'localhost');
define ('user' , 'root');
define ('pass' , '');
define ('db_name' , 'taskmanager');
session_start();
$link=mysql_connect(host,user,pass);
mysql_select_db(db_name,$link) OR die (mysql_error());