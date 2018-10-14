<?php
//We start sessions
session_start();

/******************************************************
------------------Required Configuration---------------
Please edit the following variables so the members area
can work correctly.
******************************************************/

//We log to the DataBase
$link = new mysqli('us-cdbr-iron-east-01.cleardb.net:3306', 'bcb94ff664a17f', 'c4780c9e', 'heroku_f71fa6cda1bf9f5');
if (!$link) {
    die('Could not connect: ' . mysql_error());
}

//Webmaster Email
$mail_webmaster = 'example@example.com';

//Top site root URL
$url_root = 'http://www.example.com/';

/******************************************************
-----------------Optional Configuration----------------
******************************************************/

//Home page file name
$url_home = 'index.php';

//Design Name
$design = 'default';
?>