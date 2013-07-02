<?php

$GLOBALS['config'] = include './_configs/default.php';

define ('PATH_ROOT',realpath(($argc>1 ? $argv[1] : dirname(__file__)).DIRECTORY_SEPARATOR.$config['path']['root']));
define ('PATH_CONFIG',realpath(PATH_ROOT.DIRECTORY_SEPARATOR.$config['path']['config']));
define ('PATH_ENGINE',realpath(PATH_ROOT.DIRECTORY_SEPARATOR.$config['path']['engine']));
define ('PATH_TMPL',realpath(PATH_ROOT.DIRECTORY_SEPARATOR.$config['path']['layout']));
define ('PATH_CONTENT',realpath(PATH_ROOT.DIRECTORY_SEPARATOR.$config['path']['source']));
define ('PATH_DEPLOY',realpath(PATH_ROOT.DIRECTORY_SEPARATOR.$config['path']['destination']));
define ('PATH_CACHE',realpath( (!empty($_ENV['TEMP']) ? $_ENV['TEMP'] : PATH_TMPL) .DIRECTORY_SEPARATOR.$config['path']['cache']) );


require realpath(PATH_ENGINE.'/process.php');

$gitignore = file_get_contents('.gitignore');		//	hardcode
var_dump(strpos($gitignore,'### hublog section ###'),false===strpos($gitignore,'### hublog section ###'));
if (false===strpos($gitignore,'### hublog section ###'))
{
	file_put_contents('.gitignore',$gitignore
					  .PHP_EOL
					  .PHP_EOL
					  .'### hublog section ###'.PHP_EOL
					  .$config['path']['config'].PHP_EOL
					  .$config['path']['engine'].PHP_EOL
					  .$config['path']['layout'].PHP_EOL
					  //.DIR_CACHE.PHP_EOL
					  .PHP_EOL
					  );
}

//	читаем дерево
function dirtree($dir='.') {
	$result=array();
    $list = scandir($dir);
    if (is_array($list))
	{
        $list = array_diff($list, array('.', '..'));
		natsort($list);	//	NB!
        if (false!==reset($list))
		{
            while ($name = next($list))
			{
                $path = realpath($rel_path = ($dir . DIRECTORY_SEPARATOR . $name));

				if (is_dir($path))	$result[$name] = dirtree($rel_path);
				if (is_file($path))	$result[$name] = $rel_path;
            }
		}
    }
	return $result;
}

$a=dirtree(PATH_CONTENT);
var_dump($a);

print 'ok';
print PHP_EOL;
?>