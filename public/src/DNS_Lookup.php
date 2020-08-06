<?php declare (strict_types = 1);
namespace Ctrl;

require_once 'db/SQLiteConn.php';
use \DB\SQLiteConn;

final class DNS_Lookup
{
    private $form_data;

    public function __construct($form_data)
    {
        $this->form_data = $form_data;
    }

    public function getData()
    {
        foreach (json_decode($this->form_data) as $domain) {

            if (self::isValidDomain($domain->value)) {
                $dns_data = dns_get_record($domain->value, DNS_A);
                if ($dns_data) {
                    $dns_records[] = $dns_data[0];
                    $pdo = ($db_handler = new \DB\SQLiteConn)->start();
                    $db_handler->is_new_insert($dns_data[0]);
                }
            }
        }
        // $response = $dns_records ?? ['status'=> 'error', 'msg' => 'Data Not Found'];
        $response = ($dns_records ?? false) ? ['status' => 'ok', 'data' => $dns_records] : ['status' => 'error', 'msg' => 'Data Not Found'];
        return json_encode($response);
    }

    public static function isValidDomain($domain): bool
    {
        $pattern = "/(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})/i";
        return (bool) preg_match($pattern, filter_var($domain, FILTER_SANITIZE_URL));
    }
}
