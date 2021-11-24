<?php

require_once 'DbQueries.php';

error_reporting(E_ALL);
ini_set('display_errors', 0);
$selected_db = '';
$selected_table = '';
$db = new DbQueries('db');
$result = [];
$query = '';
if (isset($_POST['sql_query'])) {
    $db_name = $_POST['db_name'];
    $selected_table = $_POST['table_name'];
    $db = new DbQueries($db_name);

    $selected_db = $db->get_db_name();
    $query = $_POST['sql_query'];
    $result = $db->query_builder($query);

    if ($result && (!array_key_exists(0, $result) || !is_array($result[0])) && $selected_table){
        $result = $db->query_builder('select * from '.$selected_table.';');
    }

}


$databases_arr = scandir('databases', 0);
$databases = array();
foreach ($databases_arr as $value) {
    if ($value != '.' && $value != '..') {
        $databases[$value] = scandir('databases/' . $value);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My phpMyAdmin/ no sql</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css"
          integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <div id="ik" class="text-center">
                <a href="#"><span id="kap" class="font-italic">php</span><span id="gaz"
                                                                               class="font-italic">MyAdmin</span> </a>
                <div class="text-center">
                    <a href="#" class="yuk" title="Home"><i class="fas fa-thumbs-up"></i></a>
                    <a href="#" class="yuk" title="documentation"><i class="fas fa-home"></i></a>
                    <a href="#" class="yuk" title="Empty"><i class="fas fa-globe"></i></a>
                    <a href="#" class="yuk" title="MariaDB"><i class="fas fa-cloud"></i></a>
                    <a href="#" class="yuk" title="settings"><i class="fas fa-file"></i></a>
                    <a href="#" class="yuk" title="reload"> <i class="fas fa-bars"></i></a>
                </div>
                <div class="text-left my-3">
                    <a href="#" class="btn btn-outline-dark btn-sm" data-toggle="tooltip" data-placement="bottom"
                       title="`db_name` `table_name`">Recent</a>
                    <a href="#" class="btn btn-outline-dark btn-sm" data-toggle="tooltip" data-placement="bottom"
                       title="There are no favorite tables!">Favorites</a>

                </div>
            </div>
            <div class="text-left my-5">
                <div id="accordion">
                    <?php
                    if ($databases) {
                        foreach ($databases as $key => $database) {
                            $show = '';
                            if ($key == $selected_db) {
                                $show = 'show';
                            }
                            ?>
                            <div class="card">
                                <div class="card-header card-header_db" data-toggle="collapse"
                                     href="#collapse_<?= $key ?>"
                                     data-db="<?= $key ?>">
                                    <a class="collapsed card-link">
                                        <i class="fas fa-plus"></i> <?= $key; ?>
                                    </a>
                                </div>
                                <div id="collapse_<?= $key ?>" class="collapse <?= $show ?>" data-parent="#accordion">
                                    <div class="card-body">
                                        <ul>
                                            <?php
                                            foreach ($database as $table) {

                                                if ($table != '.' && $table != '..') {
                                                    ?>
                                                    <li><a href="#" class="my_table"><?= $table ?></a></li>
                                                <?php }
                                            } ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <?php
                        }
                    }
                    ?>
                    <div class="card">
                        <div class="card-header" data-toggle="collapse" href="#collapse_one">
                            <a class="card-link">
                                <i class="fas fa-plus"></i> Information_schema
                            </a>
                        </div>
                        <div id="collapse_one" class="collapse" data-parent="#accordion">
                            <div class="card-body">
                                <ul>
                                    <li><a href="#">info</a></li>
                                    <li><a href="#">info</a></li>
                                    <li><a href="#">info</a></li>
                                    <li><a href="#">info</a></li>
                                    <li><a href="#">info</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header" data-toggle="collapse" href="#collapse_two">
                            <a class="collapsed card-link">
                                <i class="fas fa-plus"></i> MySQL
                            </a>
                        </div>
                        <div id="collapse_two" class="collapse" data-parent="#accordion">
                            <div class="card-body">
                                <ul>
                                    <li><a href="#">info</a></li>
                                    <li><a href="#">info</a></li>
                                    <li><a href="#">info</a></li>
                                    <li><a href="#">info</a></li>
                                    <li><a href="#">info</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header" data-toggle="collapse" href="#collapse_four">
                            <a class="collapsed card-link">
                                <i class="fas fa-plus"></i> performance_schema
                            </a>
                        </div>
                        <div id="collapse_four" class="collapse" data-parent="#accordion">
                            <div class="card-body">
                                <ul>
                                    <li><a href="#">info</a></li>
                                    <li><a href="#">info</a></li>
                                    <li><a href="#">info</a></li>
                                    <li><a href="#">info</a></li>
                                    <li><a href="#">info</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-md-10">
            <ul class="breadcrumb bg-dark">
                <li class="breadcrumb-item "><a href="#" class="text-white">Server: 127.0.0.1 <i
                                class="fas fa-chevron-right"></i>
                    </a></li>
                <li class="breadcrumb-item"><a href="#" class="text-white">Database: <span
                                class="my_db_name">my_test</span> <i
                                class="fas fa-chevron-right"></i>
                    </a></li>
                <li class="breadcrumb-item"><a href="#" class="text-white">
                        Table: <span class="my_table_name">orders</span></a></li>
                <button type="button" class="btn btn-primary m-l-15" data-toggle="modal"
                        data-target="#exampleModalCenter">
                    Documentation
                </button>
            </ul>
            <ul class="nav nav-tabs " role="tablist">
                <li class="nav-item">
                    <a class="nav-link text-info" data-toggle="tab" href="#home">Browse</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-info" data-toggle="tab" href="#menu1">Structure</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-info active" data-toggle="tab" href="#menu2">SQL</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-info" data-toggle="tab" href="#menu3">Search</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-info" data-toggle="tab" href="#menu4">Insert</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-info" data-toggle="tab" href="#menu5">Export</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-info" data-toggle="tab" href="#menu6">Import</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-info" data-toggle="tab" href="#menu7">Privileges</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-info" data-toggle="tab" href="#menu8">Operations</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-info" data-toggle="tab" href="#menu9">Tracking</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-info" data-toggle="tab" href="#menu10">Triggers</a>
                </li>
            </ul>
            <div class="tab-content">
                <div id="home" class="container tab-pane fade"><br>
                    <h3>Browse</h3>
                </div>
                <div id="menu1" class="container tab-pane fade"><br>
                    <h3>Structure</h3>
                </div>
                <div id="menu2" class="container tab-pane active"><br>
                    <h5 class="text-left">Run SQL query/queries on table <span
                                class="my_db_name">my_test</span>.<span class="my_table_name">orders</span>: </h5>
                    <div class="row">
                        <div class="col-md-9">
                            <form action="#" method="post">
                                <div class="form-group">
                                    <textarea class="form-control" rows="8" id="no_sql_query" name="sql_query"
                                              placeholder="SELECT * FROM orders WHERE id = 1"
                                              required><?= $query; ?></textarea>
                                    <input type="hidden" class="db_name" name="db_name" required
                                           value="<?= $selected_db; ?>">
                                    <input type="hidden" class="table_name" name="table_name"
                                           value="<?= $selected_table; ?>">
                                </div>

                                <div class="btn-group">
                                    <button type="button" class="btn btn-secondary set_code"
                                            data-code="SELECT * FROM table_name WHERE param = value;">* SELECT
                                    </button>
                                    <button type="button" class="btn btn-secondary set_code"
                                            data-code="SELECT field_1, field_2 FROM table_name WHERE param_1 = value_1 AND param_2 = value_2;">
                                        SELECT
                                    </button>
                                    <button type="button" class="btn btn-secondary set_code"
                                            data-code='insert into table_name values(1, "val_2", "val_3");'>INSERT
                                    </button>
                                    <button type="button" class="btn btn-secondary set_code"
                                            data-code="UPDATE first_table SET val_2 = 2 WHERE product_id = 2 AND column_name < param;">
                                        UPDATE
                                    </button>
                                    <button type="button" class="btn btn-secondary set_code"
                                            data-code='delete from first_table where id = value and name = "value_2";'>
                                        DELETE
                                    </button>
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-secondary clear">Clear</button>
                                </div>
                                <div class="btn-group float-right">
                                    <button type="submit" class="btn btn-secondary">Go</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-3">
                            <form action="/action_page.php">

                                <select multiple class="form-control" id="sel2" name="sellist22" style="height:210px">
                                    <!--                                    <option>Id</option>-->
                                    <!--                                    <option>OrderNumber</option>-->
                                    <!--                                    <option>ShopId</option>-->
                                    <!--                                    <option>personId</option>-->
                                    <!--                                    <option>data_time</option>-->
                                </select>
                                <button type="button" class="btn btn-secondary my-3 float-right"> <<</button>

                            </form>
                        </div>
                    </div>

                </div>
                <div id="menu3" class="container tab-pane fade"><br>
                    <h3>Search</h3>
                </div>
                <div id="menu4" class="container tab-pane fade"><br>
                    <h3>Insert</h3>
                </div>
                <div id="menu5" class="container tab-pane fade"><br>
                    <h3>Export</h3>
                </div>
                <div id="menu6" class="container tab-pane fade"><br>
                    <h3>Import</h3>
                </div>
                <div id="menu7" class="container tab-pane fade"><br>
                    <h3>Privileges</h3>
                </div>
                <div id="menu8" class="container tab-pane fade"><br>
                    <h3>Operations</h3>
                </div>
                <div id="menu9" class="container tab-pane fade"><br>
                    <h3>Tracking</h3>
                </div>
                <div id="menu10" class="container tab-pane fade"><br>
                    <h3>Triggers</h3>
                </div>

            </div>

            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <?php if ($query) { ?>
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <?= $query; ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="col-md-12">
                        <div class="table_responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <?php if ($result && array_key_exists(0, $result) && is_array($result[0])) {

                                        foreach ($result[0] as $value) {
                                            foreach ($value as $key => $val) {
                                                ?>
                                                <th><?= $key; ?></th>
                                                <?php
                                            }
                                            break;
                                        }
                                    } ?>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if ($result && array_key_exists(0, $result) && is_array($result[0])) {
                                    foreach ($result[0] as $value) {
                                        ?>
                                        <tr>
                                            <?php
                                            foreach ($value as $key => $val) {
                                                ?>
                                                <td><?= $val; ?></td>
                                                <?php
                                            }
                                            ?>
                                        </tr>
                                        <?php
                                    }
                                } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog  modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h2 class="text-center text-success">Please use commands like this</h2>
                <h3 class="text-center text-success">Count spaces between words must be ONE</h3>
                <h5 class="">On  arguments of WHERE condition do not use spaces (<span class="text-danger"> where name = first name </span> use <span class="text-success"> where name = first_name </span> ))</h5>
                <p>1) create database db_name;</p>
                <p>2) drop database db_name;</p>
                <p>3) create table books columns (id, name, pages, author);</p>
                <p>4) select * from table_name;</p>
                <p>5) select id, name from books;</p>
                <p>6) select id, name, pages from books where id > 5 and author = William Shakespeare;</p>
                <p>7) select * from books where id > 5 and author = William Shakespeare order by author asc;</p>
                <p>8) select * from table_name  order by id desc;</p>
                <p>9) insert into table_name values(value1, value2, 5, text);</p>
                <p>10) drop table table_name;</p>
                <p>11) update table_name set x = y where a > b;</p>
                <p>12) delete from table_name where a = b and x < 4;</p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();

        $('.clear').on('click', function () {
            $('#no_sql_query').val('');
        });

        $('.set_code').on('click', function () {
            $('#no_sql_query').val($(this).data('code'));
        });

        $('.card-header_db').on('click', function () {
            $('.db_name').val($(this).data('db'));
            $('.my_db_name').text($(this).data('db'))
        });

        $('.my_table').on('click', function () {
            let table = $(this).text();
            let db = $('.db_name').val();
            $('.my_table_name').text(table);
            $('.table_name').val(table);

            let request = $.ajax({
                url: 'ajax.php',
                method: "POST",
                data: {
                    table: table,
                    db: db,
                    action: 'get_table_columns'
                }
            });

            request.done(function (message) {
                $('#sel2').html(message);
            });
        })
    });
</script>
</body>
</html>
