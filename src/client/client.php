
<?php

$server_ip = '192.168.1.8'; 
$port = 8080; 

$client_socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
if ($client_socket === false) {
    die("Nuk u krijua socket: " . socket_strerror(socket_last_error()) . "\n");
}

echo "Klienti është duke përdorur protokollin UDP për t'u lidhur me serverin në $server_ip:$port\n";

while (true) {
    
    echo "Shkruani mesazhin për serverin: ";
    $message = trim(fgets(STDIN));

    if ($message === "exit") {
        break;
    }

    // Dërgimi i mesazhit tek serveri
    if (socket_sendto($client_socket, $message, strlen($message), 0, $server_ip, $port) === false) {
        echo "Dërgimi i mesazhit dështoi: " . socket_strerror(socket_last_error($client_socket)) . "\n";
        continue;
    }

    // Leximi i përgjigjes nga serveri
    $response = '';
    $from = '';
    $port_from = 0;
    $bytes_received = socket_recvfrom($client_socket, $response, 1024, 0, $from, $port_from);

    if ($bytes_received === false) {
        echo "Gabim në marrjen e përgjigjes: " . socket_strerror(socket_last_error($client_socket)) . "\n";
        continue;
    }

    $response = trim($response);
    echo "Përgjigje nga serveri: $response\n";
}

socket_close($client_socket);
?>
