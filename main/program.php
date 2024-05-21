<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Document</title>
</head>

<body class="">

    <div class="m-auto card p-5 mt-5" style="width: fit-content;">
        <center>
            <form action="program_data_set_form.php" method="post">
                <label for="">Select a colorspace </label> <br />
                <select name="color_space" id="" class="mt-3 mb-3">
                    <option value="1">RGB</option>
                    <option value="2">HSV</option>
                    <option value="3">YCbCr</option>
                </select><br />
                <button type="submit" class="btn btn-primary m-auto">Submit</button>
            </form>
        </center>
    </div>

</body>

</html>