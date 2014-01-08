<div class="container">
      <!-- Example row of columns -->
    <div class="row">
        <div class="col-lg-12">
          <h2>Update Club</h2>
       		<? if (isset($saved)) { ?><div class="alert alert-success">Club record saved</div><? } ?>
          	<?php echo validation_errors('<div class="alert alert-danger">','</div>'); ?>
      		<?php echo form_open('/club/manage/'.$club['ref'],array('role'=>'form','class'=>'form-horizontal')); ?>			
      		  <div class="form-group">
			    <label class="control-label" for="inputName">Name</label>
			    <input type="text" class="form-control" name="name" id="inputName"  value="<?= set_value('name',$club['name'])?>" placeholder="">
			  </div>
			  <div class="form-group">
			    <label class="control-label" for="inputCoaches">Coaches</label>
			    <input type="text" class="form-control" name="coaches" id="inputCoaches"  disabled="disabled" value="<?=$coach_list?>">                                         
			  </div>
			   <div class="form-group">
			    <label class="control-label" for="inputManagers">Managers</label>
			    <input type="text" class="form-control" name="managers" id="inputManagers"  disabled="disabled" value="<?=$manager_list?>">                                         
			  </div>
			  <div class="form-group">
			    <label class="control-label" for="inputEmail">Email</label>
			    <input type="text" class="form-control" name="email" id="inputEmail" value="<?= set_value('email',$club['email'])?>" >
			  </div>
			  <div class="form-group">
			    <label class="control-label" for="inputWebsite">Website</label>
			    <input type="text" class="form-control" name="website" id="inputWebsite" value="<?= set_value('website',$club['website'])?>">
			  </div>
			  <div class="form-group">
			    <label class="control-label" for="inputTel">Telephone Number</label>
			    <input type="text" class="form-control" name="tel" id="inputTel" value="<?= set_value('tel',$club['phone'])?>" >
			  </div>
			  <div class="form-group">
			    <label class="control-label" for="inputAddr1">Address Line 1</label>
			    <input type="text" class="form-control" name="addr_1" id="inputAddr1" value="<?= set_value('addr_1',$club['addr_1'])?>" >
			  </div>
			  <div class="form-group">
			    <label class="control-label" for="inputAddr2">Address Line 2</label>
			    <input type="text" class="form-control" name="addr_2" id="inputAddr2" value="<?= set_value('addr_2',$club['addr_2'])?>" >
			  </div>
			  <div class="form-group">
			    <label class="control-label" for="inputAddrCity">Address Line: City</label>
			    <input type="text" class="form-control" name="addr_city" id="inputAddrCity" value="<?= set_value('addr_city',$club['addr_city'])?>" >
			  </div>
			  <div class="form-group">
			    <label class="control-label" for="inputAddrCountry">Address Line: Country</label>
			    <select id="inputAddrCountry" name="addr_country"  class="form-control">
	              <? foreach($countries as $country): ?>
	              <option value="<?=$country['code']?>" <?php echo set_select('addr_country', $country['code'],($club['addr_country']==$country['code'])); ?> ><?=$country['label']?></option>
	              <? endforeach; ?>
	            </select>
			  </div>
			
			  <div class="form-group">
			    <label class="control-label" for="inputAddrPostcode">Address Line: Postcode</label>
			    <input type="text" class="form-control" name="addr_postcode" id="inputAddrPostcode" value="<?= set_value('addr_postcode',$club['addr_postcode'])?>" >
			  </div>
			  <button type="submit" class="btn btn-primary">Update Club</button>
			
			</form>
		</div>
	</div>
</div>