<?php


$is_ajax = 'xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '');

if($is_ajax) {
    
    // print_r(dns_get_record('google.com'));
    
    foreach (json_decode($_POST['form_data']) as $domain) {
        
        if( dns_get_record($domain->value, DNS_A) ) {
            $dns_records[] = dns_get_record($domain->value, DNS_A)[0];
        }

        // add sqlite, create db, create tb, select, insert
        // $db_tbl = \DB\SQlite\Domain_DNS();
        // $db_tbl->save($dns); # if not exist then insert
    }
    sleep(1);
    print json_encode($dns_records?? []);

} else {

    require_once 'dashboard.php';
}
