<?php

/**
 * This code is written under BSD license.
 * Averybody can use for free with out any restrictions.
 * Copyright Valera Leontyev, 2013.
 */

class LinearVisualizer
{
	static function draw(array $values, $bgColor, $gradMinColor, $gradMaxColor,
						 $minValue, $maxValue, $width, $height)
	{
		$image = @imagecreate($width, $height);
		if (!$image) {
			throw new Exception("Cannot Initialize new GD image stream");
		}

		$backgroundColor = imagecolorallocate($image, self::getChannel($bgColor, self::CHANNEL_RED), 
													self::getChannel($bgColor, self::CHANNEL_GREEN),
													self::getChannel($bgColor, self::CHANNEL_BLUE));
		imagefill($image, 0, 0, $backgroundColor);

		self::fillData($image, $values, $gradMinColor, $gradMaxColor,
					   $minValue, $maxValue, $width, $height);

		header("Content-type: image/png");
		imagepng($image);
		imagedestroy($image);
	}

	static function fillData($image, array $values, $gradMinColor, $gradMaxColor,
						 $minValue, $maxValue, $width, $height)
	{
		ksort($values);

		$scale = $width / ($maxValue - $minValue);
		$scaledWidth = $scale < 1 ? 1 : round($scale);

		$greed = array();
		foreach ($values as $value => $count) {
			if ($value < $minValue || $value > $maxValue) {
				continue;
			}

			$x = round(($value - $minValue) * $scale);

			for ($i = $scaledWidth - 1; $i >= 0; $i--) {
				If (!isset($greed[$x + $i])) {
					$greed[$x + $i] = $count;
				} else {
					$greed[$x + $i] += $count;
				}
			}
		}

		if (count($greed) < 1) {
			throw new Exception('No data to display');
		}

		$minCount = min($greed);
		$maxCount = max($greed);
		$countDelta = ($maxCount - $minCount + 1);

		$black = imagecolorallocate($image, 0, 0, 0);
		$colors = array();
		for ($i = 0; $i < $width; $i++) {
			if (isset($greed[$i])) {
				$rel = ($greed[$i] - $minCount) / $countDelta;
				$color = self::findGradColor($rel, $gradMinColor, $gradMaxColor);
				if (!isset($colors[$color])) {
					$colors[$color] = imagecolorallocate($image, self::getChannel($color, self::CHANNEL_RED), 
														 self::getChannel($color, self::CHANNEL_GREEN),
														 self::getChannel($color, self::CHANNEL_BLUE));
				}
				imageline($image, $i, 0, $i, $height, $colors[$color]);
			}
		}
	}

	const CHANNEL_RED = 'red';
	const CHANNEL_GREEN = 'green';
	const CHANNEL_BLUE = 'blue';
	static private function getChannel($color, $channel)
	{
		if ($channel == self::CHANNEL_RED) {
			return ($color & 0xFF0000) >> 16;
		}
		else if ($channel == self::CHANNEL_GREEN) {
			return ($color & 0x00FF00) >> 8;
		}
		else {
			return $color & 0x0000FF;
		}
	}
	static private function getColor($red, $green, $blue) {
		return (($red & 0xFF) << 16) | (($green & 0xFF) << 8) | ($blue & 0xFF); 
	}

	static private function findGradColor($rel, $gradMinColor, $gradMaxColor)
	{
		$startRed = self::getChannel($gradMinColor, self::CHANNEL_RED);
		$startGreen = self::getChannel($gradMinColor, self::CHANNEL_GREEN);
		$startBlue = self::getChannel($gradMinColor, self::CHANNEL_BLUE);

		$endRed = self::getChannel($gradMaxColor, self::CHANNEL_RED);
		$endGreen = self::getChannel($gradMaxColor, self::CHANNEL_GREEN);
		$endBlue = self::getChannel($gradMaxColor, self::CHANNEL_BLUE);

		return self::getColor(
			round($startRed + ($endRed - $startRed) * $rel),
			round($startGreen + ($endGreen - $startGreen) * $rel),
			round($startBlue + ($endBlue - $startBlue) * $rel)
		);
	}
}