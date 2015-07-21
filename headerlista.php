<?php
function __autoload($class_name) {
require_once $class_name . '.php';
}


$database=new ConnectDatabase("localhost","root","aicon07","fantacalcio",3306);


?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>La Listona del Fanta Zenit</title>
        <link href="css/ion.rangeSlider.css" rel="stylesheet" />
        <link href="css/normalize.min.css" rel="stylesheet"/>
        <link href="css/ion.rangeSlider.skinFlat.css" rel="stylesheet" />
        <link href="style.css" rel="stylesheet" />
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
        <!--<link rel="stylesheet" href="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.css">-->
		<script src="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.js"></script>
        <script src="js/jquery-1.11.0.min.js"></script>
		<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
		<script src="js/ion.rangeSlider.min.js"></script>
		<script src="js/chart.js"></script>

		<script>
			var changecompetition=function(mo){
		        console.log(mo);
		        var id=mo.value;

		       var text='<form action="' + document.URL + '" method="post">';

		       text=text+'<input type="hidden" name="competition_change" value="'+id+'" />';


		        var form = $(text + '</form>');
		        console.log(form);

		        $('body').append(form);  // This line is not necessary
		        $(form).submit();
			};

			$(document).ready(function(){
				$(function() {
					$( "#datepicker" ).datepicker({ dateFormat: "dd-mm-yy" });
				});

			});

        </script>

    </head>
    <body>

        <div id="wrapper">
            <div id="header">

                <div id="logo"></div>
                <div id="menu-top">
	                <ul>
		                <li><a href="index.php">Home</a></li>
	                </ul>
                </div>
            </div>
            <!-- <div id="menu-settings"></div> -->

            <div id="content">