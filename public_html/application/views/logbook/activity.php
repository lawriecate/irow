<div class="container">
    <div class="row">
        <div class="col-lg-12">
        	<?/*<h4>Activity Record</h4>*/?>
        	<h1><?=$activity['label']?> <small><?=date("D jS M Y",strtotime($activity['sort_time']))?></small></h1>
        	<?/*<p class="text-muted">Added <?$activity['added']?><?//=relative_time(strtotime($activity['added']))?></p>*/?>
        	<? if($activity['total_time'] != NULL) { ?><h2><small>Time</small> <?= $this->activity_model->outputSplit($activity['total_time']) ?></h2><? } ?>
        	<? if($activity['avg_split'] != NULL) { ?><h2><small>Split</small> <?= $this->activity_model->outputSplit($activity['avg_split']) ?></h2><? } ?>
        	<? if($activity['total_distance'] != NULL) { ?><h2><small>Distance</small> <?= $activity['total_distance'] ?></h2><? } ?>
            <p><a id="btnDelete" href="<?=base_url()?>logbook/detail/<?=$activity['ref']?>?delete" class="btn btn-danger">Delete</a></p>
        </div>
    </div>
</div>
<script type="text/javascript">
$("#btnDelete").click(function() {
    event.preventDefault();
    $.get('<?=base_url()?>logbook/ajax_delete?ref=<?=$activity['ref']?>', function(data) {
        if(data == true) {
            $("#btnDelete").text("Deleted");
            $("#btnDelete").attr("disabled","disabled");
            $("#viewWorkoutModal").modal('hide');
            $("#ex<?=$activity['ref']?>").fadeOut();
        } else {
            alert('Error: Could not delete activity');
        }
    }); 
});
</script>