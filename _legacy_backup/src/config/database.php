<?php
// Load .env file automatically (useful when running locally without Docker)
$envFile = dirname(__DIR__, 2) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $val) = explode('=', $line, 2);
            $key = trim($key);
            $val = trim($val, " \t\n\r\0\x0B\"'");
            if (getenv($key) === false) {
                putenv("$key=$val");
                $_ENV[$key] = $val;
            }
        }
    }
}

// Retrieve environment variables
$url = getenv('DB_URL'); 
$host_env = getenv('DB_HOST');

// If the user accidentally pasted the entire URL into DB_HOST, let's parse it for them!
if ($host_env && strpos($host_env, 'mysql://') === 0) {
    $url = $host_env;
}

if ($url) {
    $dbparts = parse_url($url);
    $servername = $dbparts['host'];
    $username = $dbparts['user'];
    $password = $dbparts['pass'];
    $dbname = ltrim($dbparts['path'], '/');
    $port = $dbparts['port'] ?? 3306;
} else {
    $servername = $host_env ?: "127.0.0.1";
    $username   = getenv('DB_USER') ?: "root";       
    $password   = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : "";          
    $dbname     = getenv('DB_NAME') ?: "attapp_db";
    $port       = getenv('DB_PORT') ?: 3306;
}

$conn = mysqli_init();

// If we are connecting to a remote cloud server like TiDB Cloud or Aiven, configure SSL.
if ($servername !== "127.0.0.1" && $servername !== "db" && $servername !== "localhost") {
    $ca = getenv('DB_SSL_CA') ?: null;
    if (!$ca) {
        if (file_exists('/etc/ssl/certs/ca-certificates.crt')) {
            $ca = '/etc/ssl/certs/ca-certificates.crt';
        } elseif (file_exists('/etc/ssl/cert.pem')) {
            $ca = '/etc/ssl/cert.pem';
        }
    }
    if ($ca) {
        mysqli_ssl_set($conn, NULL, NULL, $ca, NULL, NULL);
    } else {
        mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);
    }
    $conn->real_connect($servername, $username, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL);
} else {
    $conn->real_connect($servername, $username, $password, $dbname, $port);
}

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
