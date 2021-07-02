<?php
$dbconnection = "mysql:host=localhost;dbname=resume";
$username = "root";
$password = "";
$pdo = new PDO($dbconnection, $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);