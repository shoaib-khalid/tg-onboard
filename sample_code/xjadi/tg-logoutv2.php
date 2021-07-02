<?php
session_start();
unset($_SESSION['phonenumber']);
session_destroy();

die("ok");
?>