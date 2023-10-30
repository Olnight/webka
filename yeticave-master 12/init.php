<?php
session_start();
// $_SESSION['user_id'] = '';
// $_SESSION['user'] = '';
// $_SESSION['auth'] = 0;


$title = 'Главная';
const HOST = "localhost";
const USER = "root";
const PASSWORD = "";
const DATABASE = "xtdtuuht_m3";
$con = mysqli_connect(HOST, USER, PASSWORD,DATABASE);
mysqli_set_charset($con, "utf8");
