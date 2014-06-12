<!DOCTYPE html>
<html>
<head>
        
<style>@import url('<?=base_url()?>/assets/css/header.css'); </style>
<style>@import url('<?=base_url()?>/assets/css/create_question.css'); </style>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>

<style>


</style>

<title>Competition</title>
</head>
<body>
<?php
        $day = 1;
        $final_day = 2;
        $arr = array(1,2,3,4);
        $answer = array(1,2,3);

        echo "<h1>Create Questions for day $day out of $final_day</h1>";

        echo '<div id=create_qustion">';
        echo '<h2>Create Questions</h2>';
        echo '<form name="create_question" action="#" method="POST" onsubmit="" enctype="multipart/form-data">';

        foreach($arr as &$value)
        {
                echo "<p>Question $value:</p>";
                echo '<textarea id = "question" name="question'.$value.'" required></textarea>';
                echo '<br />';

                foreach($answer as &$ans)
                {
                        echo "<p>Answer $ans:</p>";
                        echo '<textarea id="answer" name="q'.$value.'answer'.$ans.'" required></textarea>';
                        echo '<input type="radio" name="correct_ans'.$value.'"/>';
                        echo '<br />';
                }
                echo '<br />';
                echo '<br />';
        }
        //echo '<input type="submit" value="Finish"/>';
        //echo '</form>';
        if($day!=$final_day)
        {
        echo '<input type="submit" value="Continue to next day"/>';
        }
        else
        echo '<input type="submit" value="Finish"/>';

        echo '</form>';
        echo '</div>';
?>

</body>
</html>
