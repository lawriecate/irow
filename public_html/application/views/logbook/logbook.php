<div class="container">
    <!-- Example row of columns -->
    <div class="row">
        <div class="col-lg-12">
        	<h1>Logbook</h1>
         	<div id="lbDiv" class="">
          	<table id="lbTable" class="table table-hover table-responsive ">
			  <thead>
			  	<tr>
			  		<th width="20%">Date</th>
			  		<th width="20%">Label</th>
			  		<th width="15%">Split</th>
			  		<th width="15%">Time</th>
			  		<th width="15%">Distance</th>
			  		<th width="15%">Rate</th>
			  	</tr>
			  </thead>
			  <tbody>
			  	<? foreach($activities as $activity): ?>
			  	<tr>
			  		<td><?= $activity['date'] ?></td>
			  		<td><?= $activity['label'] ?></td>
			  		<td><?= $activity['split'] ?></td>
			  		<td><?= $activity['time'] ?></td>
			  		<td><?= $activity['distance'] ?></td>
			  		<td><?= $activity['rate'] ?></td>
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