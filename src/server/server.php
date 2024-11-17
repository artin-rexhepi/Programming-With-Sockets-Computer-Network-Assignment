<?php
// Server.php
$port = 12345; // Numër i portit të caktuar
$ip_address = '192.168.1.19'; // IP adresa e serverit (reale)

$server_socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
if ($server_socket === false) {
    die("Failed to create socket: " . socket_strerror(socket_last_error()) . "\n");
}

if (socket_bind($server_socket, $ip_address, $port) === false) {
    die("Binding failed: " . socket_strerror(socket_last_error($server_socket)) . "\n");
}

echo "Server running on UDP at $ip_address:$port\n";



$permissions = []; // Lista e aprovimeve për klientët

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
        echo "Kërkesë nga $from:$port_from: $buf\n";

        // Kontrollo llojin e kërkesës
        if ($buf === 'kerko_full_access' || $buf === 'kerko_read_only' || $buf === 'kerko_edit') {
            // Kërko aprovimin nga operatori
            echo "Shkruani aprovimin për $buf (yes/no): ";
            $approval = trim(fgets(STDIN));

            if ($approval === 'yes') {
                $permissions[$from] = $buf;
                $response = "$buf u aprovua.";
            } else {
                $response = "Kërkesa u refuzua.";
            }
            socket_sendto($server_socket, $response, strlen($response), 0, $from, $port_from);
        } elseif (isset($permissions[$from])) {
            // Kontrollo aksesin e aprovuar për këtë klient
            $access_type = $permissions[$from];

            // Përdor logjikën e komandave të bazuara në akses
            $command_parts = explode(" ", $buf);
            $command = $command_parts[0];
            $file_name = isset($command_parts[1]) ? $command_parts[1] : '';

            if (!in_array($file_name, ['file1.txt', 'file2.txt', 'file3.txt']) && $command !== 'create') {
                $response = "Skedar i panjohur.";
            } else {
                switch ($command) {
                    case 'read':
                        if ($access_type === 'kerko_full_access' || $access_type === 'kerko_read_only' || $access_type === 'kerko_edit') {
                            $content = file_get_contents($file_name);
                            $response = "Përmbajtja e $file_name:\n$content";
                        } else {
                            $response = "Nuk keni qasje për të lexuar.";
                        }
                        break;
                    case 'write':
                        if ($access_type === 'kerko_full_access' || $access_type === 'kerko_edit') {
                            $new_content = implode(" ", array_slice($command_parts, 2));
                            file_put_contents($file_name, $new_content);
                            $response = "Përmbajtja e re u ruajt në $file_name.";
                        } else {
                            $response = "Nuk keni qasje për të shkruar.";
                        }
                        break;
                    case 'delete':
                        if ($access_type === 'kerko_full_access') {
                            unlink($file_name);
                            $response = "$file_name u fshi.";
                        } else {
                            $response = "Nuk keni qasje për të fshirë skedarët.";
                        }
                        break;
                    case 'open':
                        if ($access_type === 'kerko_full_access') {
                            // Përpiquni të hapni skedarin (në varësi të sistemit operativ)
                            if (PHP_OS_FAMILY === 'Windows') {
                                exec("start " . escapeshellarg($file_name));
                            } elseif (PHP_OS_FAMILY === 'Linux') {
                                exec("xdg-open " . escapeshellarg($file_name) . " > /dev/null &");
                            } elseif (PHP_OS_FAMILY === 'Darwin') {
                                exec("open " . escapeshellarg($file_name));
                            }
                            $response = "$file_name u hap.";
                        } else {
                            $response = "Nuk keni qasje për të ekzekutuar skedarët.";
                        }
                        break;
                    case 'create':
                        if ($access_type === 'kerko_full_access') {
                            if (!file_exists($file_name)) {
                                file_put_contents($file_name, ""); // Krijon një skedar bosh
                                $response = "$file_name u krijua me sukses.";
                            } else {
                                $response = "Skedari $file_name tashmë ekziston.";
                            }
                        } else {
                            $response = "Nuk keni qasje për të krijuar skedarë.";
                        }
                        break;
                    default:
                        $response = "Komandë e panjohur.";
                        break;
                }
            }

            socket_sendto($server_socket, $response, strlen($response), 0, $from, $port_from);
        } else {
            $response = "Ju nuk keni qasje të aprovuar. Kërkoni qasje.";
            socket_sendto($server_socket, $response, strlen($response), 0, $from, $port_from);
        }
    }
}

socket_close($server_socket);
?>
