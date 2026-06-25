<?php
/**
 * Created by PhpStorm.
 * User: goran
 * Date: 25.10.2019
 * Time: 11:31
 */
$password='ela1234';
echo"Osnovni:",$password,"<br/>";
$password_hash = password_hash($password, PASSWORD_BCRYPT);
echo "Heshiran: ",$password_hash;

// Mysql : UPDATE `users` SET `password`= '$2y$10$3dJTN05y6vb5Fq0b9kP0IuojvZ.2dXY37maXd7vLHYT/xTa1WVUWi' WHERE `id`=4