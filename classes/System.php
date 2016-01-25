<?php defined('SYS') or die('No direct script access.');

class System {

	public static function auto_load($class)
	{
		$file = str_replace('_', DIRECTORY_SEPARATOR, $class);
		$path = __DIR__ .  DIRECTORY_SEPARATOR . $file . '.php';

		if (file_exists($path)) {
			// Подгружаю файл
			require $path;

			// Класс найден
			return TRUE;
		}

		// Класс не найден
		return FALSE;
	}
}