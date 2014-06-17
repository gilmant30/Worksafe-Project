<!DOCTYPE html>
<html>
<head>

<style>@import url('<?=base_url()?>/assets/css/header.css'); </style>


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
	<table>
		<tr>
			<td>Organization id</td>
			<td>Organization name</td>
		</tr>
  <?php
  	foreach ($organization->result() as $row) {
    	echo "<tr>";
    	echo '<td><a href ="'.base_url().'index.php/admin/showParticipants/'.$row->user_id.'" id="org_id_link">'.$row->user_id.'</a></td>';
    	echo "<td>$row->name</td>";
    	echo "</tr>";
  }
  ?>
	</table>

<br />
<a href="<?= base_url();?>index.php/admin/competition">Home page</a>


</div>

</body>
</html>
