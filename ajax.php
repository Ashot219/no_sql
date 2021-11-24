<?php
require_once 'DbQueries.php';

if (isset($_POST['action']) && $_POST['action'] == 'get_table_columns'){
    $db_name = $_POST['db'];
    $table = $_POST['table'];
    $db = new DbQueries($db_name);
    $table_columns = $db->get_table_columns($table);

    $result = '';
    foreach ($table_columns as $column){
        $result .= "<option>$column</option>";
    }

    echo $result;
}
?>