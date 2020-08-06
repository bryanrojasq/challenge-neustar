<?php

function is_ajax_call() {
    return 'xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '');
}

function load_view($path) {
    require_once  "{$path}.php";
}
