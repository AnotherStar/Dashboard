<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=1184">
	<title>Document</title>
	<link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:400,400italic,300,300italic,700,700italic&subset=cyrillic-ext,latin' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" type="text/less" href="style.less">
	<script src="js/jquery.js"></script>
    <script src="js/less.js"></script>
    <script src="js/dash.js"></script>
    <script src="js/paper.js"></script>

</head>
<body>
	<div id="dash_container">
		<canvas id="dash"></canvas>
		<div id="speed"></div>
		<div id="rpm"></div>
		<canvas id="temp"></canvas>
		<div id="timestamp"></div>
	</div>
	Тахометр: <input type="range" min="6" max="60" id="control_1">
	Спидометр: <input type="range" min="6" max="120" id="control_2">
</body>
</html>

<script>

var path;
var points;
var dash;
var tacho;

//Переменные тахометра
var tacho_sensor_length = 100;
var tacho_delay_array = new Array();
var tacho_impulse_old = 0;

//Переменные спидометра
var speed_sensor_length = 30;
var speed_delay_array = new Array();
var speed_impulse_old = 0;

$(document).ready(function() {

	$.plugin('Dashboard', Dashboard);

	$('#dash_container').Dashboard();

	dash = $('#dash_container').data('Dashboard');

	setInterval(function(){
		var temp = Math.round((90 + Math.random() * 20)*10)/10;

		//Мгновеная температура каждые 10 сек
		dash.sensorTemp(temp);

		dash.setSpeed(Math.round(temp));
	}, 1000);


	//onMessage
	setInterval(function(){
		loop_1();
	}, 25);

	setInterval(function(){
		loop_2();
	}, 500);

	generatorTacho();
	generatorSpeed();
	
});


function loop_1(){
	a = Appromixation(tacho_delay_array);
	dash.sensorTacho(a);
}

function loop_2(){
	b = Appromixation(speed_delay_array);
	dash.setSpeed(b);
}

function Appromixation(array){
	var min = array[0];
	var max = array[0];
	var array_summ = 0;
	for (var i=0; i<array.length; i++){
		if (max < array[i]){max = array[i]}
		if (min > array[i]){min = array[i]}
		array_summ = array_summ + array[i];
	}
	array_summ = array_summ - min - max;
	return array_summ/(array.length - 2);
}

function generatorTacho(){
	tacho_impulse_delay = Date.now() - tacho_impulse_old;
	tacho_impulse_old = Date.now();
	for (var i=0; i<tacho_sensor_length; i++){
		tacho_delay_array[i] = tacho_delay_array[i + 1];
	}
	tacho_delay_array[tacho_sensor_length - 1] = tacho_impulse_delay;
	setTimeout(function(){
		generatorTacho();
	}, parseInt($('#control_1').val()));
}

function generatorSpeed(){
	speed_impulse_delay = Date.now() - speed_impulse_old;
	speed_impulse_old = Date.now();
	for (var i=0; i<speed_sensor_length; i++){
		speed_delay_array[i] = speed_delay_array[i + 1];
	}
	speed_delay_array[speed_sensor_length - 1] = speed_impulse_delay;
	setTimeout(function(){
		generatorSpeed();
	}, parseInt($('#control_2').val()));
}

</script>

