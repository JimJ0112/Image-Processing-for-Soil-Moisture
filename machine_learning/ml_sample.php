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


$data = new CsvDataset('../datasets/data_set_values_rgb.csv', 3, true);
$data_set = new RandomSplit($data, 0.2, 155);
/*data set arrays*/
$data_set->getTrainSamples();
$data_set->getTrainLabels();
$data_set->getTestSamples();
$data_set->getTestLabels();


$trainSamples = $data_set->getTestSamples();
/*
echo "<table> <thead> <tr> <th>DN(Red)</th> <th> DN(Green) </th> <th> DN(Blue) </th></tr> </thead> <tbody> ";
foreach ($trainSamples as $trainsample) {
    echo "<tr> <td>" . $trainsample[0] . "</td> <td> " . $trainsample[1] . "</td> <td>" . $trainsample[2] . "</td> </tr> ";
}
echo "</tbody> </table>";
*/
$data_set->getTrainLabels();
$data_set->getTestSamples();
print_r($data_set->getTestLabels());
// training a model
//$regression = new SVR(Kernel::LINEAR);
$regression = new LeastSquares();
$regression->train($data_set->getTrainSamples(), $data_set->getTrainLabels());

/*
$modelManager = new ModelManager();
$modelManager->saveToFile($regression, "models/regressionModel_rgb_least_squares");
*/

/*
// loading a trained model
$modelManager = new ModelManager();
$regression = $modelManager->restoreFromFile("models/regressionModel_rgb_least_squares");
*/

$predict = $regression->predict($data_set->getTestSamples());
echo "<table> <tbody> ";
foreach ($predict as $pred) {
    echo "<tr> <td>" . $pred . "</td> </tr>";
}
echo "</table> </tbody> ";

$score = Regression::r2Score($data_set->getTestLabels(), $predict);
$accuracy = Accuracy::score($data_set->getTestLabels(), $predict);
echo "<br/>R2 Score is: " . $score . "<br/>";
echo "<br/> <br/>";

/*
$testArray = array(array(47.568518518518516, 40.21111111111111, 33.916666666666664), array(58.24074074074074, 49.75370370370371, 40.28703703703704), array(69.39814814814815, 60.757407407407406, 52.45), array(72.41481481481482, 60.44814814814815, 49.32592592592592), array(64.85185185185185, 57.08148148148148, 48.54074074074074), array(60.483333333333334, 48.05740740740741, 37.43518518518518));
$count = 25;
foreach ($testArray as $data) {
    echo "Number:" . $count . "<br/>";
    echo $regression->predict($data);
    echo "<br/> <br/>";
    $count++;
}*/
