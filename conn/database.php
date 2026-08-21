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
        $this->dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset;port=$this->port";

        try {
            $this->pdo = new PDO($this->dsn, $this->user, $this->pass, $this->options);

            // ✅ Set PHP timezone (server-side)
            date_default_timezone_set('Asia/Manila');

            // ✅ Set MySQL timezone (database-side)
            $this->pdo->exec("SET time_zone = '+08:00'");

        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }

        return $this->pdo;
    }

    public static function getConnection()
    {
        $instance = new self();
        return $instance->initConnection();
    }
}

$connect = new Database();
$db = $connect->initConnection();