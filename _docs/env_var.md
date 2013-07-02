## переменные окружения

### 'глобальные' переменные

- `site` - доступна на всём сайте, содержит инфу из корневого /_config.yml
(вполне вероятно, можно будет подгружать локальные конфиги)

- `page` доступна для каждой страницы отдельно, предварительно подгружается инфа из YAML-заголовка страницы (поста)

- `content` содержит, собственно, контент этой страницы (всё, что после заголовка)

- `paginator` связана с постраничным преставлением поста, пока недоступна

### сайтовые переменные
- `site.time` The current time (когда скрипт запускается), выставлено по таймзоне
- `site.posts` A reverse chronological list of all Posts. список содержимого каталога /_posts
- `site.related_posts` какая-то магия: If the page being processed is a Post, this contains a list of up to ten related Posts. By default, these are low quality but fast to compute. For high quality but slow to compute results, run the jekyll command with the --lsi (latent semantic indexing) option.
- `site.categories.CATEGORY` хорошие полезные фичи (The list of all Posts in category `CATEGORY`.)
- `site.tags.TAG` The list of all Posts with tag `TAG`.
- `site.[CONFIGURATION_DATA]` другие переменные (All the variables set via the command line and your /_config.yml are available through the site variable. For example, if you have url: http://mysite.com in your configuration file, then in your Posts and Pages it will be stored in site.url. Jekyll does not parse changes to /_config.yml in watch mode, you must restart Jekyll to see changes to variables.

### страничные переменные
- `page.content` The un-rendered content of the Page.
- `page.title` The title of the Post.
- `page.excerpt` The un-rendered excerpt of the Page.
- `page.url` The URL of the Post without the domain, but with a leading slash, e.g. /2008/12/14/my-post.html
- `page.date` The Date assigned to the Post. This can be overridden in a Post’s front matter by specifying a new date/time in the format YYYY-MM-DD HH:MM:SS
- `page.id` An identifier unique to the Post (useful in RSS feeds). e.g. /2008/12/14/my-post
- `page.categories` The list of categories to which this post belongs. Categories are derived from the directory structure above the _posts directory. For example, a post at /work/code/_posts/2008-12-24-closures.md would have this field set to ['work', 'code']. These can also be specified in the YAML Front Matter.
- `page.tags` The list of tags to which this post belongs. These can be specified in the YAML Front Matter.
- `page.path` The path to the raw post or page. Example usage: Linking back to the page or post's source on GitHub. This can be overridden in the YAML Front Matter.



### Paginator
- `paginator.per_page` Number of Posts per page.
- `paginator.posts` Posts available for that page.
- `paginator.total_posts` Total number of Posts.
- `paginator.total_pages` Total number of Pages.
- `paginator.page` The number of the current page.
- `paginator.previous_page` The number of the previous page.
- `paginator.previous_page_path` The path to the previous page.
- `paginator.next_page` The number of the next page.
- `paginator.next_page_path` The path to the next page.