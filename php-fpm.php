#!/usr/bin/php

<?php

/**
* @author Devlopnet
* @licence CC
*/

exec("ps aux | grep php-fpm | awk '{print $11,$3,$6,$8,$12,$13}'", $result);

$states = array(
    'D'    =>    'Uninterruptible sleep',
    'R'    =>    'Running or runnable',
    'S'    =>    'Interruptible sleep',
    'T'    =>    'Stopped',
    'W'    =>    'Paging',
    'X'    =>    'Dead',
    'Z'    =>    'Defund (Zombie)'
);

$groups = array();

foreach ($result as $line)
{
    $args = explode(' ', $line);
    if (strpos(array_shift($args), 'php-fpm') === false)
    {
        continue;
    }
    list($cpu, $ram, $state, $type, $poolName) = $args;
    
    $state = substr($state, 0, 1);

    if ($type == 'master') $groupName = 'Master';
    else               $groupName = $poolName;

    if (!isset($groups[$groupName]))
    {
        $groups[$groupName] = array();
        foreach ($states as $key=>$currState)
        {
            $groups[$groupName]['status'][$key] = 0;
        }
    }
	
    $groups[$groupName]['status'][$state]++;
	$ramMo = $ram / 1024;
	$groups[$groupName]['memory'][] = $ramMo;
	if ($type != 'master')
	{
		if (!isset($groups[$groupName]['minMemory'])) {
			$groups[$groupName]['maxMemory'] = 0;
			$groups[$groupName]['minMemory'] = PHP_INT_MAX;
		}
		if ($ramMo > $groups[$groupName]['maxMemory']) {
			$groups[$groupName]['maxMemory'] = $ramMo;
		}
		if ($ramMo < $groups[$groupName]['minMemory']) {
			$groups[$groupName]['minMemory'] = $ramMo;
		}
		$groups[$groupName]['averageMemory'] = array_sum($groups[$groupName]['memory']) / count($groups[$groupName]['memory']);
	}
    $groups[$groupName]['cpu'][] = $cpu;
}

$fileCalled = basename($argv[0]);
$isConfig = isset($argv[1]) && $argv[1] == 'config';

switch ($fileCalled)
{
// ------------------------------------------------------
	case 'php-fpm-status':
// ------------------------------------------------------	
		$elements = array();
		foreach ($groups as $name=>$array)
		{
			foreach ($array['status'] as $state=>$number)
			{
				$id = $name.'_'.$state;
				$elements[$id] = array(
					'label'	=>	$name . ' : ' . $states[$state],
					'type'	=>	'GAUGE',
					'value'	=>	$number
				);		
			}
		}
		$config = array(
			'title'		=>	'PHP-FPM Status',
			'legend'	=>	'Nombre processus',
			'elements'	=>	$elements
		);
		break;
// ------------------------------------------------------		
	case 'php-fpm-memory':
// ------------------------------------------------------
		$elements = array();
		foreach ($groups as $name=>$array)
		{
			foreach ($array['memory'] as $nbProc=>$memProcessus)
			{
				$id = $name.'_'.$nbProc;
				$elements[$id] = array(
					'label'	=>	$name . ' : Processus n°' . $nbProc,
					'type'	=>	'GAUGE',
					'value'	=>	$memProcessus
				);
			}
		}
		$config = array(
			'title'		=>	'PHP-FPM Memory',
			'legend'	=>	'Mémoire Mo',
			'elements'	=>	$elements
		);	
		break;
// ------------------------------------------------------		
	case 'php-fpm-memoryPreview':
// ------------------------------------------------------
		foreach ($groups as $name=>$array)
		{
			if (isset($array['averageMemory']))
			{
				$elements = array(
					$name.'_'.'minMemory' => array(
						'label'	=>	$name . ' : Min',
						'type'	=>	'GAUGE',
						'value'	=>	$array['minMemory']
					),
					$name.'_'.'averageMemory' => array(
						'label'	=>	$name . ' : Moyenne',
						'type'	=>	'GAUGE',
						'value'	=>	$array['averageMemory']
					),
					$name.'_'.'maxMemory' => array(
						'label'	=>	$name . ' : Max',
						'type'	=>	'GAUGE',
						'value'	=>	$array['maxMemory']
					)
				);				
			}
		}
		$config = array(
			'title'		=>	'PHP-FPM Memory Preview',
			'legend'	=>	'Mémoire Mo',
			'elements'	=>	$elements
		);	
		break;
// ------------------------------------------------------		
	case 'php-fpm-cpu':
// ------------------------------------------------------
		$elements = array();
		foreach ($groups as $name=>$array)
		{
			foreach ($array['cpu'] as $nbProc=>$memProcessus)
			{
				$id = $name.'_'.$nbProc;
				$elements[$id] = array(
					'label'	=>	$name . ' : Processus n°' . $nbProc,
					'type'	=>	'GAUGE',
					'value'	=>	$memProcessus
				);		
			}
		}
		$config = array(
			'title'		=>	'PHP-FPM CPU',
			'legend'	=>	'%',
			'elements'	=>	$elements
		);	
		break;
// ------------------------------------------------------
	default:
		die("Unrecognized Plugin name $fileCalled\n");
}

if ($isConfig)
{
	echo 'graph_title ' . $config['title'] . "\n";
	echo 'graph_vlabel ' . $config['legend'] . "\n";
	echo "graph_category PHP-FPM\n";
	foreach ($config['elements'] as $element=>$data)
	{
		foreach ($data as $key=>$value)
		{
			if ($key == 'value') continue;
			echo $element . '.' . $key . ' ' . $value . "\n";
		}
	}
} else {
	foreach ($config['elements'] as $element=>$data)
	{
		echo $element . '.value ' . $data['value'] . "\n";
	}
}
