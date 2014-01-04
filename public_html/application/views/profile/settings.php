<div class="container">
    <div class="row">
        <div class="col-lg-12">
          <h2>My Account</h2>
          <? if (isset($saved)) { ?><div class="alert alert-success">Your settings have been saved</div><? } ?>
       		<?php echo validation_errors('<div class="alert alert-danger">','</div>'); ?>
      <?php echo form_open('/profile/settings',array('role'=>'form','class'=>'')); ?>
			  <div class="form-group">
			    <label class="control-label" for="inputEmail">Email</label>
			    <input type="text" class="form-control" name="email" id="inputEmail" value="<?= $profile['email'] ?>">
			  </div>
			   <div class="form-group">
			    <label class="control-label" for="inputName">Name</label>
			    <input type="text" class="form-control" name="name" id="inputName" value="<?= $profile['name'] ?>">
			  </div>
        <div class="form-group">
          <label class="control-label" for="inputClubs">Clubs:</label>
          <input type="text" class="form-control" name="clubs" id="inputClubs" disabled="disabled" value="<?= $clubs ?>">
        </div>
			   <div class="form-group">
			    <label class="control-label" for="inputPassword">Enter New Password:</label>
			    <input type="password" class="form-control" name="password" id="inputPassword" value="">
			  </div>
			  <div class="form-group">
			    <label class="control-label" for="inputPassword">Confirm New Password:</label>
			    <input type="password" class="form-control" name="password2" id="inputPassword2" value="">
			  </div>
			  <div class="form-group">
            <label class="col-lg-2 control-label" for="dob">Date Of Birth</label>
            
              <input type="text" class="form-control" id="dob" name="dob" placeholder="00-00-0000" value="<?= $profile['dob'] ?>" autofocus>
        
          </div>
          <div class="form-group">
            <label class="col-lg-2 control-label" for="gender">Gender</label>
            <div class="col-lg-10">
              <div class="radio">
                <label>
                  <input type="radio" name="gender" id="optionsRadios1" value="m" <?= ($profile['gender'] == 'm' ? 'checked="checked"' : '') ?> >
                  Male </label>
              </div>
              <div class="radio">
                <label>
                  <input type="radio" name="gender" id="optionsRadios2" value="f" <?= ($profile['gender'] == 'f' ? 'checked="checked"' : '') ?> >
                  Female </label>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-lg-10">
              <button type="submit" class="btn btn-success">Update Profile</button>
            </div>
          </div>
			
			</form>
		</div>
	</div>
</div>