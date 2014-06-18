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
<h1>Show Organizations</h1>

<div id='org_table'>
	<table class="bordered">
    <thead>
		<tr>
			<th>Organization id</th>
			<th>Organization name</th>
      <th>Total Commitments</th>
		</tr>
  </thead>
      <tbody>
  <?php
  	foreach ($organization as $row) {
    	echo "<tr>";
    	echo '<td><a href ="'.base_url().'index.php/admin/showParticipants/'.$row['user_id'].'" id="org_id_link">'.$row['user_id'].'</a></td>';
    	echo '<td>'.$row['name'].'</td>';
      echo '<td>'.$row['total_commits'].'</td>';
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
