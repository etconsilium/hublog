<?php
/**
 * 1. yaml
 * 2. twig
 * 3. md
 */

if (!function_exists('yaml_parse'))
{
	require_once HUBLOG_PATH_ENGINE.'/yaml/Spyc.php';
	function yaml_parse($string) {
		return Spyc::YAMLLoadString($string);
	}
}
if (!function_exists('yaml_parse_file'))
{
	require_once HUBLOG_PATH_ENGINE.'/yaml/Spyc.php';
	function yaml_parse_file($filename) {
		return Spyc::YAMLLoad($filename);
	}
}


require_once HUBLOG_PATH_ENGINE.'/Twig/Autoloader.php';
Twig_Autoloader::register();
$loader = new Twig_Loader_String();
$twig = new Twig_Environment($loader, array(
	'cache'       => PATH_CACHE,
	'auto_reload' => false	//	ибо бесполезно
));

function render($tmpl,$var) {
	global $twig;
	return $twig->render($tmpl,$var);
}

require_once HUBLOG_PATH_ENGINE.'/markdown/markdown.php';

if (!function_exists('array_column'))
{
	function array_column($input, $column_key, $index_key=null){
		if (!array_key_exists($column_key,$input[0]))	return	null;
		if (!empty($index_key) && !array_key_exists($index_key,$input[0]))	return	null;

		$a=array(); reset($input);
		if (empty($index_key))
			while (list(,$v)=each($input))
				$a[]=$v[$column_key];
		else
			while (list(,$v)=each($input))
				$a[$v[$index_key]]=$v[$column_key];

		return $a;
	}
}
?>