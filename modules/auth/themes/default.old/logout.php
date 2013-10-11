<div class="row">
	<div id="content" class="span3 offset4">
		<h2><?php echo __('auth.logout.messages.success'); ?></h2>
		<?php
			echo \Html::anchor(\Uri::create('auth/login'), __('auth.login.title'));
			echo \Html::br();
			echo \Html::anchor(\Uri::create('auth/register'), __('auth.register.title'));
		?>
	</div>
</div>