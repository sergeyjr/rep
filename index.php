<?php

//Уровень сообщений об ошибках
//error_reporting(E_ALL);

//Старт сессии
session_start();

//Проверка версии PHP
if (version_compare(phpversion(), '5.3.0', '<')) {
die('Внимание! Для правильной работы скрипта необходим PHP версии 5.3.0 и выше.');
}

//Вывод заголовка с данными о кодировке страницы
header('Content-Type: text/html; charset=utf-8');

//Кодировка UTF-8 
setlocale(LC_ALL, 'Russian_Russia.65001');

//Устанавливаем временную зону по умолчанию для всех функций даты/времени в скрипте
date_default_timezone_set('Europe/Moscow');

//Запускаем счетчик времени
list($usec, $sec)=explode(" ", microtime());
$querytime_before=((float)$usec+(float)$sec);

/////////////////////////////////////////////////

//Подключение файла с настройками CMS
include_once(__DIR__.'/admin/config.php');

//Подключение класса
include_once($_SERVER["DOCUMENT_ROOT"].$_CONFIG['dir_name'].'/libs/ndv-class.php');

/////////////////////////////////////////////////

//Действие по умолчанию
if (!isset($_POST['action'])) {
$_POST['action']='';
}
if (!isset($_GET['action'])) {
$_GET['action']='';
}

//Ф.И.О.
if (!isset($_POST['client_name'])) {
$_POST['client_name']='';
}
if (!isset($_GET['client_name'])) {
$_GET['client_name']='';
}

//Тип поиска (0 - обычный поиск, 1 - живой поиск)
if (!isset($_POST['live_search'])) {
$_POST['live_search']='1';
}
if (!isset($_GET['live_search'])) {
$_GET['live_search']='1';
}

//Тип сортировка (id, name)
if (!isset($_POST['sort'])) {
$_POST['sort']='id';
}
if (!isset($_GET['sort'])) {
$_GET['sort']='id';
}

//Кол-во записей на странице
if (!isset($_POST['data_num'])) {
$_POST['data_num']='20';
}
if (!isset($_GET['data_num'])) {
$_GET['data_num']='20';
}

//Инициализация класса
$ndvObj=new NdvClass($_CONFIG);

//Установка кодировки вывода записей из БД
mysql_query("SET NAMES 'utf8'"); 
mysql_query("SET CHARACTER SET 'utf8'");
mysql_query("SET SESSION collation_connection='utf8_general_ci'");

/////////////////////////////////////////////////

//Живой поиск
if ($_GET['action'] == 'live_search') {
$info_array=$ndvObj->LiveSearch($_GET['client_name'], $_GET['sort'], $_GET['data_num']);
$info_array["processing_time"]=$ndvObj->ProcessingTime($querytime_before);
print json_encode($info_array);
exit();
}

//Проверка существования таблиц в БД
if ($ndvObj->TablesExistsDB() == 0) {
header('Location: '.$_CONFIG['dir_name'].'/admin/index.php');
exit();
}

/////////////////////////////////////////////////

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<HTML>
<HEAD>
<TITLE>Тестовое задание НДВ</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<LINK REL="stylesheet" TYPE="text/css" HREF="css/bootstrap.css">
<LINK REL="stylesheet" TYPE="text/css" HREF="css/bootstrap-theme.css">
<SCRIPT TYPE="text/javascript" SRC="js/jquery-1.8.3.min.js"></SCRIPT>
<SCRIPT TYPE="text/javascript" SRC="js/bootstrap.min.js"></SCRIPT>
<SCRIPT TYPE="text/javascript" SRC="js/jquery-ndv.js"></SCRIPT>
<STYLE TYPE="text/css">

#progress {
position: fixed;
display: none;
top: expression(parseInt(document.documentElement.scrollTop, 10)+"px");
left: 0;
color: #111111;
width: 100%;
height: 100%;
padding: 0;
margin: 0;
z-index: 99999;
}

#progress_container {
position: absolute;
left: 50%;
top: 50%;
margin-left: -102px;
margin-top: -77px;
}

#progress_main {
position: relative;
width: 200px;
height: 140px;
font-family: Arial, Tahoma, Verdana;
font-size: 10pt;
border: 2px solid #6699CC;
background-color: #FFFFFF;
padding-top: 40px;
text-align: center;
filter: alpha(opacity=90);
opacity: 0.9;
}

#loader_icon {
position: relative;
display: inline-block;
width: 32px;
height: 32px;
background: url("images/loading-blue-big.gif") 0 0 no-repeat;
}

</STYLE>
</HEAD>
<BODY>

<!-- Progress Container Start //-->

<DIV ID="progress">
<DIV ID="progress_container">
<DIV ID="progress_main">
<DIV ID="loader_icon"></DIV>
<DIV><B>Действие выполняется...</B></DIV>
<DIV>Пожалуйста подождите.</DIV>
</DIV>
</DIV>
</DIV>

<!-- Progress Container End //-->

<!-- Форма поиска //-->

<nav class="navbar navbar-inverse">
<div class="container">
<div class="navbar-header">
<a class="navbar-brand" href="<?=$_CONFIG['dir_name'];?>">НДВ Демо</a>
</div>
<div id="navbar" class="collapse navbar-collapse">
<ul class="nav navbar-nav">
<li class="active"><a href="<?=$_CONFIG['dir_name'];?>">Публичная часть</a></li>
<li><a href="<?=$_CONFIG['dir_name'];?>/admin">Панель управления</a></li>
<li><a href="mailto:sergeyjr79@mail.ru">Обратная связь</a></li>
</ul>
</div>
</div>
</nav>

<FORM METHOD="POST" ACTION="<?=$_SERVER["SCRIPT_NAME"];?>" NAME="filter_form" ID="filter_form">

<DIV CLASS="well">

<TABLE BORDER="0" CELLPADDING="5" CELLSPACING="0">
<TR>
<TD><B>Тип поиска</B>:</TD>
<TD><INPUT TYPE="radio" NAME="live_search" ID="live_search2" VALUE="1" <?php if ($_POST['live_search']) print "CHECKED"; ?>>&nbsp;<LABEL FOR="live_search2">Живой поиск (без перезагрузки страницы)</LABEL><BR><INPUT TYPE="radio" NAME="live_search" ID="live_search" VALUE="0" <?php if (!$_POST['live_search']) print "CHECKED"; ?>>&nbsp;<LABEL FOR="live_search">Обычный поиск</LABEL></TD>
</TR>
<TR>
<TD><B>Ф.И.О.</B>:</TD>
<TD><INPUT TYPE="text" NAME="client_name" ID="client_name" VALUE="<?=urldecode($_POST['client_name']);?>" SIZE="40">&nbsp;<INPUT TYPE="submit" NAME="search_button" ID="search_button" VALUE="Найти"></TD>
</TR>
<TR>
<TD><B>Сортировать</B>:</TD>
<TD><SELECT NAME="sort" ID="sort">
<OPTION VALUE="id">По ID</OPTION>
<OPTION VALUE="name" <?php if ($_POST["sort"] == "name") print "SELECTED"; ?>>По Ф.И.О.</OPTION>
</SELECT></TD>
</TR>
<TR>
<TD><B>Показывать</B>:</TD>
<TD><SELECT NAME="data_num" ID="data_num">
<?php $ndvObj->PrintOptions($_POST['data_num']); ?>
</SELECT> записей на странице</TD>
</TR>
</TABLE>

</DIV>

</FORM>

<DIV><H3>Список клиентов:</H3></DIV>

<?php

if ($ndvObj->_error) {
print "<DIV CLASS=\"alert alert-danger\" ROLE=\"alert\"><B>".$ndvObj->_error."</B></DIV>\n";
}

$query='SELECT id FROM '.$_CONFIG['tb_prefix'].'clients';
$result=$ndvObj->query($query);
$total_num_rows=mysql_num_rows($result);

$results_array=$ndvObj->GetClientsInfo($_POST['client_name'], $_POST['sort'], $_POST['data_num'], '0');

$num_rows=count($results_array);

if (!$num_rows) {

print "<DIV>Результатов нет.<BR><BR><A HREF=\"".$_CONFIG['dir_name']."\">Показать все записи</A></DIV>\n";

}
else {

?>

<DIV ID="no_results" STYLE="display: none;">Результатов нет.</DIV>

<DIV ID="results">

<TABLE BORDER="0" CELLPADDING="5" CELLSPACING="0" WIDTH="100%" CLASS="table table-striped">
<THEAD>
<TR>
<TH>№</TH>
<TH>Ф.И.О.</TH>
<TH>Адрес</TH>
<TH>Эл. почта</TH>
<TH>Телефон</TH>
</TR>
</THEAD>
<TBODY ID="content">
<?php
$num=1;
foreach ($results_array as $key_array) {
print "<TR>\n";
print "<TD>".$num."</TD>\n";
print "<TD>".$key_array['name']."</TD>\n";
print "<TD>".$key_array['address']."</TD>\n";
print "<TD>".$key_array['emails']."</TD>\n";
print "<TD>".$key_array['phones']."</TD>\n";
print "</TR>\n";
$num++;
}
?>
</TBODY>
</TABLE>

<DIV><B>Всего</B>: <?=$total_num_rows;?> записей.</DIV>

</DIV>

<?php } ?>

<DIV><HR></DIV>

<DIV ALIGN="center">Время генерации страницы: <SPAN ID="processing_time"><?=$ndvObj->ProcessingTime($querytime_before);?></SPAN> сек.</DIV>

</BODY>
</HTML>
<?php

//Закрытие соединения с MySQL
$ndvObj->closedb();

?>