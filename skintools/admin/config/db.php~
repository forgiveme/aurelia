<?php

/**
 * Configuration for: Database Connection
 *
 * For more information about constants please @see http://php.net/manual/en/function.define.php
 * If you want to know why we use "define" instead of "const" @see http://stackoverflow.com/q/2447791/1114320
 *
 * DB_HOST: database host, usually it's "127.0.0.1" or "localhost", some servers also need port info
 * DB_NAME: name of the database. please note: database and database table are not the same thing
 * DB_USER: user for your database. the user needs to have rights for SELECT, UPDATE, DELETE and INSERT.
 * DB_PASS: the password of the above user
 */
//define("DB_HOST", "localhost");
//define("DB_NAME", "skintool_db");
//define("DB_USER", "skintool_user");
//define("DB_PASS", "kVBC%KC^^v5p");

define("DB_HOST", "localhost");
define("DB_NAME", "aureliag_arlagitdb");
define("DB_USER", "aureliag_arlagit");
define('DB_PASS', 'OK3a$ERym4q&');
// Create connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, 3306);

if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}