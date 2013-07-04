<div class="row">
	<div id="content" class="span6 offset3">
		<form class="form-horizontal">
			<h2 class="form-signin-heading"><?php echo __('auth.register.title'); ?></h2>
			<div class="control-group">
				<label class="control-label" for="first_name">First Name</label>
				<div class="controls">
					<div class="input-prepend">
						<span class="add-on"><i class="icon-user"></i></span>
						<input type="text" class="input-xlarge" id="first_name" name="first_name" placeholder="First Name">
					</div>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Last Name</label>
				<div class="controls">
					<div class="input-prepend">
						<span class="add-on"><i class="icon-user"></i></span>
						<input type="text" class="input-xlarge" id="lname" name="lname" placeholder="Last Name">
					</div>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Email</label>
				<div class="controls">
					<div class="input-prepend">
						<span class="add-on"><i class="icon-envelope"></i></span>
						<input type="text" class="input-xlarge" id="email" name="email" placeholder="Email">
					</div>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Gender</label>
				<div class="controls">

						<p><div id="gender" name="gender" class="btn-group" data-toggle="buttons-radio">
						<button type="button" class="btn btn-info">Male</button>
						<button type="button" class="btn btn-info">Female</button>

					  </div></p>

				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Password</label>
				<div class="controls">
					<div class="input-prepend">
					<span class="add-on"><i class="icon-lock"></i></span>
						<input type="Password" id="passwd" class="input-xlarge" name="passwd" placeholder="Password">
					</div>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Confirm Password</label>
				<div class="controls">
					<div class="input-prepend">
					<span class="add-on"><i class="icon-lock"></i></span>
						<input type="Password" id="conpasswd" class="input-xlarge" name="conpasswd" placeholder="Re-enter Password">
					</div>
				</div>
			</div>

			<button class="btn btn-large btn-primary" type="submit"><?php echo __('auth.login.fields.sign_in'); ?></button>
		</form>
		<?php
			echo \Html::anchor(\Uri::create('auth/register'), __('auth.register.title'));
			echo "<br />";
			echo \Html::anchor(\Uri::create('auth/reset'), __('auth.reset.title'));
		?>
	</div>
</div>