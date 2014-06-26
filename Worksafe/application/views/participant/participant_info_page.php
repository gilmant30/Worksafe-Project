<!DOCTYPE html>
<html>
<head>


<style>@import url('<?=base_url()?>/assets/css/participant_info.css'); </style>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

</head>
<body>


<div id="body">
<br />
<div id="commitment"><?=$commitment?></div>
<h1>Welcome to this years workSAFE week online competition!</h1>
<p>This competition will help improve peoples awareness of safety as well as be a competitive competition between departments</p>
<p>You can click the learn more button at the top to learn more, or click the continue to questions to get started with todays quiz!</p>

<br />
<a class="button" href="<?=base_url();?>index.php/participant/questionPage">Continue to questions</a>
<a class="button" href="<?=base_url();?>index.php/participant/leaderboard">Check out the leaderboard</a>

</div>
</body>
</html>