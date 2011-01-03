<?php

function call_external_func($script_name, $script_func=false, $attributes=false, $time_limit=false, $PID=false){

    /**
     * Requirements:
     *     - Call external script with minimal effort
     *     - Manage Time-outs
     *     - Log Processes
     *     - Safely run standard php files as external
     *     - Reinstate arguments as GET/POST vars
     **/

    /**
     * Process ID Structure: [timestamp]_[####]_[time_limit_seconds] e.g. 1233195060_7621_300
     **/
    
    $start_time = time();
    
    if(!$time_limit) $time_limit = ini_get('max_execution_time');
    if(!$PID) $PID = $start_time.'_'.rand(1000,9999).'_'.$time_limit;
    while(file_exists(dirname($_SERVER["SCRIPT_FILENAME"]).'/proc_'.$PID))
        $PID = $start_time.'_'.rand(1000,9999).'_'.$time_limit;
 
    /**
     * The includer is a small file that will load the required file,
     * run a function with args, set global vars, chdir, and run
     * independently of the original script.
     **/
    $web_access = $_SERVER['DOCUMENT_ROOT'];
    $includer = '/ext_'.$PID.'.php';
    $includer_ns = 'ext_'.$PID.'.php';
    $cur_dir = dirname($_SERVER["SCRIPT_FILENAME"]);
    $get='';$attrs='';$func='';
    if(strstr($cur_dir,$web_access))
        $includer_path = $cur_dir.$includer;
    else
        $includer_path = $web_access.$includer;
    if($script_func&&$attributes){
        $attrs = serialize($attributes);
        $func = " call_user_func_array('$script_func',unserialize(<<<EOD\n$attrs\nEOD\n));";
    }elseif($attributes){
        $attrs = serialize($attributes);
        // Register as GET/POST
        $get = " foreach(unserialize(<<<EOD\n$attrs\nEOD\n) as \$k=>\$v){\$_GET[\$k]=\$v;\$_POST[\$k]=\$v;\$_REQUEST[\$k]=\$v;}";
    }elseif($script_func){
        $func = " call_user_func('$script_func')";
    }
    $file_append = "function file_append_$PID(\$filename,\$string){if(\$handle = fopen(\$filename,'ab')){fwrite(\$handle,\$string);fclose(\$handle);}else return false; return true;}";
    $start_log = "Process Started at $start_time\nPID: $PID\nScript Name: $script_name\nFunction: $script_func\nAttributes: $attrs\nTime Limit: {$time_limit}s";
    $end_log = "Process Completed at '.time().' ('.(\time()-\$start).' seconds)";
    $content = "<?php $file_append file_append_$PID('proc_$PID','$start_log'); ignore_user_abort(true); set_time_limit($time_limit); chdir('$cur_dir');$get require_once('$script_name'); unlink('$includer_ns');$func unlink('proc_$PID') ?>";
    if ($handle = fopen($includer_path, 'w')) {
        fwrite($handle, $content);
    }else{ return false; }
    
    /**
     * This function uses sockets to open a connection to the PHP script loader (run it externally).
     **/
    $errno = '';
    $errstr = '';
    $server = $_SERVER["HTTP_HOST"];
    $includer_url = str_replace(array($web_access,'\\'),array('','/'),$includer_path);
    $rw_timeout = $time_limit;
    if($fp=fsockopen($server, 80, $errno, $errstr, $rw_timeout)){
        $out = "GET $includer_url HTTP/1.1\r\n";
        $out .= "Host: $server\r\n";
        $out .= "Connection: Close\r\n\r\n";
        stream_set_blocking($fp, false);
        stream_set_timeout($fp, $rw_timeout);
        fwrite($fp, $out);
        fclose($fp);
    }else return false;
    return true;
}

?>