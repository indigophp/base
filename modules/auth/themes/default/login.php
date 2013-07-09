<div class="row">
	<div id="content" class="span3 offset4">
		<form class="form-signin">
			<h2 class="form-signin-heading"><?php echo __('auth.login.title'); ?></h2>
			<div class="input-prepend">
				<span class="add-on"><i class="icon-user"></i></span>
				<input type="text" class="input-block-level" name="username" placeholder="<?php echo __('auth.login.fields.username'); ?>">
			</div>
			<div class="input-prepend">
				<span class="add-on"><i class="icon-lock"></i></span>
				<input type="password" class="input-block-level" placeholder="<?php echo __('auth.login.fields.password'); ?>">
			</div>
			<label class="checkbox">
				<input type="checkbox" value="remember-me"> <?php echo __('auth.login.fields.remember_me'); ?>
			</label>
			<button class="btn btn-large btn-primary pull-right" type="submit"><?php echo __('auth.login.fields.sign_in'); ?></button>
		</form>
		<?php
			echo \Html::anchor(\Uri::create('auth/register'), __('auth.register.title'));
			echo \Html::br();
			echo \Html::anchor(\Uri::create('auth/reset'), __('auth.reset.title'));
		?>
	</div>
</div>