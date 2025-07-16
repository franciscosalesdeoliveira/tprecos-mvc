<?php

namespace Source\Models;

use PDO;
use PDOException;

class Connect
{
    private static $instance = null;
    private $pdo;

    // Configurações do banco de dados
    private const DB_CONFIG = [
        'driver' => 'mysql', // mysql ou sqlite
        'host' => 'localhost',
        'port' => '3306',
        'database' => 'nome_do_banco',
        'username' => 'usuario',
        'password' => 'senha',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'sqlite_path' => __DIR__ . '/../../database/database.sqlite'
    ];

    // Opções do PDO
    private const PDO_OPTIONS = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_PERSISTENT => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'
    ];

    /**
     * Construtor privado para implementar Singleton
     */
    private function __construct()
    {
        try {
            $this->pdo = $this->createConnection();
        } catch (PDOException $e) {
            $this->handleConnectionError($e);
        }
    }

    /**
     * Previne clonagem da instância
     */
    private function __clone() {}

    /**
     * Previne deserialização da instância
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }

    /**
     * Retorna a instância única da conexão
     *
     * @return PDO
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance->pdo;
    }

    /**
     * Cria a conexão com o banco de dados
     *
     * @return PDO
     * @throws PDOException
     */
    private function createConnection(): PDO
    {
        $driver = self::DB_CONFIG['driver'];

        if ($driver === 'sqlite') {
            return $this->createSQLiteConnection();
        } else {
            return $this->createMySQLConnection();
        }
    }

    /**
     * Cria conexão MySQL
     *
     * @return PDO
     * @throws PDOException
     */
    private function createMySQLConnection(): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            self::DB_CONFIG['host'],
            self::DB_CONFIG['port'],
            self::DB_CONFIG['database'],
            self::DB_CONFIG['charset']
        );

        $options = self::PDO_OPTIONS;

        return new PDO(
            $dsn,
            self::DB_CONFIG['username'],
            self::DB_CONFIG['password'],
            $options
        );
    }

    /**
     * Cria conexão SQLite
     *
     * @return PDO
     * @throws PDOException
     */
    private function createSQLiteConnection(): PDO
    {
        $dsn = 'sqlite:' . self::DB_CONFIG['sqlite_path'];

        $options = array_filter(self::PDO_OPTIONS, function ($key) {
            return $key !== PDO::MYSQL_ATTR_INIT_COMMAND;
        }, ARRAY_FILTER_USE_KEY);

        $pdo = new PDO($dsn, null, null, $options);

        // Configurações específicas do SQLite
        $pdo->exec('PRAGMA foreign_keys = ON');
        $pdo->exec('PRAGMA journal_mode = WAL');

        return $pdo;
    }

    /**
     * Testa a conexão com o banco de dados
     *
     * @return bool
     */
    public static function testConnection(): bool
    {
        try {
            $pdo = self::getInstance();
            $pdo->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Retorna informações sobre a conexão
     *
     * @return array
     */
    public static function getConnectionInfo(): array
    {
        try {
            $pdo = self::getInstance();
            return [
                'driver' => $pdo->getAttribute(PDO::ATTR_DRIVER_NAME),
                'server_version' => $pdo->getAttribute(PDO::ATTR_SERVER_VERSION),
                'client_version' => $pdo->getAttribute(PDO::ATTR_CLIENT_VERSION),
                'connection_status' => $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS),
                'server_info' => $pdo->getAttribute(PDO::ATTR_SERVER_INFO) ?? 'N/A'
            ];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Executa uma query e retorna o resultado
     *
     * @param string $sql
     * @param array $params
     * @return array
     * @throws PDOException
     */
    public static function query(string $sql, array $params = []): array
    {
        $pdo = self::getInstance();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Executa uma query e retorna apenas o primeiro resultado
     *
     * @param string $sql
     * @param array $params
     * @return array|false
     * @throws PDOException
     */
    public static function queryFirst(string $sql, array $params = [])
    {
        $pdo = self::getInstance();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    /**
     * Executa uma query de inserção/atualização/exclusão
     *
     * @param string $sql
     * @param array $params
     * @return int Número de linhas afetadas
     * @throws PDOException
     */
    public static function execute(string $sql, array $params = []): int
    {
        $pdo = self::getInstance();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Retorna o último ID inserido
     *
     * @return string
     */
    public static function lastInsertId(): string
    {
        return self::getInstance()->lastInsertId();
    }

    /**
     * Inicia uma transação
     *
     * @return bool
     */
    public static function beginTransaction(): bool
    {
        return self::getInstance()->beginTransaction();
    }

    /**
     * Confirma uma transação
     *
     * @return bool
     */
    public static function commit(): bool
    {
        return self::getInstance()->commit();
    }

    /**
     * Desfaz uma transação
     *
     * @return bool
     */
    public static function rollback(): bool
    {
        return self::getInstance()->rollback();
    }

    /**
     * Verifica se está em uma transação
     *
     * @return bool
     */
    public static function inTransaction(): bool
    {
        return self::getInstance()->inTransaction();
    }

    /**
     * Trata erros de conexão
     *
     * @param PDOException $e
     * @throws PDOException
     */
    private function handleConnectionError(PDOException $e): void
    {
        $isDev = $this->isDevelopmentEnvironment();

        if ($isDev) {
            error_log("Erro de conexão com banco de dados: " . $e->getMessage());
            throw new PDOException("Erro de conexão: " . $e->getMessage(), (int)$e->getCode());
        } else {
            error_log("Erro de conexão com banco de dados: " . $e->getMessage());
            throw new PDOException("Erro interno do sistema. Tente novamente mais tarde.", 500);
        }
    }

    /**
     * Verifica se está em ambiente de desenvolvimento
     *
     * @return bool
     */
    private function isDevelopmentEnvironment(): bool
    {
        return defined('ENVIRONMENT') && ENVIRONMENT === 'development';
    }

    /**
     * Método para configurar as credenciais do banco dinamicamente
     * (útil para testes ou configurações específicas)
     *
     * @param array $config
     */
    public static function setConfig(array $config): void
    {
        // Reset da instância para forçar nova conexão
        self::$instance = null;

        // Atualizar configurações (seria melhor usar um sistema de configuração mais robusto)
        foreach ($config as $key => $value) {
            if (array_key_exists($key, self::DB_CONFIG)) {
                self::DB_CONFIG[$key] = $value;
            }
        }
    }
}
