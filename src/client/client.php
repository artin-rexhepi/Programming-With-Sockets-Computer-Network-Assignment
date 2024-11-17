<?php 
$server_ip = '192.168.1.8'; 
$port = 8080; 
$client_socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
 echo "Shkruani mesazhin për serverin: "; $message = trim(fgets(STDIN)); 
 socket_sendto($client_socket, $message, strlen($message), 0, $server_ip, $port); 
 socket_close($client_socket); 
 ?>