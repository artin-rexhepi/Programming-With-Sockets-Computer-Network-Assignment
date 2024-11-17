<?php 
 $port = 8080;
 $ip_address = '192.168.1.8';
  $server_socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP); 
  socket_bind($server_socket, $ip_address, $port); 
  while (true)
   {
     $buf = ''; $from = ''; 
     $port_from = 0; 
     socket_recvfrom($server_socket, $buf, 1024, 0, $from, $port_from); 
     echo "Mesazh i pranuar nga $from:$port_from: $buf\n";
   }
    socket_close($server_socket);
 ?>
