<?php
class Database
{

    private $host = 'localhost';
    private $db = 'mmbpos';
    private $user = 'root';
    private $pass = '';
    private $charset = 'utf8mb4';
    private $port = '3306';

    private $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    private $pdo;
    private $dsn;


    public function initConnection()
    {
        // Production override: create conn/config.local.php on the server (never committed to git)
        // Example: <?php return ['host'=>'sql101.infinityfree.com','db'=>'if0_42761744_mmbpos','user'=>'if0_42761744','pass'=>'...','port'=>'3306'];
        $local = __DIR__ . '/config.local.php';
        if (is_readable($local)) {
            $cfg = include $local;
            if (is_array($cfg)) {
                foreach (['host', 'db', 'user', 'pass', 'port', 'charset'] as $k) {
                    if (isset($cfg[$k]) && $cfg[$k] !== '') {
                        $this->{$k} = $cfg[$k];
                    }
                }
            }
        }

        $this->dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset};port={$this->port}";

        try {
            $this->pdo = new PDO($this->dsn, $this->user, $this->pass, $this->options);

            // Set PHP timezone (server-side)
            date_default_timezone_set('Asia/Manila');

            // Set MySQL timezone (database-side)
            $this->pdo->exec("SET time_zone = '+08:00'");

        } catch (\PDOException $e) {
            $this->failSetup($e->getMessage());
        }

        return $this->pdo;
    }

    /**
     * Connection failed. Local dev (XAMPP): show the raw error so students can debug.
     * Production: show a friendly setup page instead of a white screen.
     */
    private function failSetup(string $raw)
    {
        // Raw error is only safe to show when no production credentials are in play
        // (local XAMPP, or the server has no config.local.php yet).
        $localDev = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true);
        $configured = is_readable(__DIR__ . '/config.local.php');
        $showRaw = $localDev || !$configured;
        $detail = $showRaw ? $raw : 'Connection failed — check conn/config.local.php values on the server.';

        $ajax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
        if ($ajax) {
            http_response_code(503);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Database is not configured on this server yet.', 'detail' => $detail]);
            exit;
        }

        if ($localDev) {
            throw new \PDOException($detail, 0);
        }

        http_response_code(503);
        echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Setup required</title></head>'
            . '<body style="font-family:system-ui,sans-serif;max-width:640px;margin:80px auto;padding:0 16px;color:#1f2937;">'
            . '<h1 style="color:#b91c1c;">Database not configured</h1>'
            . '<p>This app is deployed, but it is not connected to a MySQL database yet.</p>'
            . '<ol style="line-height:1.8;">'
            . '<li>In your hosting control panel, create a MySQL database. Note the <b>host</b> (e.g. <code>sqlXXX.infinityfree.com</code>), <b>database name</b>, <b>username</b> and <b>password</b>.</li>'
            . '<li>Open <code>/setup.php</code> in your browser and fill in those values — it writes the config and imports the schema automatically.</li>'
            . '<li>When the installer says DONE, log in normally.</li>'
            . '</ol>'
            . '<pre style="background:#f3f4f6;padding:12px;border-radius:8px;font-size:13px;overflow:auto;">' . htmlspecialchars($detail) . '</pre>'
            . '</body></html>';
        exit;
    }

    public static function getConnection()
    {
        $instance = new self();
        return $instance->initConnection();
    }
}

$connect = new Database();
$db = $connect->initConnection();