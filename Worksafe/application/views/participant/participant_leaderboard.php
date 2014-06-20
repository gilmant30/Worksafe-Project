<!DOCTYPE html>
<html>
<head>

<style>@import url('<?=base_url()?>/assets/css/header.css'); </style>
<style>@import url('<?=base_url()?>/assets/css/leaderboard.css'); </style>
<script type="text/javascript" src="<?= base_url();?>assets/js/jquery-latest.js" ></script>
<script type="text/javascript" src="<?= base_url();?>assets/js/jquery.tablesorter.js"></script>

<!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>

<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />-->




<script type="text/javascript">
$(document).ready(function() 
    { 
        $("#myTable").tablesorter()  
}); 

</script>

<title>Competition</title>
</head>
<body>
<h1><?= $competition->name ?> Leaderboard</h1>


<div id='org_table'>
	<table id="myTable" class="tablesorter">
    <thead>
		<tr>
			<th>Organization name</th>
      <th>Total Commitments</th>
		</tr>
  </thead>
      <tbody>
  <?php
  	foreach ($organization as $row) {
    	echo "<tr>";
    	echo '<td>'.$row['name'].'</td>';
      echo '<td>'.$row['total_commits'].'</td>';
    	echo "</tr>";
  }
  ?>
</tbody>
</table>

<br />
<a href="<?= base_url();?>index.php/participant">Back to info page</a>

</div>

</body>
</html>