<?php

function wiki_parse($string){
    
//http://www.example.com    
    
// Settings: XHTML compliant (no u, s tags)       
    
    // Format GraphViz Images
    preg_match_all('/<graphviz((?:\s*(?:(?:layout=([\w]+))|(?:type=([\w]+))))*)>([\w\W]*?)<\/graphviz>/',$string,$matches);
    //print_r($matches[1]);
    foreach($matches[4] as $k=>$graph){
        if($graph!=''){
            $format='png'; $layoutengine='dot';
            if($matches[3][$k]!=''){ $format=$matches[3][$k]; }
            if($matches[2][$k]!=''){ $layoutengine=$matches[2][$k]; }
            $hash = md5($graph);
            if(!file_exists("{$hash}_gv_{$layoutengine}.{$format}")){
                //require_once 'Image/GraphViz.php';
                //$tmpgvz = new Image_GraphViz();
                if($f=fopen('./tmpgvz', "wb")) {
                    fwrite($f, $graph);
                    fclose($f);
                    //$tmpgvz -> renderDotFile('tmpgvz',"{$hash}_gv_{$layoutengine}.{$format}",'png');
                    //echo "<img src='{$hash}_gv.png' />";
                    $string = str_replace("<graphviz{$matches[1][$k]}>$graph</graphviz>","<img src='{$hash}_gv_{$layoutengine}.{$format}' />",$string);
                    $command  = 'dot'; // or neato,twopi,circo,fdp
                    $command .= ' -T' . escapeshellarg($format) . ' -K'  . escapeshellarg($layoutengine) . ' -o'  . escapeshellarg($hash.'_gv_'.$layoutengine.'.'.$format) . ' ' . escapeshellarg('tmpgvz');
                    //echo "<h1>C:$command</h1>";
                    @`$command`;
                }else{
                    //echo "Error!";
                }
            }else{
                $string = str_replace("<graphviz{$matches[1][$k]}>$graph</graphviz>","<img src='{$hash}_gv_{$layoutengine}.{$format}' />",$string);
            }
        }
    }
    
    // Remove protected elements
    preg_match_all('/(?:<((?:pre)|(?:script)|(?:style)|(?:code)|(?:nowiki))>)[\w\W]*?<\/\\1>/',$string,$matches);
    $protected[0] = $matches;
    preg_match_all('/\{\{\{[\w\W]*?\}\}\}/',$string,$matches);
    array_push($protected,$matches);
    preg_match_all('/""[\w\W]*?""/',$string,$matches);
    array_push($protected,$matches);
    preg_match_all('/<!--[\w\W]*?-->/',$string,$matches);
    array_push($protected,$matches);
    
    $prot_i=0;
    foreach($protected as $p){
        foreach($p[0] as $text){
            $string = str_replace($text,'<span id="prot_xx'.$prot_i.'"></span>',$string);
            $prot_i++;
        }
    }
    
    // Fix Word
    #$string = str_replace(array('�','“','”','’'),array('"','"','"',"'"),$string);
    #$string = htmlspecialchars($string);
    #$string = htmlentities($string);
    
    
    // Links
    $fullurl = '\w{2,8}:\/\/(?:[\w\d-]+\.)+\w{2,10}(?:\/[^\n\s\/ \]]*)*';
    $localurl= '(?:[\/#][^\n \s]+)'; //anchor or same domain (# or /)
    $email   = '[a-z0-9_\-@\.]+';
    $string = preg_replace('/((?:[^\:[\w\d"])|$)('.$fullurl.')/','\\1<a href="\\2">\\2</a>',$string);
    $string = preg_replace('/\[((?:'.$fullurl.')|(?:'.$localurl.')) ([^\n\]]+)\]/','<a href="\\1">\\2</a>',$string);
    $string = preg_replace('/\[((?:'.$fullurl.')|(?:'.$localurl.'))\]/','<a href="\\1">\\1</a>',$string);
    $string = preg_replace('/\[mailto:('.$email.') ([^\n\]]+)\]/i','<a href="mailto:\\1">\\2</a>',$string);
    $string = preg_replace('/\[mailto:('.$email.')\]/i','<a href="mailto:\\1">\\1</a>',$string);
    
    // Image
    $string = preg_replace('/\[IMG:((?:'.$fullurl.')|(?:'.$localurl.')) (\d+) (\d+) ([^\n\]]+)\]/i','<img src="\\1" alt="\\4" width="\\2" height="\\3" />',$string);
    $string = preg_replace('/\[IMG:((?:'.$fullurl.')|(?:'.$localurl.')) ([^\n\]]+)\]/i','<img src="\\1" alt="\\2" />',$string);
    // think about fetching image and dimensions dynamically
    
    // Newline Fix
    $string = preg_replace('/\r\n/',"\n",$string);
    $string = preg_replace('/\r/',"\n",$string);
    // Para-Spacing
    $string = preg_replace('/\n\n/',"\n\n\n\n",$string);
    // Para Match
    $string = preg_replace('/(?:^|\n\n)([^=\*\#<:\|!;\n][\w\W]+?)(?:\n\n|$)/',"\n\n<p>\\1</p>\n\n",$string);
    // Para Re-Space
    $string = preg_replace('/\n\n\n\n/',"\n\n",$string);
    
    // Headings
    $string = preg_replace('/(?:^|\n)={6} ([^\n]+?)( ={6})?(?:$|\n)/',"\n<h6>\\1</h6>",$string);
    $string = preg_replace('/(?:^|\n)={5} ([^\n]+?)( ={5})?(?:$|\n)/',"\n<h5>\\1</h5>",$string);
    $string = preg_replace('/(?:^|\n)={4} ([^\n]+?)( ={4})?(?:$|\n)/',"\n<h4>\\1</h4>",$string);
    $string = preg_replace('/(?:^|\n)={3} ([^\n]+?)( ={3})?(?:$|\n)/',"\n<h3>\\1</h3>",$string);
    $string = preg_replace('/(?:^|\n)={2} ([^\n]+?)( ={2})?(?:$|\n)/',"\n<h2>\\1</h2>",$string);
    $string = preg_replace('/(?:^|\n)={1} ([^\n]+?)( ={1})?(?:$|\n)/',"\n<h1>\\1</h1>",$string);
    
    // Emphasis, Strong, Bold, Italic, Deleted, Inserted
    $string = preg_replace('/\'{3}([^\'][^\n\r]+?)\'{3}/','<strong>\\1</strong>',$string);
    $string = preg_replace('/\'{2}([^\'][^\n\r]+?)\'{2}/','<em>\\1</em>',$string);
    $string = preg_replace('/\/\/\/([^\n\r]+?)\/\/\//','<i>\\1</i>',$string);
    $string = preg_replace('/\*\*([^\n\r]+?)\*\*/','<b>\\1</b>',$string);
    $string = preg_replace('/--([^\n]+?)--/','<del>\\1</del>',$string);
    $string = preg_replace('/\+\+([^\n]+?)\+\+/','<ins>\\1</ins>',$string);
    $string = preg_replace('/__([^\n]+?)__/','<u>\\1</u>',$string);
    
    // Superscript, Subscript
    $string = preg_replace('/\^\^([^\n]+?)\^\^/','<sup>\\1</sup>',$string);
    $string = preg_replace('/~~([^\n]+?)~~/','<sub>\\1</sub>',$string);
    
    // Big, Small Text
    $string = preg_replace('/<<([^\n]+?)>>/','<big>\\1</big>',$string);
    $string = preg_replace('/>>([^\n]+?)<</','<small>\\1</small>',$string);
    
    $string = parse_lists($string);
    
    $string = preg_replace('/^\n/',"",$string);
    $string = preg_replace('/\n$/',"",$string);
    
    // Table
    /*
    preg_match_all('/(?:^|\n\n)(?:!(?:\!(?:[^\!]|(?:\\\!))*)+!!\n)?(?:|(?:\|(?:[^\|]|(?:\\\|))*)+\||\n)+\n/',$string,$matches);
    foreach($matches as $table){
        $tcode="\n\n<table>\n";
        //$tr = preg_split('/\n/',trim($table[0]));
        $tr = preg_split('/\n\|\|/',trim($table[0]));
        foreach($tr as $row){
            if($row[0]=='!') { $delim='!'; $tag='th'; }
            else             { $delim='|'; $tag='td'; }
            $tcode.="<tr>";
            $td = explode($delim,trim($row,'|!'));
            foreach($td as $cell){
                $tcode.="<$tag>$cell</$tag>";
            }
            $tcode.="</tr>\n";
        }
        $tcode.="</table>\n";
        $string = str_replace($table[0],$tcode,$string);
    }*/
    
    // Linebreak
    $string = preg_replace('/;;;\n?/',"<br />\n",$string);
    $string = preg_replace('/(?:\\\\\n)|(?:\/\/\n)/',"<br />\n",$string);
    
    // Return Protected
    $prot_i=0;
    foreach($protected as $p){
        foreach($p[0] as $text){
            $string = str_replace('<span id="prot_xx'.$prot_i.'"></span>',$text,$string);
            $prot_i++;
        }
    }
    // Remove Protected Markers
    $string = preg_replace('/<((?:nowiki))>([\w\W]*?)<\/\\1>/','\\2',$string);
    $string = preg_replace('/\{\{\{([\w\W]*?)\}\}\}/','\\1',$string);
    $string = preg_replace('/""([\w\W]*?)""/','\\1',$string);
    
    return $string;
}


function parse_lists($string){
  
  preg_match_all('/(?:^|\n)(#|\*)(?:[^\n*#][^\n]*)?(?:\n\\1[^\n]*)*(?:$|\n)/',$string,$lists,PREG_SET_ORDER);
  foreach($lists as $k=>$list){
    $type = $list[1];
    preg_match_all('/(?:^|\n)(\\'.$type.')((?:[^\n*#][^\n]*)?(?:\n\\1[*#][^\n]*)*)/',$list[0],$items,PREG_PATTERN_ORDER);
    $items = $items[2];
    foreach($items as $k=>$item){
      if(preg_match('/\n\\'.$type.'/',$item)){
        $item = str_replace("\n$type","\n",$item);
        $item = parse_lists($item);
      }
      $items[$k] = trim($item);
    }
    if($type=='*') $type = 'ul';
    else           $type = 'ol';
    $lhtml = "<$type>\n";
    $lhtml.= "<li>".implode('</li><li>',$items).'</li>';
    $lhtml.= "</$type>";
    $string = str_replace($list[0],$lhtml,$string);
  }
  return $string;
}
?>
