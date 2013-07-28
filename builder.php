<?php	//	REM online md-html http://markdown.pioul.fr/
//namespace Hublog;

$config_default = include './_configs/default.php';

empty($config_default['timezone'])?:date_default_timezone_set($config_default['timezone']);

define ('HUBLOG_PATH_ROOT',realpath(($argc>1 ? $argv[1] : dirname(__file__)).DIRECTORY_SEPARATOR.$config_default['path']['root']));
define ('HUBLOG_PATH_CONFIG',realpath(HUBLOG_PATH_ROOT.DIRECTORY_SEPARATOR.$config_default['path']['config']));
define ('HUBLOG_PATH_ENGINE',realpath(HUBLOG_PATH_ROOT.DIRECTORY_SEPARATOR.$config_default['path']['engine']));
define ('HUBLOG_PATH_TMPL',realpath(HUBLOG_PATH_ROOT.DIRECTORY_SEPARATOR.$config_default['path']['layout']));
define ('HUBLOG_PATH_CONTENT',realpath(HUBLOG_PATH_ROOT.DIRECTORY_SEPARATOR.$config_default['path']['source']));
define ('HUBLOG_PATH_DEPLOY',realpath(HUBLOG_PATH_ROOT.DIRECTORY_SEPARATOR.$config_default['path']['destination']));
define ('HUBLOG_PATH_CACHE',realpath( (!empty($_ENV['TEMP']) ? $_ENV['TEMP'] : PATH_TMPL) .DIRECTORY_SEPARATOR.$config_default['path']['cache']) );

require realpath(HUBLOG_PATH_ENGINE.'/process.php');
require realpath(HUBLOG_PATH_ENGINE.'/hublog.php');

Hublog::prepare_gitignore();
Hublog::prepare_htaccess();

Hublog::step1(HUBLOG_PATH_CONTENT);
Hublog::step2();
Hublog::step3();


print 'ok';
print PHP_EOL;
?>