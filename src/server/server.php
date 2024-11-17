<?php
// Server.php
$port = 8080; 
$ip_address = '192.168.1.19';
$server_socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
socket_close($server_socket);
?>