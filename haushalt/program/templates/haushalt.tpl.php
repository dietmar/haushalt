<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
       "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Haushalt</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="expires" content="0">
<meta http-equiv="pragma" content="no-cache">


<script type="text/javascript">
function aufteilen()
{
   myregex = /^([0-9]*)([,.][0-9][0-9]?)?$/;
   parts = myregex.exec(document.getElementById("wieviel").value);

   if(parts == null) {
      alert("Bitte Zahlen in einem der folgenden Formate eingeben:\n " +
         "23\n 23,40\n 23.40\n 0,01\n 0.01\n Also höchstens zwei " +
         "Nachkommastellen, Punkt oder Komma als Trennzeichen.");
      return;
   }

   if(parts[2] == null) {
      wieviel = parseInt(parts[1], 10) * 100;
   } else {
      str = parts[2].substr(1);
      if(str.length == 1) {
         n = 10 * parseInt(str, 10);
      } else {
         n = parseInt(str, 10);
      }
      wieviel = parseInt(parts[1], 10) * 100 + n;
   }

   <?php $cnt = count($people); ?>

   rest = wieviel % <?php echo $cnt; ?>;
   ergebnis = (wieviel - rest) / <?php echo $cnt; ?>;
   <?php
   for($i = 0; $i < $cnt; $i++) {
      if($i == $cnt-1):
   ?>
   document.getElementById("<?php echo $people[$i]; ?>").value = (ergebnis + rest) / 100;
   <?php else: ?>
   document.getElementById("<?php echo $people[$i]; ?>").value = ergebnis / 100;
   <?php endif; ?>
   <?php } ?>
}

function delline(line)
{
   if(confirm("Soll die Zeile Nr " + line + " wirklich gelöscht werden?")) {
      document.getElementById('delline_line').value = line;
      document.getElementById('delform').submit();
   }
}

function ask_close()
{
    return confirm("Soll die laufende Abrechnung wirklich abgeschlossen und eine neue begonnen werden?");
}
</script>

<style type="text/css">
body {
background-color: #6e6e6e;
margin: 0;
padding: 2ex;
font-family: sans;
}
div.alert {
background-color: white;
text-align: left;
padding: 3ex;
color: red;
}
#list {
background-color: white;
border-collapse: collapse;
}
#list th {
border: 1px solid black;
padding: 1ex;
}
#list td {
border: 1px solid black;
padding: 0.5ex;
}
.A {
    background-color: #eeeeee;
}
.right {
text-align: right;
}
#summary {
background-color: white;
}
#summary td {
padding: 0.5ex;
}
#newline {
margin-top: 2ex;
margin-bottom: 2ex;
background-color: white;
padding: 1ex;
}
</style>

</head>
<body>

<h1 style="color: white;">Aktuelle Abrechnung</h1>

<div style="margin-bottom: 1em; text-align: right;">
    <form action="index.php" method="post">
        <input type="hidden" name="act" value="listold">
        <button type="submit">alte Abrechnungen anzeigen</button>
    </form>
    <br>
    <form action="index.php" method="post" onsubmit="return ask_close()">
        <input type="hidden" name="act" value="close">
        <button type="submit">aktuelle Abrechnung abschließen</button>
    </form>
</div>

<table id="summary">
<?php foreach($people as $p): ?>
<tr>
   <td><?php echo $p; ?> hat</td>
   <td class="right"><?php echo htmlspecialchars($has_paid[$p]); ?></td>
   <td>ausgegeben und muss</td>
   <td class="right"><?php echo htmlspecialchars($should_pay[$p]); ?></td>
   <td>zahlen:</td>
   <td class="right"><?php echo htmlspecialchars($has_paid[$p] - $should_pay[$p]); ?></td>
</tr>
<?php endforeach; ?>
</table>

<div id="newline">
Neue Zeile hinzufügen:<br><br>
<form action="index.php" method="post">
<input type="hidden" name="act" value="newline">
<table>
   <tr>
      <td>Datum</td>
      <td>wer</td>
      <td>was</td>
      <td>wie viel</td>
      <?php foreach($people as $p): ?>
      <td><?php echo $p; ?></td>
      <?php endforeach; ?>
      <td></td>
   </tr>
   <tr>
      <td><input type="text" name="datum" size="10" value="<?php echo htmlspecialchars($datum); ?>"></td>
      <td>
         <select name="wer" size="1">
            <?php foreach($people as $p): ?>
            <option value="<?php echo $p; ?>" <?php if($wer == $p) echo 'selected'; ?>><?php echo $p; ?></option>
            <?php endforeach; ?>
         </select>
      </td>
      <td><input type="text" name="was" size="40" value="<?php echo htmlspecialchars($was); ?>"></td>
      <td><input type="text" name="wieviel" id="wieviel" size="5" value="<?php echo htmlspecialchars($wieviel); ?>"></td>
      <?php foreach($people as $p): ?>
      <td><input type="text" name="<?php echo $p; ?>" id="<?php echo $p; ?>" size="5" value="<?php echo htmlspecialchars($_POST[$p]); ?>"></td>
      <?php endforeach; ?>      
      <td><button type="button" onclick="aufteilen()">aufteilen</button></td>
   </tr>
   <tr>
      <td colspan="8" style="text-align: center;">
      <br><button type="submit">speichern</button>
      <?php if(!empty($message)): ?>
      <div class="alert"><?php echo nl2br(htmlspecialchars($message)); ?></div>
      <?php endif; ?>
      </td>
   </tr>
</table>

</form>
</div>

<form action="index.php" method="post" id="delform">
<input type="hidden" name="act" value="delline">
<input type="hidden" name="line" id="delline_line" value="">
</form>

<table id="list">
   <tr>
      <th>Nr</th>
      <th>Datum</th>
      <th>wer</th>
      <th>was</th>
      <th>wie viel</th>
      
      <?php foreach($people as $p): ?>
      <th><?php echo $p; ?></th>
      <?php endforeach; ?>
      <th></th>
   </tr>
   
   <?php $bgcol = 65; ?>
   <?php foreach($data as $d): ?>
   <tr class="<?php echo chr($bgcol); ?>">
      <td><?php echo htmlspecialchars($d[0]); ?></td>
      <td><?php echo htmlspecialchars($d[1]); ?></td>
      <td><?php echo htmlspecialchars($d[2]); ?></td>
      <td><?php echo htmlspecialchars($d[3]); ?></td>
      <td class="right"><?php echo htmlspecialchars($d[4]); ?></td>
      
      <?php $i = 5; ?>
      <?php foreach($people as $p): ?>
      <td class="right"><?php echo htmlspecialchars($d[$i++]); ?></td>
      <?php endforeach; ?>
            <td><button type="button" onclick="delline(<?php echo htmlspecialchars($d[0]); ?>)">löschen</button></td>
   </tr>
   <?php $bgcol = 66 - ($bgcol + 1) % 2; ?>
   <?php endforeach; ?>
   
   <tr>
   <td colspan="4"></td>
   <th class="right"><?php echo $sum_wieviel; ?></th>
   
   <?php foreach($people as $p): ?>
   <th class="right"><?php echo $should_pay[$p]; ?></th>
   <?php endforeach; ?>
   <td></td>
   </tr>
   
</table>

</body>
</html>
