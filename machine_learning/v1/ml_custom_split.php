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

$data_set->getTrainLabels();
$data_set->getTestSamples();
$data_set->getTestLabels();
// training a model

$regression = new LeastSquares();
$regression->train($data_set->getTrainSamples(), $data_set->getTrainLabels());

/*
// model saving
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
