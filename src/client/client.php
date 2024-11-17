<?php
// Client.php
$server_ip = '192.168.1.19'; 
$port = 8080; 

$client_socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
if ($client_socket === false) {
    die("Failed to create socket: " . socket_strerror(socket_last_error()) . "\n");
}

echo "Client running. Connecting to $server_ip:$port\n";

while (true) {
    echo "Enter command or access request (full_access, read_only, edit, or 'exit' to quit): ";
    $message = trim(fgets(STDIN));

    if ($message === 'exit') {
        break;
    }

    if (socket_sendto($client_socket, $message, strlen($message), 0, $server_ip, $port) === false) {
        echo "Failed to send message: " . socket_strerror(socket_last_error($client_socket)) . "\n";
        continue;
    }

    $response = '';
    $from = '';
    $port_from = 0;
    $bytes_received = socket_recvfrom($client_socket, $response, 1024, 0, $from, $port_from);

    if ($bytes_received === false) {
        echo "Error receiving response: " . socket_strerror(socket_last_error($client_socket)) . "\n";
        continue;
    }

    $response = trim($response);
    echo "Server response: $response\n";
}

socket_close($client_socket);
?>
