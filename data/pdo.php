<?php
try {
  $pdo = new PDO('mysql:host=localhost;port=3306;dbname=controlalarmas;charset=utf8','jm', 'jm');
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    print "¡Error!: " . $e->getMessage() . "<br/>";
    die();
}
