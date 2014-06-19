<!DOCTYPE html>
<html>
<head>

<style>@import url('<?=base_url()?>/assets/css/header.css'); </style>
<style>@import url('<?=base_url()?>/assets/css/participant_info.css'); </style>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

</head>
<body>
<h1>Information Page about the contest</h1>
<div id="commitment"><?=$commitment?></div>
<p>Information about the contest</p>
<p>Formatted differently and styled but will show what the contest is about and why it's so great</p>

<br />
<a href="<?=base_url();?>index.php/participant/questionPage">Continue to questions</a>
<a href="<?=base_url();?>index.php/participant/leaderboard">Check out the leaderboard</a>


</body>
</html>