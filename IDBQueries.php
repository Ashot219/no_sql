<?php

interface IDBQueries
{
    public function create_db($db_name);

    public function remove_db($db_name);

    public function set_db_name($name);

    public function get_db_name();

    public function create_table($query_arr);

    public function remove_table($table_name);

    public function get_table_columns($table_name);

    public function select($query_arr);

    public function insert($query_arr);

    public function update($query_arr);

    public function delete($query_arr);

    public function prepare_where($where);

    public function query_builder($query_str);

    public function r_rmdir($dir);

    public function checkMySqlSyntax($query);

    public function replaceCharacterWithinQuotes($str, $char, $repl);

    public function select_query($input_array);

    public function insert_query($input_array);

    public function update_query($input_array);

    public function delete_query($input_array);

    public function check_query_type($query);

    public function order_by($array, $field, $sort);
}