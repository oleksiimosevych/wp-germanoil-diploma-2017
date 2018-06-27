<?php
global  $ccsve_export_check,
        //$custom_query_check,
        $export_only;

function ccsve_export(){
    global  $ccsve_export_check,
            $export_only;

    $ccsve_export_check = isset($_REQUEST['export']) ? $_REQUEST['export'] : '';
    $export_only = isset($_REQUEST['only']) ? $_REQUEST['only'] : '';
    //$custom_query_check = isset($_REQUEST['custom_query']) ;

    //if ($custom_query_check == false) {
        require_once(SIMPLE_CSV_XLS_EXPORTER_PROCESS."simple_csv_xls_exporter_csv_xls.php");
        simple_csv_xls_exporter_csv_xls();
    /*} elseif ($custom_query_check == true) {
        require_once(SIMPLE_CSV_XLS_EXPORTER_PROCESS."simple_csv_xls_exporter_custom_csv_xls.php");
        simple_csv_xls_exporter_custom_csv_xls();
    }*/
    exit;
}
