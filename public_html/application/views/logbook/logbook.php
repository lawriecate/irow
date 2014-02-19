<div class="container">
    <div class="row">
        <div class="col-lg-12">
        	<h1>Personal Logbook</h1>
        	<p><a role="button" href="<?=base_url()?>logbook/dl_csv" class="btn btn-default btn-xs">
  <span class="glyphicon glyphicon-download-alt"></span> Download CSV
</a></p>


         	<div id="lbDiv" class="">
          	<table id="lbTable" class="table table-hover table-responsive ">
			  <thead>
			  	<tr>
			  		<th width="14%">Date</th>
			  		<th width="14%">Label</th>
			  		<th width="14%">Split</th>
			  		<th width="14%">Time</th>
			  		<th width="14%">Distance</th>
			  		<th width="14%">Rate</th>
			  		<th width="14%">Heart Rate</th>
			  	</tr>
			  </thead>
			  <tbody>
			  	<? foreach($activities as $activity): ?>
			  	<tr>
			  		<td><?= $activity['date'] ?></td>
			  		<td><a href="<?=base_url()?>logbook/detail/<?=$activity['ref']?>"><?= $activity['label'] ?></a></td>
			  		<td><?= $activity['split'] ?></td>
			  		<td><?= $activity['time'] ?></td>
			  		<td><?= $activity['distance'] ?></td>
			  		<td><?= $activity['rate'] ?></td>
			  		<td><?= $activity['hr'] ?></td>
			  	</tr>
			  <? endforeach; ?>
			  </tbody>
			</table>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$(function() {
	var page = 1;
//$('#lbTable').fixedHeaderTable({ footer: true, cloneHeadToFoot: true, fixedColumn: false });
$(window).scroll(function() {
   if($(window).scrollTop() + $(window).height() == $(document).height() && page != false) {
       page = page + 1;
       $.getJSON( '<?=base_url()?>logbook/ajax_loadpage?p='+page,
       	function(data) {
       			
       		if($.isEmptyObject(data)) {
       			page = false;
       		} else {
	       		$.each(data,function(key,obj) {
	       			var row = $('<tr></tr>');
	       			row.append('<td>' + obj.date + '</td>');
	       			row.append('<td>' + obj.label + '</td>');
	       			row.append('<td>' + obj.split + '</td>');
	       			row.append('<td>' + obj.time + '</td>');
	       			row.append('<td>' + obj.distance + '</td>');
	       			row.append('<td>' + obj.rate + '</td>');
	       			$("#lbTable tbody").append(row);
	       		});
       		}
       	});
   }
});
});
</script>