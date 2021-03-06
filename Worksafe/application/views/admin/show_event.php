<!DOCTYPE html>
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<html>
<head>

<style>@import url('<?=base_url()?>/assets/css/header.css'); </style>
<style>@import url('<?=base_url()?>/assets/css/show_tables.css'); </style>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>

<script>

</script>

<title>Event</title>
</head>
<body>
<h1>Show Event Page</h1>

<?php
	echo '<div id="delete_competition">'.$delete_competition.'</div>';
	echo '<table class="bordered">';
	echo "<thead>";
	echo "<tr>";
	echo "<th>Id</th>";
	echo "<th>Name</th>";
	echo "<th>Start date</th>";
	echo "<th>End date</th>";
	echo "<th>Active</th>";
	echo "<th>Type</th>";
	echo "<th>Show Organization</th>";
	echo "<th>Set to Active?</th>";
	echo "<th>Delete Competition</th>";
	echo "</tr>";
	echo "</thead>";
	echo "<tbody>";
	foreach ($array->result() as $row) {
		echo "<tr>";
		echo "<td>$row->EVENT_ID</td>";
		echo "<td>$row->EVENT_NAME</td>";
		echo "<td>$row->START_DATE</td>";
		echo "<td>$row->END_DATE</td>";
		if($row->ACTIVE == 'y')
			echo "<td>yes</td>";
		else
		{
			echo "<td>no</td>";
		}

		//go through and figure out what event type each
		foreach($event_type->result() as $type)
		{
			if($row->EVENT_TYPE_ID == $type->EVENT_TYPE_ID)
			{
				echo '<td>'.$type->EVENT_TYPE.'</td>';
			}
		}

		$competition_event_type_id = '1';
		if($row->EVENT_TYPE_ID == $competition_event_type_id)
		{
			echo '<td><a href="'.base_url().'index.php/admin/showOrganization/'.$row->EVENT_ID.'">Show Organization</a></td>';
		}
		else
		{
			echo '<td>No Organizations for courses</td>';
		}

		if($row->ACTIVE == 'n')
			echo '<td><a href="'.base_url().'index.php/admin/activateEvent/'.$row->EVENT_ID.'">Activate</a></td>';
		else
			echo '<td><a href="'.base_url().'index.php/admin/activateEvent/'.$row->EVENT_ID.'">Deactivate</a></td>';
		
		echo '<td><a href="'.base_url().'index.php/admin/deleteCompetition/'.$row->EVENT_ID.'">Delete</a></td>';
		echo "</tr>";
	}
	echo "</tbody>";
	echo "</table>";

?>

<br />
<a href="http://localhost/worksafe/index.php/admin/competition">Home page</a>


</div>

</body>
</html>