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

function call_display(filename)
{
    document.getElementById("display_act").value = "displayold";
    document.getElementById("display_which").value = filename;
    document.getElementById("display_form").submit()
}

function call_download(filename)
{
    document.getElementById("display_act").value = "downloadold";
    document.getElementById("display_which").value = filename;
    document.getElementById("display_form").submit()
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
border: 1px solid black;
}
#list th {
border: 1px solid black;
padding: 1ex;
}
#list td {
border-bottom: 1px solid black;
padding: 0.5ex;
padding-left: 2ex;
padding-right: 2ex;
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

<h1 style="color: white;">Alte Abrechnungen</h1>

<div style="margin-bottom: 1em; text-align: right;">
    <form action="index.php" method="post">
        <input type="hidden" name="act" value="show">
        <button type="submit">zurück zur aktuellen Abrechnung</button>
    </form>
</div>

<form action="index.php" method="post" id="display_form">
<input type="hidden" name="act" id="display_act" value="-">
<input type="hidden" name="which" id="display_which" value="-">
<table id="list">

   <?php $bgcol = 65; ?>
   <?php foreach($data as $d): ?>
   <tr class="<?php echo chr($bgcol); ?>">
      <td><?php echo htmlspecialchars($d[0]); ?></td>
      <td><?php echo htmlspecialchars($d[1]); ?></td>
      <td><button type="button" onClick="call_display('<?php echo htmlspecialchars($d[2]); ?>')">anzeigen</button></td>
      <td><button type="button" onClick="call_download('<?php echo htmlspecialchars($d[2]); ?>')">als CSV herunterladen</button></td>
   </tr>
   <?php $bgcol = 66 - ($bgcol + 1) % 2; ?>
   <?php endforeach; ?>
   
</table>
</form>

</body>
</html>
