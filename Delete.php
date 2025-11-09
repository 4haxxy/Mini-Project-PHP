<?php

require 'Conn.php';

$id = $_GET['id'];
deleteUserByID($id);
header("Location: index.php");
exit();

?>