<?php
/**
 * 1. yaml
 * 2. twig
 * 3. md
 */

if (!function_exists('yaml_parse'))
{
	require_once DIR_ENGINE.'/yaml/Spyc.php';
	function yaml_parse_file($filename) {
		return Spyc::YAMLLoadString($filename);
	}
}
if (!function_exists('yaml_parse_file'))
{
	require_once DIR_ENGINE.'/yaml/Spyc.php';
	function yaml_parse_file($filename) {
		return Spyc::YAMLLoad($filename);
	}
}


require_once DIR_ENGINE.'/Twig/Autoloader.php';
Twig_Autoloader::register();
$loader = new Twig_Loader_String();
$twig = new Twig_Environment($loader, array(
	'cache'       => DIR_CACHE,
	'auto_reload' => false	//	ибо бесполезно
));

function render($tmpl,$var) {
	global $twig;
	return $twig->render($tmpl,$var);
}

require_once DIR_ENGINE.'/markdown/markdown.php';

function process($source_filename,$target_filename,$variable=array()) {
	//$yaml_config = yaml_parse_file($source_filename);	//	оба парсера работают неудовлетворительно
	$origin=trim(file_get_contents($source_filename));
	if (strlen($origin))
	{
		$ar=explode('---',$origin);
		if (''===$ar[0])	//	считаем, что это yaml-front-matter и парсим его на конфиг
		{
			$variable=array_merge_recursive(yaml_parse($ar[1]),$variable);
			$origin=trim(implode('---',array_slice($ar,2)));
		}
		var_dump($variable,$origin);

		//	надо собрать все переменные


	}
}
?>