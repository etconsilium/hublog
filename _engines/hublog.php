<?php

final class Hublog {
	static $default=array();	//	_config/default.php + /_post/config.yml
	static $site=array();	//	site' config
						 //,'PAGE'=>array()	//	current page
						 //,'PAGINATOR'=>array()	//	current page
						 //);

	static $files=array(/*rel_path=>array(name,realname,)*/);	//	see dirtree()
	static $pages=array(/*rel_path=>array(content,url,)*/);

/*********************************************/

	//	step 1 :: читаем дерево в линейный массив
	static function step1($dir=HUBLOG_PATH_CONTENT) {
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

					if (is_dir($path) && !in_array(basename($path),Hublog::$default['files']['keep'])) {
						$result = array_merge($result,Hublog::step1($rel_path));
					}
					if (is_file($path)) {
						$result[$rel_path] = array(
							'name'=>$name
							,'extension'=>($ext=array_pop( explode('.',$name)))
							,'realname'=>$path
							,'parsed'=>(!in_array($ext,Hublog::$default['files']['exclude']) && in_array($ext,Hublog::$default['files']['parse']))
							,'copied'=>(!in_array($ext,Hublog::$default['files']['exclude']) && !in_array($name,Hublog::$default['files']['keep']))
						);
					}
				}
			}
		}
		Hublog::$files=$result;
		return $result;
	}

	//	step 2 :: парсим линейный массив для конфига SITE
	static function step2($file_array=array()){
		if (!count($file_array)) $file_array=Hublog::step1(HUBLOG_PATH_CONTENT) ;

		$posts=array();
		$categories=$tags=array();

		while (list($relative_path,$file) = each($file_array))
		{
			if (!$file['parsed']) {
				if ($file['copied'])
					Hublog::copy_file();

				continue;
			}

			//	попарсить файл
			$date=Hublog::date_from_file($file);
			$posts[]=array('date'=>$date,'post'=>$relative_path);

			$origin=file_get_contents($file['realname']);
			//	лестница проверок
			if (strlen($origin))
			{
				$yaml=array();
				//	т.к. парсеры неудачные, делаем всё руками
				$a_orig=explode('---',$origin);
				if (''===trim($a_orig[0]))	//	считаем, что это yaml-front-matter и парсим его на конфиг
				{
					$yaml=array_merge(
						array('title'=>basename($file['name'])
							  ,'date'=>$date
							  ,'layout'=>'default.html')
						,yaml_parse($a_orig[1])
					);
					$content=trim(implode('---',array_slice($a_orig,2)));

					//	достаём категории и теги
					{
						//	@TODO: The list of categories to which this post belongs. Categories are derived from the directory structure above the _posts directory.
					$cat_post=array_merge(
						array_key_exists('category',$yaml) ? array($yaml['category']):array()
						,(array_key_exists('categories',$yaml)
							? (is_array($yaml['categories'])
							   ? $yaml['categories']
							   : array_filter( explode(',',$yaml['categories']), 'trim' )
							   )
							: array()
						 )
					);
					$tag_post=array_merge(
						array_key_exists('tag',$yaml) ? array($yaml['tag']):array()
						,(array_key_exists('tags',$yaml)
							? (is_array($yaml['tags'])
							   ? $yaml['tags']
							   : array_filter( explode(',',$yaml['tags']), 'trim' )
							   )
							: array()
						 )
					);
					}

					Hublog::$pages[$relative_path] = array_merge(
						$yaml
						,array(
							'content'=>$content
							,'categories'=>$cat_post
							,'tags'=>$tag_post
							,'path'=>$relative_path
							,'url'=>Hublog::create_page_url($file)
							,'id'=>uniqid(preg_replace('~[^a-zA-Z0-9_]~','_',$file['name']).'_',1)
						)
					);

					array_walk($cat_post,function($i)use($relative_path){Hublog::$site['categories'][$i]=$relative_path;});
					array_walk($tag_post,function($i)use($relative_path){Hublog::$site['tags'][$i]=$relative_path;});
				}
			}
		}

		usort($posts, function($a,$b){return -strcmp($a['date'],$b['date']);});
		$posts=array_column($posts,'post');

		Hublog::$site['posts']=$posts;	Hublog::$site['time']=time();
		return Hublog::$pages;
	}

	//	парсим шаблонизатором и пишем
	static function step3($pages_array=array()){
		//	@TODO: где-то забыли шаблон layout

		if (!count($pages_array)) $pages_array=Hublog::step2() ;

		require_once HUBLOG_PATH_ENGINE.'/markdown/markdown.php';

		$mrkdwn = new Markdown_Parser();

		Twig_Autoloader::register();
		$loader = new Twig_Loader_Filesystem( HUBLOG_PATH_TMPL );

		$twig = new Twig_Environment($loader, array(
			'cache'       => HUBLOG_PATH_CACHE
			,'auto_reload' => false	//	ибо бесполезно
			,'autoescape' => false	//	дефолтно включен
//			,'layout'=>realpath(HUBLOG_PATH_TMPL.DIRECTORY_SEPARATOR.$page['layout'])
		));

		while (list(,$page)=each($pages_array)){

			$target=$twig->loadTemplate( $page['layout'] );
			$tpl=$target->render(
					array(
						'site'=>array_merge_recursive(Hublog::$default,Hublog::$site)
						,'page'=>array_merge($page,array('content'=>Markdown($page['content'])))
						,'content'=>$page['content']
						,'paginator'=>array()
						)
				);

			$filename=HUBLOG_PATH_DEPLOY.$page['url'];
			$dirname=dirname( $filename=str_replace(array('\\/','\\\\','\\','/'),DIRECTORY_SEPARATOR,$filename) );	//	чистый хак
			if (!is_dir($dirname))	mkdir($dirname,0755,true);

			file_put_contents($filename, $tpl );
		}
	}

	static function date_from_file($file){
		list($year,$month,$day)=preg_split('~-~',$file['name'],3);
		return mktime(0,0,0,$month,$day,$year) ?: filemtime($file['realname']);
	}
	static function create_page_url($file,$rule=null/*function*/){
		//	rule  Hublog::$config['MAIN']['permalink']
		//	@TODO доделать разные правила
		return
			str_replace(array('\\/','\\\\','\\','//'),'/',	//	нормализация
				(Hublog::$default['server']['baseurl'])
				. str_replace(array(HUBLOG_PATH_CONTENT,$file['name']),'',$file['realname'])
				. preg_replace('~^(\d{4})-(\d\d)-(\d\d)-(.+)~'
							   , '$1/$2/$3/$4'
							   , str_replace('.'.$file['extension'], '.html', $file['name'])
							  )
			);
	}

	static function copy_file($source_name=null,$target_name=null){}

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
		Hublog::$default=array_merge_recursive(
			include ('./_configs/default.php')
			,(is_readable($f=HUBLOG_PATH_ROOT.'/_config.yml')?yaml_parse_file($f):array())
			,$config
		);
		Hublog::$site=array_merge_recursive(
			Hublog::$default
			,array(
				'time'=>time()	//	обновлять или не обновлять при парсинге? The current time (когда скрипт запускается), выставлено по таймзоне
				,'posts'=>array()	// пока пусть так будет. но надо брать дату из парсинга A reverse chronological list of all Posts. список содержимого каталога /_posts
				,'related_posts'=>array()	//	тоже брать после парсинга	//	какая-то магия: If the page being processed is a Post, this contains a list of up to ten related Posts. By default, these are low quality but fast to compute. For high quality but slow to compute results, run the jekyll command with the --lsi (latent semantic indexing) option.
				,'categories'=>array(/*'CATEGORY'=>array()*/)	//	после парсинга 	//	(The list of all Posts in category `CATEGORY`.)
				,'tags'=>array(/*'TAG'=>array()*/)	//	после парсинга 	//	The list of all Posts with tag `TAG`.
			)
		);

		return Hublog::$site;
	}
}
return Hublog::init();
?>