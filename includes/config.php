<?php

ini_set('display_errors', 1);
session_start();

if (isset($_GET['reset'])) {
    session_destroy();
    die();
}

if (!defined("SESSIONPREFIX")) {
    define("SESSIONPREFIX", "_formValidator_");
}

$_SESSION[SESSIONPREFIX . 'language'] = "en";
include_once dirname(__FILE__) . "/../languages/" . $_SESSION[SESSIONPREFIX . 'language'] . "/lang.php";
include_once dirname(__FILE__) . "/functions.php";
?>