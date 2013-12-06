<?php /*?><div class="modal fade" id="viewWorkoutModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false"><?php */?>
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button id="showAddForm" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">View Log</h4>
        </div>
        <div class="modal-body">
        	<p><?= $exercise['label'] ?></p>
        	<p><pre><? print_r($exercise) ?></pre></p>
          </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
<?php /*?>  </div><!-- /.modal --><?php */?>