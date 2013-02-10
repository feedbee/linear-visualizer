<?php

require('LinearVisualizer.php');

/* == Значения для тестовой генерации == */
/* Начальное и конечное значения (val) в массиве данных */
$startValue = 0;
$endValue = 30000;
/* Количество элементов в массиве данных */
$count = 100;
/* Минимальное и максимальное количество (count) в массиве данных */
$startCount = 1;
$endCount = 100;

/* == Либо установка входного массива вручную */
/*$data = convertArray(array(
	'0' => array('val' => '2000', 'count' => '7'),
	'1' => array('val' => '2700', 'count' => '8'),
	'2' => array('val' => '2650', 'count' => '3'),
	'3' => array('val' => '2600'),
));*/

/* == Входные параметры == */
/* Ограничение по значениям (показаны будут только значения от-до) */
$showFrom = 10;
$showTo = 30000;
/* Ширина и высота графика */
$width = 300;
$height = 30;
/* Цвет фона */
$bgColor = 0xF0F0F0;
/* Цвет начала (первый цвет) градиента*/
$gradMinColor = 0xC7D8EA;
/* Цвет конца (последний цвет) градиента */
$gradMaxColor = 0x1A74CC;

/* Генерация массива данных для тестов, если не был установлен выше */
if (!isset($data)) {
	$data = array();
	$i = 0;
	while ($i < $count) {
		$data[rand($startValue, $endValue)] = rand($startCount, $endCount);
		$i++;
	}
}

/* Нарисовать и отобразить график */
LinearVisualizer::draw($data, $bgColor, $gradMinColor, $gradMaxColor, $showFrom, $showTo, $width, $height);


function convertArray(array $externalData)
{
	$internalData = array();
	foreach ($externalData as $row) {
		if (isset($row['count'])) {
			$internalData[$row['val']] = (int)$row['count'];
		}
		else {
			$internalData[$row['val']] = 1;
		}
	}

	return $internalData;
}