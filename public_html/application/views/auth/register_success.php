<div class="container">
      <!-- Example row of columns -->
    <div class="row">
    	<div class="col-md-12">
    		 <h2>You have created your account!</h2>
             <p>Before you start using iRow, you need to setup your profile</p>
             <? /*if($registration_failure == TRUE){ ?>
        	<div class="alert alert-danger">
        		<h4>Your account could not be registered due to a technical problem, please contact support@irow.co.uk</h4>
        	</div>
        	<? } */?>
        	<?php //echo validation_errors('<div class="alert alert-danger">','</div>'); ?>
			<?php //echo form_open('register',array('role'=>'form','class'=>'form-horizontal')); ?>
			  <div class="form-group">
			    <label class="col-lg-2 control-label" for="inputDob">Date Of Birth</label>
			    <div class="col-lg-10">
			      <input type="date" class="form-control" id="inputDob" name="dob" placeholder="00-00-0000">
			    </div>
			  </div>
			  <div class="form-group">
			    <label class="col-lg-2 control-label" for="inputDob">Gender</label>
			    <div class="col-lg-10">
			      <div class="radio">
					  <label>
					    <input type="radio" name="optionsRadios" id="optionsRadios1" value="option1" checked>
					    Male
					  </label>
					</div>
					<div class="radio">
					  <label>
					    <input type="radio" name="optionsRadios" id="optionsRadios2" value="option2">
					    Female
					  </label>
					</div>
			    </div>
			  </div>
              
              <div class="form-group">
			    <label class="col-lg-2 control-label" for="inputDob">Height</label>
			    <div class="col-lg-10">
                <div class="row">
                    <div class="col-xs-2">
                      <div class="input-group ">
                          <input type="text" class="form-control" maxlength="3" >
                          <span class="input-group-addon">cm</span>
                      </div>
                    </div>
			    </div>
			  </div>
              <div class="form-group">
			    <label class="col-lg-2 control-label" for="inputDob">Arm Span</label>
			    <div class="col-lg-10">
                <div class="row">
                    <div class="col-xs-2">
                      <div class="input-group ">
                          <input type="text" class="form-control" maxlength="3" >
                          <span class="input-group-addon">cm</span>
                      </div>
                    </div>
			    </div>
			  </div>
              
              <div class="form-group">
			    <label class="col-lg-2 control-label" for="inputDob">Weight</label>
			    <div class="col-lg-10">
                <div class="row">
                    <div class="col-xs-2">
                      <div class="input-group ">
                          <input type="text" class="form-control" maxlength="3" >
                          <span class="input-group-addon">kg</span>
                      </div>
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