<?php defined('SYS') or die('No direct script access.');

abstract class CLI_Task {

	public static function factory($options)
	{
		if (isset($options[0]))
		{
			$task = $options[0];
		}

		$task = trim($task);
		$class = 'Task_' . ucfirst(strtolower($task));


		if ( ! class_exists($class))
		{
			CLI_Helper::write('Task "' . $class . '" not found');
			exit(1);
		}

		$class = new $class;

		if ( ! $class instanceof CLI_Task)
		{
			CLI_Helper::write('Task "' . $class . '" not found');
			exit(1);
		}

		$class->set_options($options);

		return $class;
	}

	protected $_options = array();

	protected $_accepted_options = array();

	protected $_method = '_execute';

	protected function __construct()
	{
		$this->_accepted_options = array_keys($this->_options);
	}

	public function set_options(array $options)
	{
		foreach ($options as $key => $value)
		{
			$this->_options[$key] = $value;
		}

		return $this;
	}

	public function get_options()
	{
		return (array) $this->_options;
	}

	public function get_accepted_options()
	{
		return (array) $this->_accepted_options;
	}

	public function execute()
	{
		$options = $this->get_options();

		// Запускаю задачу
		$method = $this->_method;
		echo $this->{$method}($options);
	}

	abstract protected function _execute(array $params);
}