<?php 
$scriptPath = $_SERVER["SCRIPT_NAME"];

switch ($scriptPath) {
	case '/index.php':
		echo '<title>Getfaces</title>';
		break;
	case '/donate.php':
		echo '<title>Donate</title>';
		break;
	case '/stat.php':
		echo '<title>Statistic</title>';
		break;
	case '/about.php':
		echo '<title>About project</title>';
		break;
	default:
		echo '<title>Please set title in main template</title>';
		break;
}
?>
<!-- param for mob devices -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/web/css/fonts.css">
<link rel="stylesheet" href="/web/css/animate.css">
<link rel="stylesheet" href="/web/css/icomoon.css">
<link rel="stylesheet" href="/web/css/bootstrap.css">
<link rel="stylesheet" href="/web/css/superfish.css">

<link type="text/css" rel="stylesheet" href="/web/js/slider/css/lightslider.css" />   
<link rel="stylesheet" href="/web/css/style.css">
<link rel="shortcut icon" href="/web/img/light-blue-background-abstract-design-260nw-121557973.jpg">
<link rel="icon" href="/web/img/favicon.png">

<script src="/web/js/jquery.min.js"></script>
<script src="/web/js/modernizr-2.6.2.min.js"></script>
<script src="/web/js/jquery.easing.1.3.js"></script>
<script src="/web/js/bootstrap.min.js"></script>
<script src="/web/js/jquery.waypoints.min.js"></script>
<script src="/web/js/hoverIntent.js"></script>
<script src="/web/js/superfish.js"></script>
<script src="/web/js/main.js"></script>
<script src="/web/js/jsapi.js"></script>
<script src="/web/js/slider/js/lightslider.js"></script>