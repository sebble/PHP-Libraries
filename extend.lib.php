<?

/* string unescapequotes ( string $string ) *\
 = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
\* unescapequotes() strips slashes from quotes.  */

function unescapequotes($string){

    return str_replace(array('\"',"\'"),array('"',"'"), $string);
    
    // Improvement/Bug: current ver. will replace "domain\\" with "domain\"
    //                  this is probably not desired.
    
    $string = preg_replace('/(?<!\\)\\(["\'])/', '\\1', $string);
    $string = preg_replace('/\\\\(["\'])/', '\\\\1', $string);
    return $string;
}



/* string escape_regex ( string $string ) *\
 = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
\* escape_regex() slashes []\^$.|?*+(){}.  */

function escape_regex($string){
    
    $replace = array('[',']','\\','^','$','.','|','?','*','+','(',')','{','}','-','/');
    $with    = array('\[','\]','\\\\','\^','\$','\.','\|','\?','\*','\+','\(','\)','\{','\}','\-','\/');
    
    return str_replace($replace, $with, $string);
}



/* mixed print_p ( mixed $expression [, bool $return ] ) *\
 = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
\* print_p() wraps the output of print_r in <pre> tags.  */

function print_p ( $expression, $return = false ) {
    
    $output  = '<pre>';
    $output .= print_r ( $expression , true );
    $output .= '</pre>';
    
    if ( $return ) {
        
        return $output;
        
    } else {
        
        echo $output;
    }
}



/* mixed echo_p ( mixed $expression ) *\
 = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
\* echo_p() wraps the output of echo in <pre> tags.  */

function echo_p ( $expression ) {
    
    echo '<pre>';
    echo $expression;
    echo '</pre>';
}



/* int micro_time ( void ) *\
 = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
\* micro_time() returns the time since the Unix Epoch in microseconds.  */

function micro_time() {
    
    $micro_time = explode ( ' ' , microtime() );
    
    return $micro_time[0] + $micro_time[1];
}



/* void header_by_code ( int $code ) *\
 = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
\* header_by_code() submits a header specified by its code.  */

function header_by_code($code,$url=null){
    
    switch ($code) {
    case 200:
        header("Status: 200 OK");
        break;
    case 404:
        header("HTTP/1.0 404 Not Found");
        break;
    case 301:
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: $url");
        exit();
    case 302:
        header("Location: $url");
        exit();
    default:
        header("Status: 200 OK");
    }
}

/* void header_by_code ( int $code ) *\
 = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
\* header_by_code() submits a header specified by its code.  */

function str_alphanumlower($string,$hyphen='-'){
    
    if($hyphen) $string = trim(preg_replace('/[-]+/','-',preg_replace('/[^a-z0-9]+/','-',strtolower($string))),'-');
    else        $string = preg_replace('/[^a-z0-9]+/','',strtolower($string));
    return $string;
}

// Document Later...

function dump_p($definedvars,$die=false){ // WHY USING PRINT_R WHEN WANT TO DUMP!!
    
    echo '<pre>'.print_r($definedvars,true).'</pre>';
    if($die){ die(); }
    
}

function istrue($string){
    
    //replace with
    $string = trim(trim(strtolower($string)),'\'"');
    if(in_array($string,array('enabled',  'active',   'true',  'yes', 'on',  '1')))
        return true;
    if(in_array($string,array('disabled', 'inactive', 'false', 'no',  'off', '0')))
        return false;
    #$true  = array("'enabled'", "'active'",  "'true'", "'yes'","'on'", "='1'","=1","=true");
    #$false = array("'disabled'","'inactive'","'false'","'no'", "'off'","='0'","=0","=false");
    #$true  = 'return $string=='.implode('||$string==',$true).';';
    #$false = 'return $string=='.implode('||$string==',$false).';';
    #if(eval($true)){ return true; }
    #if(eval($false)){ return false; }
    return NULL;
}

function propogate_server_vars($assoc_array){
    
    $_SERVER = array_merge($_SERVER,$assoc_array);
    $_HTTP_SERVER_VARS = array_merge($_HTTP_SERVER_VARS,$_SERVER);
    $_HTTP_ENV_VARS = array_merge($_HTTP_ENV_VARS,$_SERVER);
    $_ENV = array_merge($_ENV,$_SERVER);
    return true;
}

//Unscape
function unescape_post($string){
    
    if (get_magic_quotes_gpc()){
        if(is_array($string)){
            foreach($string as $k=>$s){
                $string[$k] = unescape_post($s);
            }
            return $string;
        }
        return str_replace(array('\\"',"\\'",'\\\\'),array('"',"'",'\\'),$string);
        // why not stripslashes();???
    }
    return $string;
}

function truncate_string($string,$length=30,$end='...'){
    
    $length-=mb_strlen($trailing);
    if (mb_strlen($str)> $length){
        return mb_substr($str,0,$length).$trailing;
    }
    return $string;
}

function scandir4($dir){

	$dir = dir($dir);
	//List files in directory
	$files=array();
	while (($file = $dir->read()) == false)
	{ if($file!='.'&&$file!='..'){$files[]=$file;} }
	$dir->close();
	return $files;
}

function format_size($size) {

    $sizes = array(" Bytes", " kB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
    if ($size == 0) { return('n/a'); } else {
    return (round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $sizes[$i]); }
}

function safe_serialise($data){
    
    return base64_encode(serialize($data));
}
function safe_unserialise($data){
    
    return unserialize(base64_decode($data));
}

function array_merge_full($array1, $array2){
    
    foreach((array)$array2 as $k=>$v){
        if(is_array($v)){
            // recursive
            $array1[$k]=array_merge_full((array)$array1[$k],$v);
        }else{
            if(is_numeric($k)){
                $array1[]=$v; //append
            }else{
                $array1[$k]=$v; // replace
            }
        }
    }
    return $array1;
}

function salt_md5($pass,$salt){
    
    return md5($salt.$pass);
}
function check_salted_md5($pass,$md5,$salt=false){
    
    if(!$salt){
        $salt = explode(':',$md5);
        $md5  = $salt[1];
        $salt = $salt[0];
    }
    $check = md5($salt.$pass);
    if($check==$md5)
        return true;
    return false;
}
function salted_md5($pass,$salt=false){
    
    if(!$salt){
        $salt = substr(md5(time()),0,8);
    }
    return $salt.':'.md5($salt.$pass);
}

function set_default(&$variable,$default/*,$default2...*/){
    
    if($variable===''||$variable===null){ $variable=$default; }
    // second go..
    $args=func_num_args();
    $i=$args-1;
    if($args>2){
        while(($variable===''||$variable===null)&&$i<$args){
            $variable=func_get_arg($i);
            $i++;
        }
    }
    // notes:
    //  the following count as no value: 0, false, null, ''
    //  in some cases 0 and false may be desired
    //  so do an explicit check for null and ''..?
    //  sometimes false 
}

function safe_define($var, $val){
    
    if(defined($var)) return false;
    define($var,$val);
    return true;
}

/* array form_vars ( string $prefix, array $array ) *\
 = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
\* return only the values of an array with keys matching $prefix_%  */
function form_vars($prefix, $array){
    
    $prefix = preg_replace('/[^a-z0-9]+/i','',$prefix);
    $result=array();
    foreach((array)$array as $k=>$var){
        if(preg_match("/^{$prefix}_([\w\W]+)$/",$k,$match)){
            $result[$match[1]]=$var;
        }
    }
    return $result;
}


/* Convert to UTF-8 */
function iconv_UTF8($string, $try=array('UTF-8','WINDOWS-1252','ISO-8859-1')){
    
    foreach($try as $enc){
        if(iconv_strlen($string,$enc)===false) continue;
        #echo "<h1>Encoding: $enc</h1>";
        return iconv($enc,'UTF-8',$string);
    }
    return $string;
}

















?>
