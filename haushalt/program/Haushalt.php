<?php

require_once('program/Config.php');
require_once('program/Template.php');

if (!function_exists('fputcsv')) {
 
  function fputcsv(&$handle, $fields = array(), $delimiter = ',', $enclosure = '"') {
    $str = '';
    $escape_char = '\\';
    foreach ($fields as $value) {
      if (strpos($value, $delimiter) !== false ||
          strpos($value, $enclosure) !== false ||
          strpos($value, "\n") !== false ||
          strpos($value, "\r") !== false ||
          strpos($value, "\t") !== false ||
          strpos($value, ' ') !== false) {
        $str2 = $enclosure;
        $escaped = 0;
        $len = strlen($value);
        for ($i=0;$i<$len;$i++) {
          if ($value[$i] == $escape_char) {
            $escaped = 1;
          } else if (!$escaped && $value[$i] == $enclosure) {
            $str2 .= $enclosure;
          } else {
            $escaped = 0;
          }
          $str2 .= $value[$i];
        }
        $str2 .= $enclosure;
        $str .= $str2.$delimiter;
      } else {
        $str .= $value.$delimiter;
      }
    }
    $str = substr($str,0,-1);
    $str .= "\n";
    return fwrite($handle, $str);
  }
 
}

class Haushalt
{

   private $datafile = HAUSHALT_CURRENTFILE;
   private $people_ser = HAUSHALT_PEOPLE;
   private $people;
   private $tpldir = HAUSHALT_TEMPLATE_DIR;
   private $data;
   private $tpl;
   private $maxid;

   // --------------------------------------------------------------------------
   function start()
   {
      $this->people = unserialize($this->people_ser);
      $this->tpl = new Template($this->tpldir);
      $this->readData($this->datafile);
      
      switch($_POST['act']) {
         case 'delline': $this->delline(); break;
         case 'newline': $this->newline(); break;
         case 'listold': $this->listold(); break;
         case 'displayold': $this->displayold(); break;
         case 'downloadold': $this->downloadold(); break;
         case 'close': $this->close(); break;
         default: $this->show(); break;
      }
   }
   
   // --------------------------------------------------------------------------
   function show($message = '')
   {
      if(!empty($message)) {
         $this->tpl->set('message', $message);
         $this->tpl->set('datum',   $_POST['datum']);
         $this->tpl->set('wer',     $_POST['wer']);
         $this->tpl->set('was',     $_POST['was']);
         $this->tpl->set('wieviel', $_POST['wieviel']);
         foreach($this->people as $p) {
            $this->tpl->set($p,  $_POST[$p]);
         }
      }
      
      $sum_wieviel = 0;
      $has_paid  = array();
      $should_pay= array();
      foreach($this->people as $p) {
         $has_paid[$p] = 0;
         $should_pay[$p] = 0;
      }
      
      $displaydata = $this->data;
      foreach($displaydata as $k => $v) {
         $displaydata[$k][4] = sprintf('%01.2f', $v[4] / 100);
         $sum_wieviel += $v[4];
         
         $i = 5;
         foreach($this->people as $p) {
            $displaydata[$k][$i] = sprintf('%01.2f', $v[$i] / 100);
            $should_pay[$p] += $v[$i];
            $i++;
            if($v[2] == $p) {
               $has_paid[$p] += $v[4];
            }
         }
      }
      
      $this->tpl->set('data', $displaydata);
      $this->tpl->set('sum_wieviel', sprintf('%01.2f', $sum_wieviel / 100));
      
      foreach($this->people as $p) {
         $has_paid[$p] = sprintf('%01.2f', $has_paid[$p]  / 100);
         $should_pay[$p] = sprintf('%01.2f', $should_pay[$p]  / 100);
      }
      
      $this->tpl->set('has_paid', $has_paid);
      $this->tpl->set('should_pay', $should_pay);
      $this->tpl->set('people', $this->people);

      echo $this->tpl->fetch('haushalt.tpl.php');
   }
   
   // --------------------------------------------------------------------------
   function readData($filename)
   {
      $this->data = array();
      $this->maxid = 0;
      if(!file_exists($filename)) {
         return;
      }
      
      $handle = @fopen($filename, 'r');
      if($handle === false) {
         die('Datei konnte nicht zum Lesen geöffnet werden.');
      }
      while(($row = fgetcsv($handle)) !== false) {
      
         if(count($row) != 5 + count($this->people)) {
            die('Die Anzahl Spalten in der Datei stimmt nicht zusammen.');
         }
      
         $this->data[] = $row;
         if($row[0] > $this->maxid) {
            $this->maxid = $row[0];
         }
      }
      fclose($handle);
   }
   
   // --------------------------------------------------------------------------
   function writeData()
   {
      $this->makeBackup();
      
      $handle = @fopen($this->datafile, 'w');
      if($handle === false) {
         die('Datei konnte nicht zum Schreiben geöffnet werden.');
      }
      $i = 0;
      for($i = 0; $i < count($this->data); $i++) {
         $this->data[$i][0] = $i+1;
         fputcsv($handle, $this->data[$i]);
      }
      fclose($handle);
      $this->maxid = $i;
   }
   
   // --------------------------------------------------------------------------
   function newline()
   {
      $datum   = $_POST['datum'];
      $wer     = $_POST['wer'];
      $was     = $_POST['was'];
      $wieviel = $_POST['wieviel'];
      
      $should_pay = array();
      foreach($this->people as $p) {
         $should_pay[$p] = $this->parseEur($_POST[$p]);
      }
            
      if(!preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $datum)) {
         $this->show('Bitte Datum im Format TT.MM.JJJJ eingeben.');
         return;
      }
      $wieviel = $this->parseEur($wieviel);
      //echo $wieviel;
            
      if(array_sum($should_pay) != $wieviel) {
         $this->show('Die aufgeteilten Beträge ergeben nicht den Gesamtbetrag!');
         return;
      }
      $this->data[] = array_merge(array(
         $this->maxid+1,
         $datum,
         $wer,
         $was,
         $wieviel),
         $should_pay);
         
      $this->writeData();
      $_POST = array();
      $this->readData($this->datafile);
      $this->show('Speichern erfolgreich');
   }
   
   // --------------------------------------------------------------------------
   function parseEur($n)
   {
      if($n === '') {
         return 0;
      }
   
      if(!preg_match('/^(\d*)([,.]\d\d?)?$/', $n, $matches)) {
         $this->show(
            "Bitte Beträge in einem der folgenden Formate eingeben:\n " .
            "23\n 23,40\n 23.40\n 0,01\n 0.01\n Also höchstens zwei " .
            "Nachkommastellen, Punkt oder Komma als Trennzeichen.");
            die();
      }
      $n = $matches[1]*100;
      if(!empty($matches[2])) {
         $matches[2] = substr($matches[2], 1);
         if(strlen($matches[2]) == 1) {
            $matches[2] *= 10;
         }
         $n += $matches[2];
      }
      return $n;
   }
   
   // --------------------------------------------------------------------------
   function delline()
   {
      $line = $_POST['line'];
      $newdata = array();
      foreach($this->data as $d) {
         if($d[0] != $line) {
            $newdata[] = $d;
         }
      }
      $this->data = $newdata;
      $this->writeData();
      $this->show();
   }
   
   // --------------------------------------------------------------------------
   function makeBackup()
   {
      if(!file_exists($this->datafile)) {
         return;
      }
      $bakname = dirname($this->datafile) . '/backup_' . date('Y-m-d_H.i.s') . '.csv';
      copy($this->datafile, $bakname);
   }
   
   // --------------------------------------------------------------------------
   function listold()
   {
      $thelist = glob('program/data/closed_*.csv');
      $data = array();
      $num = 1;
      foreach($thelist as $d) {
         $germandate = preg_replace('/.*closed_(\d+)-(\d+)-(\d+)_[0-9.]+\.csv/', '\3.\2.\1', $d);
         $data[] = array($num++, $germandate, $d);
      }
      $this->tpl->set('data', $data);
      echo $this->tpl->fetch('listold.tpl.php');
   }
   
   // --------------------------------------------------------------------------
   function displayold()
   {
      $which = $_POST['which'];
      if(!preg_match('/^.*closed_[0-9._-]+.csv$/', $which)) {
         return;
      }
      $germandate = preg_replace('/.*closed_(\d+)-(\d+)-(\d+)_[0-9.]+.csv/', '\3.\2.\1', $which);
      $this->tpl->set('germandate', $germandate);
      $this->readData($which);

      $sum_wieviel = 0;
      $has_paid  = array();
      $should_pay= array();
      foreach($this->people as $p) {
         $has_paid[$p] = 0;
         $should_pay[$p] = 0;
      }
      
      $displaydata = $this->data;
      foreach($displaydata as $k => $v) {
         $displaydata[$k][4] = sprintf('%01.2f', $v[4] / 100);
         $sum_wieviel += $v[4];
         
         $i = 5;
         foreach($this->people as $p) {
            $displaydata[$k][$i] = sprintf('%01.2f', $v[$i] / 100);
            $should_pay[$p] += $v[$i];
            $i++;
            if($v[2] == $p) {
               $has_paid[$p] += $v[4];
            }
         }
      }
      
      $this->tpl->set('data', $displaydata);
      $this->tpl->set('sum_wieviel', sprintf('%01.2f', $sum_wieviel / 100));
      
      $this->tpl->set('data', $displaydata);
      $this->tpl->set('sum_wieviel', sprintf('%01.2f', $sum_wieviel / 100));
      
      foreach($this->people as $p) {
         $has_paid[$p] = sprintf('%01.2f', $has_paid[$p]  / 100);
         $should_pay[$p] = sprintf('%01.2f', $should_pay[$p]  / 100);
      }
      
      $this->tpl->set('has_paid', $has_paid);
      $this->tpl->set('should_pay', $should_pay);
      $this->tpl->set('people', $this->people);
      
      echo $this->tpl->fetch('displayold.tpl.php');
   }

   // --------------------------------------------------------------------------
   function downloadold()
   {
      $which = $_POST['which'];
      if(!preg_match('/^.*closed_[0-9._-]+.csv$/', $which)) {
         return;
      }
      $name = preg_replace('/.*closed_([0-9._-]+).csv/', 'haushalt_\1.csv', $which);
      header('Content-type: text/csv');
      header("Content-Disposition: attachment; filename=\"$name\"");
      readfile($which);
   }

   // --------------------------------------------------------------------------
   function close()
   {
        $closedname = dirname($this->datafile) . '/closed_' . date('Y-m-d_H.i.s') . '.csv';
        rename($this->datafile, $closedname);
        touch($this->datafile);
        chmod($this->datafile, 0666);
        $this->data = array();
        $this->show();
   }
}

?>
