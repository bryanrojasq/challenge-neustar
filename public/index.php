<?php

require_once 'utils/helpers.php';

if (is_ajax_call()) {
    
    // print_r(dns_get_record('google.com'));

    if (empty($_POST['form_data'])) {
        print json_encode(['msg' => 'Empty input', 'status' => 'error']);
        die();
    }

    sleep(1);
    
    require_once 'src/DNS_Lookup.php';
    print (new Ctrl\DNS_Lookup($_POST['form_data']))->getData();

} else {

    // require_once 'views/layout.php';
    load_view('views/layout');
}
