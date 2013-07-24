<?php
/**
 * 1. yaml
 * 2. twig
 * 3. md
 */

if (!function_exists('yaml_parse'))
{
	require_once PATH_ENGINE.'/yaml/Spyc.php';
	function yaml_parse($string) {
		return Spyc::YAMLLoadString($string);
	}
}
if (!function_exists('yaml_parse_file'))
{
	require_once PATH_ENGINE.'/yaml/Spyc.php';
	function yaml_parse_file($filename) {
		return Spyc::YAMLLoad($filename);
	}
}


require_once PATH_ENGINE.'/Twig/Autoloader.php';
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

require_once PATH_ENGINE.'/markdown/markdown.php';

function parse_file($source_filename,$variable=array()) {

}
function cr8_path($path) {

}
function copy_file($source_filename,$target_filename) {

}
function cr8_url($from,$rule) {

}
function get_date($filename) {
	$filename=basename($filename);

}
?>