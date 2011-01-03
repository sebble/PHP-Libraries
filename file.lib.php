<?
/**
 *  Standard and Extended File Manipulation Functions Library (lib.file.php)
 *  Last Updated: 08/12/08
 *
 *  08/02/09 - ToDo: Add file locking (extra param, don't close file, return pointer/handle)
 *                   Other functions can't recieve pointer, so either change all or create new set.
 **/
 
//require_once('dir.lib.php');

function file_fetch($path) {

  if ($f=fopen($path,"rb")){
    $x="";
    while (!feof($f)) { $x.=fgetc($f); }
    fclose($f);
    return $x;
  }
  else { return false; }
}

function file_save($path, $data) {

  if($f=fopen($path, "wb")) {
    fwrite($f, $data);
    fclose($f);
    return true;
  }
  else { return false; }
}

function file_append($path, $data) {

  if($f=fopen($path, "ab")) {
    fwrite($f, $data);
    fclose($f);
    return true;
  }
  else { return false; }
}

function file_delete($path,$dir=false,$sub=false) {

  if (file_exists($path)&&!is_dir($path)) {
    unlink($path);
    return true;
  }
  elseif (is_dir($path)) {
    if($dir){
      if(dir_empty($path)){
        unlink($path);
      }elseif($sub){
        $contents = dir_list($path);
        foreach($contents['file'] as $fname){
          unlink($path.'/'.$fname);
        }
        foreach($contents['dir'] as $dirname){
          file_delete($path.'/'.$dirname,true,true);
        }
      }else{
        return false;
      }
    }else{
      return false;
    }
  }
  else { return false; }
}

function file_rename($path, $newpath) {

  if (file_exists($path) && !file_exists($newpath)) {
    rename($path, $newpath);
    return true;
  }
  else { return false; }
}

function file_move($path, $newpath) { // Alias
  
  return file_rename($path, $newpath);
}

function file_create($path) { // Alias-ish
  
  return file_save($path,'');
}

function file_lock($path) { // No real purpose

  if ($f=fopen($path,"rb")){
    return $f;
  }
  else { return false; }
}

function file_unlock($fp) { // No real purpose

  if (fclose($f)){
    return true;
  }
  else { return false; }
}

function file_release($fp){ // Alias

    return file_unlock($fp);
}

?>