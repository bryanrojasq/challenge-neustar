<?php declare (strict_types = 1);
use PHPUnit\Framework\TestCase;

final class DBTest extends TestCase
{
    public function testConnectionSucceed()
    {
        $pdo = (new DB\SQLiteConn())->start();
        $this->assertTrue(!is_null($pdo));
    }

    public function testDmlInsertData()
    {
        $input = [
            'title' => "TITLE FOR TEST",
            'message' => "Buy me a coffee. ;3",
            'price' => 18,
        ];

        ($db_handler = new DB\SQLiteConn())->start();
        $res = $db_handler->insert($input);
        $this->assertTrue($res);
    }

    public function testDqlGetData()
    {
        $pdo = (new DB\SQLiteConn())->start();
        $result = $pdo->query("SELECT * FROM test_logs order by id DESC LIMIT 1");
        foreach ($result as $log) {
            $this->assertEquals("TITLE FOR TEST", $log['title']);
        }
    }

    public function testDupRecord()
    {
        $input = [
            'title' => "TITLE FOR TEST",
            'message' => "Buy me a coffee. ;3",
            'price' => 18,
        ];
        ($db_handler = new DB\SQLiteConn())->start();
        $this->assertTrue($db_handler->is_dup($input));
    }
}
