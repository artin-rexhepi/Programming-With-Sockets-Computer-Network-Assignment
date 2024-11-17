<?php
// Server.php
$port = 8080; 
$ip_address = '192.168.1.18';
$server_socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
if ($server_socket === false) {
    die("Nuk u krijua socket: " . socket_strerror(socket_last_error()) . "\n");
}

if (socket_bind($server_socket, $ip_address, $port) === false) {
    die("Lidhja dështoi: " . socket_strerror(socket_last_error($server_socket)) . "\n");
}
socket_close($server_socket);
?>