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
border: 1px solid black;
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

<h1 style="color: white;">Alte Abrechnung vom <?php echo htmlspecialchars($germandate); ?></h1>

<div style="margin-bottom: 1em; text-align: right;">
    <form action="index.php" method="post">
        <input type="hidden" name="act" value="listold">
        <button type="submit">zurück zur Liste der alten Abrechnungen</button>
    </form>
    <br>
    <form action="index.php" method="post">
        <input type="hidden" name="act" value="show">
        <button type="submit">zurück zur aktuellen Abrechnung</button>
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
<br>
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
   </tr>
   <?php $bgcol = 66 - ($bgcol + 1) % 2; ?>
   <?php endforeach; ?>
   
   <tr>
   <td colspan="4"></td>
   <th class="right"><?php echo $sum_wieviel; ?></th>
   
   <?php foreach($people as $p): ?>
   <th class="right"><?php echo $should_pay[$p]; ?></th>
   <?php endforeach; ?>
   </tr>
   
</table>

</body>
</html>
