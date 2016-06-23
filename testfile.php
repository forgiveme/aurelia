<?php
error_reporting(E_ALL);
ini_set("display_errors", true);
$ftp_server_name = "aureliadatabase.co.uk";//Write in the format "ftp.servername.com"	
$conn_id = ftp_connect ( $ftp_server_name );// make a connection to the ftp server
$ftp_user_name = "aureliad";
$ftp_user_pass = "l1fh65wRU2";
$fileatt = "/home/webdev/orangemedia.co.uk/var/magikbackup/aureliadb-1461826667_dbbackup.sql.gz";
$file = $fileatt;
$login_result = ftp_login ( $conn_id , $ftp_user_name , $ftp_user_pass );// login with username and password
ftp_pasv($conn_id, true);
$fp = fopen($file, 'r') or die ("couldn't open!");
$destination_file = "aureliadb-1461826667_dbbackup.sql.gz";
$source_file = $fileatt;
$upload = ftp_put ( $conn_id , $destination_file , $source_file , FTP_BINARY );				
ftp_close ( $conn_id ); 