<?php
session_start();
//$_SESSION['username'] = 'xiaoyang';
//$_SESSION['password'] = '123456';

var_dump($_SESSION);

session_destroy();
var_dump($_SESSION);