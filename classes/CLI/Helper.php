<?php defined('SYS') or die('No direct script access.');

class CLI_Helper {

    public static function options($options = NULL)
    {
        // Получаю все опции
        $options = func_get_args();

        // Найденные значения
        $values = array();

        for ($i = 1; $i < $_SERVER['argc']; $i++)
        {
            if ( ! isset($_SERVER['argv'][$i]))
            {
                // Аргументов больше нет
                break;
            }

            // Получаю опцию
            $opt = $_SERVER['argv'][$i];

            if (substr($opt, 0, 2) !== '--')
            {
                $values[] = $opt;
                continue;
            }

            // Удаляю "--" префикс
            $opt = substr($opt, 2);

            if (strpos($opt, '='))
            {
                // Разделяю название и значение
                list ($opt, $value) = explode('=', $opt, 2);
            }
            else
            {
                $value = NULL;
            }

            $values[$opt] = $value;
        }

        if ($options)
        {
            foreach ($values as $opt => $value)
            {
                if ( ! in_array($opt, $options))
                {
                    unset($values[$opt]);
                }
            }
        }

        return count($options) == 1 ? array_pop($values) : $values;
    }

    public static function read($text = '', array $options = NULL)
    {
        // Если есть выводимый текст
        $options_output = '';
        if ( ! empty($options))
        {
            $options_output = ' [ '.implode(', ', $options).' ]';
        }

        fwrite(STDOUT, $text.$options_output.': ');

        // Чтение
        $input = trim(fgets(STDIN));

        // Повтор ввода при ошибке
        if ( ! empty($options) && ! in_array($input, $options))
        {
            CLI_Helper::write('This is not a valid option. Please try again.');

            $input = CLI_Helper::read($text, $options);
        }

        return $input;
    }

    public static function write($text = '')
    {
        if (is_array($text))
        {
            foreach ($text as $line)
            {
                CLI_Helper::write($line);
            }
        }
        else
        {
            fwrite(STDOUT, $text.PHP_EOL);
        }
    }
}