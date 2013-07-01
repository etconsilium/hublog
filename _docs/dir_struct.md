# структура файлов и каталогов
## оригинальная структура каталогов jekyll:
```
..
├── _config.yml
├── _includes
|   ├── footer.html
|   └── header.html
├── _layouts
|   ├── default.html
|   └── post.html
├── _posts
|   ├── 2007-10-29-why-every-programmer-should-play-nethack.textile
|   └── 2009-04-26-barcamp-boston-4-roundup.textile
├── _site
└── index.html
```
где `_config.yml`, `_layouts`, `_posts` являются обязательными, а имена файлов (постов) в `_posts` начинаются с даты формата `YYYY-MM-DD`. разрешёнными типами являются `.html`, `.markdown`, `.md`, `.textile`.
подробней [http://jekyllrb.com/docs/structure/](http://jekyllrb.com/docs/structure/)

## несущественные отличия
* так как я не знаком с форматом `.textile`, он не поддерживается (пока)
* поддерживаются также `.php`, `.phtml`
* посты необязательно начинать с даты, но желательно нумеровать или другим способом указывать сортировку
