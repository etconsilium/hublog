## конфигурационные переменные

оригинал [http://jekyllrb.com/docs/configuration/](http://jekyllrb.com/docs/configuration/)

глобальные:

- source: DIR
- destination: DIR
- safe: BOOL
- exclude: [DIR, FILE, ...]
- include: [DIR, FILE, ...]
- timezone: TIMEZONE

- конфигурация сборки не поддерживается

- future: BOOL
Publish posts with a future date.

- lsi: BOOL	//	???
Produce an index for related posts.

- limit_posts: NUM
Limit the number of posts to parse and publish.

- серверные конфиги не поддерживаются

- baseurl: URL

оригинальная документация пишет: Do not use tabs in configuration files; -- нашим парсерам это всё без разницы, только помните про YAML

Default Configuration находится в _configs/default.php
```
source:      .
destination: ./_site
plugins:     ./_plugins
layouts:     ./_layouts
include:     ['.htaccess']
exclude:     []
keep_files:  ['.git','.svn']
timezone:    nil

future:      true
show_drafts: nil
limit_posts: 0
pygments:    true

relative_permalinks: true

permalink:     date
paginate_path: 'page:num'

markdown:      maruku
markdown_ext:  markdown,mkd,mkdn,md
textile_ext:   textile

excerpt_separator: "\n\n"

safe:        false
watch:       false    # deprecated
server:      false    # deprecated
host:        0.0.0.0
port:        4000
baseurl:     /
url:         http://localhost:4000
lsi:         false

maruku:
  use_tex:    false
  use_divs:   false
  png_engine: blahtex
  png_dir:    images/latex
  png_url:    /images/latex

rdiscount:
  extensions: []

redcarpet:
  extensions: []

kramdown:
  auto_ids: true
  footnote_nr: 1
  entity_output: as_char
  toc_levels: 1..6
  smart_quotes: lsquo,rsquo,ldquo,rdquo
  use_coderay: false

  coderay:
    coderay_wrap: div
    coderay_line_numbers: inline
    coderay_line_numbers_start: 1
    coderay_tab_width: 4
    coderay_bold_every: 10
    coderay_css: style

redcloth:
  hard_breaks: true
```


пс: судя по дальнейшей документации [http://jekyllrb.com/docs/posts/](http://jekyllrb.com/docs/posts/),
все переменные этого раздела (которые дальше сливаются с _config.yml) передаются массивом site: `{{ site.url }}`.
алсо доступен `site.posts` - список постов в каталоге `_posts`