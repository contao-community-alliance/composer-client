<?php
/**
 * Translations are managed using Transifex. To create a new translation
 * or to help to maintain an existing one, please register at transifex.com.
 *
 * @link http://help.transifex.com/intro/translating.html
 * @link https://www.transifex.com/projects/p/composer/language/ja/
 *
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 *
 * last-updated: 2016-05-04T02:00:14+02:00
 */


$GLOBALS['TL_LANG']['composer_client']['added_candidate']               = 'パッケージ%sをバージョン%sで追加しています。変更を適用するにはパッケージを更新してください。';
$GLOBALS['TL_LANG']['composer_client']['check']                         = '互換性を確認';
$GLOBALS['TL_LANG']['composer_client']['clear_composer_cache']          = 'Composerのキャッシュを消去';
$GLOBALS['TL_LANG']['composer_client']['close']                         = '閉じる';
$GLOBALS['TL_LANG']['composer_client']['composerCacheCleared']          = 'Composerのキャッシュを消去しました。';
$GLOBALS['TL_LANG']['composer_client']['composerUpdateNecessary']       = '非互換なComposerのライブラリを使用しています。Composerのライブラリの更新が必要で、そうしないとComposerクライアントは期待した動作をしないかもしれません。';
$GLOBALS['TL_LANG']['composer_client']['composerUpdateRequired']        = 'Composerが30日を越えてフルいーバージョンです、Composerを更新してください。';
$GLOBALS['TL_LANG']['composer_client']['composerUpdated']               = 'Composerを更新しました。';
$GLOBALS['TL_LANG']['composer_client']['composer_install_headline']     = 'Composerのインストール';
$GLOBALS['TL_LANG']['composer_client']['composer_missing']              = 'Composerのライブラリを完全にインストールできていません。<br><strong>Composerをインストール</strong>をクリックしてComposerとその依存するパッケージをインストールしてください。';
$GLOBALS['TL_LANG']['composer_client']['configValid']                   = 'この構成は正しい状態です。';
$GLOBALS['TL_LANG']['composer_client']['confirmRemove']                 = '%sのパッケージを本当に削除しますか?';
$GLOBALS['TL_LANG']['composer_client']['confirmRemovePackages']         = 'これらのパッケージを本当に削除しますか: %s?';
$GLOBALS['TL_LANG']['composer_client']['could_not_disable_apc']         = 'APCを無効にできません。<br>APCとComposerは様々なエラーを引き起こします、<a href="http://php.net/apc" target="_blank">APC</a>を無効にしてください。';
$GLOBALS['TL_LANG']['composer_client']['curl_missing']                  = 'パッケージのダウンロードにcURLが必要です。<br>PHPの<a href="http://php.net/curl" target="_blank">curl</a>モジュールをインストールまたは有効にしてください。';
$GLOBALS['TL_LANG']['composer_client']['databaseUpdated']               = 'データベースを更新しました、%dの問い合わせを実行しました。';
$GLOBALS['TL_LANG']['composer_client']['databaseUptodate']              = 'データベースは最新の状態です。';
$GLOBALS['TL_LANG']['composer_client']['dependency_graph_headline']     = '依存関係のグラフ';
$GLOBALS['TL_LANG']['composer_client']['dependency_of']                 = '%sの依存関係';
$GLOBALS['TL_LANG']['composer_client']['dependency_recursion']          = '(循環した依存関係)';
$GLOBALS['TL_LANG']['composer_client']['detached']                      = 'パッケージを更新';
$GLOBALS['TL_LANG']['composer_client']['discard_changes_no']            = '保持(更新を取り止め)';
$GLOBALS['TL_LANG']['composer_client']['discard_changes_stash']         = '隠して再適用';
$GLOBALS['TL_LANG']['composer_client']['discard_changes_yes']           = '破棄';
$GLOBALS['TL_LANG']['composer_client']['download_impossible']           = 'ダウンロード処理をできません。PHPのzip拡張を有効にするかproc_open()がunzipを実行できるか確かめてください。';
$GLOBALS['TL_LANG']['composer_client']['dry-run']                       = 'リハーサル';
$GLOBALS['TL_LANG']['composer_client']['editor_headline']               = '専門家モード';
$GLOBALS['TL_LANG']['composer_client']['errors_headline']               = 'システム要件';
$GLOBALS['TL_LANG']['composer_client']['experts_mode']                  = '専門家モード';
$GLOBALS['TL_LANG']['composer_client']['ftp_mode']                      = 'セーフモード対処はサポートしていません。<br>ホスティングサービスを構成してサーフモード対処なしでContaoを実行<br>&rarr;<a href="http://de.contaowiki.org/Safemode_Hack" target="_blank">Contao Wikiのセーフモード対処に関する記事(German)</a>';
$GLOBALS['TL_LANG']['composer_client']['incompatiblePackage']           = '(このContaoのバージョンに非互換)';
$GLOBALS['TL_LANG']['composer_client']['incompatiblePackageLong']       = 'このContaoのバージョンに、このパッケージのバージョンは互換性がありません。';
$GLOBALS['TL_LANG']['composer_client']['install_auto']                  = '自動';
$GLOBALS['TL_LANG']['composer_client']['install_composer']              = 'Composerをインストール';
$GLOBALS['TL_LANG']['composer_client']['install_dist']                  = '配布アーカイブ';
$GLOBALS['TL_LANG']['composer_client']['install_headline']              = 'パッケージをインストール';
$GLOBALS['TL_LANG']['composer_client']['install_source']                = 'ソース';
$GLOBALS['TL_LANG']['composer_client']['install_via']                   = '%sによる: %s';
$GLOBALS['TL_LANG']['composer_client']['installed_headline']            = 'インストール済みのパッケージ';
$GLOBALS['TL_LANG']['composer_client']['installed_in']                  = 'バージョン%sをインストール済み';
$GLOBALS['TL_LANG']['composer_client']['mark_and_install']              = '今すぐパッケージをインストール';
$GLOBALS['TL_LANG']['composer_client']['mark_to_install']               = 'パッケージをインストールするように印付け';
$GLOBALS['TL_LANG']['composer_client']['migrate']                       = '移行';
$GLOBALS['TL_LANG']['composer_client']['migrate_clean']['0']            = 'パッケージを削除';
$GLOBALS['TL_LANG']['composer_client']['migrate_clean']['1']            = '以前にインストールしたパッケージを削除して、真新しい設定で開始します。';
$GLOBALS['TL_LANG']['composer_client']['migrate_development']['0']      = '開発向け';
$GLOBALS['TL_LANG']['composer_client']['migrate_development']['1']      = 'パッケージはgit、mercurial、svnのいずれかを使用してソースで取得します。ファイルはシンボリックリンクでインストールします。';
$GLOBALS['TL_LANG']['composer_client']['migrate_do']                    = '移行を実施';
$GLOBALS['TL_LANG']['composer_client']['migrate_faq']                   = '
<h2>よくある質問</h2>
<ul class="questions">
<li>
    <h3>このクライアントを使用しないといけませんか?</h3>
    その必要はまったくありません、あくまでも選択肢です。しかし、新しい機能や新しい機能拡張をこのパッケージ管理だけで提供している開発者もいます。使用しないと、本質的な更新を見落とすかもしれません。
</li>
<li>
    <h3>現在の機能拡張リポジトリで利用できている機能拡張をインストールできますか?</h3>
   はい、できます。すべての公開されているパッケージを新しいリポジトリに同期しています。(それらは<em>contao-legacy/</em>を最初に付けています。)
    <em>既存の有償の機能拡張はライセンスの制約によりComposerでインストールできません。
    製作者にComposerをサポートするように依頼してください。</em>
</li>
<li>
    <h3>新しい機能拡張リポジトリがあるのですか?</h3>
    はい、新しい機能拡張リポジトリは<a href="http://legacy-packages-via.contao-community-alliance.org/"
      target="_blank">legacy-packages-via.contao-community-alliance.org</a>にあります。
    現在は単純なpackagistによるインストールですが、すべての必要な要件を含めるように直に改善する予定です。
</li>
<li>
    <h3>ComposerとこのComposerパッケージ管理とは何ですか?</h3>
    この質問への答はここに書くには長すぎます。<a href="http://de.contaowiki.org/Composer_Client" target="_blank">Contao Wiki</a>にあるComposerクライアントについての記事を読んでください。
</li>
<li>
    <h3>古いパッケージ管理に戻せますか?</h3>
    はい、できますが、たいへんな作業が必要です。ガイドとして<a href="https://github.com/contao-community-alliance/composer-client/wiki/Switch-back">wiki</a>を読んでください。
</li>
<li>
    <h3>新しいクライアントに問題があtります、どこに手助けを求めればよいでしょうか?</h3>
    このクライアントはコミュニティによって動いています。
    <a href="https://community.contao.org/de/forumdisplay.php?6-Entwickler-Fragen" target="_blank">コミュニティ・ボード</a>、公式のIRCチャンネル、<a href="irc://chat.freenode.net/%23contao.composer">#contao.composer</a>、 <a href="https://github.com/contao-community-alliance/composer/issues" target="_blank">チケットシステム</a>に問い合わせで来ます。
</li>
</ul>';
$GLOBALS['TL_LANG']['composer_client']['migrate_intro']                 = '
<p>ユーザーの皆様、PHPの依存関係マネージャーに基づいた、新しいContaoのパッケージ管理です。
   <a href="http://getcomposer.org/" target="_blank">Composer</a>.</p>
<p>これは公開ベータテストの段階にあります。この機能拡張管理をContaoの初期設定のものとするには、皆様の手助けとテストが必要です。</p>';
$GLOBALS['TL_LANG']['composer_client']['migrate_mode']['0']             = '移行モード';
$GLOBALS['TL_LANG']['composer_client']['migrate_mode']['1']             = '古い機能拡張管理で%d個の機能拡張のインストールを検出しました。古いパッケージをどうすべきか、次に確認させてください。';
$GLOBALS['TL_LANG']['composer_client']['migrate_none']['0']             = '何もしません(専門家向けだけ!)';
$GLOBALS['TL_LANG']['composer_client']['migrate_none']['1']             = '何もしません; すべてをそのままに保持します。これは問題となる可能性がありますので、何を行おうとしているかわかっている場合だけ選択してください。';
$GLOBALS['TL_LANG']['composer_client']['migrate_preconditions']         = '
<h2>前提条件</h2>
<ul class="preconditions">
<li class="{if smhEnabled==true}fail{else}pass{endif}">
  セーフモード対処は{if smhEnabled==true}有効{else}無効{endif}
</li>
<li class="{if allowUrlFopenEnabled==true}pass{else}fail{endif}">
  allow_url_fopenは{if allowUrlFopenEnabled==true}有効{else}無効{endif}
</li>
<li class="{if pharSupportEnabled==true}pass{else}fail{endif}">
  PHARのサポートは{if pharSupportEnabled==true}有効{else}無効{endif}
</li>
<li class="{if composerSupported==true}pass{else}fail{endif}">
  {if composerSupported==true}Composerクライアントを使用できます。 :-){else}Composerクライアントを使用できません。 :-({endif}
</li>
{if commercialPackages!==false}
<li class="fail">
  有償の機能拡張をインストールしています: ##commercialPackages##.<br>
  移行によって機能拡張を失うかもしれません。<br>
  機能拡張の製作者と相談してください、製作者がComposerをサポートしている場合は気にせずに使用続けられるでしょう。
</li>
{endif}
<li class="{if apcOpcodeCacheEnabled==true}warn{else}pass{endif}">
  APCオペコード・キャッシュは
  {if apcOpcodeCacheEnabled==true}有効で、予期しない例外を起こすかもしれません。
  "cannot redeclare class"というエラーとなった場合は、APCオペコードキャッシュを無効にしてください。
  {elseif apcDisabledByUs==true}
  Composerクライアントによって一時的に無効
  {else}
  無効
  {endif}.</li>
</ul>';
$GLOBALS['TL_LANG']['composer_client']['migrate_production']['0']       = '実稼働向け';
$GLOBALS['TL_LANG']['composer_client']['migrate_production']['1']       = 'パッケージをアーカイブされた状態で取得します。(zipのサポートだけが必要です。)  ファイルをコピーしてインストールします。';
$GLOBALS['TL_LANG']['composer_client']['migrate_setup']['0']            = '構成の設定';
$GLOBALS['TL_LANG']['composer_client']['migrate_setup']['1']            = 'このインストールで使用する設定を選択してください。';
$GLOBALS['TL_LANG']['composer_client']['migrate_setup_pre']             = '
<h2>設定の移行</h2>
<p>新しいクライアントを始める前に、いくつかの質問をします。</p>';
$GLOBALS['TL_LANG']['composer_client']['migrate_skip']                  = '移行を省略(何を行うか理解している場合だけ使用)';
$GLOBALS['TL_LANG']['composer_client']['migrate_skip_confirm']          = '移行を省略するのは危険です、何を行うか理解している場合だけ移行の省略を指定してください。本当に移行を省略しますか?';
$GLOBALS['TL_LANG']['composer_client']['migrate_upgrade']['0']          = 'パッケージをComposerにアップグレード';
$GLOBALS['TL_LANG']['composer_client']['migrate_upgrade']['1']          = '既存のパッケージをComposerパッケージ管理に追加して、再インストールします。';
$GLOBALS['TL_LANG']['composer_client']['migrationDone']                 = '移行を完了しました。';
$GLOBALS['TL_LANG']['composer_client']['migrationSkipped']              = '移行を省略しました。';
$GLOBALS['TL_LANG']['composer_client']['noInstallationCandidates']      = '<em>%s</em>への候補が見つかりません。';
$GLOBALS['TL_LANG']['composer_client']['noSearchResult']                = '<em>%s</em>へのパッケージがありません!';
$GLOBALS['TL_LANG']['composer_client']['no_conflicts']                  = '競合なし';
$GLOBALS['TL_LANG']['composer_client']['no_provides']                   = '製作者なし';
$GLOBALS['TL_LANG']['composer_client']['no_releasedate']                = '-';
$GLOBALS['TL_LANG']['composer_client']['no_replaces']                   = '置き換えなし';
$GLOBALS['TL_LANG']['composer_client']['no_requires']                   = '依存関係なし';
$GLOBALS['TL_LANG']['composer_client']['no_suggests']                   = '推奨なし';
$GLOBALS['TL_LANG']['composer_client']['not_installed']                 = 'インストールの要求';
$GLOBALS['TL_LANG']['composer_client']['package_authors']               = '開発者';
$GLOBALS['TL_LANG']['composer_client']['package_conflicts']             = '競合';
$GLOBALS['TL_LANG']['composer_client']['package_dependend_version']     = '依存しているバージョン';
$GLOBALS['TL_LANG']['composer_client']['package_homepage']              = 'ホームページ';
$GLOBALS['TL_LANG']['composer_client']['package_installed_version']     = 'インストール済みのバージョン';
$GLOBALS['TL_LANG']['composer_client']['package_keywords']              = 'キーワード';
$GLOBALS['TL_LANG']['composer_client']['package_name']                  = 'パッケージ';
$GLOBALS['TL_LANG']['composer_client']['package_provides']              = '製作者';
$GLOBALS['TL_LANG']['composer_client']['package_reference']             = '参照';
$GLOBALS['TL_LANG']['composer_client']['package_replaces']              = '置き換え';
$GLOBALS['TL_LANG']['composer_client']['package_requested_version']     = '要求したバージョン';
$GLOBALS['TL_LANG']['composer_client']['package_requires']              = '依存関係';
$GLOBALS['TL_LANG']['composer_client']['package_source']                = 'ソース';
$GLOBALS['TL_LANG']['composer_client']['package_suggests']              = '推奨';
$GLOBALS['TL_LANG']['composer_client']['package_support']               = 'サポート';
$GLOBALS['TL_LANG']['composer_client']['package_support_email']         = '電子メール';
$GLOBALS['TL_LANG']['composer_client']['package_support_irc']           = 'IRCチャット';
$GLOBALS['TL_LANG']['composer_client']['package_support_issues']        = 'Issues';
$GLOBALS['TL_LANG']['composer_client']['package_support_source']        = 'ソース';
$GLOBALS['TL_LANG']['composer_client']['package_support_wiki']          = 'Wiki';
$GLOBALS['TL_LANG']['composer_client']['package_type']                  = '種類';
$GLOBALS['TL_LANG']['composer_client']['package_version']               = 'バージョン';
$GLOBALS['TL_LANG']['composer_client']['php_version']                   = 'PHPのバージョンは<strong>PHP %1$s</strong>以降が必要です。使用しているシステムのPHPバージョンは<strong>%2$s</strong>です。<br>PHPを更新してください。';
$GLOBALS['TL_LANG']['composer_client']['pinPackage']                    = 'バージョンを固定';
$GLOBALS['TL_LANG']['composer_client']['pluginNotFound']                = 'Contao Composerプラグインがありません!';
$GLOBALS['TL_LANG']['composer_client']['removeCandidate']               = 'パッケージ%sを削除しています。変更を適用するにはパッケージを更新してください。';
$GLOBALS['TL_LANG']['composer_client']['removePackage']                 = 'パッケージを削除';
$GLOBALS['TL_LANG']['composer_client']['removePackages']                = '削除';
$GLOBALS['TL_LANG']['composer_client']['resyncFailed']                  = '%sのパッケージの再同期をできませでした、次のメッセージがあります: %s';
$GLOBALS['TL_LANG']['composer_client']['resyncPackage']                 = 'パッケージを再同期中: %s';
$GLOBALS['TL_LANG']['composer_client']['resyncedPackage']               = '%sのパッケージを同期しました。';
$GLOBALS['TL_LANG']['composer_client']['save']                          = '保存';
$GLOBALS['TL_LANG']['composer_client']['search']                        = '検索';
$GLOBALS['TL_LANG']['composer_client']['search_headline']               = '検索結果';
$GLOBALS['TL_LANG']['composer_client']['search_placeholder']            = 'パッケージ名またはキーワード';
$GLOBALS['TL_LANG']['composer_client']['settings_dialog']               = '設定';
$GLOBALS['TL_LANG']['composer_client']['show_dependants']               = '依存するパッケージを表示';
$GLOBALS['TL_LANG']['composer_client']['show_dependencies']             = '%d個の依存パッケージがインストール';
$GLOBALS['TL_LANG']['composer_client']['show_dependency_graph']         = '依存関係のグラフ';
$GLOBALS['TL_LANG']['composer_client']['solve_headline']                = '依存関係';
$GLOBALS['TL_LANG']['composer_client']['stability_alpha']               = 'アルファリリース';
$GLOBALS['TL_LANG']['composer_client']['stability_beta']                = 'ベータリリース';
$GLOBALS['TL_LANG']['composer_client']['stability_dev']                 = '開発版リリース';
$GLOBALS['TL_LANG']['composer_client']['stability_rc']                  = 'リリース候補';
$GLOBALS['TL_LANG']['composer_client']['stability_stable']              = '安定';
$GLOBALS['TL_LANG']['composer_client']['suhosin_enabled']               = 'Suhosinが有効です。<br>SuhosinはPharのサポートを壊します、<a href="http://www.hardened-php.net/suhosin/" target="_blank">Suhosin</a>を無効にしてください。';
$GLOBALS['TL_LANG']['composer_client']['terminate']                     = '中断';
$GLOBALS['TL_LANG']['composer_client']['toBeRemoved']                   = '削除します';
$GLOBALS['TL_LANG']['composer_client']['tools_dialog']                  = 'ツール';
$GLOBALS['TL_LANG']['composer_client']['tools_resync']['0']             = '再同期';
$GLOBALS['TL_LANG']['composer_client']['tools_resync']['1']             = 'すべての隠されたコピーとシンボリックリンクを同期します。';
$GLOBALS['TL_LANG']['composer_client']['tools_resync']['2']             = '今すぐ再同期';
$GLOBALS['TL_LANG']['composer_client']['unknown_license']               = '不明なライセンス';
$GLOBALS['TL_LANG']['composer_client']['unpinPackage']                  = 'バージョンの固定を解除';
$GLOBALS['TL_LANG']['composer_client']['unpinablePackage']              = '開発版は固定できません';
$GLOBALS['TL_LANG']['composer_client']['update']                        = 'パッケージを更新';
$GLOBALS['TL_LANG']['composer_client']['update_composer']               = 'Composerを更新';
$GLOBALS['TL_LANG']['composer_client']['update_database']               = 'データベースを更新';
$GLOBALS['TL_LANG']['composer_client']['vcs_requirements']              = '
<ul class="preconditions">
<li class="{if gitAvailable==true}pass{else}fail{endif}">
    gitが{if gitAvailable==true}available{else}ありません、殆どのパッケージのインストールに失敗するでしょう!{endif}
</li>
<li class="{if hgAvailable==true}pass{else}fail{endif}">
    mercurialが{if hgAvailable==true}available{else}ありません、いくつかのパッケージのインストールに失敗するでしょう!{endif}
</li>
<li class="{if svnAvailable==true}pass{else}fail{endif}">
    svnが{if svnAvailable==true}available{else}ありません、いくつかのパッケージのインストールに失敗するでしょう{endif}
</li>
</ul>
';
$GLOBALS['TL_LANG']['composer_client']['version_bugfix']                = 'バグ修正リリース %s(%s)';
$GLOBALS['TL_LANG']['composer_client']['version_exact']                 = '期待するバージョン %s';
$GLOBALS['TL_LANG']['composer_client']['version_feature']               = '機能リリース %s(%s)';
$GLOBALS['TL_LANG']['composer_client']['version_micro']                 = 'マイクロリリース %s(%s)';
$GLOBALS['TL_LANG']['composer_client']['version_upstream']              = '%s(%s)から上位のリリース';
$GLOBALS['TL_LANG']['composer_client']['widget_discard_changes']['0']   = '変更を破棄';
$GLOBALS['TL_LANG']['composer_client']['widget_discard_changes']['1']   = '変更をどのように処理するか選択してください。';
$GLOBALS['TL_LANG']['composer_client']['widget_github_oauth']['0']      = 'GitHubのoauthトークン';
$GLOBALS['TL_LANG']['composer_client']['widget_github_oauth']['1']      = 'GitHubで"api limit reached"の問題がある場合は、ここにGitHubのoauthトークンを入力してください。';
$GLOBALS['TL_LANG']['composer_client']['widget_minimum_stability']['0'] = '最低限の安定性';
$GLOBALS['TL_LANG']['composer_client']['widget_minimum_stability']['1'] = '最低限の安定性では、インストールを許可する最小の安定性を設定します。';
$GLOBALS['TL_LANG']['composer_client']['widget_prefer_stable']['0']     = '安定性を優先';
$GLOBALS['TL_LANG']['composer_client']['widget_prefer_stable']['1']     = '可能であれば、最低限の安定性が安定版よりも低くても安定版を優先します。';
$GLOBALS['TL_LANG']['composer_client']['widget_preferred_install']['0'] = '優先するインストール';
$GLOBALS['TL_LANG']['composer_client']['widget_preferred_install']['1'] = 'ソースパッケージ(git、mercurial、svnを必要)か、配布アーカイブ(何時でも動作可能)のどちらを優先するか選択してください。';

