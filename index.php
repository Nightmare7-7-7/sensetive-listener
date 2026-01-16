<?php
// Simple listener
$log = date('Y-m-d H:i:s') . ' | ' . 
       ($_SERVER['HTTP_USER_AGENT'] ?? 'No UA') . ' | ' . 
       json_encode($_GET) . "\n";
       
file_put_contents('log.txt', $log, FILE_APPEND);

// Show simple page
?>
<!DOCTYPE html>
<html>
<head>
    <title>Listener</title>
    <meta http-equiv="refresh" content="5">
</head>
<body>
    <h1>Listener Active</h1>
    <p>URL: https://nightmare7-7-7.github.io/sensetive-listener/</p>
    
    <h2>Recent logs:</h2>
    <pre><?php 
        if(file_exists('log.txt')) {
            echo htmlspecialchars(file_get_contents('log.txt')); 
        } else {
            echo "No requests yet";
        }
    ?></pre>
</body>
</html>
