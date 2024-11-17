<?php
// Server.php
$port = 12345; // Port number
$ip_address = '192.168.1.19'; // Server's IP address

$server_socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
if ($server_socket === false) {
    die("Failed to create socket: " . socket_strerror(socket_last_error()) . "\n");
}

if (socket_bind($server_socket, $ip_address, $port) === false) {
    die("Binding failed: " . socket_strerror(socket_last_error($server_socket)) . "\n");
}

echo "Server running on UDP at $ip_address:$port\n";

$permissions = []; // Store permissions for clients

while (true) {
    $buf = '';
    $from = '';
    $port_from = 0;

    // Receive data from the client
    $bytes_received = socket_recvfrom($server_socket, $buf, 1024, 0, $from, $port_from);
    if ($bytes_received === false) {
        echo "Error receiving message: " . socket_strerror(socket_last_error($server_socket)) . "\n";
        continue;
    }

    $buf = trim($buf);
    if ($buf) {
        echo "Request from $from:$port_from: $buf\n";

        // Handle password validation
        if ($buf === 'admin2024') {
            $permissions[$from] = 'full_access';
            $response = "Access granted: Full Access";
        } elseif ($buf === 'editor2024') {
            $permissions[$from] = 'edit_access';
            $response = "Access granted: Edit Access";
        } elseif ($buf === 'guest') {
            $permissions[$from] = 'read_only';
            $response = "Access granted: Read Only";
        } elseif (isset($permissions[$from])) {
            // Handle commands based on permissions
            $access_type = $permissions[$from];
            $command_parts = explode(" ", $buf);
            $command = $command_parts[0];
            $file_name = $command_parts[1] ?? '';

            if ($command === 'list') {
                $files = array_diff(scandir(getcwd()), ['.', '..']);
                $response = "Files:\n" . implode("\n", $files);
            } elseif ($command === 'read') {
                if (file_exists($file_name)) {
                    $content = file_get_contents($file_name);
                    $response = "Content of $file_name:\n$content";
                } else {
                    $response = "File $file_name does not exist.";
                }
            } elseif ($command === 'open') {
                if (file_exists($file_name)) {
                    if ($access_type === 'full_access' || $access_type === 'edit_access' || $access_type === 'read_only') {
                        if (PHP_OS_FAMILY === 'Windows') {
                            exec("notepad " . escapeshellarg($file_name));
                        } elseif (PHP_OS_FAMILY === 'Linux') {
                            exec("xdg-open " . escapeshellarg($file_name) . " > /dev/null &");
                        } elseif (PHP_OS_FAMILY === 'Darwin') {
                            exec("open " . escapeshellarg($file_name));
                        }
                        $response = "File $file_name opened.";
                    } else {
                        $response = "You do not have permission to open files.";
                    }
                } else {
                    $response = "File $file_name does not exist.";
                }
            } elseif ($command === 'write' && in_array($access_type, ['full_access', 'edit_access'])) {
                $new_content = implode(" ", array_slice($command_parts, 2));
                file_put_contents($file_name, $new_content);
                $response = "Content written to $file_name.";
            } elseif ($command === 'delete' && $access_type === 'full_access') {
                if (file_exists($file_name)) {
                    unlink($file_name);
                    $response = "File $file_name deleted.";
                } else {
                    $response = "File $file_name does not exist.";
                }
            } elseif ($command === 'create' && $access_type === 'full_access') {
                if (!file_exists($file_name)) {
                    file_put_contents($file_name, "");
                    $response = "File $file_name created.";
                } else {
                    $response = "File $file_name already exists.";
                }
            } else {
                $response = "Command not allowed or unknown.";
            }
        } else {
            $response = "Invalid password or access request.";
        }

        socket_sendto($server_socket, $response, strlen($response), 0, $from, $port_from);
    }
}

socket_close($server_socket);
?>
