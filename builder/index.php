<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>request builder</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }
        html {

        }
        body {
            background-color: #EEE;
        }
        h1 {
            color: #777;
            font-size: 28px;
            margin-bottom: 15px;
        }
        #top {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100px;
            background-color: #394C81;
        }
        #top span {
            position: absolute;
            top: 40px;
            left: 120px;
            font-size: 18px;
            color: #FFF;
        }
        #top select {
            position: absolute;
            top: 60px;
            left: 425px;
            width: 250px;
            height: 30px;
            background-color: #EEE;
        }
        #content {
            position: absolute;
            top: 150px;
            left: 45px;
            right: 45px;
            background-color: #FEFEFE;
            padding: 15px;
            border: 1px solid #CCC;
            border-radius: 5px;
            text-align: center;
        }
        #content input {
            width: 125px;
            padding: 4px 7px 4px 7px;
            border: 1px solid #CCC;
        }
        #content textarea {
            padding: 4px 7px 4px 7px;
            border: 1px solid #CCC;
        }
    </style>
</head>
<body>
    <div id="top">
        <span>TrogonCalao request builder</span>
        <select name="" onchange="window.location = '?module=' + this.value;">
            <option value="#">----</option>
            <option value="home">home</option>
            <option value="get">get a data</option>
            <option value="set">set a data</option>
        </select>
    </div>
    <div id="content">
        <?php
            if(isset($_GET['module']) AND in_array($_GET['module'], array('get','set'))) {
                include($_GET['module'].'.php');
            }
            else{
                echo "choose on the menu below";
            }
        ?>
    </div>
</body>
</html>