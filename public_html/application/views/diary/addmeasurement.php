<div class="container">
      <!-- Example row of columns -->
    <div class="row">
        <div class="col-lg-12">
          <h2>Log Measurements</h2>
          <p>Fill in any new personal measurements, then press save</p>
       		<?php //echo validation_errors('<div class="alert alert-danger">','</div>'); ?>
			<form>
			  <div class="form-group">
			    <label class="control-label" for="inputEmail">Weight (kg)</label>
			    <input type="text" class="form-control" name="email" id="inputEmail" placeholder="">
			  </div>
			  <div class="form-group">
			    <label class="control-label" for="inputPassword">Height (m)</label>
			    <input type="text" class="form-control" name="password" id="inputPassword" placeholder="">
			  </div>
			  <div class="form-group">
			    <label class="control-label" for="inputPassword">Arm Span (m)</label>
			    <input type="text" class="form-control" name="password" id="inputPassword" placeholder="">
			  </div>
			  
			  <button type="submit" class="btn btn-primary">Save To Record</button>
			
			</form>
		</div>
	</div>
</div>