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




if (isset($_POST['color_space'])) {
    $csvFile = file('datasets/data_set/data_set.csv');
    $mc_values_data = [];
    foreach ($csvFile as $line) {
        $mc_values_data[] = str_getcsv($line);
    }

    $color_space = $_POST['color_space'];
    switch ((int)$color_space) {
        case 1:
            $dn_results = get_rgb_values($mc_values_data);
            break;

        case 2:
            break;

        case 3:
            break;
    }
}




function get_mean_of_rgb($image_url)
{

    $r = array();
    $g = array();
    $b = array();

    $image = imagecreatefromjpeg($image_url);
    $size   = getimagesize($image_url);
    $width  = $size[0];
    $height = $size[1];

    for ($x = 0; $x < $width; $x++) {
        for ($y = 0; $y < $height; $y++) {
            $rgb = imagecolorat($image, $x, $y);
            $r[$x] = ($rgb >> 16) & 0xFF;
            $g[$x] = ($rgb >> 8) & 0xFF;
            $b[$x] = $rgb & 0xFF;
        }
    }

    //$r = array_filter($r);
    $average_r = array_sum($r) / count($r);
    //$g = array_filter($g);
    $average_g = array_sum($g) / count($g);
    //$b = array_filter($b);
    $average_b = array_sum($b) / count($b);

    return array('R' => $average_r, 'G' => $average_g, 'B' => $average_b);
}

function get_rgb_values($mc_values_data)
{
    // ignore data[0] as it only contains table headers
    $rgbvalues = array();
    $iterator = 1;
    $training_set_limit = count($mc_values_data);
    for ($i = 1; $i < $training_set_limit - 1; $i++) {
        $image_url = "images/" . $mc_values_data[$i][0];


        $rgb_values = get_mean_of_rgb($image_url);
        $temp_array = array(
            'image' => $mc_values_data[$i][0],
            'r' =>  $rgb_values['R'],
            'g' =>  $rgb_values['G'],
            'b' => $rgb_values['B'],
            'mc' => $mc_values_data[$i][1]
        );

        $rgbvalues[$iterator] = $temp_array;
        $iterator++;
    }

    return $rgbvalues;
}

function display_dataset_checkboxes($dataset)
{
    $iterator = 1;
    foreach ($dataset as $data) {
        $image = $data['image'];
        $r = $data['r'];
        $g = $data['g'];
        $b = $data['b'];
        $mc = $data['mc'];
        //$input_name = $image . "_set_type";
        $input_name = "image_set_type[$iterator]";
        echo "<tr> <td> $image &nbsp;&nbsp; </td> <td> $r &nbsp; &nbsp; </td> <td> $g &nbsp; &nbsp; </td> <td> $b &nbsp; &nbsp; </td> <td> $mc &nbsp; &nbsp;</td> <td> <input type='radio' class='m-auto' value='training_set' name='$input_name'></td> <td> <input type='radio' value='testing_set' class='m-auto' name='$input_name'></td></tr>";
        $iterator++;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Document</title>
</head>

<body class="p-5">

    <?php if (isset($color_space)) { ?>
        <div class="card m-auto p-2" style="width: fit-content;">
            <form action="program_predict.php" method="post">
                <input type="hidden" name="dn_values" value='<?php echo json_encode($dn_results); ?>'>
                <table class="text-center table">
                    <thead>
                        <tr>
                            <?php if ((int)$color_space === 1) { ?>
                                <td><b>Image</b></td>
                                <td><b>DN(R)</b></td>
                                <td><b>DN(G)</b></td>
                                <td><b>DN(B)</b></td>
                                <td><b>MC </b> </td>
                                <td><b>Training set</b> </td>
                                <td><b>Testing set </b> </td>
                            <?php } else if ((int)$color_space === 2) { ?>
                                <td><b>Image</b></td>
                                <td><b>DN(H)</b></td>
                                <td><b>DN(S)</b></td>
                                <td><b>DN(V)</b></td>
                                <td><b>MC</b></td>
                                <td><b>Training set</b> </td>
                                <td><b>Testing set </b> </td>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php display_dataset_checkboxes($dn_results); ?>
                    </tbody>

                </table>

                <input type="submit" value="Submit" class="btn btn-primary" style="float: right;">
            </form>

        </div>

    <?php } ?>

</body>

</html>