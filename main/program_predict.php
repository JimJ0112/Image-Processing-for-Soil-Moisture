<?php

use Phpml\Metric\Regression;
use Phpml\Regression\LeastSquares;
use Phpml\Dataset\CsvDataset;
use Phpml\CrossValidation\RandomSplit;
use Phpml\Metric\Accuracy;
use Phpml\ModelManager;
use Phpml\Regression\SVR;
use Phpml\SupportVectorMachine\Kernel;

require "../vendor/autoload.php";


$testing_set_array_assoc = array();
$training_set_array_assoc = array();


$testing_set_array = array();
$training_set_array = array();

$testing_set_labels = array();
$training_set_labels = array();

$dn_values = $_POST['dn_values'];
$dn_values = json_decode($dn_values);
$iterator = 1;
$second_iterator = 0;

foreach ($dn_values as $val) {

    //print_r($val);
    $set_type = $_POST["image_set_type"][$iterator];

    if ($set_type === "testing_set") {
        $testing_set_array_assoc[$second_iterator] =  $val;
        $testing_set_array[$second_iterator] =  array($val->r, $val->g, $val->b);
        $testing_set_labels[$second_iterator] = $val->mc;
    } else {
        $training_set_array_assoc[$second_iterator] =  $val;
        $training_set_array[$second_iterator] =  array($val->r, $val->g, $val->b);
        $training_set_labels[$second_iterator] = $val->mc;
    }

    $iterator++;
    $second_iterator++;
}


$testing_set_array_assoc = array_values($testing_set_array_assoc);
$testing_set_array = array_values($testing_set_array);
$testing_set_labels = array_values($testing_set_labels);

$training_set_array_assoc = array_values($training_set_array_assoc);
$training_set_array = array_values($training_set_array);
$training_set_labels = array_values($training_set_labels);


$regression = new LeastSquares();
$regression->train($training_set_array, $training_set_labels);

$predict = $regression->predict($testing_set_array);

$new_iterator = 0;
echo ' <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">';
echo "<table class='table p-5 m-3' style='width:fit-content;'> <tr> <th>Image </th> <th>MC </th> </tr> <tr>";
foreach ($predict as $predictions) {
    echo "<tr> <td>" . $testing_set_array_assoc[$new_iterator]->image . " </td> <td>" . $predictions . "</td>";
    $new_iterator++;
}

echo "</tr> </table>";

$score = Regression::r2Score($testing_set_labels, $predict);

echo "<p class='m-3'> <b> R2 Score: </b> " . $score . "</p>";
