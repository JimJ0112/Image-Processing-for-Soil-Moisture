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

$hsv_values = array();
// ignore data[0] as it only contains table headers
for ($i = 1; $i <= $training_set_limit; $i++) {

    $image_url = "image_samples/" . $mc_values_data[$i][0];
    $hsv_result = get_mean_of_hsv($image_url);

    $temp_array = array(
        'image' => $mc_values_data[$i][0],
        'mc' => $mc_values_data[$i][1],
        'H' =>  $hsv_result['H'],
        'S' =>  $hsv_result['S'],
        'V' => $hsv_result['V']
    );
    $hsv_values[$i] = $temp_array;
}

echo json_encode($hsv_values);

/*
echo "<table>";
echo "<thead>";
echo "<tr>";
echo "<th> Filename </th>";
echo "<th> MC </th>";
echo "<th> H </th>";
echo "<th> S </th>";
echo "<th> V </th>";
echo "</tr>";

echo "</thead>";
echo "<tbody>";



foreach ($hsv_values as $mc) {
    echo "<tr>";
    echo "<td>" . $mc['image'] . " &nbsp; </td>";
    echo "<td>" . $mc['mc'] . " &nbsp;</td>";
    echo "<td>" . $mc['H'] . " &nbsp;</td>";
    echo "<td>" . $mc['S'] . " &nbsp;</td>";
    echo "<td>" . $mc['V'] . " &nbsp;</td>";

    echo "</tr>";
}

echo "</tbody>";
*/

function get_mean_of_hsv($image_url)
{

    $h = array();
    $s = array();
    $v = array();

    $image = imagecreatefromjpeg($image_url);
    $size   = getimagesize($image_url);
    $width  = $size[0];
    $height = $size[1];

    for ($x = 0; $x < $width; $x++) {
        for ($y = 0; $y < $height; $y++) {
            $rgb = imagecolorat($image, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;

            $hsv = RGBtoHSV($r, $g, $b);

            $h[$x] = $hsv['H'];
            $s[$x] = $hsv['S'];
            $v[$x] = $hsv['V'];
        }
    }


    $average_h = array_sum($h) / count($h);

    $average_s = array_sum($s) / count($s);

    $average_v = array_sum($v) / count($v);

    return array('H' => $average_h, 'S' => $average_s, 'V' => $average_v);
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

function rgb_to_hsv($R, $G, $B)  // RGB Values:Number 0-255
{                                 // HSV Results:Number 0-1
    $HSL = array();

    $var_R = ($R / 255);
    $var_G = ($G / 255);
    $var_B = ($B / 255);

    $var_Min = min($var_R, $var_G, $var_B);
    $var_Max = max($var_R, $var_G, $var_B);
    $del_Max = $var_Max - $var_Min;

    $V = $var_Max;

    if ($del_Max == 0) {
        $H = 0;
        $S = 0;
    } else {
        $S = $del_Max / $var_Max;

        $del_R = ((($var_Max - $var_R) / 6) + ($del_Max / 2)) / $del_Max;
        $del_G = ((($var_Max - $var_G) / 6) + ($del_Max / 2)) / $del_Max;
        $del_B = ((($var_Max - $var_B) / 6) + ($del_Max / 2)) / $del_Max;

        if ($var_R == $var_Max) $H = $del_B - $del_G;
        else if ($var_G == $var_Max) $H = (1 / 3) + $del_R - $del_B;
        else if ($var_B == $var_Max) $H = (2 / 3) + $del_G - $del_R;

        if ($H < 0) $H++;
        if ($H > 1) $H--;
    }

    $HSL['H'] = $H;
    $HSL['S'] = $S;
    $HSL['V'] = $V;

    return $HSL;
}

function RGBtoHSV($r, $g, $b)
{
    $r = ($r / 255);
    $g = ($g / 255);
    $b = ($b / 255);
    $maxRGB = max($r, $g, $b);
    $minRGB = min($r, $g, $b);
    $chroma = $maxRGB - $minRGB;
    if ($chroma == 0) return array('H' => 0, 'S' => 0, 'V' => $maxRGB);
    if ($r == $minRGB) $h = 3 - (($g - $b) / $chroma);
    elseif ($b == $minRGB) $h = 1 - (($r - $g) / $chroma);
    else $h = 5 - (($b - $r) / $chroma);

    $H = 60 * $h;
    $S = ($chroma / $maxRGB) * 100;
    $V = $maxRGB * 100;
    return array('H' => $H, 'S' => $S, 'V' => $V);
}
