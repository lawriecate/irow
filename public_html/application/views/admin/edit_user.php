<div class="container">

      
        <h1>Edit User        </h1>
        <? if (isset($saved)) { ?><div class="alert alert-success">User record saved</div><? } ?>
          <?php echo validation_errors('<div class="alert alert-danger">','</div>'); ?>
      <?php echo form_open('/admin/edit_user/'.$profile['id'],array('role'=>'form','class'=>'form-horizontal')); ?>
          <div class="form-group">
            <label for="inputEmail1" class="col-lg-2 control-label">Email</label>
            <div class="col-lg-10">
              <input type="text" class="form-control" id="inputEmail" name="email" value="<?=$profile['email']?>" >
            </div>
          </div>
          
        
     
         
         <div class="form-group">
             <label for="inputTime1" class="col-lg-2 control-label">Name</label>
            <div class="col-lg-10">
              <input type="text" class="form-control" id="inputName" name="name" value="<?=$profile['name']?>" >
          </div>
          </div>
          <div class="form-group">
             <label for="inputDis1" class="col-lg-2 control-label">Create New Password</label>
            <div class="col-lg-10">
              <input type="text" class="form-control" id="inputPass" name="password" >
          </div>
          </div>
          <div class="form-group">
             <label for="inputSplit1" class="col-lg-2 control-label">Confirm New Password</label>
            <div class="col-lg-10">
              <input type="text" class="form-control" id="inputConfirmPass" name="password2" >
          </div>
          </div>
          
          
          <div class="form-group">
             <label for="inputRate" class="col-lg-2 control-label">D.O.B.</label>
            <div class="col-lg-10">
              <input type="text" class="form-control" id="inputDob" name="dob" value="<?=$profile['dob']?>" >
          </div>
          </div>
          
           <div class="form-group">
            <label for="gender" class="col-lg-2 control-label">Gender</label>
             <div class="col-lg-6">
               <div class="radio">
                  <label>
                    <input type="radio" name="gender" id="inputGenderM" value="m" <?= ($profile['gender'] == 'm' ? 'checked="checked"' : '') ?> >
                    Male
                  </label>
                </div>
                <div class="radio">
                  <label>
                    <input type="radio" name="gender" id="inputGenderF" value="f" <?= ($profile['gender'] == 'f' ? 'checked="checked"' : '') ?> >
                    Female</label></div>
                
              </div><!-- /.col-lg-6 -->
            
          </div>

          <div class="form-group">
            <label for="suspend" class="col-lg-2 control-label">Suspend Account</label>
             <div class="col-lg-6">
               <div class="radio">
                  <label class="text-success">
                    <input type="radio" name="suspend" id="inputSuspendNo" value="0" <?= ($profile['disabled'] == '' ? 'checked="checked"' : '') ?> >
                    Enabled
                  </label>
                </div>
                <div class="radio">
                  <label class="text-danger">
                    <input type="radio" name="suspend" id="inputSuspendYes" value="1" <?= ($profile['disabled'] == '1' ? 'checked="checked"' : '') ?> >
                    <strong>Suspend Account</strong></label></div>
                
              </div><!-- /.col-lg-6 -->
            
          </div>

          <div class="form-group">
            <label for="admin" class="col-lg-2 control-label">Administrator Account</label>
             <div class="col-lg-6">
               <div class="radio">
                  <label >
                    <input type="radio" name="admin" id="inputAdminNo" value="0" <?= ($profile['admin'] == '' ? 'checked="checked"' : '') ?> >
                    Standard Account
                  </label>
                </div>
                <div class="radio">
                  <label >
                    <input type="radio" name="admin" id="inputAdminYes" value="1" <?= ($profile['admin'] == '1' ? 'checked="checked"' : '') ?> >
                    <strong>Administrator Account</strong></label></div>
                
              </div><!-- /.col-lg-6 -->
            
          </div>
          
           <div class="form-group" >
            <label for="inputEmail1" class="col-lg-2 control-label">Register To Club</label>
           

             <div class="col-lg-6">
              
               <select id="inputRegClub" class="form-control " style="width:300px; display:inline-block">
                 <? foreach($this->club_model->get_all() as $club): ?>
                <option value="<?=$club['id']?>"><?= $club['name'] ?></option>
                <? endforeach; ?> 
                </select>
                <button id="registerClub" type="button" class="btn btn-default">Register To Club</button>
              </div><!-- /.col-lg-6 -->
  
            
          
          </div>
          
               
          <div class="form-group">

            <label for="inputEmail1" class="col-lg-2 control-label">Club Role</label>
           

             <div class="col-lg-6" id="clubsSection">
               <? foreach($memberships as $membership): 
               $name = 'clubs['.$membership['id'].']'; ?>
                    <div class="panel panel-default">
                     <div class="panel-body">
                      <?= $membership['name'] ?>
                       <div class="radio">
                          <label>
                            <input type="radio" name="<?=$name?>" id="<?='clubs'.$membership['id'].''?>O1" value="athlete" <?php if ($membership['level']=="athlete") { echo "checked=\"checked\""; } ?>>
                            Athlete
                          </label>
                        </div>
                        <div class="radio">
                          <label>
                            <input type="radio" name="<?=$name?>"  id="<?='clubs'.$membership['id'].''?>O2" value="coach"  <?php if ($membership['level']=="coach") { echo "checked=\"checked\""; } ?> >
                          Coach
                        </div>
                        <div class="radio">
                          <label>
                            <input type="radio" name="<?=$name?>"  id="<?='clubs'.$membership['id'].''?>O3" value="manager"  <?php if ($membership['level']=="manager") { echo "checked=\"checked\""; } ?> >
                            Manager</label></div>
                        <div class="radio">
                          <label>
                            <input type="radio" name="<?=$name?>"  id="<?='clubs'.$membership['id'].''?>O4" value="REMOVE" >
                            Remove Membership</label></div>
                    </div><!-- /.col-lg-6 -->
                  </div>
                <? endforeach; ?>
  
              </div>
            </div>
          </div>
      
          
          <div class="form-group">
            <div class="col-lg-offset-2 col-lg-10">
              <button type="submit" class="btn btn-default">Save User</button>
            </div>
          </div>
          
        </form>
     

    </div><!-- /.container -->
    <script type="text/javascript">
    $(document).ready(function() {
      $("#registerClub").click(function() {
      
        $.get('<?=base_url()?>admin/ajax_user_regclub?id=<?=$profile['id']?>&club='+$("#inputRegClub").val(),function(data) {
          if(data.status==true) {
            $("#clubsSection").append(data.panel);
          }
        });
      });
    });
    </script>