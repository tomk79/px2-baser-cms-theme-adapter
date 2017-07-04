<?php
/**
 * メールフォーム送信メール本体（スマホ用）
 * 呼出箇所：メールフォーム（メール送信時）
 */
?>

<?php echo $other['date']; ?>
━━━━━━━━━━━━━━
　お問合せを受付けました
━━━━━━━━━━━━━━

<?php if ($other['mode'] === 'user'): ?>
	　この度は、お問い合わせいただきありがとうございます。
	　送信内容は下記のようになっております。
<?php elseif ($other['mode'] === 'admin'): ?>
	　<?php echo $mailConfig['site_name'] ?> へのお問い合わせを受け付けました。
	　受信内容は下記のとおりです。
<?php endif; ?>

━◇◆━━━━━━━━━━━
　◆ お問い合わせ内容
━━━━━━━━━━━◆◇━
<?php echo $this->element('../Emails/text/mail_data') ?>



──────────────

<?php if ($other['mode'] === 'user'): ?>
	　なお、このメールは自動返信メールとなっております。
	　メールを確認させて頂き次第、早急にご連絡させていただきます。
	　恐れ入りますがしばらくお待ちください。
<?php elseif ($other['mode'] === 'admin'): ?>
	　なお、このメールは自動転送システムです。
	　受け付けた旨のメールもユーザーへ送られています。
<?php endif; ?>

━━━━━━━━━━━━━━

<?php echo $mailConfig['site_name'] ?>

<?php echo $mailConfig['site_url'] ?>

<?php echo $mailConfig['site_email'] ?>

<?php if($mailConfig['site_tel']): ?>TEL　<?php echo $mailConfig['site_tel'] ?><?php endif; ?>

<?php if($mailConfig['site_fax']): ?>FAX　<?php echo $mailConfig['site_fax'] ?><?php endif; ?>

━━━━━━━━━━━━━━


