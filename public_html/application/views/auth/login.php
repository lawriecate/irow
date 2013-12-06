<div class="container">
      <!-- Example row of columns -->
    <div class="row">
        <div class="col-lg-12">
          <h2>Login</h2>
          <? if($display_redirected_message) { ?>
          <div class="alert alert-info">Please login to access iRow</div>
          <? } ?>
       		<?php echo validation_errors('<div class="alert alert-danger">','</div>'); ?>
			<?php echo form_open($action,array('role'=>'form')); ?>

			  <div class="form-group">
			    <label class="control-label" for="inputEmail">Email</label>
			    <input type="text" class="form-control" name="email" id="inputEmail" placeholder="Email" value="<?= set_value('email') ?>" autofocus>
			  </div>
			  <div class="form-group">
			    <label class="control-label" for="inputPassword">Password</label>
			    <input type="password" class="form-control" name="password" id="inputPassword" placeholder="Password">
			  </div>
			  <div class="form-group">
			   
			      <label class="checkbox">
			      <input type="checkbox"> Remember me
			      </label>
			      
			  </div>
			  <button type="submit" class="btn btn-primary">Sign in</button>
			  <a  href="<?= base_url('register/') ?>" type="submit" class="btn">Register</a>
			</form>
		</div>
	</div>
</div>