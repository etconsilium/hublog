<?php
//	yaml http://jekyllrb.com/docs/frontmatter/

define ('LEADING_NUMBERS',false);	//	experemental
define ('CACHED',false);	//	experemental
define ('WATCH',false);	//	experemental

//	@see http://jekyllrb.com/docs/configuration/
define ('DIR_ROOT', '/./');
define ('DIR_CONFIG','_configs');
define ('DIR_ENGINE','_engines');
define ('DIR_TMPL','_layouts');
define ('DIR_CONTENT','_posts');	//	--source dir
define ('DIR_DEPLOY','/./');		//	--destination dir
define ('DIR_CACHE','_hublog_cache');

define ('PATH_ROOT',realpath(($argc>1 ? $argv[1] : dirname(__file__)).DIRECTORY_SEPARATOR.DIR_ROOT));
define ('PATH_CONFIG',realpath(PATH_ROOT.DIRECTORY_SEPARATOR.DIR_CONFIG));
define ('PATH_ENGINE',realpath(PATH_ROOT.DIRECTORY_SEPARATOR.DIR_ENGINE));
define ('PATH_TMPL',realpath(PATH_ROOT.DIRECTORY_SEPARATOR.DIR_TMPL));
define ('PATH_CONTENT',realpath(PATH_ROOT.DIRECTORY_SEPARATOR.DIR_CONTENT));
define ('PATH_DEPLOY',realpath(PATH_ROOT.DIRECTORY_SEPARATOR.DIR_DEPLOY));
define ('PATH_CACHE',realpath( (!empty($_ENV['TEMP']) ? $_ENV['TEMP'] : PATH_TMPL) .DIRECTORY_SEPARATOR.DIR_CACHE) );


require PATH_ENGINE.'/process.php';

$gitignore = file_get_contents('.gitignore');
if (false===strpos('### hublog section ###', $gitignore))
{
	file_put_contents('.gitignore',$gitignore
					  .PHP_EOL
					  .PHP_EOL
					  .'### hublog section ###'.PHP_EOL
					  .DIR_CONFIG.PHP_EOL
					  .DIR_ENGINE.PHP_EOL
					  .DIR_TMPL.PHP_EOL
					  //.DIR_CACHE.PHP_EOL
					  .PHP_EOL
					  );
}

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