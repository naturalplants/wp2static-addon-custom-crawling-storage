# WP2Static Custom Crawling Storage Add-on

任意のディレクトリをクローリング保存用として使うためのWP2Staticのアドオン。

## プラグインの目的
以下のように `process_site` `deploy` 処理を最適化するためのもの。
- 新たにクロールしてSSGされたファイルのみ、 `process_site` するため
- 新たに `process_site` されたファイルのみ　`deploy` するため

## Get started

依存ライブラリのインストール
```
% composer install
```

## Structure

https://github.com/leonstafford/wp2static-addon-* を基準にしている

## Composer スクリプト

テスト（Lint、PHPコーディング規約、静的解析）
```
% composer run test
```

ビルド。Zipファイルが `~/Downloads/wp2static-addon-custom-crawling-storage.zip` に出来る。
```
% composer run build wp2static-addon-custom-crawling-storage
```

## プラグインの使い方

1. ダッシュボード > プラグイン　から有効化。WP2Staticが有効化していない場合は合わせて有効化。
2. WP2Static > Addon から `Enable` にする
3. 歯車アイコンから設定画面に移動
4. 以下の情報を入力。デフォルトで `*StoragePath` には `/tmp/wp2static/*` が指定されており、`perpetuatedStoragePath*` にはWordpressデフォルトのアップロードパスが入力されている。
    - `crawlingStoragePath`: クロールしてSSGしたものを保存する場所
    - `processingStoragePath`: SSGしたHTML内の内部リンクをデプロイ先のリンクに置換する際に利用する場所
    - `perpetuatedStoragePathForCrawledSite`: SSGしたファイルを永続化するための場所
    - `perpetuatedStoragePathForProcessedSite`: リンク置換後のファイルを永続化するための場所
