<?php
// Client.php
$server_ip = '192.168.1.19'; // IP adresa e serverit
$port = 12345; // Porti i serverit

$client_socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
if ($client_socket === false) {
    die("Failed to create socket: " . socket_strerror(socket_last_error()) . "\n");
}

echo "Client running. Connecting to $server_ip:$port\n";

while (true) {
    echo "Zgjidhni një opsion për të kërkuar qasje: full_access, read_only, edit (ose shkruani 'exit' për të dalë): ";
    $request = trim(fgets(STDIN));

    if ($request === 'exit') {
        break;
    }

    // Kërkesa për qasje
    if ($request === 'full_access' || $request === 'read_only' || $request === 'edit') {
        $request_message = 'kerko_' . $request;
        if (socket_sendto($client_socket, $request_message, strlen($request_message), 0, $server_ip, $port) === false) {
            echo "Dërgimi i kërkesës dështoi: " . socket_strerror(socket_last_error($client_socket)) . "\n";
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

        if (strpos($response, 'aprovua') !== false) {
            // Pasi të aprovohet qasja, lejo komandat
            while (true) {
                echo "Shkruani komandën (read <file>, write <file> <content>, delete <file>, open <file>, create <file> ose 'back' për t'u kthyer): ";
                $command = trim(fgets(STDIN));
                if ($command === 'back') {
                    break;
                }

                // Dërgo komandën te serveri
                if (socket_sendto($client_socket, $command, strlen($command), 0, $server_ip, $port) === false) {
                    echo "Dërgimi i komandës dështoi: " . socket_strerror(socket_last_error($client_socket)) . "\n";
                    continue;
                }

                // Lexo përgjigjen nga serveri
                $response = '';
                socket_recvfrom($client_socket, $response, 1024, 0, $from, $port_from);
                $response = trim($response);
                echo "Përgjigje nga serveri: $response\n";
            }
        }
    } else {
        echo "Opsion i pavlefshëm. Provoni përsëri.\n";
    }
}

socket_close($client_socket);
?>
