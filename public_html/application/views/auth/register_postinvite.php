<div class="container"> 
  <!-- Example row of columns -->
  <div class="row">
    <div class="col-md-12">
      <h2>Welcome to iRow</h2>
      <p>Please enter your password so you can login in future</p>
      <? if($system_error == TRUE){ ?>
        	<div class="alert alert-danger">
        		<h4>Your account could not be updated due to a technical problem, please contact support@irow.co.uk</h4>
        	</div>
        	<? } ?>
      <?php echo validation_errors('<div class="alert alert-danger">','</div>'); ?>
      <?php echo form_open('/register/invited_new_password',array('role'=>'form','class'=>'form-horizontal')); ?>
          <div class="form-group">
            <label class="col-lg-2 control-label" for="email">Email</label>
            <div class="col-lg-10">
              <?=$email?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-lg-2 control-label" for="password">Password</label>
            <div class="col-lg-10">
              <input type="password" class="form-control" id="password" name="password" value="" autofocus>
            </div>
          </div>
           <div class="form-group">
            <label class="col-lg-2 control-label" for="password2">Confirm Password</label>
            <div class="col-lg-10">
              <input type="password" class="form-control" id="password2" name="password2" value="">
            </div>
          </div>
          <div class="form-group">
            <div class="col-lg-10">
              <button type="submit" class="btn btn-success">Save Password</button>
            </div>
          </div>
      </form>
    </div>
  </div>
</div>