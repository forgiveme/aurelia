<?php
include("./config/db.php");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Aurelia Admin Area</title>
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <style>
    html,
    body {
        background-image: url(views/img/bg.jpg);
        width: 100%;
        height: 100%;
    }
    
    input.pure-button.pure-input-1-2.pure-button-primary {
        background-color: pink;
    }
    
    #login_input_username,
    #login_input_password,
    #login_input_button {
        margin-left: auto;
        margin-right: auto;
        width: 400px;
    }
    
    img {
        display: block;
        margin-left: auto;
        margin-right: auto;
        margin-bottom: 30px;
        margin-top: 50px;
    }
    
    #logoutbutton {
        background: pink;
        border-radius: 0;
        position: absolute;
        color: white;
        right: 0;
        border-radius: 6px;
    }
    td{
        text-align: right;
    }
    .row{
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
    }
    .col-md-4 {
        width: 100%;
    }
    @media print {
        .printable{
            margin-top: 120px;
        }
        .printable_2{
            margin-top: 160px;
        }
    }
    </style>
</head>

<body>
    <a id="logoutbutton" class="btn" href="index.php?logout">Logout</a>
    <div class="container">
            <div class="row">
                <div class="col-md-4" style="border-bottom:5px solid pink">
                    <h2>Total Users: <span>
                            <?php

                            $sql = "SELECT COUNT(id) FROM skintools_questionsdata limit 1";
                            $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    // output data of each row
                                    while ($row = $result->fetch_assoc()) {

                                        echo $row["COUNT(id)"];
                                    }
                                }
                            ?>
                            </span>
                    </h2>
                </div>
                <div class="col-md-4">
                    <h2>Question 1</h2>
                    <p>How does it all begin? (Select up to three)</p>
                    <table class="table table-striped">
                        <tbody>
                        <?php

                            $sql    = "SELECT question2 FROM skintools_questionsdata WHERE id > 6";
                            $result = $conn->query($sql);
                            $row_2 = "";

                            $a = 0;
                            $b = 0;
                            $c = 0;
                            $d = 0;
                            $e = 0;
                            $f = 0;
                            $g = 0;

                            if ($result->num_rows > 0) {
                                // output data of each row
                                while ($row = $result->fetch_assoc()) {

                                    $row_2 = $row["question2"];

                                    $a += substr_count($row_2, 'house_work');
                                    $b += substr_count($row_2, 'shower');
                                    $c += substr_count($row_2, 'excercise');
                                    $d += substr_count($row_2, 'children');
                                    $e += substr_count($row_2, 'breakfast');
                                    $f += substr_count($row_2, 'social_media');
                                    $g += substr_count($row_2, 'coffee');

                                }
                                        echo "<tr>\n";
                                        echo "<th>Housework</th>\n";
                                        echo "<td>" . $a . "</td>\n";
                                        echo "</tr>\n";
                                        echo "<tr>\n";
                                        echo "<th>Shower</th>\n";
                                        echo "<td>" . $b . "</td>\n";
                                        echo "</tr>\n";
                                        echo "<tr>\n";
                                        echo "<th>Exercise</th>\n";
                                        echo "<td>" . $c . "</td>\n";
                                        echo "</tr>\n";
                                        echo "<tr>\n";
                                        echo "<th>Children</th>\n";
                                        echo "<td>" . $d . "</td>\n";
                                        echo "</tr>\n";
                                        echo "<tr>\n";
                                        echo "<th>Breakfast</th>\n";
                                        echo "<td>" . $e . "</td>\n";
                                        echo "</tr>\n";
                                        echo "<tr>\n";
                                        echo "<th>Social media</th>\n";
                                        echo "<td>" . $f . "</td>\n";
                                        echo "</tr>\n";
                                        echo "<tr>\n";
                                        echo "<th>Coffee on the run</th>\n";
                                        echo "<td>" . $g . "</td>\n";
                                        echo "</tr>\n";

                            }else{
                                echo "<h4>" . $nodata . "</h4>";
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-4">
                    <h2>Question 2</h2>
                    <p>Most mornings I'm feeling...</p>
                    <table class="table table-striped">
                        <tbody>
                           <?php

                        $sql    = "SELECT question3 FROM skintools_questionsdata";
                        $result = $conn->query($sql);

                        $r1 = 0;
                        $r2 = 0;
                        $r3 = 0;

                        if ($result->num_rows > 0) {
                            // output data of each row
                            while ($row = $result->fetch_assoc()) {
                                // echo "<tr>\n";
                                $n = (int) $row["question3"];
                                
                                if ($n == 1) {
                                    $r1++;
                                }
                                if ($n == 2) {
                                    $r2++;
                                }
                                if ($n == 3) {
                                    $r3++;
                                }
                            }

                            // echo "<tr>\n";
                            echo "<tr>\n";
                            echo "<th>Ready for life</th>\n";
                            echo "<td>" . $r3 . " </td>\n";
                            echo "</tr>\n";
                            echo "<tr>\n";
                            echo "<th>Somewhere in the middle</th>\n";
                            echo "<td>" . $r2 . " </td>\n";
                            echo "</tr>\n";
                            echo "<tr>\n";
                            echo "<th>Ready for bed</th>\n";
                            echo "<td>" . $r1 . " </td>\n";
                            echo "</tr>\n";
                        }else{
                            echo "<h4>" . $nodata . "</h4>";
                        }

                        ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-4">
                    <h2>Question 3</h2>
                    <p>What is your morning skincare regime?</p>
                    <table class="table table-striped">
                        <tbody>
                        <?php

                        $sql    = "SELECT question4, count(*) FROM skintools_questionsdata GROUP BY question4 ORDER BY count(*) DESC";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            // output data of each row
                            while ($row = $result->fetch_assoc()) {
                                $n      = $row["question4"];
                                $regime = substr($n, -1, strlen($n));
                                 if ($n != ""){
                         
                                    echo "<tr>\n";
                                    if ($regime == 1) {
                                        echo "<th>I am yet to start one</th>\n";
                                    }
                                    if ($regime == 2) {
                                        echo "<th>Simple and quick</th>\n";
                                    }
                                    if ($regime == 3) {
                                        echo "<th>My tried & tested - cleanse, tone, moisturise</th>\n";
                                    }
                                    if ($regime == 4) {
                                        echo "<th>The complete routine</th>\n";
                                    }
                                    echo "<td>" . (int) $row["count(*)"] . "</td>\n";
                                    echo "</tr>\n";
                                }
                            }
                        }else{
                            echo "<h4>" . $nodata . "</h4>";
                        }

                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <h2 class="printable">Question 4</h2>
                    <p>I'm usually happy with the results</p>
                    <table class="table table-striped">
                        <tbody>
                            <?php

                            $sql    = "SELECT question5, count(*) FROM skintools_questionsdata GROUP BY question5 ORDER BY count(*) DESC";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                // output data of each row
                                while ($row = $result->fetch_assoc()) {
                                    $n = (string) $row["question5"];
                                    if ($n != ""){
                                        echo "<tr>\n";
                                        if ($n == "sometimes") {
                                            echo "<th>Sometimes</th>\n";
                                        }
                                        if ($n == "never") {
                                            echo "<th>Never</th>\n";
                                        }
                                        if ($n == "always") {
                                            echo "<th>Always</th>\n";
                                        }
                                        echo "<td>" . (int) $row["count(*)"] . "</td>\n";
                                        echo "</tr>\n";
                                    }
                                }
                            }else{
                                echo "<h4>" . $nodata . "</h4>";
                            }

                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-4">
                    <h2 >Question 5</h2>
                    <p>Show us what you don't love?</p>
                    <table class="table table-striped">
                        <tbody>
                            <?php

                            $sql    = "SELECT question6, count(*) FROM skintools_questionsdata GROUP BY question6 ORDER BY count(*) DESC";
                            $result = $conn->query($sql);
                            $arr;
                            if ($result->num_rows > 0) {
                                // output data of each row
                                while ($row = $result->fetch_assoc()) {
                                    $n = (string) $row["question6"];
                                   

                                    if (strlen($row["question6"]) > 5){
                    
                                        $arr .= $row["question6"];
                                    }
                                }
                               
                                
                                echo "<tr>\n";
                                echo "<th>Shine</th>\n";
                                echo "<td class=\"num\">" . substr_count($arr, "Shine") . "</td>\n";
                                echo "</tr>\n";
                                echo "<tr>\n";
                                echo "<th>Dry or flaky patches</th>\n";
                                echo "<td class=\"num\">" . substr_count($arr, "Dry or flaky patches") . "</td>\n";
                                echo "</tr>\n";
                                echo "<tr>\n";
                                echo "<th>Fine lines</th>\n";
                                echo "<td class=\"num\">" . substr_count($arr, "Fine lines") . "</td>\n";
                                echo "</tr>\n";
                                echo "<tr>\n";
                                echo "<th>Redness &amp; broken capillaries</th>\n";
                                echo "<td class=\"num\">" . substr_count($arr, "Redness and broken capillaries") . "</td>\n";
                                echo "</tr>\n";
                                echo "<tr>\n";
                                echo "<th>Mild to moderate breakouts or spots</th>\n";
                                echo "<td class=\"num\">" . substr_count($arr, "Mild to moderate breakouts or spots") . "</td>\n";
                                echo "</tr>\n";
                                echo "<tr>\n";
                                echo "<th>Sensitivity</th>\n";
                                echo "<td class=\"num\">" . substr_count($arr, "Sensitivity") . "</td>\n";
                                echo "</tr>\n";
                                echo "<tr>\n";
                                echo "<th>Deeper wrinkles</th>\n";
                                echo "<td class=\"num\">" . substr_count($arr, "Deeper wrinkles") . "</td>\n";
                                echo "</tr>\n";
                                echo "<tr>\n";
                                echo "<th>Sun damage / brown spots</th>\n";
                                echo "<td class=\"num\">" . substr_count($arr, "Sun damage / brown spots") . "</td>\n";
                                echo "</tr>\n";
                                 echo "<th>Large pores</th>\n";
                                echo "<td class=\"num\">" . substr_count($arr, "Large pores") . "</td>\n";
                                echo "</tr>\n";
                            }else{
                                echo "<h4>" . $nodata . "</h4>";
                            }
                           
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-4">
                    <h2>Question 6</h2>
                    <p>I'd love to look...</p>
                    <table class="table table-striped">
                        <tbody>
                            <?php

                            $sql    = "SELECT question7, count(*) FROM skintools_questionsdata GROUP BY question7 ORDER BY count(*) DESC";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                // output data of each row
                                while ($row = $result->fetch_assoc()) {

                                    $n = (string) $row["question7"];
                                    if ($n != ""){
                                        
                                        echo "<tr>\n";
                                        if ($n == "thebest") {
                                            echo "<th>The best I can for my age</th>\n";
                                        }
                                        if ($n == "fresh") {
                                            echo "<th>Fresh-faced and glowing</th>\n";
                                        }
                                        if ($n == "natural") {
                                            echo "<th>Natural and healthy</th>\n";
                                        }
                                        if ($n == "asyoung") {
                                            echo "<th>As young as possible</th>\n";
                                        }
                                        echo "<td>" . (int) $row["count(*)"] . "</td>\n";
                                        echo "</tr>\n";
                                    }
                                }
                            }else{
                                echo "<h4>" . $nodata . "</h4>";
                            }

                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <h2 class="printable">Question 7</h2>
                    <p>How old I think I look on a good day?</p>
                    <table class="table table-striped">
                        <tbody>
                            <?php

                            $sql = "SELECT question8 FROM skintools_questionsdata";
                            $result = $conn->query($sql);

                            $Teen = 0;
                            $Twentie = 0;
                            $Thirties = 0;
                            $Fortie = 0;
                            $Older = 0;

                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    $n = (int) $row["question8"];
                                    if($n == 0){
                                        $Teen++;
                                    }
                                    if($n == 1){
                                        $Twentie++;
                                    }

                                    if($n == 2){
                                        $Thirties++;
                                    }

                                    if($n == 3){
                                        $Fortie++;
                                    }

                                    if($n == 4){
                                        $Older++;
                                    }
                                }

                                echo "<tr>\n";
                                echo "<th>Teens</th>\n";
                                echo "<td>" . $Teen . " </td>\n";
                                echo "</tr>\n";

                                echo "<tr>\n";
                                echo "<th>Twenties</th>\n";
                                echo "<td>" . $Twentie . " </td>\n";
                                echo "</tr>\n";

                                echo "<tr>\n";
                                echo "<th>Thirties</th>\n";
                                echo "<td>" . $Thirties . " </td>\n";
                                echo "</tr>\n";

                                echo "<tr>\n";
                                echo "<th>Forties</th>\n";
                                echo "<td>" . $Fortie . " </td>\n";
                                echo "</tr>\n";

                                echo "<tr>\n";
                                echo "<th>Older & Wiser</th>\n";
                                echo "<td>" . $Older . " </td>\n";
                                echo "</tr>\n";

                            }else{
                                echo "<h4>" . $nodata . "</h4>";
                            }

                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-4">
                    <h2 >Question 8</h2>
                    <p>How hydrated are you?</p>
                    <table class="table table-striped">
                        <tbody>
                            <?php

                            $sql    = "SELECT question9 FROM skintools_questionsdata";
                            $result = $conn->query($sql);

                            $r1 = 0;
                            $r2 = 0;
                            $r3 = 0;

                            if ($result->num_rows > 0) {
                                // output data of each row
                                while ($row = $result->fetch_assoc()) {
                                    $n = (int) $row["question9"];
                                    if ($n >= 0 && $n < 11) {
                                        $r1++;
                                    }
                                    if ($n >= 11 && $n < 22) {
                                        $r2++;
                                    }
                                    if ($n >= 22) {
                                        $r3++;
                                    }
                                }
                                echo "<tr>\n";
                                echo "<th>Not so hydrated</th>\n";
                                echo "<td>" . $r1 . " </td>\n";
                                echo "</tr>\n";
                                echo "<tr>\n";
                                echo "<th>Somewhere in the middle</th>\n";
                                echo "<td>" . $r2  . " </td>\n";
                                echo "</tr>\n";
                                echo "<tr>\n";
                                echo "<th>Very hydrated</th>\n";
                                echo "<td>" . $r3 . " </td>\n";
                                echo "</tr>\n";
                            }else{
                                echo "<h4>" . $nodata . "</h4>";
                            }


                            function t1($val, $min, $max)
                            {
                                return ($val >= $min && $val <= $max);
                            }

                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-4">
                    <h2>Question 9</h2>
                    <p>At 5.00pm where are you most likely to be?</p>
                    <table class="table table-striped">
                        <tbody>
                            <?php

                            $sql    = "SELECT question10, count(*) FROM skintools_questionsdata GROUP BY question10 ORDER BY count(*) DESC";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                // output data of each row
                                while ($row = $result->fetch_assoc()) {
                                    $n = (string) $row["question10"];
                                    
                                    if ($n != ""){
                                        
                                        echo "<tr>\n";
                                        if ($n == "children_2") {
                                            echo "<th>With my children</th>\n";
                                        }
                                        if ($n == "shopping") {
                                            echo "<th>Busy street / shopping</th>\n";
                                        }
                                        if ($n == "relaxing") {
                                            echo "<th>Relaxing</th>\n";
                                        }
                                        if ($n == "travelling") {
                                            echo "<th>Travelling</th>\n";
                                        }
                                        if ($n == "work") {
                                            echo "<th>Work</th>\n";
                                        }
                                        if ($n == "somethingelse") {
                                            echo "<th>Something else</th>\n";
                                        }
                                        echo "<td>" . (int) $row["count(*)"] . "</td>\n"; 
                                        echo "</tr>\n";
                                    }
                                   
                                }
                            }else{
                                echo "<h4>" . $nodata . "</h4>";
                            }

                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <h2 class="printable_2">Question 10</h2>
                    <p>My stress levels are...</p>
                    <table class="table table-striped">
                        <tbody>
                            <?php
                            $sql    = "SELECT question11 FROM skintools_questionsdata";
                            $result = $conn->query($sql);

                            $r1 = 0;
                            $r2 = 0;
                            $r3 = 0;

                            if ($result->num_rows > 0) {
                                // output data of each row
                                while ($row = $result->fetch_assoc()) {

                                    if ((int) $row["question11"] != -1){
                
                                        $n = (int)$row["question11"];
                                        if ($n >= 0 && $n < 33) {
                                            $r1++;
                                        }
                                        if ($n >= 33 && $n < 66) {
                                            $r2++;
                                        }
                                        if ($n >= 66) {
                                            $r3++;
                                        }
                                    }
                                }
                                echo "<tr>\n";
                                echo "<th>Stressed</th>\n";
                                echo "<td>" . $r1 . " </td>\n";
                                echo "</tr>\n";
                                echo "<tr>\n";
                                echo "<th>Somewhere in the middle</th>\n";
                                echo "<td>" . $r2 . " </td>\n";
                                echo "</tr>\n";
                                echo "<tr>\n";
                                echo "<th>Relaxed</th>\n";
                                echo "<td>" . $r3 . " </td>\n";
                                echo "</tr>\n";
                            }else{
                                echo "<h4>" . $nodata . "</h4>";
                            }

                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-4">
                    <h2>Question 11</h2>
                    <p>If we took a peek in your beauty bag, what skincare would we find?<br /> (Pick as many as apply)</p>
                    <table class="table table-striped">
                        <tbody>
                            <?php
                            $sql    = "SELECT question12, count(*) FROM skintools_questionsdata GROUP BY question12 ORDER BY count(*) DESC";
                            $result = $conn->query($sql);
                            $arr    = "";
                            if ($result->num_rows > 0) {
                                // output data of each row
                                while ($row = $result->fetch_assoc()) {
                                    $n = (string) $row["question12"];
                                    $arr .= $n;
                                }

                                $arr = substr($arr, 0, -1);
                                echo "<tr>\n";
                                echo "<th>All the basics</th>\n";
                                echo "<td class=\"num\">" . substr_count($arr, "bag1") . "</td>\n";
                                echo "</tr>\n";
                                echo "<tr>\n";
                                echo "<th>Ethical & fair trade</th>\n";
                                echo "<td class=\"num\">" . substr_count($arr, "bag2") . "</td>\n";
                                echo "</tr>\n";
                                echo "<tr>\n";
                                echo "<th>Organic / BioOrganic</th>\n";
                                echo "<td class=\"num\">" . substr_count($arr, "bag3") . "</td>\n";
                                echo "</tr>\n";
                                echo "<tr>\n";
                                echo "<th>Anti-ageing / New Science</th>\n";
                                echo "<td class=\"num\">" . substr_count($arr, "bag4") . "</td>\n";
                                echo "</tr>\n";
                                echo "<tr>\n";
                                echo "<th>The latest beauty recommendations</th>\n";
                                echo "<td class=\"num\">" . substr_count($arr, "bag5") . "</td>\n";
                                echo "</tr>\n";
                                echo "<tr>\n";
                                echo "<th>My go to classics</th>\n";
                                echo "<td class=\"num\">" . substr_count($arr, "bag6") . "</td>\n";
                                echo "</tr>\n";
                                echo "<tr>\n";
                                echo "<th>Mostly one brand, but all the products</th>\n";
                                echo "<td class=\"num\">" . substr_count($arr, "bag7") . "</td>\n";
                                echo "</tr>\n";
                            }else{
                                echo "<h4>" . $nodata . "</h4>";
                            }

                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-4">
                    <h2>Question 12</h2>
                    <p>What matters most?</p>
                    <table class="table table-striped">
                        <tbody>
                            <?php

                            $sql    = "SELECT question13, count(*) FROM skintools_questionsdata GROUP BY question13 ORDER BY count(*) DESC";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                // output data of each row
                                while ($row = $result->fetch_assoc()) {
                                    $n = (string) $row["question13"];
                                    echo "<tr>\n";
                                    if ($n == "trust") {
                                        echo "<th>I choose my beauty products on the basis of their ingredients</th>\n";
                                        echo "<td>" . (int) $row["count(*)"] . "</td>\n";
                                    }
                                    if ($n == "ingredients") {
                                        echo "<th>There are some ingredients I avoid, but not all the time</th>\n";
                                        echo "<td>" . (int) $row["count(*)"] . "</td>\n";
                                    }
                                    if ($n == "beauty") {
                                        echo "<th style=\"background-color: white;\"> I trust the brand to make the right choices about ingredients</th>\n";
                                        echo "<td style=\"background-color: white;\">" . (int) $row["count(*)"] . "</td>\n";
                                    }
                                    echo "</tr>\n";
                                }
                            }else{
                                echo "<h4>" . $nodata . "</h4>";
                            }

                            ?>
                        </tbody>
                    </table>
                </div>
             </div>
            <div class="row">
                <div class="col-md-4">
                    <h2>Question 14</h2>
                    <p>...and how does your day end?</p>
                    <table class="table table-striped">
                        <tbody>
                            <?php

                            $sql    = "SELECT question14, count(*) FROM skintools_questionsdata GROUP BY question14 ORDER BY count(*) DESC";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                // output data of each row
                                while ($row = $result->fetch_assoc()) {

                                    $n = (string) $row["question14"];
                                    echo "<tr>\n";
                                    if ($n == "awake") {
                                        echo "<th>Wide awake at night </th>\n";
                                    }
                                    if ($n == "sleep") {
                                        echo "<th>Fast asleep at night</th>\n";
                                    }
                                    if ($n == "wine") {
                                        echo "<th>Wine and moonlight</th>\n";
                                    }
                                    if($n != "null"){
                                        echo "<td>" . (int) $row["count(*)"] . "</td>\n";
                                    }
                                    echo "</tr>\n";
                                }
                            }else{
                                echo "<h4>" . $nodata . "</h4>";
                            }

                            ?>
                        </tbody>
                    </table>
                </div>
                 <div class="col-md-4">
                    <h2>Question 15</h2>
                    <p>How do you feel at the end of the day?</p>
                    <table class="table table-striped">
                        <tbody>
                            <?php

                            $sql    = "SELECT question15 FROM skintools_questionsdata";
                            $result = $conn->query($sql);

                            $r1 = 0;
                            $r2 = 0;

                            if ($result->num_rows > 0) {
                                // output data of each row
                                while ($row = $result->fetch_assoc()) {

                                    if ((int) $row["question15"] != -1){
                                        
                                        $n = (int) $row["question15"];                                    
                                        if ($n < 50) {
                                            $r1++;
                                        }
                                        if ($n >= 50) {
                                            $r2++;
                                        }
                                    }

                                }

                                echo "<tr>\n";
                                echo "<th>A little drained</th>\n";
                                echo "<td>" . $r1 . " </td>\n";
                                echo "</tr>\n";
                                echo "<tr>\n";
                                echo "<th>Roll on tomorrow!</th>\n";
                                echo "<td>" . $r2 . " </td>\n";
                                echo "</tr>\n";

                            }else{
                                echo "<h4>" . $nodata . "</h4>";
                            }

                            ?>
                        </tbody>
                    </table>
                </div>
             </div>
        </div>
    <!-- <img src="adminlogo.png"> -->
    <!-- <a href="register.php">Register new account</a> -->

</body>

</html>

<?php

$conn->close();
    ?>