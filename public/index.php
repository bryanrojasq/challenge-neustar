<?php

// require 'vendor/autoload.php';

require_once 'utils/helpers.php';
require_once 'db/SQLiteConn.php';

if (is_ajax_call()) {
    
    // sleep(1);

    if (empty($_POST['form_data'])) {
        print json_encode(['msg' => 'Empty input', 'status' => 'error']);
        die();
    }
    
    require_once 'src/DNS_Lookup.php';
    print (new Ctrl\DNS_Lookup($_POST['form_data']))->getData();

} else {
    
    load_view('views/layout');
}

if(isset($_GET['show_latest']) && $_GET['show_latest'] == true) {

    $pdo = ($db_handler = new \DB\SQLiteConn)->start();
    $res = $db_handler->all();
    
    if($res) {
        $render ='';
        foreach ($res as $log) {
            $render .= "<tr><td>{$log['id']}</td><td>{$log['title']} </td><td>IP: {$log['price']} </td><td>Created: {$log['created']}</td></tr>";
        }
    }
    print sprintf("<table class='tbl'><tr><th colspan='4'>LATEST RECORDS</th></tr>%s</table>", $render?? '');
}
