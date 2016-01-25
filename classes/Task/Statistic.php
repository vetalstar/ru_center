<?php defined('SYS') or die('No direct script access.');

class Task_Statistic extends CLI_Task {

	protected function _execute(array $params)
	{
		//var_dump($params);

		if (array_key_exists('without-documents', $params))
			$sel_type = 'without-documents';
		elseif (array_key_exists('with-documents', $params))
			$sel_type = 'with-documents';
		else
		{
			CLI_Helper::write('Specify selection type (--without-documents or --with-documents)');
			exit(1);
		}

		$format = 'Y-m-d';

		$i = 0;
		// 3 попытки ввода корректного значения
		while($i < 3)
		{
			$start_d = CLI_Helper::read('Please enter start date');

			$sd = DateTime::createFromFormat($format, $start_d);

			if ($sd && $sd->format($format) == $start_d)
				break;
			elseif ($i == 2)
				exit(1);

			$i++;
		}


		$i = 0;

		while($i < 3)
		{
			$end_d = CLI_Helper::read('Please enter end date');

			$sd = DateTime::createFromFormat($format, $end_d);

			if ($sd && $sd->format($format) == $end_d)
				break;
			elseif ($i == 2)
				exit(1);

			$i++;
		}

		$sql = '';

		if ($sel_type == 'without-documents')
		{
			$sql .= '
				SELECT
					COUNT(`p`.`id`) as `count`,
					SUM(`p`.`amount`) as `amount`
				FROM
					`payments` as `p`
				LEFT JOIN
					`documents` as `d`
				ON
					`p`.`id`=`d`.`entity_id`
				WHERE
					`d`.`entity_id` is null AND
					DATE(`p`.`create_ts`) >= :start AND
					DATE(`p`.`create_ts`) < :end
				GROUP BY
					`p`.`id`
					';
		}

		if ($sel_type == 'with-documents')
		{
			$sql .= '
				SELECT
					COUNT(`d`.`entity_id`) as `count`,
					SUM(`p`.`amount`) as `amount`
				FROM
					`payments` as `p`
				JOIN
					`documents` as `d`
				ON
					`p`.`id`=`d`.`entity_id`
				WHERE
					DATE(`p`.`create_ts`) >= :start AND
					DATE(`p`.`create_ts`) < :end
				GROUP BY
					`d`.`entity_id`
					';
		}

		try
		{
			$quest = new Quest();
			$db = $quest->getDb();

			$st = $db->prepare($sql);

			$st->bindParam(':start', $start_d);
			$st->bindParam(':end', $end_d);

			$st->setFetchMode(PDO::FETCH_OBJ);

			$st->execute();
		}
		catch (PDOException $e)
		{
			CLI_Helper::write($e->getMessage());
			exit(1);
		}

		CLI_Helper::write('count	|	amount');

		foreach($st as $row)
		{
			CLI_Helper::write($row->count . '	|	' . $row->amount);
		}
	}
}