<?php

$port = 8080; 
$ip_address = '192.168.1.8';

$server_socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
if ($server_socket === false) {
    die("Nuk u krijua socket: " . socket_strerror(socket_last_error()) . "\n");
}

// Lidhja e socket me IP adresën dhe portin
if (socket_bind($server_socket, $ip_address, $port) === false) {
    die("Lidhja dështoi: " . socket_strerror(socket_last_error($server_socket)) . "\n");
}

echo "Serveri është duke përdorur protokollin UDP dhe është duke dëgjuar në $ip_address:$port...\n";

while (true) {
    $buf = '';
    $from = '';
    $port_from = 0;
    $bytes_received = socket_recvfrom($server_socket, $buf, 1024, 0, $from, $port_from);
    
    if ($bytes_received === false) {
        echo "Gabim në pranimin e mesazhit: " . socket_strerror(socket_last_error($server_socket)) . "\n";
        continue;
    }

    $buf = trim($buf);
    if ($buf) {
        echo "Mesazh i pranuar nga $from:$port_from: $buf\n";
        $response = "Mesazhi juaj u pranua: $buf";
        if (socket_sendto($server_socket, $response, strlen($response), 0, $from, $port_from) === false) {
            echo "Gabim në dërgimin e përgjigjes: " . socket_strerror(socket_last_error($server_socket)) . "\n";
        }
    }
}

socket_close($server_socket);
?>
