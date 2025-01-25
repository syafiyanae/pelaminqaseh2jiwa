<?php

session_start();
unset($_SESSION['CustomerID']);
header("Location:Login.php");
?>