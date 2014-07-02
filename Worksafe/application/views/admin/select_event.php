<!DOCTYPE html>
<html>
<head>

<style>@import url('<?=base_url()?>/assets/css/header.css'); </style>


<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>

<script>

</script>

<title>Choose Event</title>
</head>
<body>
<h1>Choose the event type</h1>


<div id='org_table'>
	<table class="bordered">
    <thead>
		<tr>
			<th>Event Type</th>
		</tr>
  </thead>
      <tbody>
  <?php

foreach($event_type->result() as $type){
    	echo "<tr>";
    	echo '<td><a href="'.base_url().'index.php/admin/createNewEvent/'.$type->EVENT_TYPE_ID.'"">Create a new '.$type->EVENT_TYPE.'</a></td>';
    	echo "</tr>";
  }
	echo '</tbody>';
	echo '</table>';
?>
</div>

</body>
</html>
