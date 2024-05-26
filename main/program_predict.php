<?php
ini_set('display_errors', 0);

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


$training_set_Images = array();
$testing_set_Images = array();

$testing_set_array = array();
$training_set_array = array();


$testing_set_labels = array();
$training_set_labels = array();

$dn_values = $_POST['dn_values'];
$dn_values = json_decode($dn_values);
$iterator = 0;
$second_iterator = 0;

$dn_array = $_POST["image_set_type"];
$dn_array = array_values($dn_array);

foreach ($dn_values as $val) {

    //print_r($val);
    //$set_type = $_POST["image_set_type"][$iterator];

    $set_type = $dn_array[$iterator];
    if ($set_type === "testing_set") {
        $testing_set_array_assoc[$second_iterator] =  $val;
        $testing_set_array[$second_iterator] =  array($val->r, $val->g, $val->b);
        $testing_set_labels[$second_iterator] = $val->mc;
        $testing_set_Images[$second_iterator] = $val->image;
    } else if ($set_type === "training_set") {
        $training_set_array_assoc[$second_iterator] =  $val;
        $training_set_array[$second_iterator] =  array($val->r, $val->g, $val->b);
        $training_set_labels[$second_iterator] = $val->mc;
        $training_set_Images[$second_iterator] = $val->image;
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

$training_set_Images = array_values($training_set_Images);
$testing_set_Images =  array_values($testing_set_Images);




$regression = new LeastSquares();
$regression->train($training_set_array, $training_set_labels);

$predict = $regression->predict($testing_set_array);


echo ' <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">';
$new_iterator_training = 0;
echo "<div class='row m-auto mt-3'>";
echo "<div class='col text-center'>";
echo "<h3> Training set </h3> ";
echo "<table class='table p-5 m-3 m-auto' style='width:fit-content;'> <tr> <th>Image </th> <th>MC </th> </tr> <tr>";
foreach ($training_set_array as $training_dat) {
    echo "<tr> <td>" .  $training_set_Images[$new_iterator_training]  . " </td> <td>" . $training_set_labels[$new_iterator_training] . "</td>";
    $new_iterator_training++;
}
echo "</tr> </table>";
echo "</div>";

echo "<div class='col text-center'>";
$new_iterator_testing = 0;
echo "<h3> Testing set </h3> ";
echo "<table class='table p-5 m-3 m-auto' style='width:fit-content;'> <tr> <th>Image </th> <th>MC </th> </tr> <tr>";
foreach ($testing_set_array as $testing_dat) {
    echo "<tr> <td>" . $testing_set_Images[$new_iterator_testing] . " </td> <td>" . $testing_set_labels[$new_iterator_testing] . "</td>";
    $new_iterator_testing++;
}
echo "</tr> </table>";
echo "</div>";

echo "<div class='col text-center'>";
$new_iterator = 0;
echo "<h3> Predicions </h3> ";
echo "<table class='table p-5 m-3 m-auto' style='width:fit-content;'> <tr> <th>Image </th> <th>MC </th> </tr> <tr>";
foreach ($predict as $predictions) {
    echo "<tr> <td>" . $testing_set_array_assoc[$new_iterator]->image . " </td> <td>" . $predictions . "</td>";
    $new_iterator++;
}

echo "</tr> </table>";

$score = Regression::r2Score($testing_set_labels, $predict);

echo "<p class='m-3'> <b> R2 Score: </b> " . $score . "</p>";
echo "</div>";
echo "</div>";
