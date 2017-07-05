<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [管理画面] サイト設定 フォーム
 */
$this->BcBaser->js('admin/site_configs/form', false, array('id' => 'AdminSiteConfigsFormScript',
	'data-safeModeOn' => (string) $safeModeOn
));
?>


<h2>基本項目</h2>


<?php echo $this->BcForm->create('SiteConfig', ['url' => ['action' => 'form']]) ?>
<?php echo $this->BcForm->hidden('SiteConfig.id') ?>
<div class="section">
<table cellpadding="0" cellspacing="0" class="form-table section">
	<tr>
		<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.formal_name', 'WEBサイト名') ?>&nbsp;<span class="required">*</span></th>
		<td class="col-input">
			<?php echo $this->BcForm->input('SiteConfig.formal_name', array('type' => 'text', 'size' => 55, 'maxlength' => 255, 'autofocus' => true, 'class' => 'full-width')) ?>
			<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpFormalName', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
			<?php echo $this->BcForm->error('SiteConfig.formal_name') ?>
			<div id="helptextFormalName" class="helptext">
				<ul>
					<li>正式なWEBサイト名を指定します。</li>
					<li>メールの送信元等で利用します。</li>
				</ul>
			</div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.name', 'WEBサイトタイトル') ?>&nbsp;<span class="required">*</span></th>
		<td class="col-input">
			<?php echo $this->BcForm->input('SiteConfig.name', array('type' => 'text', 'size' => 55, 'maxlength' => 255, 'counter' => true, 'class' => 'full-width')) ?>
			<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpName', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
			<?php echo $this->BcForm->error('SiteConfig.name') ?>
			<div id="helptextName" class="helptext">
				<ul>
					<li>Webサイトの基本タイトルとして利用されます。（タイトルタグに影響します）</li>
					<li>テンプレートで利用する場合は、<br />
						&lt;?php $this->BcBaser->title() ?&gt; で出力します。</li>
				</ul>
			</div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.keyword', 'サイト基本キーワード') ?></th>
		<td class="col-input"><?php echo $this->BcForm->input('SiteConfig.keyword', array('type' => 'text', 'size' => 55, 'maxlength' => 255, 'counter' => true, 'class' => 'full-width')) ?>
			<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpKeyword', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
			<?php echo $this->BcForm->error('SiteConfig.keyword') ?>
			<div id="helptextKeyword" class="helptext">テンプレートで利用する場合は、<br />
				&lt;?php $this->BcBaser->keywords() ?&gt; で出力します。</div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.description', 'サイト基本説明文') ?></th>
		<td class="col-input"><?php echo $this->BcForm->input('SiteConfig.description', array('type' => 'textarea', 'cols' => 36, 'rows' => 5, 'counter' => true)) ?>
			<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpDescription', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
			<?php echo $this->BcForm->error('SiteConfig.description') ?>
			<div id="helptextDescription" class="helptext">テンプレートで利用する場合は、<br />
				&lt;?php $this->BcBaser->description() ?&gt; で出力します。</div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.email', '管理者メールアドレス') ?>&nbsp;<span class="required">*</span></th>
		<td class="col-input">
			<?php echo $this->BcForm->input('SiteConfig.email', array('type' => 'text', 'size' => 35, 'maxlength' => 255)) ?>
			<?php echo $this->BcForm->error('SiteConfig.email') ?>
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.admin_list_num', '管理システムの<br />初期一覧件数') ?>&nbsp;<span class="required">*</span></th>
		<td class="col-input">
			<?php
			echo $this->BcForm->input('SiteConfig.admin_list_num', array('type' => 'select', 'options' => array(
					10 => '10件',
					20 => '20件',
					50 => '50件',
					100 => '100件'
			)))
			?>
<?php echo $this->BcForm->error('SiteConfig.admin_list_num') ?>
		</td>
	</tr>
	<?php echo $this->BcForm->dispatchAfterForm() ?>
</table>
</div>

<h2 class="btn-slide-form"><a href="javascript:void(0)" id="formOption">オプション</a></h2>

<div id ="formOptionBody" class="slide-body section">
	<table cellpadding="0" cellspacing="0" class="form-table">
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.login_credit', 'ログインページのクレジット表示') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.login_credit', array('type' => 'radio', 'options' => $this->BcText->booleanDoList('利用'))) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div class="helptext">ログインページに表示されているクレジット表示を利用するかどうか設定します。</div>
				<?php echo $this->BcForm->error('SiteConfig.login_credit') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.admin_side_banner', '管理システムサイドバーの<br />バナー表示') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.admin_side_banner', array('type' => 'radio', 'options' => $this->BcText->booleanDoList('利用'))) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div class="helptext">管理システムのサイド部分にバナーを表示するかどうか設定します。</div>
				<?php echo $this->BcForm->error('SiteConfig.admin_side_banner') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.site_url', 'WebサイトURL') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.site_url', array_merge(array('type' => 'text', 'size' => 35, 'maxlength' => 255), $disableSettingInstallSetting)) ?><br />
				<?php echo $this->BcForm->input('SiteConfig.ssl_url', array_merge(array('type' => 'text', 'size' => 35, 'maxlength' => 255, 'after' => '<small>[SSL]</small>'), $disableSettingInstallSetting)) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpSiteUrl', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php if ($disableSettingInstallSetting): ?>
					<?php echo $this->BcForm->input('SiteConfig.site_url', array('type' => 'hidden')) ?>
				<?php endif ?>
				<?php echo $this->BcForm->error('SiteConfig.site_url') ?>
				<?php echo $this->BcForm->error('SiteConfig.ssl_url') ?>
				<div id="helptextSiteUrl" class="helptext">baserCMSを設置しているURLを指定します。管理画面等でSSL通信を利用する場合は、SSL通信で利用するURLも指定します。</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.admin_ssl', '管理画面SSL設定') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.admin_ssl', array_merge(array('type' => 'radio', 'options' => $this->BcText->booleanDoList('SSL通信を利用'), 'separator' => '　', 'legend' => false), $disableSettingInstallSetting)) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpAdminSsl', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $this->BcForm->error('SiteConfig.admin_ssl') ?>
				<div id="helptextAdminSslOn" class="helptext">管理者ページでSSLを利用する場合は、事前にSSLの申込、設定が必要です。<br />
					また、SSL用のWebサイトURLの指定が必要です。</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.address', 'GoogleMaps住所') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.address', array('type' => 'text', 'size' => 35, 'maxlength' => 255, 'placeholder' => '住所')) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpAddress', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextAddress" class="helptext">GoogleMapを利用する場合は地図を表示させたい住所を入力してください。郵便番号からでも大丈夫です。<br>
					<br>
					入力例1) 福岡市中央区大名2-11-25<br>
					入力例2) 〒819-0041 福岡県福岡市中央区大名2-11-25<br>
					<br>
					建物名を含めるとうまく表示されない場合があります。<br>
					その時は建物名を省略して試してください。<br>APIキーを入力しないと地図が表示されない場合があります。<a href="https://developers.google.com/maps/web/" target="_blank">「ウェブ向け Google Maps API」</a></div>
				<br />
				<?php echo $this->BcForm->input('SiteConfig.google_maps_api_key', array('type' => 'text', 'size' => 35, 'maxlength' => 255, 'placeholder' => 'APIキー')) ?>
				<?php echo $this->BcForm->error('SiteConfig.address') ?>
				<?php echo $this->BcForm->error('SiteConfig.google_maps_api_key') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.google_analytics_id', 'Google Analytics<br />トラッキングID') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.google_analytics_id', array('type' => 'text', 'size' => 35, 'maxlength' => 16)) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpGoogleAnalyticsId', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextGoogleAnalyticsId" class="helptext">
					Googleの無料のアクセス解析サービス <a href="http://www.google.com/intl/ja/analytics/" target="_blank">Google Analytics</a> を利用される方は、取得したトラッキングID (UA-000000-01 のような文字列）を入力してください。<br />
					※事前に<a href="http://www.google.com/intl/ja/analytics/" target="_blank">Google Analytics</a> で登録作業が必要です。<br />
					テンプレートで利用する場合は、 <pre>&lt;?php $this->BcBaser->googleAnalytics() ?&gt;</pre> で出力します。
				</div>
				<?php echo $this->BcForm->error('SiteConfig.google_analytics_id') ?><br />
				ユニバーサルアナリティクスを <?php echo $this->BcForm->input('SiteConfig.use_universal_analytics', array('type' => 'radio', 'options' => array('0' => '利用していない', '1' => '利用している'))) ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.widget_area', '標準ウィジェットエリア') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.widget_area', array('type' => 'select', 'options' => $this->BcForm->getControlSource('WidgetArea.id'), 'empty' => 'なし')) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpWidgetArea', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextWidgetArea" class="helptext">
					公開ページ全般で利用するウィジェットエリアを指定します。<br />
					ウィジェットエリアは「<?php $this->BcBaser->link('ウィジェットエリア管理', array('controller' => 'widget_areas', 'action' => 'index')) ?>」より追加できます。
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.main_site_display_name', 'メインサイト表示名称') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.main_site_display_name', array('type' => 'text', 'size' => 35, 'maxlength' => 255)) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('class' => 'btn help', 'alt' => 'ヘルプ')) ?>
					<div class="helptext">サブサイトを利用する際に、メインサイトを特定する識別名称を設定します。</div>
				<?php echo $this->BcForm->error('SiteConfig.main_site_display_name') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.use_site_device_setting', 'デバイス・言語設定') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.use_site_device_setting', ['type' => 'checkbox', 'label' => 'サブサイトでデバイス設定を利用する']) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div class="helptext">サブサイトにデバイス属性を持たせ、サイトアクセス時、ユーザーエージェントを判定し適切なサイトを表示する機能を利用します。</div>
				<?php echo $this->BcForm->input('SiteConfig.use_site_lang_setting', ['type' => 'checkbox', 'label' => 'サブサイトで言語設定を利用する']) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div class="helptext">サブサイトに言語属性を持たせ、サイトアクセス時、ブラウザの言語設定を判定し適切なサイトを表示する機能を利用します。</div>
				<?php echo $this->BcForm->error('SiteConfig.use_site_device_setting') ?>
				<?php echo $this->BcForm->error('SiteConfig.use_site_lang_setting') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.maintenance', '公開状態') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.maintenance', array('type' => 'select', 'options' => array(0 => '公開中', 1 => 'メンテナンス中'))) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpMaintenance', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextMaintenance" class="helptext">
					公開状態を指定します。<br />
					メンテナンス中の場合に、公開ページを確認するには、管理画面にログインする必要があります。
					ただし、制作・開発モードがデバッグモードに設定されている場合は、メンテナンス中にしていても公開ページが表示されてしまいますので注意が必要です。
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.mode', '制作・開発モード') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.mode', array_merge(array('type' => 'select', 'options' => $this->BcForm->getControlSource('mode')), $disableSettingInstallSetting)) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpDebug', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextDebug" class="helptext">制作・開発時のモードを指定します。通常は、ノーマルモードを指定しておきます。<br />
					※ CakePHPのデバッグモードを指します。<br />
					※ インストールモードはbaserCMSを初期化する場合にしか利用しませんので普段は利用しないようにしてください。</div>
			</td>
		</tr>
	</table>

	<h2>エディタ設定関連</h2>

	<table cellpadding="0" cellspacing="0" class="form-table">
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.editor_enter_br', 'エディタタイプ') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.editor', array('type' => 'radio', 'options' => Configure::read('BcApp.editors'))) ?>
			</td>
		</tr>
		<tr class="ckeditor-option">
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.editor_enter_br', '改行モード') ?></th>
			<td class="col-input">
				<?php
				echo $this->BcForm->input('SiteConfig.editor_enter_br', array('type' => 'radio', 'options' => array(
						'0' => '改行時に段落を挿入する',
						'1' => '改行時にBRタグを挿入する'
				)))
				?>
			</td>
		</tr>
		<tr class="ckeditor-option">
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.editor_styles', 'エディタスタイルセット') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.editor_styles', array('type' => 'textarea', 'cols' => 36, 'rows' => 10)) ?>
						<?php echo $this->Html->image('admin/icn_help.png', array('class' => 'btn help', 'alt' => 'ヘルプ')) ?>
						<?php echo $this->BcForm->error('SiteConfig.editor_styles') ?>
				<div id="helptextFormalName" class="helptext">
					<p>固定ページなどで利用するエディタのスタイルセットをCSS形式で記述する事ができます。</p>
					<pre>
# タイトル
タグ {
	プロパティ名：プロパティ値
}

 《記述例》
 # 見出し
 h2 {
	font-size:20px;
	color:#333;
 }
					</pre>
					<p>タグにプロパティを設定しない場合は次のように記述します。</p>
					<pre>
# 見出し
h2 {}
					</pre>
				</div>
			</td>
		</tr>
	</table>

	<h2>メール設定関連</h2>

	<table cellpadding="0" cellspacing="0" class="form-table">
		<tr>
			<th><?php echo $this->BcForm->label('SiteConfig.mail_encode', 'メール送信文字コード') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.mail_encode', array('type' => 'select', 'options' => Configure::read('BcEncode.mail'))) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpEncode', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextEncode" class="helptext">送信メールの文字コードを選択します。<br />受信したメールが文字化けする場合に変更します。</div>
				<?php echo $this->BcForm->error('SiteConfig.mail_encode') ?>
			</td>
		</tr>
		<tr>
			<th><?php echo $this->BcForm->label('SiteConfig.smtp_host', 'SMTP設定') ?></th>
			<td class="col-input">
				<div style="margin-bottom: 0.5em;">
				<?php echo $this->BcForm->label('SiteConfig.smtp_host', 'ホスト') ?>
				<?php echo $this->BcForm->input('SiteConfig.smtp_host', array('type' => 'text', 'size' => 35, 'maxlength' => 255, 'autocomplete' => 'off')) ?>
				<?php echo $this->BcForm->error('SiteConfig.smtp_host') ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpSmtpHost', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextSmtpHost" class="helptext">メールの送信にSMTPサーバーを利用する場合指定します。</div>
				</div>
				<div style="margin-bottom: 0.5em;">
				<?php echo $this->BcForm->label('SiteConfig.smtp_port', 'ポート') ?>
				<?php echo $this->BcForm->input('SiteConfig.smtp_port', array('type' => 'text', 'size' => 35, 'maxlength' => 255, 'autocomplete' => 'off')) ?>
				<?php echo $this->BcForm->error('SiteConfig.smtp_port') ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div class="helptext">メールの送信にSMTPサーバーを利用する場合指定します。入力を省略した場合、25番ポートを利用します。</div>
				</div>
				<div style="margin-bottom: 0.5em;">
				<?php echo $this->BcForm->label('SiteConfig.smtp_user', 'ユーザー') ?>
				<?php echo $this->BcForm->input('SiteConfig.smtp_user', array('type' => 'text', 'size' => 35, 'maxlength' => 255, 'autocomplete' => 'off')) ?>
				<?php echo $this->BcForm->error('SiteConfig.smtp_user') ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpSmtpUsername', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextSmtpUsername" class="helptext">メールの送信にSMTPサーバーを利用する場合指定します。</div>
				</div>
				<div style="margin-bottom: 0.5em;">
				<!-- ↓↓↓自動入力を防止する為のダミーフィールド↓↓↓ -->
				<input type="password" name="dummypass" style="display: none;">
				<?php echo $this->BcForm->label('SiteConfig.smtp_password', 'パスワード') ?>
				<?php echo $this->BcForm->input('SiteConfig.smtp_password', array('type' => 'password', 'size' => 35, 'maxlength' => 255, 'autocomplete' => 'off')) ?>
				<?php echo $this->BcForm->error('SiteConfig.smtp_password') ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpSmtpPassword', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextSmtpPassword" class="helptext">メールの送信にSMTPサーバーを利用する場合指定します。</div>
				</div>
				<div style="margin-bottom: 1.5em;">
				<?php echo $this->BcForm->label('SiteConfig.smtp_tls', 'TLS暗号化') ?>
				<?php echo $this->BcForm->input('SiteConfig.smtp_tls', array('type' => 'radio', 'options' => $this->BcText->booleanDoList('TLS暗号化を利用'))) ?>
				<?php echo $this->BcForm->error('SiteConfig.smtp_tls') ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpSmtpTls', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextSmtpTls" class="helptext">SMTPサーバーがTLS暗号化を利用する場合指定します。</div>
				</div>
				<p>
					<?php echo $this->BcForm->button('メール送信テスト', array('type' => 'button', 'class' => 'button-small', 'id' => 'BtnCheckSendmail')) ?>　<span id=ResultCheckSendmail></span>
					<?php echo $this->BcBaser->img('admin/ajax-loader-s.gif', array('id' => 'AjaxLoaderCheckSendmail', 'style' => 'display:none')) ?>
				</p>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm('option') ?>
	</table>
</div>

<div class="submit">
<?php echo $this->BcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
</div>

<?php echo $this->BcForm->end() ?>
