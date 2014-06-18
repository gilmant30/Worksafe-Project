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
<h1>Show Competition Page</h1>

<?php
	echo '<table class="bordered">';
	echo "<thead>";
	echo "<tr>";
	echo "<th>Id</th>";
	echo "<th>Name</th>";
	echo "<th>Questions per day</th>";
	echo "<th>Answers per questions</th>";
	echo "<th>Start date</th>";
	echo "<th>End date</th>";
	echo "<th>active</th>";
	echo "</tr>";
	echo "</thead>";
	echo "<tbody>";
	foreach ($array->result() as $row) {
		echo "<tr>";
		echo "<td>$row->competition_id</td>";
		echo "<td>$row->name</td>";
		echo "<td>$row->question_per_day</td>";
		echo "<td>$row->answers_per_day</td>";
		echo "<td>$row->start_date</td>";
		echo "<td>$row->end_date</td>";
		if($row->active == 'y')
			echo "<td>yes</td>";
		else
			echo "<td>no</td>";
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