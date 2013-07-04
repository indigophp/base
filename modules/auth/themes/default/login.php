<div class="row">
	<div id="content" class="span3 offset4">
		<form class="form-signin">
			<h2 class="form-signin-heading"><?php echo __('auth.login.title'); ?></h2>
			<input type="text" class="input-block-level" placeholder="<?php echo __('auth.login.fields.username'); ?>">
			<input type="password" class="input-block-level" placeholder="<?php echo __('auth.login.fields.password'); ?>">
			<label class="checkbox">
				<input type="checkbox" value="remember-me"> <?php echo __('auth.login.fields.remember_me'); ?>
			</label>
			<button class="btn btn-large btn-primary" type="submit"><?php echo __('auth.login.fields.sign_in'); ?></button>
		</form>
		<?php
			echo \Html::anchor(\Uri::create('auth/register'), __('auth.register.title'));
			echo "<br />";
			echo \Html::anchor(\Uri::create('auth/reset'), __('auth.reset.title'));
		?>
	</div>
</div>