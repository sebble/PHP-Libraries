<?

/**
 *  CSV File Manipulation Functions Library (lib.csv.php)
 *  Last Updated: 08/12/08
 **/
 
function csv_load($path, $col_head=false, $id_col=-1, $max_len=4096){
    $dataset = NULL;
    if($path=="") return false; // No Path
    if(!file_exists($path)) return false; // No File
    
    $rowID=-1;
    $handle = fopen($path, "r");
    while (($data_row = fgetcsv($handle, $max_len, ",")) !== FALSE) { // Read File
      $rowID++;
      if($rowID==0) $cols = count($data_row); // Columns 
      if($rowID==0&&$col_head) { // First row is field names
        
        $fields = $data_row;
        
        if(!is_int($id_col)){ // Find ID Column by Name (only poss if headings active)
          foreach($fields as $k=>$v){
            if($v==$id_col){
              $id_col=$k; // Set ID_Col to int value
              break;
            }
          }
        }
        continue;
      }
      if($rowID==0&&!$col_head){ // First row is data
        for($i=0;$i<count($data_row);$i++){
          $fields[$i]=$i; // Set fields to be numeric
        }
        $rowID++;
      }
      
      for($i=0;$i<$cols;$i++) { // for each field, set data-array
        if($id_col<0) { $rowIDname = $rowID; }
        else { $rowIDname = $data_row[$id_col]; }
        $dataset[$rowIDname][$fields[$i]]=$data_row[$i];
      }
    }
    $rows = $rowID;
    fclose($handle);
    
    return $dataset;
}

function csv_to_string($array, $col_head=false){ // Optional header line
    
    // Updated 06-02-09: Fixed save empty table, note: loses headers.
    if(!is_array($array)) return null;
    if(count($array)<1) return null;
    
    $string='';
    if($col_head){
        $array2=$array;
        $fields=array_keys(array_pop($array2));
        foreach($fields as $k=>$f){
            $fields[$k] = str_replace('"','""',$f);
        }
        $string.='"'.implode('","',$fields)."\"\r\n";
    }
    foreach($array as $row){
        foreach($row as $k=>$data){ // Escape '"'
             $row[$k] = str_replace('"','""',$data);
        }
        $string.='"'.implode('","',$row)."\"\r\n";
    }
    return $string;
}

function csv_from_string($string, $head=false, $id=-1){ // Requires file.lib.php
    
    $r = false;
    $fname = './~csvFromString'.rand(1000,9999).'.tmp';
    if(file_save($fname)){
      if(csv_load($fname, $head, $id)) $r=true;
      file_delete($fname);
    }
    return $r;
}

function csv_save($file_path, $array, $col_head=false){ // Requires file.lib.php
    
    return file_save($file_path, csv_to_string($array, $col_head));
}

?>