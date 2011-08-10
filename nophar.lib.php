<?php

/**
 *  Simple class for combining files for distribution.
 *  Named after the lack of a Phar library on some shared hosts.
 **/

/*
  Methods to implement:
   - addFile
   - addFromString
   - close
   - extractTo
   - open
  
  Example:
   $nophar = new NoPharPackage();
   $nophar->open($filename, NOPHAR::CREATE);
   $nophar->addFile($filename, $localname);
   $nophar->addFromString($localname, $contents);
   $nophar->close();
   
  Example:
   $nophar = new NoPharPackage();
   $nophar->open($nopharfilename);
   $nophar->extractTo($directory);
  
*/

class NoPhar{
    
    const CREATE      = 1;
    const DELIMITER   = 'Thisisthegapbetw\'eenfiles';
    const MARKER      = 'Thisisthemarkerf\'orfilename';
    
    var $contents;
    var $file;
    
    function NoPhar(){
        
        $this->contents = array();
    }
    
    function open($filename, $flag=false){
        
        if($flag == NOPHAR::CREATE){
            touch($filename) or die('Cannot create NoPhar.');
        }else{
            //$c = file_get_contents($filename);
            ob_start();
            readgzfile($filename);
            $c=ob_get_clean();
            $fs = explode(NOPHAR::DELIMITER, $c);
            foreach($fs as $f){
                if($f=='') continue;
                $fb = explode(NOPHAR::MARKER, $f, 2);
                $this->contents[$fb[0]] = $fb[1];
            }
        }
        
        $this->file = $filename;
    }
    function close(){
        
        $nophar = '';
        foreach($this->contents as $f=>$c){
            $nophar.=$f.NOPHAR::MARKER.$c.NOPHAR::DELIMITER;
        }
        $nophar = gzencode($nophar, 9);
        file_put_contents($this->file, $nophar);
    }
    
    function addFile($filename, $localname=false){
        
        if(!$localname){
            return false;
        }
        $this->addFromString($localname, file_get_contents($filename));
    }
    function addFromString($localname, $contents){
        
        $this->contents[$localname] = $contents;
    }
    
    function extractTo($dir){
        
        foreach($this->contents as $f=>$c){
            $d = dirname($f);
            $this->create_path($dir.'/'.$d);
            file_put_contents($dir.'/'.$f,$c);
        }
    }
    
    function create_path($path){
        
        if(!file_exists($path)){
            $p=explode(DIRECTORY_SEPARATOR,strrev($path),2);
            if(isset($p[1])){
                $this->create_path(strrev($p[1]));
            }
            @mkdir($path);
            return true;
        }elseif(is_dir($path)){
            return true;
        }
        return false;
    }
};

?>
