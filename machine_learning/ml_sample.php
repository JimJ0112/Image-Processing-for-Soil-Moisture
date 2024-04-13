<?php

use Phpml\Metric\Regression;
use Phpml\Regression\LeastSquares;
use Phpml\Dataset\CsvDataset;
use Phpml\CrossValidation\RandomSplit;

require "../vendor/autoload.php";


$data = new CsvDataset('../datasets/data_set_values.csv', 3, true);
$data_set = new RandomSplit($data, 0.2, 156);
/*data set arrays*/
$data_set->getTrainSamples();
$data_set->getTrainLabels();
$data_set->getTestSamples();
$data_set->getTestLabels();


$regression = new LeastSquares();
$regression->train($data_set->getTrainSamples(), $data_set->getTrainLabels());



$predict = $regression->predict($data_set->getTestSamples());

$score = Regression::r2Score($data_set->getTestLabels(), $predict);
echo "<br/>R2 Score is: " . $score;

echo "<br/> <br/>";

$testArray = array(array(47.568518518518516, 40.21111111111111, 33.916666666666664), array(58.24074074074074, 49.75370370370371, 40.28703703703704), array(69.39814814814815, 60.757407407407406, 52.45), array(72.41481481481482, 60.44814814814815, 49.32592592592592), array(64.85185185185185, 57.08148148148148, 48.54074074074074), array(60.483333333333334, 48.05740740740741, 37.43518518518518));
$count = 25;
foreach ($testArray as $data) {
    echo "Number:" . $count . "<br/>";
    print_r($regression->predict($data));
    echo "<br/> <br/>";
    $count++;
}