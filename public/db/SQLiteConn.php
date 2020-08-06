<?php declare (strict_types = 1);
namespace DB;

// if( ! defined( 'ABSPATH' ) ) exit;

/**
 * SQLite connnection
 */
class SQLiteConn
{
    /**
     * PDO instance
     * @var type
     */
    const PATH_TO_SQLITE_FILE = 'test.db';
    private $pdo;
    public $errMsg;

    /**
     * return in instance of the PDO object that connects to the SQLite database
     * @return \PDO
     */
    public function start()
    {
        if ($this->pdo == null) {
            try {
                $this->pdo = new \PDO("sqlite:" . self::PATH_TO_SQLITE_FILE);
            } catch (\PDOException $e) {
                // handle the exception here
                $this->errMsg = $e;
            }
        }
        return $this->pdo;
    }

    public function runMigration()
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS test_logs (
        id INTEGER PRIMARY KEY,
        title TEXT,
        message TEXT,
        price NUMERIC,
        index_data TEXT,
        created DEFAULT CURRENT_TIMESTAMP);");
    }

    public function dropTable()
    {
        $this->pdo->exec("DROP TABLE test_logs;");
    }

    public function insert($data)
    {
        $index = \serialize($data);
        $stmt = $this->pdo->prepare("INSERT INTO test_logs (title, message, price, index_data) VALUES (:title, :message, :price, :index)");
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':message', $data['message']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':index', $index);
        return $stmt->execute();
    }

    public function find($input)
    {
        return $this->pdo->query(sprintf("SELECT * FROM test_logs WHERE index_data LIKE '%%%s%%'", \serialize($input)));
    }

    public function is_dup($input)
    {
        return is_array($this->find($input)->fetch());
    }

    public function is_new_insert($input)
    {
        $item = [
            'title' => $input['host'],
            'message' => $input['type'],
            'price' => $input['ip'],
            'ttl' => $input['ttl'],
        ];
        if ($this->is_dup($item) == false) {
            return $this->insert($item);
        }
    }

    public function all()
    {
        return $this->pdo->query("SELECT * FROM test_logs order by id DESC;");
    }
}
