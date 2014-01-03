<div class="container">
      <!-- Example row of columns -->
    <div class="row">
    	<div class="col-md-12">
    		 <h2>Register</h2>
    	</div>
        <div class="col-md-8">
        	<p>Already have an account? <a href="<?=base_url('login')?>">Click here to log in</a></p>
        	<? if($registration_failure == TRUE){ ?>
        	<div class="alert alert-danger">
        		<h4>Your account could not be registered due to a technical problem, please contact support@irow.co.uk</h4>
        	</div>
        	<? } ?>
        	<?php echo validation_errors('<div class="alert alert-danger">','</div>'); ?>
			<?php echo form_open('register',array('role'=>'form','class'=>'form-horizontal')); ?>
			  <div class="form-group">
			    <label  class="col-lg-2 control-label" for="inputEmail">Email</label>
			    <div class="col-lg-10">
			    	<input type="text" class="form-control" id="inputEmail" name="email" placeholder="Email" value="<?= set_value('email') ?>" autofocus>
				</div>
			  </div>
			  <div class="form-group">
			    <label class="col-lg-2 control-label" for="inputName">Name</label>
			    <div class="col-lg-10">
			      <input type="text" class="form-control" id="inputName" name="name" placeholder="Name" value="<?= set_value('name') ?>">
			  	</div>
			  </div>
			  <div class="form-group">
			    <label class="col-lg-2 control-label" for="inputPassword">Password</label>
			    <div class="col-lg-10">
			      <input type="password" class="form-control" id="inputPassword" name="password" placeholder="Password" value="<?= set_value('password') ?>">
			    </div>
			 </div>
			 <div class="form-group">
			    <label class="col-lg-2 control-label" for="inputCPassword">Confirm Password</label>
			    <div class="col-lg-10">
			      <input type="password" class="form-control" id="inputCPassword" name="password2" placeholder="Confirm Password">
			    </div>
			  </div>
			  <div class="form-group">
                    <label class="col-lg-10">
                        <input name="tosconsent" type="checkbox" name="checkbox" value="yes" <?= set_checkbox('tosconsent', 'yes') ?>> I agree to all your <a href="http://beta.irow.co.uk/terms.html" target="_blank">Terms of Services</a>
                    </label>
                    <div class="col-lg-10">
                        <button type="submit" class="btn btn-success">Sign Up</button>
                        <button type="button" class="btn">Help</button>
                    </div>
                

              </div>
			</form>
		</div>
	</div>
</div>