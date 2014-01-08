<div class="container">
    <div class="row">
      <div class="col-lg-12">
       	<h1>Analyse</h1>
		<p>Show Graph:</p>
		<form id="graphForm" role="form">
		  <div class="form-group">
		    <label class="sr-only" for="inputGraphType">Graph Type</label>
		    <select class="form-control" name="inputGraphType" id="inputGraphType">
		    <? foreach($graphs as $key => $graph): ?>
            <option value="<?=$key?>"><?=$graph['name']?></option>
            <? endforeach; ?>
	      </select>
		  </div>
		  <div class="form-group">
		    <label class="sr-only" for="inputStart">Start</label>
			<input class="form-control"  name="inputStart" type="text" id="inputStart" value="<?=date("d-m-Y",(time() - (31 * 86400)))?>">
		  </div>
		   <div class="form-group">
		    <label class="sr-only" for="inputEnd">End</label>
		    <input class="form-control"  type="text" name="inputEnd" id="inputEnd" value="<?=date("d-m-Y")?>">
		  </div>
		  <div class="form-group">
		    <label class="sr-only" for="inputPeople">Who</label>
		    <input class="form-control"  type="text" name="inputPeople" id="inputPeople">
		  </div>
		<button type="submit" class="btn btn-default">Load Graph</button>
		</form>		
        </div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			 <div id="dashboard">
                    <div id="chart" style='width: 100%; height: 600px;'>&nbsp;</div>
                </div>
        </div>
        <div id='table_div'></div>
    </div>
</div>	
<script type="text/javascript" src="//www.google.com/jsapi"></script>
<script type="text/javascript">
 initGraphs();
  function initGraphs() {
   
      google.load('visualization', '1.0', {'packages':['corechart','controls','table']});
   //google.setOnLoadCallback();
    
  }
$(document).ready(function() {

     
	$("#inputPeople").selectize({
                valueField: 'user_id',
                labelField: 'name',
                searchField: 'name',
                maxItems: 25,
                options: [],
                render: {
                    option: function(item, escape) {
                        return '<div>' +
                            '<span class="title">' +
                                '<span class="name">' + escape(item.name) + '</span>' +
                            '</span>' +
                            '<span class="clubs">' + escape(item.clubs || 'NA') + '</span>' +
                           
                        '</div>';
                    }
                },
                load: function(query, callback) {
                    if (!query.length) return callback();
                    $.ajax({
                        url: '<?=base_url()?>coach/ajax_namesuggest',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            q: query
                        },
                        error: function() {
                            callback();
                        },
                        success: function(res) {
                            callback(res);
                        }
                    });
                },
                create: false
            });

	$("#graphForm").submit(function() {	
		event.preventDefault();
		var data = {
			type: $("#inputGraphType").val(),
			start: $("#inputStart").val(),
			end: $("#inputEnd").val(),
			who: $("#inputPeople").val(),
		};
		
		//// LOAD GRAPH

			$.ajax({
              url: "<?=base_url()?>coach/ajax_graphdata",
              dataType:"text",
              data: data,
              async: false
            }).done(function(ajaxData) { 
            	// ajax request sent back
               var data = new google.visualization.DataTable(ajaxData);

                var chart = new google.visualization.LineChart(document.getElementById('chart'));
                chart.draw(data);
                 var table = new google.visualization.Table(document.getElementById('table_div'));
                table.draw(data, {showRowNumber: true});
            	// end ajax handler
            });
		   

		////////////////
	});
});
</script>
