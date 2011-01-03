<?php
/*** SQL Utility Functions ***/

// Insert Array
function sql_insert_array($table,$array){
    
    $array = mysql_real_escape_array($array);
    $cols = '`'.implode('`,`',array_keys($array)).'`';
    $values = '\''.implode('\',\'',$array).'\'';
    $sql = "INSERT INTO $table ($cols) VALUES ($values)";
    mysql_query($sql);
    return mysql_error();
}
// Update Array
function sql_update_array($table,$array,$where){
    
    if(is_numeric($where)){
        $where = "id=$where";}
    
    $array = mysql_real_escape_array($array);
    $sql = "UPDATE $table SET";
    foreach($array as $k=>$v){
        $sql.= " `$k`='$v'";
    }
    $sql.= " WHERE $where";
    mysql_query($sql);
    return mysql_error();
}
// SQL Escape Array
function mysql_real_escape_array($array,$post=false){
    
    if(!is_array($array)) return false;
    foreach($array as $k=>$v){
        if($post){ $v=str_replace(array('\\"',"\\'",'\\\\'),array('"',"'",'\\'),$v); }
        $return[$k] = mysql_real_escape_string($v);
    }
    return $return;
}

?>
