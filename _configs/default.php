<?php
return array(
'source'=>'.',
'destination'=>'./_site',
'plugins'=>'./_plugins',
'layouts'=>'./_layouts',
'include'=>array('.htaccess'),
'exclude'=>array(),
'keep_files'=>array('.git','.svn'),
'timezone'=>'Europe/Moscow',

'future'=>true,
'show_drafts'=>null,
'limit_posts'=>0,
'pygments'=>true,

'relative_permalinks'=>true,

'permalink'=>'date',
'paginate_path'=>'page:num',

'markdown'=>'maruku',
'markdown_ext'=>array('markdown','mkd','mkdn','md'),
'textile_ext'=>array('textile'),

'excerpt_separator'=>"\n\n",

'safe'=>false,
'watch'=>false,    # deprecated
'server'=>false,    # deprecated
'host'=>'0.0.0.0',
'port'=>4000,
'baseurl'=>'/',
'url'=>'http://localhost:4000',
'lsi'=>false,

'maruku'=>array(
  'use_tex'=>false,
  'use_divs'=>false,
  'png_engine'=>'blahtex',
  'png_dir'=>'images/latex',
  'png_url'=>'/images/latex'
),
'rdiscount'=>array(
  'extensions'=>array()
),
'redcarpet'=>array(
  'extensions'=>array()
),
'kramdown'=>array(
  'auto_ids'=>true,
  'footnote_nr'=>1,
  'entity_output'=>'as_char',
  'toc_levels'=>range(1,6),
  'smart_quotes'=>array('lsquo','rsquo','ldquo','rdquo'),
  'use_coderay'=>false,

  'coderay'=>array(
    'coderay_wrap'=>'div',
    'coderay_line_numbers'=>'inline',
    'coderay_line_numbers_start'=>1,
    'coderay_tab_width'=>4,
    'coderay_bold_every'=>10,
    'coderay_css'=>'style'
   )
),

'redcloth'=>array(
  'hard_breaks'=>true
  )
);
?>