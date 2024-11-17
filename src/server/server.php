<?php
// Server.php
$port = 8080; 
$ip_address = '192.168.1.19'; 

$server_socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
if ($server_socket === false) {
    die("Failed to create socket: " . socket_strerror(socket_last_error()) . "\n");
}

if (socket_bind($server_socket, $ip_address, $port) === false) {
    die("Binding failed: " . socket_strerror(socket_last_error($server_socket)) . "\n");
}

echo "Server running on UDP at $ip_address:$port\n";

$permissions = []; 

while (true) {
    $buf = '';
    $from = '';
    $port_from = 0;

    $bytes_received = socket_recvfrom($server_socket, $buf, 1024, 0, $from, $port_from);
    if ($bytes_received === false) {
        echo "Error receiving message: " . socket_strerror(socket_last_error($server_socket)) . "\n";
        continue;
    }

    $buf = trim($buf);
    if ($buf) {
        echo "Request from $from:$port_from: $buf\n";

        if ($buf === 'kerko_full_access' || $buf === 'kerko_read_only' || $buf === 'kerko_edit') {
            echo "Approve access for $buf (yes/no): ";
            $approval = trim(fgets(STDIN));

            if ($approval === 'yes') {
                $permissions[$from] = $buf;
                $response = "$buf approved.";
            } else {
                $response = "Request denied.";
            }
            socket_sendto($server_socket, $response, strlen($response), 0, $from, $port_from);
        } elseif (isset($permissions[$from])) {
            $command_parts = explode(" ", $buf);
            $command = $command_parts[0];
            $file_name = isset($command_parts[1]) ? $command_parts[1] : '';

            if ($command === 'read') {
                if ($permissions[$from] === 'kerko_full_access' || $permissions[$from] === 'kerko_read_only' || $permissions[$from] === 'kerko_edit') {
                    if (file_exists($file_name)) {
                        $content = file_get_contents($file_name);
                        $response = "Content of $file_name:\n$content";
                    } else {
                        $response = "File $file_name does not exist.";
                    }
                } else {
                    $response = "You do not have permission to read files.";
                }
            } elseif ($command === 'write') {
                if ($permissions[$from] === 'kerko_full_access' || $permissions[$from] === 'kerko_edit') {
                    $new_content = implode(" ", array_slice($command_parts, 2));
                    file_put_contents($file_name, $new_content);
                    $response = "New content written to $file_name.";
                } else {
                    $response = "You do not have permission to write to files.";
                }
            } elseif ($command === 'delete') {
                if ($permissions[$from] === 'kerko_full_access') {
                    if (file_exists($file_name)) {
                        unlink($file_name);
                        $response = "$file_name deleted.";
                    } else {
                        $response = "File $file_name does not exist.";
                    }
                } else {
                    $response = "You do not have permission to delete files.";
                }
            } else {
                $response = "Unknown command.";
            }
            socket_sendto($server_socket, $response, strlen($response), 0, $from, $port_from);
        } else {
            $response = "You do not have approved access. Please request access.";
            socket_sendto($server_socket, $response, strlen($response), 0, $from, $port_from);
        }
    }
}

socket_close($server_socket);
?>
