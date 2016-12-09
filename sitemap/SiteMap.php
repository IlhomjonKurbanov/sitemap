<?php
namespace borysenko\sitemap;

use Yii;
use yii\base\Model;
use yii\helpers\Url;


class SiteMap extends Model{
 private $db = null;
 private $tables_info = array();
 private $data = array();
 private $sitemap_index = 0;
 private $sitemap_file_index = array();
 private $dir;
 private $folder_sitemap;


 public function __construct($dir, $folder_sitemap){
     $this->dir = $dir;
     $this->folder_sitemap = $folder_sitemap;
     $dir_sitemaps = Yii::getAlias($this->dir . $this->folder_sitemap);
     if(!is_dir($dir_sitemaps))
        mkdir($dir_sitemaps, 0777);
 }

 public function __destruct() {
  unset($this->tables_info);
  unset($this->data);
 }
 
 public function addTable($loc,$priority,$lastmod,$changefreq,$table,$field, $where = "1=1"){
  array_push($this->tables_info,array('loc'=>$loc,'priority'=>$priority,'lastmod'=>$lastmod,'changefreq'=>$changefreq,'table'=>$table,'field'=>$field, 'where'=>$where));
 }

 public function viewTablesData(){
  print_r($this->data);
 }
 
 private function addData($res,$url){
  foreach($res as $row){
   $this->addUrl($this->sprint_f($url['loc'],$row),$url['priority'],$url['lastmod'],$url['changefreq']);
  }
 }
 
 private function sprint_f($str,$vars = array()){
   $eval = "\$r = sprintf(\$str,";
            //for($i=0;$i<count($vars);$i++){$eval .= "\$vars[".$i."]";if($i<count($vars)-1)$eval .= ",";}
            $i = 0;
            foreach($vars as $key=>$value){$eval .= "\$vars['".$key."']";if($i<count($vars)-1)$eval .= ",";$i++;}
             $eval .= ");";
            eval($eval);
            return $r;
 }
 
 function addUrl($loc,$priority,$lastmod,$changefreq){
   array_push($this->data,array('loc'=>$loc,'priority'=>$priority,'lastmod'=>$lastmod,'changefreq'=>$changefreq));
 }
 
 public function start(){
  foreach($this->tables_info as $row){
   $sql = "select ".implode(",",$row['field'])." from ".$row['table']." where ".$row['where'];
   $res = Yii::$app->db->createCommand($sql)->queryAll();
      //print_r($res);
   $this->addData($res, $row);
  }
 }
 
 private function save($file,$xml){
  $handle = fopen($file, "w");
  fwrite($handle,$xml);
  fclose($handle);
 }

 public function saveXML($file){
  $this->sitemap_index ++;
  $xml = '<?xml version="1.0" encoding="UTF-8" ?>';
  $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
     $i = 1;$rec = false;
     foreach($this->data as $key=>$row){
           $xml .= "<url>";
           $xml .= sprintf("<loc>%s</loc>", $this->escapeLoc($row['loc']));
           $xml .= sprintf("<lastmod>%s</lastmod>", $row['lastmod']);
           $xml .= sprintf("<changefreq>%s</changefreq>", $row['changefreq']);
           $xml .= sprintf("<priority>%s</priority>", $row['priority']);
           $xml .= "</url>";

         unset($this->data[$key]);

         if($i == 49000){$rec = true;break;}

         $i++;
     }
  $xml .= '</urlset>';
     $xml_file = sprintf($file, $this->sitemap_index);
     $this->sitemap_file_index[] = $xml_file;
     $this->save(Yii::getAlias($this->dir . $this->folder_sitemap . '/' . $xml_file),$xml);

     if($rec)$this->saveXML($file);
 }

 public function saveIndexXml($file){
     $xml = '<?xml version="1.0" encoding="UTF-8" ?>';
     $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

     foreach($this->sitemap_file_index as $index){
         $xml .= "<sitemap>";
         $xml .= sprintf("<loc>%s</loc>", $this->escapeLoc(Yii::$app->urlManager->createAbsoluteUrl($this->folder_sitemap . '/' . $index)));
         $xml .= sprintf("<lastmod>%s</lastmod>", date('c'));
         $xml .= "</sitemap>";
     }

     $xml .= '</sitemapindex>';
     $xml_file = $file;
     $this->save(Yii::getAlias($this->dir . $xml_file),$xml);
 }
 
 private function escapeLoc($loc){
        $patterns = array();
        $patterns[0] = '/&/';
        $patterns[1] = '/\'/';
        $patterns[2] = '/"/';
        $patterns[3] = '/>/';
        $patterns[4] = '/</';
        $replacements = array();
        $replacements[4] = '&amp;';
        $replacements[3] = '&apos;';
        $replacements[2] = '&quot;';
        $replacements[1] = '&gt;';
        $replacements[0] = '&lt;';
        return preg_replace($patterns, $replacements, $loc);
 }
 
 
}

?>