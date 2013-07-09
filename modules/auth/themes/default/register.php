<div class="row">
	<div id="content" class="span6 offset3">
		<form class="form-horizontal">
			<h2 class="form-signin-heading"><?php echo __('auth.register.title'); ?></h2>
			<div class="control-group">
				<label class="control-label" for="username"><?php echo __('auth.register.fields.username'); ?></label>
				<div class="controls">
					<div class="input-prepend">
						<span class="add-on"><i class="icon-user"></i></span>
						<input type="text" class="input-xlarge" id="username" name="username" placeholder="<?php echo __('auth.register.fields.username'); ?>">
					</div>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="first_name"><?php echo __('auth.register.fields.first_name'); ?></label>
				<div class="controls">
					<div class="input-prepend">
						<span class="add-on"><i class="icon-user"></i></span>
						<input type="text" class="input-xlarge" id="first_name" name="first_name" placeholder="<?php echo __('auth.register.fields.first_name'); ?>">
					</div>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="last_name"><?php echo __('auth.register.fields.last_name'); ?></label>
				<div class="controls">
					<div class="input-prepend">
						<span class="add-on"><i class="icon-user"></i></span>
						<input type="text" class="input-xlarge" id="last_name" name="last_name" placeholder="<?php echo __('auth.register.fields.last_name'); ?>">
					</div>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="email"><?php echo __('auth.register.fields.email'); ?></label>
				<div class="controls">
					<div class="input-prepend">
						<span class="add-on"><i class="icon-envelope"></i></span>
						<input type="text" class="input-xlarge" id="email" name="email" placeholder="<?php echo __('auth.register.fields.email'); ?>">
					</div>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="password"><?php echo __('auth.register.fields.password'); ?></label>
				<div class="controls">
					<div class="input-prepend">
					<span class="add-on"><i class="icon-lock"></i></span>
						<input type="password" class="input-xlarge" id="password" name="password" placeholder="<?php echo __('auth.register.fields.password'); ?>">
					</div>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="confirm_password"><?php echo __('auth.register.fields.confirm_password'); ?></label>
				<div class="controls">
					<div class="input-prepend">
					<span class="add-on"><i class="icon-lock"></i></span>
						<input type="password" class="input-xlarge" id="confirm_password" name="confirm_password" placeholder="<?php echo __('auth.register.fields.confirm_password'); ?>">
					</div>
				</div>
			</div>

			<button class="btn btn-large btn-primary pull-right" type="submit"><?php echo __('auth.register.fields.register'); ?></button>
		</form>
		<?php echo \Html::anchor(\Uri::create('auth/login'), __('auth.login.title')); ?>
	</div>
</div>