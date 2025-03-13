<?php
require_once 'settings.php';

session_start();


if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}
echo $_SESSION['admin_id'];
echo $_SESSION['admin_username'];

function debug()
{
    echo "merhaba dostlar";
}

?>