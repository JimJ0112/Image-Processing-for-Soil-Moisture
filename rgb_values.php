<?php

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, POST');

header("Access-Control-Allow-Headers: X-Requested-With");

$training_set_limit = 30;
$csvFile = file('mc_values_soil.csv');
$mc_values_data = [];
foreach ($csvFile as $line) {
    $mc_values_data[] = str_getcsv($line);
}

// ignore data[0] as it only contains table headers
$rgbvalues = array();
$iterator = 1;

for ($i = 1; $i <= $training_set_limit; $i++) {
    $image_url = "image_samples/" . $mc_values_data[$i][0];
    $rgb_values = get_mean_of_rgb($image_url);

    $temp_array = array(
        'image' => $mc_values_data[$i][0],
        'mc' => $mc_values_data[$i][1],
        'r' =>  $rgb_values['R'],
        'g' =>  $rgb_values['G'],
        'b' => $rgb_values['B']
    );

    $rgbvalues[$iterator] = $temp_array;
    $iterator++;
}

echo json_encode($rgbvalues);

/*
    echo"<table>";
    echo"<thead>";
    echo"<tr>";
    echo"<th> Filename </th>";
    echo"<th> MC </th>";
    echo"<th> R </th>";
    echo"<th> G </th>";
    echo"<th> B </th>";
    echo"</tr>";

    echo"</thead>";
    echo"<tbody>";



    foreach($rgbvalues as $mc){
        echo"<tr>";
        echo"<td>".$mc['image']." &nbsp; </td>";
        echo"<td>".$mc['mc']." &nbsp;</td>";
        echo"<td>".$mc['r']." &nbsp;</td>";
        echo"<td>".$mc['g']." &nbsp;</td>";
        echo"<td>".$mc['b']." &nbsp;</td>";

        echo"</tr>";

    }

    echo"</tbody>";
    */

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

/**
 * returns the pearson correlation coefficient (least squares best fit line)
 * 
 * @param array $x array of all x vals
 * @param array $y array of all y vals
 */

function pearson(array $x, array $y)
{
    // number of values
    $n = count($x);
    $keys = array_keys(array_intersect_key($x, $y));

    // get all needed values as we step through the common keys
    $x_sum = 0;
    $y_sum = 0;
    $x_sum_sq = 0;
    $y_sum_sq = 0;
    $prod_sum = 0;
    foreach ($keys as $k) {
        $x_sum += $x[$k];
        $y_sum += $y[$k];
        $x_sum_sq += pow($x[$k], 2);
        $y_sum_sq += pow($y[$k], 2);
        $prod_sum += $x[$k] * $y[$k];
    }

    $numerator = $prod_sum - ($x_sum * $y_sum / $n);
    $denominator = sqrt(($x_sum_sq - pow($x_sum, 2) / $n) * ($y_sum_sq - pow($y_sum, 2) / $n));

    return $denominator == 0 ? 0 : $numerator / $denominator;
}
