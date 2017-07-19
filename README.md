# px2-baser-cms-theme-adapter

[baserCMS](https://basercms.net/) のテーマを [Pickles 2](http://pickles2.pxt.jp/) 上に適用するための仲介プラグインです。

## セットアップ - Setup

### 1. [Pickles 2](http://pickles2.pxt.jp/) プロジェクトを[セットアップ](http://pickles2.pxt.jp/manual/setup/projects.html)

### 2. `composer.json` に、パッケージ情報を追加

```json
{
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/tomk79/px2-baser-cms-theme-adapter.git"
        }
    ],
    "require": {
        "tomk79/px2-baser-cms-theme-adapter": "dev-master"
    }
}
```

### 3. composer update

更新したパッケージ情報を反映します。

```
$ composer update
```

### 4. config.php を更新

`$conf->funcs->html` に、プラグインを設定します。

予め設定されているテーマプラグイン `tomk79\pickles2\multitheme\theme::exec` を削除し、代替として設定します。

```php
<?php
return call_user_func( function(){

  /* (中略) */

  $conf->funcs->processor->html = array(

    /* (中略) */

    // // テーマ
    // 'theme'=>'tomk79\pickles2\multitheme\theme::exec('.json_encode([
    // 	'param_theme_switch'=>'THEME',
    // 	'cookie_theme_switch'=>'THEME',
    // 	'path_theme_collection'=>'./px-files/themes/',
    // 	'attr_bowl_name_by'=>'data-contents-area',
    // 	'default_theme_id'=>'pickles2'
    // ]).')' ,

    // テーマ
    'theme'=>'tomk79\pickles2\baserCmsThemeAdapter\theme::exec('.json_encode([
      'h1'=>'supply', // 'supply' = h1 を付加する, 'pass' = 付加しない
      'param_theme_switch'=>'THEME',
      'cookie_theme_switch'=>'THEME',
      'path_theme_collection'=>'./px-files/bc_themes/',
      'attr_bowl_name_by'=>'data-contents-area',
      'default_theme_id'=>'bc_sample',
    ]).')' ,

    /* (中略) */

  );

  /* (中略) */

  return $conf;
} );
```

### 5. baserCMS のテーマを設置する

`path_theme_collection` に設定したパスに、baserCMS のテーマを設置してください。

テーマは複数設置できます。 デフォルトに設定するテーマ名を、 `default_theme_id` に設定してください。


## ライセンス - License

Copyright (c)2001-2017 Tomoya Koyanagi, and Pickles 2 Project<br />
MIT License https://opensource.org/licenses/mit-license.php

## 作者 - Author

- Tomoya Koyanagi tomk79@gmail.com
- website: http://www.pxt.jp/
- Twitter: @tomk79 http://twitter.com/tomk79/
