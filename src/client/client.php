<?php
$server_ip = '192.168.1.18'; 
$port = 8080;
$client_socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
socket_close($client_socket);
?>