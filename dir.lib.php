<?

// List Directory Contents, recursive optional
// Version 1 (files and dirs grouped)
function dir_list($path,$r=false) {

  if (is_dir($path)) {
    if (is_dir($path."/")) { $path.= "/"; }
    $subs[0] = "";
    $files[0] = "";
    $subX = 0;
    $fileX = 0;
    if ($dh = opendir($path)) {
      while (($file = readdir($dh))) {
        if ($file  != "." && $file  != "..") {
          if (filetype($path.$file) == "dir")  { $subs[$subX]   = $file; $subX++; }
          if (filetype($path.$file) == "file") { $files[$fileX] = $file; $fileX++; }
          if (filetype($path.$file) == "dir" && $r) { $subs[$file] = dir_list($path.$file,true); $subX++; }
        }
      }
      closedir($dh);
    }
    $all['dir'] = $subs;
    $all['file'] = $files;
    if($all['dir'][0]==''){$all['dir']=array();}
    if($all['file'][0]==''){$all['file']=array();}
    return $all;
  }
  else { return false; }
}

function dir_new($path) {

  return mkdir($path);
}

function dir_delete($path,$sub=false) { // WARNING: Requires file.lib.php
  
  require_once('file.lib.php');
  
  return file_delete($path,true,$sub);
}

function dir_empty($path){

    if ($dh = opendir($path)) {
        while ($file = readdir($dh)) {
            if ($file != '.' && $file != '..') {
                closedir($dh);
                return false;
            }
        }
        closedir($dh);
        return true;
    }
    else return false;
}

function dir_name($path,$unix=true,$clean=true){
    
    $path = dirname($path);
    if($unix) $path = str_replace('\\','/',$path);
    if($clean) $path = str_replace(array('\/','\\/','//','/\\'),array('/','/','/','/'),$path);
    if($path=='.'||$path=='/') return'';
    return $path;
}

function safe_dir($safe,$path){
    
    $real = realpath($path).'/';echo $path.$real;
    #require_once('extend.lib.php'); // escape_regex
    #echo "hello! $safe $path $real ";
    if(strpos($real,$safe)===0){
        return $real;
    }
    return false;
}

function safe_dir2($safe,$path){
    
    #$real = realpath($path).'/';echo $path.$real;
    #require_once('extend.lib.php'); // escape_regex #### NOT AT ALL SECURE!!
    $path = str_replace('\\','/',$path);
    if(strpos($path,$safe)==0){
        $path2 = substr($path,strlen($safe));
        while(strstr($path2,'..')){
            $last = $path2;
            $path2 = preg_replace('¬(?<=^|/)[^/]+/\.\./¬','',$path2);
            if($path2==$last){ return false; }
        }
        return $path;
    }else{
        return false;
    }
}

?>
