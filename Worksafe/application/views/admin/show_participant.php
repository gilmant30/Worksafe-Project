<!DOCTYPE html>
<html>
<head>

<style>@import url('<?=base_url()?>/assets/css/header.css'); </style>
<style>@import url('<?=base_url()?>/assets/css/show_tables.css'); </style>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>

<script>

</script>

<title>Competition</title>
</head>
<body>
<h1>Show Participants</h1>

<div id='org_table'>
	<table class="bordered">
    <thead>
		<tr>
			<th>Participant id</th>
			<th>Participant email</th>
      <th>Commitment count</th>
		</tr>
  </thead>
  <tbody>
  <?php
  	foreach ($participant as $row) {
    	
      echo "<tr>";
    	echo '<td>'.$row['user_id'].'</td>';
    	echo '<td>'.$row['email'].'</td>';
      echo '<td>'.$row['commit'].'</td>';
    	echo "</tr>";
  }
  ?>
</tbody>
	</table>

<br />
<a href="<?= base_url();?>index.php/admin/competition">Home page</a>


</div>

</body>
</html>