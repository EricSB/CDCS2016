<?php

include "config.php";
include "Include/UtilityFunctions.php";

//Gets comments from database function
function db_get_comments($num){
    mysql_connect(mysql_host,mysql_username,mysql_password);
    mysql_select_db(mysql_db) or die( "Unable to select database");
    $query="select * from comments limit ". strip_number($num);
    $result=mysql_query($query);
    mysql_close();
    return $result;
}
//Add a comment to the database
function db_put_comment($comment) {
    mysql_connect(mysql_host, mysql_username, mysql_password);
    mysql_select_db(mysql_db) or die( "Unable to select database");
    $query="insert into comments (c) values ('" . mysql_escape_string($comment) ."')"; //redundant but you can't be retroactively paranoid..
    $result=mysql_query($query);
    mysql_close();

    return $result;
}

//Get DB Users
function db_get_users(){

    mysql_connect(mysql_host, mysql_username, mysql_password);
    mysql_select_db(mysql_db) or die( "Unable to select database");
    $query="select * from credentials";
    $users = array();
    $result=mysql_query($query);

    while ($row = mysql_fetch_assoc($result)) {
        $users[] = strip_only_alphabet_nocase($row["username"]);
    }

    mysql_close();
    return $users;
}


?>

