<?php

final class Hublog {
	static $config=array('DEFAULT'=>array()	//	_config/default.php + /_post/config.yml

						 ,'SITE'=>array()	//	site' config
						 ,'PAGE'=>array()	//	current page
						 ,'PAGINATOR'=>array()	//	current page
						 );

	static $file=array(/*rel_path=>array(name,realname,)*/);	//	see dirtree()

/*********************************************/

	//	читаем дерево в линейный массив
	static function dirtree($dir=HUBLOG_PATH_CONTENT) {
		$result=array();
		$list = scandir($dir);
		if (is_array($list))
		{
			$list = array_diff($list, array('.', '..'));
			natsort($list);	//	NB!
			if (false!==reset($list))
			{
				while (list(,$name) = each($list))
				{
					$path = realpath($rel_path = ($dir . DIRECTORY_SEPARATOR . $name));

					if (is_dir($path) && !in_array(basename($path),Hublog::$config['DEFAULT']['files']['keep'])) {
						$result = array_merge($result,dirtree($rel_path));
					}
					if (is_file($path)) {	//	ничего не парсится
						$result[$rel_path] = array(
							'name'=>$name
							,'extension'=>($ext=array_pop( explode('.',$name)))
							,'realname'=>$path
							,'parse'=>(!in_array($ext,Hublog::$config['MAIN']['files']['exclude']) && in_array($ext,Hublog::$config['MAIN']['files']['parse']))
							,'copy'=>(!in_array($ext,Hublog::$config['MAIN']['files']['exclude']) && !in_array($name,Hublog::$config['MAIN']['files']['keep']))
						);
					}
				}
			}
		}
		return $result;
	}

	


	static function date_from_file($file){
		list($year,$month,$day)=preg_split('~-~',$file['name'],3);
		return mktime(0,0,0,$month,$day,$year) ?: filemtime($file['realname']);
	}
	static function create_page_url($file){
		//	rule  Hublog::$config['MAIN']['permalink']
		//	@TODO доделать разные правила
		return
			(Hublog::$config['SITE']['server']['baseurl'])
			. preg_replace('~^(\d{4})-(\d\d)-(\d\d)-(.+)~'
						   , '$1/$2/$3/$4'
						   , str_replace('.'.$file['extension'], '.html', $file['name'])
						  );
	}


	static function prepare_gitignore(){
		$gitignore = file_get_contents('.gitignore');		//	hardcode
		if (false===strpos($gitignore,'### hublog section ###'))
		{
			file_put_contents('.gitignore',$gitignore
							  .PHP_EOL
							  .PHP_EOL
							  .'### hublog section ###'.PHP_EOL
							  .Hublog::$config['MAIN']['path']['config'].PHP_EOL
							  .Hublog::$config['MAIN']['path']['engine'].PHP_EOL
							  .Hublog::$config['MAIN']['path']['layout'].PHP_EOL
							  //.DIR_CACHE.PHP_EOL
							  .PHP_EOL
							  );
		}
	}

	static function prepare_htaccess(){}


	static function init($config=array()){
		Hublog::$config['DEFAULT']=array_merge_recursive(
			include ('./_configs/default.php')
			,(is_readable($f=HUBLOG_PATH_ROOT.'/_config.yml')?yaml_parse_file($f):array())
			,$config
		);
		Hublog::$config['SITE']=array_merge_recursive(
			Hublog::$config['DEFAULT']
			,array(
				'time'=>time()	//	обновлять или не обновлять при парсинге? The current time (когда скрипт запускается), выставлено по таймзоне
				,'posts'=>array()	// пока пусть так будет. но надо брать дату из парсинга A reverse chronological list of all Posts. список содержимого каталога /_posts
				,'related_posts'=>array()	//	тоже брать после парсинга	//	какая-то магия: If the page being processed is a Post, this contains a list of up to ten related Posts. By default, these are low quality but fast to compute. For high quality but slow to compute results, run the jekyll command with the --lsi (latent semantic indexing) option.
				,'categories'=>array(/*'CATEGORY'=>array()*/)	//	после парсинга 	//	(The list of all Posts in category `CATEGORY`.)
				,'tags'=>array(/*'TAG'=>array()*/)	//	после парсинга 	//	The list of all Posts with tag `TAG`.
			)
		);
		Hublog::$config['PAGE']=array(
			'content'=>''	//	The un-rendered content of the Page.
			,'title'=>''	//	The title of the Post.
			,'excerpt'=>''	//	The un-rendered excerpt of the Page.
			,'url'=>''	//	The URL of the Post without the domain, but with a leading slash, e.g. /2008/12/14/my-post.html
			,'date'=>''	//	The Date assigned to the Post. This can be overridden in a Post’s front matter by specifying a new date/time in the format YYYY-MM-DD HH:MM:SS
			,'id'=>''	//	An identifier unique to the Post (useful in RSS feeds). e.g. /2008/12/14/my-post
			,'categories'=>array()	//	The list of categories to which this post belongs. Categories are derived from the directory structure above the _posts directory. For example, a post at /work/code/_posts/2008-12-24-closures.md would have this field set to ['work', 'code']. These can also be specified in the YAML Front Matter.
			,'tags'=>array()	//	The list of tags to which this post belongs. These can be specified in the YAML Front Matter.
			,'path'=>''	//	The path to the raw post or page. Example usage: Linking back to the page or
		);
		Hublog::$config['PAGINATOR']=array(	//	тоже формировать на момент парсинга (не рендеринга)
			'per_page'=>''	//	Number of Posts per page.
			,'posts'=>''	//	Posts available for that page.
			,'total_posts'=>''	//	Total number of Posts.
			,'total_pages'=>''	//	Total number of Pages.
			,'page'=>''	//	The number of the current page.
			,'previous_page'=>''	//	The number of the previous page.
			,'previous_page_path'=>''	//	The path to the previous page.
			,'next_page'=>''	//	The number of the next page.
			,'next_page_path'=>''	//	The path to the next page.
		);

		return Hublog::$config['SITE'];
	}

	static function yml($config=array()){
		Hublog::$config['YML']=array_merge_recursive((is_readable($f=HUBLOG_PATH_ROOT.'/_config.yml')?yaml_parse_file($f):array()),$config);
	}
	static function site($config=array()){
		Hublog::$config['SITE']=
		array_merge_recursive(
							  Hublog::$config['MAIN']
							  ,array(
			'time'=>time()	//	обновлять или не обновлять при парсинге? The current time (когда скрипт запускается), выставлено по таймзоне
			,'posts'=>rsort(array_keys($source_tree))	// пока пусть так будет. но надо брать дату из парсинга A reverse chronological list of all Posts. список содержимого каталога /_posts
			,'related_posts'=>array()	//	тоже брать после парсинга	//	какая-то магия: If the page being processed is a Post, this contains a list of up to ten related Posts. By default, these are low quality but fast to compute. For high quality but slow to compute results, run the jekyll command with the --lsi (latent semantic indexing) option.
			,'categories'=>array(/*'CATEGORY'=>array()*/)	//	после парсинга 	//	(The list of all Posts in category `CATEGORY`.)
			,'tags'=>array(/*'TAG'=>array()*/)	//	после парсинга 	//	The list of all Posts with tag `TAG`.
			)
							  ,Hublog::$config['YML']	//	другие переменные (All the variables set via the command line and your /_config.yml are available through the site variable. For example, if you have url: http://mysite.com in your configuration file, then in your Posts and Pages it will be stored in site.url. Jekyll does not parse changes to /_config.yml in watch mode, you must restart Jekyll to see changes to variables.
							  ,$config);

	}
	static function page($config=array()){
		return array_merge_recursive(array()
			,$config);
	}
	static function paginator($config=array()){
		return array_merge_recursive(array()
			,$config);
	}
	static function yaml($config=array()){
		return array_merge_recursive(array(),
			array('title'=>''
				  ,'date'=>''
				 )
			,$config);
	}
}
return Hublog::init();
?>