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
<h1>Show Competition Page</h1>

<?php
	echo "<table>";
	echo "<tr>";
	echo "<td>Id</td>";
	echo "<td>Name</td>";
	echo "<td>Questions per day</td>";
	echo "<td>Answers per questions</td>";
	echo "<td>Start date</td>";
	echo "<td>End date</td>";
	echo "</tr>";
	foreach ($array->result() as $row) {
		echo "<tr>";
		echo "<td>$row->competition_id</td>";
		echo "<td>$row->name</td>";
		echo "<td>$row->question_per_day</td>";
		echo "<td>$row->answers_per_day</td>";
		echo "<td>$row->start_date</td>";
		echo "<td>$row->end_date</td>";
		echo "</tr>";
	}
	echo "</table>";

?>


</div>

</body>
</html>