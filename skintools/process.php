<?php
include("admin/config/db.php");
// post data
$q1  = $_GET['question1'];
$q2  = $_GET['question2'];
$q3  = $_GET['question3'];
$q4  = $_GET['question4'];
$q5  = $_GET['question5'];
$q6  = $_GET['question6'];
$q7  = $_GET['question7'];
$q8  = $_GET['question8'];
$q9  = $_GET['question9'];
$q10 = $_GET['question10'];
$q11 = $_GET['question11'];
$q12 = $_GET['question12'];
$q13 = $_GET['question13'];
$q14 = $_GET['question14'];
$q15 = $_GET['question15'];

// prepare and bind
$stmt = $conn->prepare("INSERT INTO skintools_questionsdata (question1, question2, question3, question4, question5, question6, question7, question8, question9, question10, question11, question12, question13, question14, question15, request_uri) 
		VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssssssssssss", $question1, $question2, $question3, $question4, $question5, $question6, $question7, $question8, $question9, $question10, $question11, $question12, $question13, $question14, $question15, $request_uri);

// set parameters and execute
$question1 = $q1; 
$question2 = $q2;
$question3 = $q3;
$question4 = $q4;
$question5 = $q5;
$question6 = $q6;
$question7 = $q7;
$question8 = $q8;
$question9 = $q9;
$question10 = $q10;
$question11 = $q11;
$question12 = $q12;
$question13 = $q13;
$question14 = $q14;
$question15 = $q15;

$top3 = explode(",", $q6);

$request_uri  = "?q1=" . $q1 . "&q2=" . $q2 . "&q3=" . $q3 . "&q4=". $q4. "&q5=". $q5 . "&q6=" . $top3[0] . ","  . $top3[1] . ","  . $top3[2] . "&q7=". $q7 . "&q8=". $q8 . "&q9=". $q9 . "&q10=". $q10 . "&q11=". $q11 . "&q12=". $q12 . "&q13=". $q13 . "&q14=". $q14 . "&q15=". $q15;


$stmt->execute();

$insertId = mysqli_insert_id($conn);

// New records created successfully
// $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
// $url_data = substr($actual_link, strpos($actual_link, "?") + 1);

// if ( strlen ($q6) > 1 ){
// 	$q6 = 1;
// }

$top3 = explode(",", $q6);

$url_data  = "?insid=". $insertId . "&q1=" . $q1 . "&q2=" . $q2 . "&q3=" . $q3 . "&q4=". $q4. "&q5=". $q5 . "&q6=" . $top3[0] . ","  . $top3[1] . ","  . $top3[2] . "&q7=". $q7 . "&q8=". $q8 . "&q9=". $q9 . "&q10=". $q10 . "&q11=". $q11 . "&q12=". $q12 . "&q13=". $q13 . "&q14=". $q14 . "&q15=". $q15;

header('Location: response.html' . $url_data);


$stmt->close();
$conn->close();

?>