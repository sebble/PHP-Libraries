<?php

function ini_parse_file($file, $sections=false){
    
    return parse_ini_file($file, $sections);
}

function ini_from_array($array){
    
    $ini='';
    foreach($array as $sct_key => $val_arr){
        if(is_array($val_arr)){
            $ini.="[$sct_key]\n";
            foreach($val_arr as $sct_key2 => $val_arr2){
                if(is_array($val_arr2)){
                    foreach($val_arr2 as $sct_key3 => $val_arr3){
                        $ini.="{$sct_key2}[] = \"$val_arr3\"\n";
                    }
                }else{
                    $ini.="$sct_key2 = \"$val_arr2\"\n";
                }
            }
        }else{
            $ini.="$sct_key = \"$val_arr\"\n";
        }
    }
    return $ini;
}

function ini_save(){
    
    //do stuff
}

?>