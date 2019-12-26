<?php
/**
 * The base configurations of the WordPress.
 *
 * このファイルは、MySQL、テーブル接頭辞、秘密鍵、言語、ABSPATH の設定を含みます。
 * より詳しい情報は {@link http://wpdocs.sourceforge.jp/wp-config.php_%E3%81%AE%E7%B7%A8%E9%9B%86 
 * wp-config.php の編集} を参照してください。MySQL の設定情報はホスティング先より入手できます。
 *
 * このファイルはインストール時に wp-config.php 作成ウィザードが利用します。
 * ウィザードを介さず、このファイルを "wp-config.php" という名前でコピーして直接編集し値を
 * 入力してもかまいません。
 *
 * @package WordPress
 */

// 注意: 
// Windows の "メモ帳" でこのファイルを編集しないでください !
// 問題なく使えるテキストエディタ
// (http://wpdocs.sourceforge.jp/Codex:%E8%AB%87%E8%A9%B1%E5%AE%A4 参照)
// を使用し、必ず UTF-8 の BOM なし (UTF-8N) で保存してください。

// ** MySQL 設定 - こちらの情報はホスティング先から入手してください。 ** //
/** WordPress のためのデータベース名 */
define('DB_NAME', 'thumbsmate');

/** MySQL データベースのユーザー名 */
define('DB_USER', 'thumbsmate');

/** MySQL データベースのパスワード */
define('DB_PASSWORD', 'password');
//define('DB_PASSWORD', 'x2fb86pn4a');

/** MySQL のホスト名 */
define('DB_HOST', '127.0.0.1:3306');

/** データベースのテーブルを作成する際のデータベースのキャラクターセット */
define('DB_CHARSET', 'utf8');

/** データベースの照合順序 (ほとんどの場合変更する必要はありません) */
define('DB_COLLATE', '');

/**#@+
 * 認証用ユニークキー
 *
 * それぞれを異なるユニーク (一意) な文字列に変更してください。
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org の秘密鍵サービス} で自動生成することもできます。
 * 後でいつでも変更して、既存のすべての cookie を無効にできます。これにより、すべてのユーザーを強制的に再ログインさせることになります。
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'r1khLhUiQ>>51mBT;S40dy-&tu,j)[`~sgEcy:qlz?e=c=jLG?>z]yb>|ZuR}!l2');
define('SECURE_AUTH_KEY',  'rlZea >j(s.b/{%RRk}E0USXhC:*|n_ESam3Oq.UZ^-x^k}figRhPCuVXClGuIT$');
define('LOGGED_IN_KEY',    'dQ~m#8+D1/cjGnYuq)Cs[cm}xE5#/Q5j5H-w);zl$CU^]G25Wsya4EFB,%y|S,&m');
define('NONCE_KEY',        'j_Z0tKKY-miK`tthsCU7(zoFd-=m^X}~+reSsO$1?V^-1fS1hXzI~VaGaa=wXh5_');
define('AUTH_SALT',        ' CDtw?@HM2cHIy]!2(i]6])zg0>}v!p7R0M*V3Og@#7u[|7p@+^7|<|MlSsDyY:0');
define('SECURE_AUTH_SALT', 'ki(01&`-f+ -?4ThSe(`*S Ps:l@2i$QsYo_9|/5[CFgk?aIUnJ,k0l:Wa+9DS^7');
define('LOGGED_IN_SALT',   'S2E1M>9df93amJf+v1uiS#6peE+MNI`9w2MoO>hp]^.t0bU !r9F7*@(qWQ)%Wb3');
define('NONCE_SALT',       'eAU{g;0)p$$gSJ)M4X1*wP}XchL#_+igg3:#Pj+=d[,gBl-?@J-~^W%9xE*[0.~V');

/**#@-*/

/**
 * WordPress データベーステーブルの接頭辞
 *
 * それぞれにユニーク (一意) な接頭辞を与えることで一つのデータベースに複数の WordPress を
 * インストールすることができます。半角英数字と下線のみを使用してください。
 */
$table_prefix  = 'thumbsmate_';

/**
 * ローカル言語 - このパッケージでは初期値として 'ja' (日本語 UTF-8) が設定されています。
 *
 * WordPress のローカル言語を設定します。設定した言語に対応する MO ファイルが
 * wp-content/languages にインストールされている必要があります。例えば de_DE.mo を
 * wp-content/languages にインストールし WPLANG を 'de_DE' に設定することでドイツ語がサポートされます。
 */
define('WPLANG', 'ja');

/**
 * 開発者へ: WordPress デバッグモード
 *
 * この値を true にすると、開発中に注意 (notice) を表示します。
 * テーマおよびプラグインの開発者には、その開発環境においてこの WP_DEBUG を使用することを強く推奨します。
 */
define('WP_DEBUG', false);

/* 編集が必要なのはここまでです ! WordPress でブログをお楽しみください。 */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
