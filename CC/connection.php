<?php
$servername="localhost";
$username="root";
$password="";
$dbname="EncryptandDecrypt";

$con = mysqli_connect($servername,$username,$password);
if(!$con){
    die("Could not connect to database :".mysql_error());
}

$db_selected = mysqli_select_db($con,'EncryptandDecrypt');

if(!$db_selected){
   $sql = mysqli_query($con,"CREATE DATABASE EncryptandDecrypt");

   if(!$sql){
       echo 'Error occured while creating the database';
   }
}

mysqli_query($con,"CREATE TABLE [IF NOT EXISTS] file_table(
    id int,
    original_file BLOB,");
?>