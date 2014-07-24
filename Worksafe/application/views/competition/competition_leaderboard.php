<!DOCTYPE html>
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<html>
<head>
<style>@import url('<?=base_url()?>/assets/css/leaderboard.css'); </style>
<script type="text/javascript" src="<?= base_url();?>assets/js/jquery-latest.js" ></script>
<script type="text/javascript" src="<?= base_url();?>assets/js/jquery.tablesorter.js"></script>
<script type="text/javascript">
$(document).ready(function() 
    { 
        $("#myTable").tablesorter()  
}); 

</script>

<title>Leaderboard</title>
</head>
<body>
<br />
<br />
<div id="leaderboard">
  <h1><?= $competition->EVENT_NAME ?> Leaderboard</h1>
  <br />
  <br />
  <br />
  <div id='org_table'>
  	<table id="myTable" class="tablesorter">
      <thead>
  		<tr>
  			<th>Team name</th>
        <th>Total Points</th>
        <th>Correct questions (%)</th>
  		</tr>
    </thead>
        <tbody>
    <?php
    	foreach ($organization as $row) {
      	echo "<tr>";
      	echo '<td>'.$row['name'].'</td>';
        echo '<td>'.$row['total_commits'].'</td>';
        echo '<td>'.$row['percent_correct'].'</td>';
      	echo "</tr>";
    }
    ?>
  </tbody>
  </table>
  </div>
</div>
</body>
</html>
