<div class="container">
      <!-- Example row of columns -->
    <div class="row">
        <div class="col-lg-12">
          <h2>Welcome</h2>
          <div id="graph_container">
          	<canvas id="dashChart" width="800" height="400"></canvas>
          	<script type="text/javascript">
          	//Get the context of the canvas element we want to select
			
			</script>
          </div>
         
       <h3>Recent Workouts</h3>
        <a data-toggle="modal" href="#newWorkoutModal" class="btn btn-primary btn-lg">New Workout</a>

			<table class="table table-hover">
		        <thead>
		          <tr>
		            <th>Type</th>
		            <th>When</th>
		            <th>Notes</th>
		          </tr>
		        </thead>
		        <tbody>
		          <tr>
		            <td>Erg</td>
		            <td>Yesterday</td>
		            <td>2000m</td>
		          </tr>
		          <tr>
		            <td>Water</td>
		            <td>2 days ago</td>
		            <td>2k pieces</td>
		          </tr>
		          <tr>
		            <td>Football</td>
		            <td>At the weekend</td>
		            <td>Match</td>
		          </tr>
		        </tbody>
		      </table>
		    <p><button type="button" class="btn btn-primary btn-lg btn-block">View More</button></p>
		</div>
	</div>
</div>
  <!-- Modal -->
  <div class="modal fade" id="newWorkoutModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">New Workout</h4>
        </div>
        <div class="modal-body">
          <form id="workout" role="form">
			  <div class="form-group">
			    <label for="inputTime">Time (HH:MM:SS)</label>
			    <input type="text" class="form-control" id="inputTime" placeholder="Enter time">
			  </div>
			  <div class="form-group">
			    <label for="inputDistance">Distance (Metres)</label>
			    <input type="text" class="form-control" id="inputDistance" placeholder="Enter distance">
			  </div>
			  <div class="form-group">
			    <label for="inputDistance">Split (HH:MM:SS.TT)</label>
			    <input type="text" class="form-control" id="inputSplit" placeholder="Enter 500m split">
			  </div>
			  <div class="form-group">
			    <label for="inputRate">Rate (strokes per minute)</label>
			    <input type="text" class="form-control" id="inputRate" placeholder="Enter rate">
			  </div>
			  <div class="checkbox">
			    <label>
			      <input type="checkbox"> Check me out
			    </label>
			  </div>
			</form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary">Save</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->