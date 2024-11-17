<?php
// Server.php
$port = 8080; 
$ip_address = '192.168.1.18';
$server_socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
socket_bind($server_socket, $ip_address, $port);

$permissions = []; // Lista e aprovimeve për klientët

while (true) {
    $buf = '';
    $from = '';
    $port_from = 0;
    socket_recvfrom($server_socket, $buf, 1024, 0, $from, $port_from);

    if ($buf === 'kerko_full_access' || $buf === 'kerko_read_only' || $buf === 'kerko_edit') {
        echo "Shkruani aprovimin për $buf (yes/no): ";
        $approval = trim(fgets(STDIN));
        if ($approval === 'yes') {
            $permissions[$from] = $buf;
            $response = "$buf u aprovua.";
        } else {
            $response = "Kërkesa u refuzua.";
        }
        socket_sendto($server_socket, $response, strlen($response), 0, $from, $port_from);
    }
}
socket_close($server_socket);
?>