<?php

require_once 'utils/helpers.php';

if (is_ajax_call()) {
    
    sleep(1);

    if (empty($_POST['form_data'])) {
        print json_encode(['msg' => 'Empty input', 'status' => 'error']);
        die();
    }
    
    require_once 'src/DNS_Lookup.php';
    print (new Ctrl\DNS_Lookup($_POST['form_data']))->getData();

} else {
    
    load_view('views/layout');
}
