<?php	//	REM online md-html http://markdown.pioul.fr/
namespace Hublog;

$config_default = include './_configs/default.php';

empty($config_default['timezone'])?:date_default_timezone_set($config_default['timezone']);

define ('HUBLOG_PATH_ROOT',realpath(($argc>1 ? $argv[1] : dirname(__file__)).DIRECTORY_SEPARATOR.$config_default['path']['root']));
define ('HUBLOG_PATH_CONFIG',realpath(PATH_ROOT.DIRECTORY_SEPARATOR.$config_default['path']['config']));
define ('HUBLOG_PATH_ENGINE',realpath(PATH_ROOT.DIRECTORY_SEPARATOR.$config_default['path']['engine']));
define ('HUBLOG_PATH_TMPL',realpath(PATH_ROOT.DIRECTORY_SEPARATOR.$config_default['path']['layout']));
define ('HUBLOG_PATH_CONTENT',realpath(PATH_ROOT.DIRECTORY_SEPARATOR.$config_default['path']['source']));
define ('HUBLOG_PATH_DEPLOY',realpath(PATH_ROOT.DIRECTORY_SEPARATOR.$config_default['path']['destination']));
define ('HUBLOG_PATH_CACHE',realpath( (!empty($_ENV['TEMP']) ? $_ENV['TEMP'] : PATH_TMPL) .DIRECTORY_SEPARATOR.$config_default['path']['cache']) );

require realpath(HUBLOG_PATH_ENGINE.'/hublog.php');
require realpath(HUBLOG_PATH_ENGINE.'/process.php');

Hublog::prepare_gitignore();
Hublog::prepare_htaccess();

Hublog::$file = Hublog::dirtree(HUBLOG_PATH_CONTENT);

while (list($rel_path,$file) = each(Hublog::$file))
{
	if ($file['copy'])
	{
		Hublog::copy_file($file['realname'],str_replace(PATH_CONTENT,PATH_DEPLOY,$file['realname']));
		continue;
	}
	if ($file['parse'])
	{
		$variable=array('site'=>Hublog::$config['SITE'],'page'=>& Hublog::$config['PAGE']);

		//$yaml_config = yaml_parse_file($source_filename);	//	оба парсера работают неудовлетворительно
		$origin=trim(file_get_contents($file['realname']));

		if (strlen($origin))
		{
			$ar=explode('---',$origin);
			if (''===trim($ar[0]))	//	считаем, что это yaml-front-matter и парсим его на конфиг
			{
				$config_page_yaml=\Hublog\Config\yaml(yaml_parse($ar[1]));
				$variable['page']['content']=trim(implode('---',array_slice($ar,2)));

				if (empty($config_page_yaml['date']))
					$config_page_yaml['date']=\Hublog\get_date_from_file($file['realname']);

				$config_page['url']=\Hublog\cretae_url();

				=(empty($config_site['url'])?:$config_site['url'])
								. (empty($config_site['baseurl'])?:$config_site['baseurl'])
								. preg_replace(array('~-~','~\.'.$file['extension'].'$~'),array('/','/index.html'),$file['name'],3);

				$categories=array();
				if (is_array($config_page_yaml['category']))
					$categories=array_merge($categories,$config_page_yaml['category']);
				if (is_string($config_page_yaml['category']))
					$categories=array_merge($categories,explode(',',$config_page_yaml['category']));
				if (is_array($config_page_yaml['categories']))
					$categories=array_merge($categories,$config_page_yaml['categories']);
				if (is_string($config_page_yaml['categories']))
					$categories=array_merge($categories,explode(',',$config_page_yaml['categories']));
				// вот такие портянки получаются. ибо пхп
				unset($config_page_yaml['category']); unset($config_page_yaml['categories']);

				$tags=array();
				if (is_array($config_page_yaml['tag']))
					$tags=array_merge($tags,$config_page_yaml['tag']);
				if (is_string($config_page_yaml['tag']))
					$tags=array_merge($tags,explode(',',$config_page_yaml['tag']));
				if (is_array($config_page_yaml['tags']))
					$tags=array_merge($tags,$config_page_yaml['tags']);
				if (is_string($config_page_yaml['tags']))
					$tags=array_merge($tags,explode(',',$config_page_yaml['tags']));
				// вот такие портянки получаются. ибо пхп
				unset($config_page_yaml['tag']); unset($config_page_yaml['tags']);

				$config_page_yaml['categories']=$categories;
				$config_page_yaml['tags']=$tags;

				while(list(,$cat)=each($categories))
				{
					if (empty($config_site['categories'][$cat]))
						$config_site['categories'][$cat]=array();

					$config_site['categories'][$cat][]=$file['name'];
				}
				while(list(,$tag)=each($tags))
				{
					if (empty($config_site['tags'][$tag]))
						$config_site['tags'][$tag]=array();

					$config_site['tags'][$tag][]=$file['name'];
				}

				$source_tree[$rel_path]['variable']=array_merge_recursive($config_default,$config_yml,$config_page_yaml);
			}
			//	надо собрать все переменные
		}
//		parse($file['realname'],$variable);
	}
}


print 'ok';
print PHP_EOL;
?>