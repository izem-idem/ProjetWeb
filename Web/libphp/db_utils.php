<?php
//Global variale of connection to database
$db_conn = null;

//Function to connect to postgres database
function connect_db(){
    global $db_conn;

    /*Parsing of .ini file to have connexion infos*/
    $db_info = parse_ini_file("dbinfo.ini");

    /*Connection to database*/
    $db_conn = pg_connect("host=".$db_info['host']
        ." user=".$db_info['user']
        ." password=".$db_info['password']
        ." dbname=".$db_info['dbname']);
}

function disconnect_db(){
    global $db_conn;
    pg_close($db_conn);
}
?>
