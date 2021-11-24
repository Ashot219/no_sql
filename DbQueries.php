<?php

require_once 'IDBQueries.php';

class DbQueries implements IDBQueries
{

    private $db_name;
    private $operators_array = array('<', '=', '>');
    private $prefix = 'databases/';

    public function __construct($db_name)
    {
        $this->set_db_name($db_name);
    }

    public function create_db($db_name)
    {
        try {
            if (!is_dir($db_name)) {
                mkdir($db_name, 0777);
            }
            $this->set_db_name($db_name);
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function remove_db($db_name)
    {
        try {
            if (is_dir($this->prefix . $db_name)) {
                $this->r_rmdir($this->prefix . $db_name);
            }
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function set_db_name($name)
    {
        try {
            $this->db_name = $this->prefix . $name;
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function get_db_name()
    {
        try {
            return str_replace($this->prefix, '', $this->db_name);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function create_table($query_arr)
    {
        try {

            $table_name = $query_arr['table_name'];
            $columns = str_replace(' ', '', $query_arr['columns']);
            $my_table = fopen($this->db_name . DIRECTORY_SEPARATOR . $table_name, "w");
            $columns_json = json_encode(['columns' => $columns, 'data' => []]);
            fwrite($my_table, $columns_json);
            fclose($my_table);
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function remove_table($table_name)
    {
        try {
            unlink($this->db_name . DIRECTORY_SEPARATOR . $table_name);
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function get_table_columns($table_name)
    {
        $result = array();
        if (is_file($this->db_name . DIRECTORY_SEPARATOR . $table_name)) {
            $select_result = file_get_contents($this->db_name . DIRECTORY_SEPARATOR . $table_name);
            if ($select_result) {
                $result_arr = json_decode($select_result, true);
                $result = explode(',', $result_arr['columns']);
            }
        }
        return $result;
    }

    public function select($query_arr)
    {
        try {
            $result = [];
            $table_name = $query_arr['table_name'];
            $select = $query_arr['select'];
            $where = $query_arr['where'];
            $order_by_field = $query_arr['order_by_field'];
            $order_by = $query_arr['order_by'];

            $select_result = file_get_contents($this->db_name . DIRECTORY_SEPARATOR . $table_name);
            if ($select_result) {
                $result_arr = json_decode($select_result, true);
                $result = $result_arr['data'];
                if ($result) {
                    if ($select != '*') {
                        $select = str_replace(' ', '', $select);
                        $select_values = explode(',', $select);
                        $deletable_keys = array();
                        foreach ($result[0] as $key => $value) {
                            if (!in_array($key, $select_values)) {
                                $deletable_keys[] = $key;
                            }
                        }
                    }

                    if (!empty($where)) {

                        $where_array = $this->prepare_where($where);

                        if ($where_array) {
                            $count_matches = count($where_array);
                            foreach ($result as $key => $value) {
                                $matches = 0;
                                foreach ($value as $arr_key => $item) {
                                    $item = trim($item);
                                    if (array_key_exists($arr_key, $where_array)) {
                                        switch ($where_array[$arr_key]['operator']) {
                                            case "=":
                                                if ($item == $where_array[$arr_key]['value']) {
                                                    $matches++;

                                                }
                                                break;
                                            case ">":
                                                if ($item > $where_array[$arr_key]['value']) {
                                                    $matches++;
                                                }
                                                break;
                                            case "<":
                                                if ($item < $where_array[$arr_key]['value']) {
                                                    $matches++;
                                                }
                                                break;
                                        }

                                    }
                                }

                                if ($matches != $count_matches) {
                                    unset($result[$key]);
                                }
                            }
                        }
                    }

                    if ($result && $select != '*') {
                        if (!empty($deletable_keys)) {
                            foreach ($result as $key => $value) {
                                foreach ($value as $arr_key => $item) {
                                    if (in_array($arr_key, $deletable_keys)) {
                                        unset($result[$key][$arr_key]);
                                    }
                                }
                            }
                        }
                    }

                    if ($order_by_field && $order_by && $result) {
                        $result = $this->order_by($result, $order_by_field, $order_by);
                    }

                }
            }


            return $result;

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function insert($query_arr)
    {
        try {
            $table_name = $query_arr['table_name'];
            $values = $query_arr['values'];
            if ($values) {
                if (is_file($this->db_name . DIRECTORY_SEPARATOR . $table_name)) {
                    $table_content = file_get_contents($this->db_name . DIRECTORY_SEPARATOR . $table_name);
                    if ($table_content) {
                        $table_info = json_decode($table_content, true);
                        $table_columns = explode(',', $table_info['columns']);
                        $ins_array = array();
                        foreach ($table_columns as $key => $column_name) {
                            if (array_key_exists($key, $values)) {
                                $ins_array[$column_name] = $values[$key];
                            } else {
                                $ins_array[$column_name] = '';
                            }
                        }

                        $table_info['data'][] = $ins_array;
                        file_put_contents($this->db_name . DIRECTORY_SEPARATOR . $table_name, json_encode($table_info));
                        $error_message = 'Successfully inserted';
                    }
                } else {
                    $error_message = 'The table `' . $table_name . '` for db `' . $this->db_name . '` not exist ';
                }
            } else {
                $error_message = 'Passed wrong parameters. Please check!';
            }
            return $error_message;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function update($query_arr)
    {
        try {
            $message = 'Successfully updated';
            $table_name = $query_arr['table_name'];
            $values = $query_arr['values'];
            $where = $query_arr['where'];
            $count_updates = 0;
            $select_result = file_get_contents($this->db_name . DIRECTORY_SEPARATOR . $table_name);
            if ($select_result && $where && $values) {
                $result_arr = json_decode($select_result, true);
                $result = $result_arr['data'];
                if ($result) {

                    if (!empty($where)) {

                        $where_array = $this->prepare_where($where);
                        $update_array = $this->prepare_where($values);


                        if ($where_array) {
                            $count_matches = count($where_array);
                            foreach ($result as $key => $value) {
                                $matches = 0;
                                foreach ($value as $arr_key => $item) {
                                    $item = trim($item);
                                    if (array_key_exists($arr_key, $where_array)) {
                                        switch ($where_array[$arr_key]['operator']) {
                                            case "=":
                                                if ($item == $where_array[$arr_key]['value']) {
                                                    $matches++;

                                                }
                                                break;
                                            case ">":
                                                if ($item > $where_array[$arr_key]['value']) {
                                                    $matches++;
                                                }
                                                break;
                                            case "<":
                                                if ($item < $where_array[$arr_key]['value']) {
                                                    $matches++;
                                                }
                                                break;
                                        }
                                    }
                                }

                                if ($matches == $count_matches) {
                                    $count_updates = 1;
                                    foreach ($update_array as $upd_key => $upd_value) {
                                        $result[$key][$upd_key] = $upd_value['value'];
                                    }
                                }
                            }
                        }
                    }
                    $result_arr['data'] = $result;
                    file_put_contents($this->db_name . DIRECTORY_SEPARATOR . $table_name, json_encode($result_arr));
                } else {
                    $message = 'No result for update!';
                }

                if (!$count_updates) {
                    $message = 'No result for update!';
                }
            } else {
                $message = 'No result for your parameters!';
            }
            return $message;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function delete($query_arr)
    {
        try {
            $table_name = $query_arr['table_name'];
            $where = $query_arr['where'];

            $select_result = file_get_contents($this->db_name . DIRECTORY_SEPARATOR . $table_name);
            if ($select_result) {
                $result_arr = json_decode($select_result, true);
                $result = $result_arr['data'];

                $where_array = $this->prepare_where($where);

                if ($where_array) {
                    $count_matches = count($where_array);
                    foreach ($result as $key => $value) {
                        $matches = 0;
                        foreach ($value as $arr_key => $item) {
                            $item = trim($item);
                            if (array_key_exists($arr_key, $where_array)) {
                                switch ($where_array[$arr_key]['operator']) {
                                    case "=":
                                        if ($item == $where_array[$arr_key]['value']) {
                                            $matches++;

                                        }
                                        break;
                                    case ">":
                                        if ($item > $where_array[$arr_key]['value']) {
                                            $matches++;
                                        }
                                        break;
                                    case "<":
                                        if ($item < $where_array[$arr_key]['value']) {
                                            $matches++;
                                        }
                                        break;
                                }

                            }
                        }

                        if ($matches == $count_matches) {
                            unset($result[$key]);
                        }
                    }
                }

                $result_arr['data'] = $result;
                file_put_contents($this->db_name . DIRECTORY_SEPARATOR . $table_name, json_encode($result_arr));
                $message = 'Successfully deleted';
            } else {
                $message = 'The table `' . $table_name . '` for db `' . $this->db_name . '` not exist ';
            }
            return $message;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function prepare_where($where)
    {
        try {
            $where_array = array();
            if (!empty($where)) {
                $where_exp = explode(' and ', $where);

                foreach ($where_exp as $value) {
                    $value_exp = explode(' ', $value);
                    if (count($value_exp) > 2 && in_array($value_exp[1], $this->operators_array)) {

                        $where_arr = array(
                            'operator' => $value_exp[1],
                            'value' => $value_exp[2],
                        );
                        $where_array[$value_exp[0]] = $where_arr;
                    }
                }
            }
            return $where_array;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function query_builder($query_str)
    {
        try {
            $ret_val = array();
            $query_arr = $this->checkMySqlSyntax($query_str);

            foreach ($query_arr as $query) {

                $ret_val[] = $this->check_query_type($query);
            }
            return $ret_val;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function r_rmdir($dir)
    {
        try {
            if (is_dir($dir)) {
                $objects = scandir($dir);
                foreach ($objects as $object) {
                    if ($object != "." && $object != "..") {
                        if (is_dir($dir . DIRECTORY_SEPARATOR . $object) && !is_link($dir . "/" . $object))
                            $this->r_rmdir($dir . DIRECTORY_SEPARATOR . $object);
                        else
                            unlink($dir . DIRECTORY_SEPARATOR . $object);
                    }
                }
                rmdir($dir);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function checkMySqlSyntax($query)
    {
        try {
            if (trim($query)) {
                // Replace characters within string literals that may *** up the process
                $query = $this->replaceCharacterWithinQuotes($query, '#', '%');
                $query = $this->replaceCharacterWithinQuotes($query, ';', ':');
                $query = " " .
                    preg_replace(array("/#[^\n\r;]*([\n\r;]|$)/",
                        "/[Ss][Ee][Tt]\s+\@[A-Za-z0-9_]+\s*:?=\s*[^;]+(;|$)/",
                        "/;\s*;/",
                        "/;\s*$/",
                        "/;/"),
                        array("", "", ";", "", "; "), $query);

                $query_arr = array();
                foreach (explode(';', $query) as $q) {
                    $query_arr[] = trim($q) . ';';
                }

                return $query_arr;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function replaceCharacterWithinQuotes($str, $char, $repl)
    {
        try {
            if (strpos($str, $char) === false) return $str;

            $placeholder = chr(7);
            $inSingleQuote = false;
            $inDoubleQuotes = false;
            for ($p = 0; $p < strlen($str); $p++) {
                switch ($str[$p]) {
                    case "'":
                        if (!$inDoubleQuotes) $inSingleQuote = !$inSingleQuote;
                        break;
                    case '"':
                        if (!$inSingleQuote) $inDoubleQuotes = !$inDoubleQuotes;
                        break;
                    case '\\':
                        $p++;
                        break;
                    case $char:
                        if ($inSingleQuote || $inDoubleQuotes) $str[$p] = $placeholder;
                        break;
                }
            }
            return str_replace($placeholder, $repl, $str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function select_query($input_array)
    {
        try {
            $query_array = array(
                'select' => $input_array[0][1],
                'table_name' => $input_array[0][2],
                'where' => '',
                'order_by_field' => '',
                'order_by' => '',
            );
            if (isset($input_array[0])) {
                if (count($input_array[0]) == 4 || count($input_array[0]) == 6) {
                    $query_array['where'] = $input_array[0][3];
                }
                if (count($input_array[0]) == 5 || count($input_array[0]) == 6) {
                    $query_array['order_by_field'] = $input_array[0][count($input_array[0]) - 2];
                    $query_array['order_by'] = $input_array[0][count($input_array[0]) - 1];
                }
            }

            return $this->select($query_array);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function insert_query($input_array)
    {
        try {
            $ins_array = array(
                'table_name' => $input_array[0][1],
                'values' => explode(',', $input_array[0][2])
            );

            return $this->insert($ins_array);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function update_query($input_array)
    {
        try {
            if (isset($input_array[0]) && count($input_array[0]) > 3) {
                $upd_arr = array(
                    'table_name' => $input_array[0][1],
                    'values' => $input_array[0][2],
                    'where' => $input_array[0][3],
                );
                return $this->update($upd_arr);
            } else {
                return 'Please write correct code!';
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function delete_query($input_array)
    {
        try {
            if (isset($input_array[0]) && count($input_array[0]) > 2) {
                $ins_array = array(
                    'table_name' => $input_array[0][1],
                    'where' => $input_array[0][2]
                );
            }
            return $this->delete($ins_array);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function check_query_type($query)
    {
        preg_match_all('/select (.*?) from ([a-zA-Z0-9_]+)/i', $query, $cases, PREG_SET_ORDER);
        if ($cases) {
            preg_match_all('/select (.*?) from ([a-zA-Z0-9_]+) where (.*?) order by (.*?) (.*?);/i', $query, $matches, PREG_SET_ORDER);
            if ($matches) {
                return $this->select_query($matches);
            } else {
                preg_match_all('/select (.*?) from ([a-zA-Z0-9_]+) where (.*?);/i', $query, $matches, PREG_SET_ORDER);
                if ($matches) {
                    return $this->select_query($matches);
                } else {
                    preg_match_all('/select (.*?) from ([a-zA-Z0-9_]+) order by (.*?) (.*?);/i', $query, $matches, PREG_SET_ORDER);
                    if ($matches) {
                        return $this->select_query($matches);
                    } else {
                        preg_match_all('/select (.*?) from ([a-zA-Z0-9_]+)/i', $query, $matches, PREG_SET_ORDER);
                        if ($matches) {
                            return $this->select_query($matches);
                        }
                    }
                }
            }
        }

        preg_match_all('/insert into ([a-zA-Z0-9_]+) values\((.*?)\);/isu', $query, $matches, PREG_SET_ORDER);
        if ($matches) {
            return $this->insert_query($matches);
        }

        preg_match_all('/delete from ([a-zA-Z0-9_]+) where (.*?);/i', $query, $matches, PREG_SET_ORDER);
        if ($matches) {
            return $this->delete_query($matches);
        }

        preg_match_all('/update ([a-zA-Z0-9_]+) set (.*?) where (.*?);/i', $query, $matches, PREG_SET_ORDER);
        if ($matches) {
            return $this->update_query($matches);
        }


        preg_match_all('/create database ([a-zA-Z0-9_]+)/i', $query, $matches, PREG_SET_ORDER);
        if ($matches) {
            return $this->create_db($matches[0][1]);
        }

        preg_match_all('/drop database ([a-zA-Z0-9_]+)/i', $query, $matches, PREG_SET_ORDER);
        if ($matches) {
            return $this->remove_db($matches[0][1]);
        }


        preg_match_all('/create table ([a-zA-Z0-9_]+) columns \((.*?)\);/i', $query, $matches, PREG_SET_ORDER);
        if ($matches) {
            if (count($matches[0]) > 2) {
                $ins_array = array(
                    'table_name' => $matches[0][1],
                    'columns' => $matches[0][2]
                );
                return $this->create_table($ins_array);

            }
        }

        preg_match_all('/drop table ([a-zA-Z0-9_]+)/i', $query, $matches, PREG_SET_ORDER);
        if ($matches) {
            return $this->remove_table($matches[0][1]);
        }


    }

    public function order_by($array, $field, $sort)
    {
        try {
            usort($array, function ($a, $b) use ($field, $sort) {
                if (strtoupper($sort) == 'DESC') {
                    return $a[$field] < $b[$field];
                } else {
                    return $a[$field] >= $b[$field];
                }
            });

            return $array;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

}
