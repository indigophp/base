<div class="row">
	<div id="content" class="span3 offset4">
		<form class="form-signin">
			<h2 class="form-signin-heading"><?php echo __('auth.reset.title'); ?></h2>
			<div class="input-prepend">
				<span class="add-on"><i class="icon-user"></i></span>
				<input type="text" class="input-block-level" name="username" placeholder="<?php echo __('auth.reset.fields.username'); ?>">
			</div>
			<button class="btn btn-large btn-primary pull-right" type="submit"><?php echo __('auth.reset.fields.reset'); ?></button>
		</form>
		<?php echo \Html::anchor(\Uri::create('auth/login'), __('auth.login.title')); ?>
	</div>
</div>