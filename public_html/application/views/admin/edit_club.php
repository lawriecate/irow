<div class="container">

      
        <h1>Edit Club        </h1>
        <? if (isset($saved)) { ?><div class="alert alert-success">Club record saved</div><? } ?>
          <?php echo validation_errors('<div class="alert alert-danger">','</div>'); ?>
      <?php echo form_open('/admin/edit_club/'.$club['id'],array('role'=>'form','class'=>'form-horizontal')); ?>


         
         <div class="form-group">
             <label for="inputName" class="col-lg-2 control-label">Name</label>
            <div class="col-lg-10">
              <input type="text" class="form-control" id="inputName" name="name" value="<?= set_value('name',$club['name'])?>" >
          </div>
          </div>
      
         <div class="form-group">
             <label for="inputEmail" class="col-lg-2 control-label">Email</label>
            <div class="col-lg-10">
              <input type="text" class="form-control" id="inputEmail" name="email" value="<?= set_value('email',$club['email'])?>" >
          </div>
          </div>
          <div class="form-group">
             <label for="inputWebsite" class="col-lg-2 control-label">Website</label>
            <div class="col-lg-10">
              <input type="text" class="form-control" id="inputWebsite" name="website" value="<?= set_value('website',$club['website'])?>" >
          </div>
          </div>
          <div class="form-group">
             <label for="inputTel" class="col-lg-2 control-label">Telephone Number</label>
            <div class="col-lg-10">
              <input type="text" class="form-control" id="inputTel" name="tel" value="<?= set_value('phone',$club['phone'])?>" >
          </div>
          </div>
          
          
          <div class="form-group">
             <label for="inputAddr1" class="col-lg-2 control-label">Address Line 1</label>
            <div class="col-lg-10">
              <input type="text" class="form-control" id="inputAddr1" name="addr1" value="<?= set_value('addr1',$club['addr_1'])?>" >
          </div>
          </div>
          
            <div class="form-group">
             <label for="inputAddr2" class="col-lg-2 control-label">Address Line 2</label>
            <div class="col-lg-10">
              <input type="text" class="form-control" id="inputAddr2" name="addr2" value="<?= set_value('addr2',$club['addr_2'])?>" >
          </div>
          </div>
          
           <div class="form-group">
             <label for="inputAddrPostcode" class="col-lg-2 control-label">Postcode</label>
            <div class="col-lg-10">
              <input type="text" class="form-control" id="inputAddrCity" name="addrPostcode" value="<?= set_value('addrPostcode',$club['addr_postcode'])?>" >
          </div>
          </div>
          
           <div class="form-group">
             <label for="inputAddrCity" class="col-lg-2 control-label">City</label>
            <div class="col-lg-10">
              <input type="text" class="form-control" id="inputAddrCity" name="addrCity" value="<?= set_value('addrCity',$club['addr_city'])?>" >
          </div>
          </div>
          
          <div class="form-group">
             <label for="inputAddrCountry" class="col-lg-2 control-label">Country</label>
            <div class="col-lg-10">
              <?/*<input type="text" class="form-control" id="inputAddrCountry" name="addrCountry" value="<?= set_value('addrCountry')?>" >*/?>
              <select id="inputAddrCountry" name="addrCountry"  class="form-control">
              <? foreach($countries as $country): ?>
              <option value="<?=$country['code']?>" <?php echo set_select('addrCountry', $country['code'],($club['addr_country']==$country['code'])); ?> ><?=$country['label']?></option>
              <? endforeach; ?>
              </select>
          </div>
          

            <div class="form-group">
            <label for="verify" class="col-lg-2 control-label">Verification Status</label>
             <div class="col-lg-6">
              <div class="radio">
                  <label class="text-danger">
                    <input type="radio" name="verify" id="inputVerifyNo" value="2" <?= set_radio('verify','2',$club['verified'] == '2') ?> >
                    <strong>Suspend</strong>
                  </label>
                </div>
               <div class="radio">
                  <label class="text-warning">
                    <input type="radio" name="verify" id="inputVerifyNo" value="" <?= set_radio('verify','0',$club['verified'] == '') ?> >
                    <strong>Unverified</strong>
                  </label>
                </div>
                <div class="radio">
                  <label class="text-success">
                    <input type="radio" name="verify" id="inputVerifyYes" value="1" <?= set_radio('verify','1',$club['verified'] == '1') ?> >
                    <strong>Verified</strong></label></div>
                
              </div><!-- /.col-lg-6 -->
            
          </div>
        
   
          
          
          <div class="form-group">
            <div class="col-lg-offset-2 col-lg-10">
              <button type="submit" class="btn btn-default">Save Club</button>
            </div>
          </div>
          
        </form>
     

    </div><!-- /.container -->