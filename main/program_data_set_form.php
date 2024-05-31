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
    //$csvFile = file('datasets/data_set/data_set.csv');

    $filepath = glob('datasets/data_set/data_set.*');
    //print_r($filepath);
    $csvFile = file($filepath[0]);
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
            $dn_results = get_hsv_values($mc_values_data);
            break;

        case 3:
            $dn_results = get_YCbCr_values($mc_values_data);
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

function display_dataset_checkboxes_rgb($dataset)
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


function display_dataset_checkboxes_hsv($dataset)
{
    $iterator = 1;
    foreach ($dataset as $data) {
        $image = $data['image'];
        $h = $data['H'];
        $s = $data['S'];
        $v = $data['V'];
        $mc = $data['mc'];
        //$input_name = $image . "_set_type";
        $input_name = "image_set_type[$iterator]";
        echo "<tr> <td> $image &nbsp;&nbsp; </td> <td> $h &nbsp; &nbsp; </td> <td> $s &nbsp; &nbsp; </td> <td> $v &nbsp; &nbsp; </td> <td> $mc &nbsp; &nbsp;</td> <td> <input type='radio' class='m-auto' value='training_set' name='$input_name'></td> <td> <input type='radio' value='testing_set' class='m-auto' name='$input_name'></td></tr>";
        $iterator++;
    }
}

function display_dataset_checkboxes_YCbCr($dataset)
{
    $iterator = 1;
    foreach ($dataset as $data) {
        $image = $data['image'];
        $Y = $data['Y'];
        $Cb = $data['Cb'];
        $Cr = $data['Cr'];
        $mc = $data['mc'];
        //$input_name = $image . "_set_type";
        $input_name = "image_set_type[$iterator]";
        echo "<tr> <td> $image &nbsp;&nbsp; </td> <td> $Y &nbsp; &nbsp; </td> <td> $Cb &nbsp; &nbsp; </td> <td> $Cr &nbsp; &nbsp; </td> <td> $mc &nbsp; &nbsp;</td> <td> <input type='radio' class='m-auto' value='training_set' name='$input_name'></td> <td> <input type='radio' value='testing_set' class='m-auto' name='$input_name'></td></tr>";
        $iterator++;
    }
}


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

function get_hsv_values($mc_values_data)
{
    $iterator = 1;
    $training_set_limit = count($mc_values_data) - 1;
    $hsv_values = array();
    //print_r($mc_values_data);
    for ($i = 1; $i <= $training_set_limit; $i++) {

        $image_url = "images/" . $mc_values_data[$i][0];
        $hsv_result = get_mean_of_hsv($image_url);

        $temp_array = array(
            'image' => $mc_values_data[$i][0],
            'mc' => $mc_values_data[$i][1],
            'H' =>  $hsv_result['H'],
            'S' =>  $hsv_result['S'],
            'V' => $hsv_result['V']
        );
        $hsv_values[$iterator] = $temp_array;
        $iterator++;
    }

    return $hsv_values;
}


function get_YCbCr_values($mc_values_data)
{
    $iterator = 1;
    $training_set_limit = count($mc_values_data) - 1;
    $YCbCr_values = array();
    //print_r($mc_values_data);
    for ($i = 1; $i <= $training_set_limit; $i++) {

        $image_url = "images/" . $mc_values_data[$i][0];
        $hsv_result = get_mean_of_YCbCr($image_url);

        $temp_array = array(
            'image' => $mc_values_data[$i][0],
            'mc' => $mc_values_data[$i][1],
            'Y' =>  $hsv_result['Y'],
            'Cb' =>  $hsv_result['Cb'],
            'Cr' => $hsv_result['Cr']
        );
        $YCbCr_values[$iterator] = $temp_array;
        $iterator++;
    }

    return $YCbCr_values;
}


function get_mean_of_YCbCr($image_url)
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

            $hsv = RGBToYCbCr($r, $g, $b);

            $Y[$x] = $hsv['Y'];
            $Cb[$x] = $hsv['Cb'];
            $Cr[$x] = $hsv['Cr'];
        }
    }


    $average_Y = array_sum($Y) / count($Y);

    $average_Cb = array_sum($Cb) / count($Cb);

    $average_Cr = array_sum($Cr) / count($Cr);

    return array('Y' => $average_Y, 'Cb' => $average_Cb, 'Cr' => $average_Cr);
}




function RGBToYCbCr($r, $g, $b)
{
    $fr = (float)$r / 255;
    $fg = (float)$g / 255;
    $fb = (float)$b / 255;


    $Y = (float)(0.2989 * $fr + 0.5866 * $fg + 0.1145 * $fb);
    $Cb = (float)(-0.1687 * $fr - 0.3313 * $fg + 0.5000 * $fb);
    $Cr = (float)(0.5000 * $fr - 0.4184 * $fg - 0.0816 * $fb);

    return array('Y' => $Y, 'Cb' => $Cb, 'Cr' => $Cr);
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
                <input type="hidden" name="color_space" value='<?php echo $color_space; ?>'>

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
                            <?php } else if ((int)$color_space === 3) { ?>
                                <td><b>Image</b></td>
                                <td><b>DN(Y)</b></td>
                                <td><b>DN(Cb)</b></td>
                                <td><b>DN(Cr)</b></td>
                                <td><b>MC</b></td>
                                <td><b>Training set</b> </td>
                                <td><b>Testing set </b> </td>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        $color_space = $_POST['color_space'];
                        switch ((int)$color_space) {
                            case 1:
                                display_dataset_checkboxes_rgb($dn_results);
                                break;

                            case 2:
                                display_dataset_checkboxes_hsv($dn_results);
                                break;

                            case 3:
                                display_dataset_checkboxes_YCbCr($dn_results);

                                break;
                        }


                        ?>
                    </tbody>

                </table>
                <input type="submit" value="Submit" class="btn btn-primary" style="float: right;">
            </form>

        </div>

    <?php } ?>

</body>

</html>