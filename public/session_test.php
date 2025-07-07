<?php
file_put_contents('debug.txt', 'session_test.php loaded');
session_start();
echo "Session user: " . ($_SESSION["user"] ?? "not set");