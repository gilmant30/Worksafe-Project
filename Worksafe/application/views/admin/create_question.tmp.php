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
        $attributes = array('name' => 'create_question', 'onsubmit' => '');

        echo "<h1>Create Questions for <strong>$title</strong> day $current_day out of $days_of_competition</h1>";
        echo '<div id=create_qustion">';
        echo '<h2>Create Questions</h2>';

        echo form_open('admin/uploadQuestions', $attributes);

        //dynamically create questions form based on user input from new_competition page
        for($i=1;$i<($questions+1);$i++)
        {
                $question_input = array('name' => 'question'.$i, 'id' => 'question');
                $category_input = array('name' => 'category'.$i, 'id' => 'category');
                $type_input = array('name' => 'type'.$i, 'id' => 'type');
                
                echo "<p>Question $i:</p>";
                echo form_label('Category: ','category'.$i);
                echo form_input($category_input);
                echo '<br />';
                echo form_label('Type: ','type'.$i);
                echo form_input($type_input);
                echo '<br />';
                echo form_textarea($question_input);
               //echo '<textarea id = "question" name="question'.$i.'" required></textarea>';
                echo '<br />';

                //dynamically create answer input and radio buttons from data given on new_competition page
                for($a=1;$a<($answers+1);$a++)
                {
                        $answer_input = array('name' => 'q'.$i.'answer'.$a, 'id' => 'answer');
                        $radio_input = array('name' => 'correct_ans_q'.$i, 'value' => 'q'.$i.'a'.$a);

                        echo "<p>Answer $a:</p>";
                        echo form_textarea($answer_input);
                        echo form_label('Question '.$i.' answer '.$a.' correct  ','correct_ans_q'.$i.'a'.$a);
                        echo form_radio($radio_input);
                        echo '<br />';
                }
                echo '<br />';
                echo '<br />';
        }

        if($current_day!=$days_of_competition)
        {
                echo form_submit('Submit', 'Continue to next day');
        }
        else
                echo form_submit('Submit', 'Finish');


        echo '</form>';
        echo '</div>';
?>

</body>
</html>
