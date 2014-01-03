<div class="container">

      
        <h1>Add Club        </h1>
          <?php echo validation_errors('<div class="alert alert-danger">','</div>'); ?>
      <?php echo form_open('/admin/add_club/',array('role'=>'form','class'=>'form-horizontal')); ?>


         
         <div class="form-group">
             <label for="inputName" class="col-lg-2 control-label">Name</label>
            <div class="col-lg-10">
              <input type="text" class="form-control" id="inputName" name="name" value="<?= set_value('name')?>" >
          </div>
          </div>
      
         <div class="form-group">
             <label for="inputEmail" class="col-lg-2 control-label">Email</label>
            <div class="col-lg-10">
              <input type="text" class="form-control" id="inputEmail" name="email" value="<?= set_value('email')?>" >
          </div>
          </div>
          <div class="form-group">
             <label for="inputWebsite" class="col-lg-2 control-label">Website</label>
            <div class="col-lg-10">
              <input type="text" class="form-control" id="inputWebsite" name="website" value="<?= set_value('website')?>" >
          </div>
          </div>
          <div class="form-group">
             <label for="inputTel" class="col-lg-2 control-label">Telephone Number</label>
            <div class="col-lg-10">
              <input type="text" class="form-control" id="inputTel" name="tel" value="<?= set_value('phone')?>" >
          </div>
          </div>
          
          
          <div class="form-group">
             <label for="inputAddr1" class="col-lg-2 control-label">Address Line 1</label>
            <div class="col-lg-10">
              <input type="text" class="form-control" id="inputAddr1" name="addr1" value="<?= set_value('addr1')?>" >
          </div>
          </div>
          
            <div class="form-group">
             <label for="inputAddr2" class="col-lg-2 control-label">Address Line 2</label>
            <div class="col-lg-10">
              <input type="text" class="form-control" id="inputAddr2" name="addr2" value="<?= set_value('addr2')?>" >
          </div>
          </div>
          
           <div class="form-group">
             <label for="inputAddrPostcode" class="col-lg-2 control-label">Postcode</label>
            <div class="col-lg-10">
              <input type="text" class="form-control" id="inputAddrCity" name="addrPostcode" value="<?= set_value('addrPostcode')?>" >
          </div>
          </div>
          
           <div class="form-group">
             <label for="inputAddrCity" class="col-lg-2 control-label">City</label>
            <div class="col-lg-10">
              <input type="text" class="form-control" id="inputAddrCity" name="addrCity" value="<?= set_value('addrCity')?>" >
          </div>
          </div>
          
          <div class="form-group">
             <label for="inputAddrCountry" class="col-lg-2 control-label">Country</label>
            <div class="col-lg-10">
              <?/*<input type="text" class="form-control" id="inputAddrCountry" name="addrCountry" value="<?= set_value('addrCountry')?>" >*/?>
              <select name="addrCountry"  class="form-control">
              <? foreach($countries as $country): ?>
              <option value="<?=$country['code']?>" <?php echo set_select('inputAddrCountry', $country['code'],($country['code']=="UK")); ?> ><?=$country['label']?></option>
              <? endforeach; ?>
              </select>
          </div>
          </div>
           
          </div>
          
        
   
          
          
          <div class="form-group">
            <div class="col-lg-offset-2 col-lg-10">
              <button type="submit" class="btn btn-default">Save Club</button>
            </div>
          </div>
          
        </form>
     

    </div><!-- /.container -->