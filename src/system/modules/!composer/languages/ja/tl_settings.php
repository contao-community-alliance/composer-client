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
 * last-updated: 2015-07-10T00:40:21+02:00
 */


$GLOBALS['TL_LANG']['tl_settings']['composerAutoUpdateLibrary']['0']                         = 'composerライブラリの自動更新';
$GLOBALS['TL_LANG']['tl_settings']['composerAutoUpdateLibrary']['1']                         = 'composerライブラリ(<code>composer.phar</code>とも言う)を30日毎に自動更新します。';
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionMode']['0']                             = '実行モード';
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionMode']['1']                             = 'composerのファイルをどのように実行するか選択してください。';
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes']['detached']['0']                = '単独のプロセスとして実行';
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes']['detached']['1']                = 'Composerを単独のサブプロセスとして実行し、切り離してバックグラウンドの処理にします。このモードが不可能または許可していないシステムがあります。(バックグラウンドのプロセスの起動を許可しているかどうかはプロバイダーに確認してください。)  この方式には実行時間の制限はりません。';
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes']['inline']['0']                  = 'HTTP要求内で実行';
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes']['inline']['1']                  = 'Composerをウェブサーバーのプロセス内で実行します。このモードは通常遅いですがすべてのシステムで動作します。そして、PHPの最大実行時間の制限の対象となります。';
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes']['process']['0']                 = 'Webサーバーのサブプロセスで実行';
$GLOBALS['TL_LANG']['tl_settings']['composerExecutionModes']['process']['1']                 = 'Composerを外部のプログラムとしてサブプロセスで実行します。このモードは通常速いですが、proc_open()をサポートしているシステムだけで実行可能です。そして、PHPの最大実行時間の制限の対象となります。';
$GLOBALS['TL_LANG']['tl_settings']['composerPhpPath']['0']                                   = 'PHPのパスとコマンド';
$GLOBALS['TL_LANG']['tl_settings']['composerPhpPath']['1']                                   = 'パスまたはphpの実行ファイルのコマンド';
$GLOBALS['TL_LANG']['tl_settings']['composerProfiling']['0']                                 = 'プロファイリングを有効';
$GLOBALS['TL_LANG']['tl_settings']['composerProfiling']['1']                                 = '時間とメモリの仕様情報を表示します。';
$GLOBALS['TL_LANG']['tl_settings']['composerRemoveRepositoryTables']['0']                    = 'リポジトリのクライアントのテーブルを削除';
$GLOBALS['TL_LANG']['tl_settings']['composerRemoveRepositoryTables']['1']                    = 'composerクライアントの更新ツールは、このチェックボックスを有効にしない限り、古いER2のリポジトリのクライアントの表を削除しません。';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_DEBUG']['0']        = 'デバッグメッセージ';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_DEBUG']['1']        = '一般的なユーザには殆ど不要なデバッグメッセージを含む、すべてのメッセージを表示します。';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_NORMAL']['0']       = '初期状態';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_NORMAL']['1']       = '初期状態の冗長レベル - 何も問題が起きていない場合はこれを使用してください。';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_QUIET']['0']        = '静粛に!';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_QUIET']['1']        = 'どのようなメッセージも表示しません。';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_VERBOSE']['0']      = '冗長';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_VERBOSE']['1']      = 'メッセージの冗長性を上げます。';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_VERY_VERBOSE']['0'] = 'たいへん冗長';
$GLOBALS['TL_LANG']['tl_settings']['composerVerbosityLevels']['VERBOSITY_VERY_VERBOSE']['1'] = '本質的でない情報のメッセージも表示します。';
$GLOBALS['TL_LANG']['tl_settings']['composer_legend']                                        = 'Composerの設定';

