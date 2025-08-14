<?php
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=misc', 'root', 'root');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>
<!-- CREATE TABLE autos (
  autos_id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
  make VARCHAR(255),
  model VARCHAR(255),
  year INTEGER,
  mileage INTEGER
) ENGINE=InnoDB DEFAULT CHARSET=utf8; -->