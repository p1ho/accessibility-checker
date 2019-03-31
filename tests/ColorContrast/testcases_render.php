<?php
/**
 * A simple script for presenting testcases
 */
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Rendered Testcases</title>
    <style>
      .case {
        border:4px solid #000;
        padding: 1rem;
      }
      .render {
        margin: 0;
        padding: 0;
        border: 2px solid #ccc;
      }
      .pass {
        color: #008000;
      }
      .fail {
        color: #800000;
      }
    </style>
  </head>
  <body>
    <h1>Rendered Testcases for Color Contrast Checker</h1>

    <?php
    require "testcase.php";
    require "testcases_basic_html_pass.php";
    require "testcases_basic_html_fail.php";

    echo '<section>';
    echo '<h2 class="pass">Pass Cases</h2>';
    foreach ($testcases_basic_html_pass as $testcase) {
      echo '<div class="case">';
      echo '<h3>Input</h3>';
      echo '<div class="render">';
      echo $testcase->input;
      echo '</div>';
      echo '<h3>Expected Output</h3>';
      echo '<p class="pass"><strong>Should Pass</strong></p>';
      echo '</div>';
    }
    echo '</section>';

    echo '<section>';
    echo '<h2 class="fail">Fail Cases</h2>';
    foreach ($testcases_basic_html_fail as $testcase) {
      echo '<div class="case">';
      echo '<h3>Input</h3>';
      echo '<div class="render">';
      echo $testcase->input;
      echo '</div>';
      echo '<h3>Expected Output</h3>';
      echo '<p class="fail"><strong>Should Fail:</strong></p>';
      echo '<ul>';
      foreach ($testcase->expected_output['errors'] as $error) {
        echo '<li>'.htmlspecialchars(print_r($error, TRUE)).'</li>';
      }
      echo '</ul>';
      echo '</div>';
    }
    echo '</section>';
    ?>

  </body>
</html>
