<div class="container">
    <div class="row">
        <div class="col-lg-12">
        	<?/*<h4>Activity Record</h4>*/?>
        	<h1><?=$activity['label']?> <small><?=date("D jS M Y",strtotime($activity['sort_time']))?></small></h1>
        	<p class="text-muted">Added <?=relative_time(strtotime($activity['added']))?></p>
        	<? if($activity['total_time'] != NULL) { ?><h2><small>Time</small> <?= $this->activity_model->outputSplit($activity['total_time']) ?></h2><? } ?>
        	<? if($activity['avg_split'] != NULL) { ?><h2><small>Split</small> <?= $this->activity_model->outputSplit($activity['avg_split']) ?></h2><? } ?>
        	<? if($activity['total_distance'] != NULL) { ?><h2><small>Distance</small> <?= $activity['total_distance'] ?></h2><? } ?>
        </div>
    </div>
</div>