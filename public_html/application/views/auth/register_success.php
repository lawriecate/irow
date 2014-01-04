<div class="container"> 
  <!-- Example row of columns -->
  <div class="row">
    <div class="col-md-12">
      <h2>You have created your account!</h2>
      <p>Before you start using iRow, you need to setup your profile</p>
      <? if($system_error == TRUE){ ?>
        	<div class="alert alert-danger">
        		<h4>Your account could not be updated due to a technical problem, please contact support@irow.co.uk</h4>
        	</div>
        	<? } ?>
      <?php echo validation_errors('<div class="alert alert-danger">','</div>'); ?>
      <?php echo form_open('/register/setup',array('role'=>'form','class'=>'form-horizontal')); ?>
          <div class="form-group">
            <label class="col-lg-2 control-label" for="dob">Date Of Birth</label>
            <div class="col-lg-10">
              <input type="text" class="form-control" id="dob" name="dob" placeholder="00-00-0000" value="<?= set_value('dob') ?>" autofocus>
            </div>
          </div>
          <div class="form-group">
            <label class="col-lg-2 control-label" for="gender">Gender</label>
            <div class="col-lg-10">
              <div class="radio">
                <label>
                  <input type="radio" name="gender" id="optionsRadios1" value="m" <?= set_checkbox('gender', 'm', TRUE) ?> >
                  Male </label>
              </div>
              <div class="radio">
                <label>
                  <input type="radio" name="gender" id="optionsRadios2" value="f" <?= set_checkbox('gender', 'f') ?>>
                  Female </label>
              </div>
            </div>
          </div>
          <p>Optional: </p>
          <div class="form-group">
            <label class="col-lg-2 control-label" for="height">Height</label>
            
                <div class="col-xs-2">
                  <div class="input-group ">
                    <input name="height" type="text" class="form-control" maxlength="3" value="<?= set_value('height') ?>" >
                    <span class="input-group-addon">cm</span> 
                  </div>
                </div>
              
          </div>
          <div class="form-group">
            <label class="col-lg-2 control-label" for="armspan">Arm Span</label>
          
                <div class="col-xs-2">
                  <div class="input-group ">
                    <input name="armspan" type="text" class="form-control" maxlength="3" value="<?= set_value('armspan') ?>" >
                    <span class="input-group-addon">cm</span> </div>
                </div>
              
          </div>
          <div class="form-group">
            <label class="col-lg-2 control-label" for="weight">Weight</label>
            
                <div class="col-xs-2">
                  <div class="input-group ">
                    <input name="weight" type="text" class="form-control" maxlength="4" value="<?= set_value('weight') ?>" >
                    <span class="input-group-addon">kg</span> </div>
                </div>
             
          </div>
          <div class="form-group">
            <label class="col-lg-2 control-label" for="club">Club</label>
            <div class="col-lg-4">
               <select id="inputClub" name="club" class="form-control">
                  <optgroup label="All Clubs">
                    <? foreach($clubs as $club): ?>
                      <option value="<?=$club['id']?>"><?=$club['name']?></option>
                    <? endforeach; ?>
                  </optgroup>
                  <optgroup label="Other Options">
                    <option value="0">*** No Club ***</option>
                  </optgroup>
                </select>
            </div>
             <p>Please remember selecting a club means verified coaches can see and add to your performance data</p>
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
<script type="text/javascript">
$(document).ready(function() {
  $("#inputClub").selectize({
    create: true
  });
});
</script>
