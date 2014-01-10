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
                    <div id="chart" style='width: 100%; height: 300px;'>&nbsp;</div>
                    <div id="control" style='width: 100%; height: 50px;'>&nbsp;</div>
                </div>
        </div>
    </div>
</div>	
<script type="text/javascript" src="//www.google.com/jsapi"></script>
<script type="text/javascript">
 initGraphs();
  function initGraphs() {
   
      google.load('visualization', '1.0', {'packages':['corechart','controls']});
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
              dataType:"json",
              data: data,
              async: false
            }).done(function(data) { 
            	// ajax request sent back

	            	var dashboard = new google.visualization.Dashboard(
	                   document.getElementById('dashboard'));
                    var d1 =new Date(data.gbegin);
                     var d2 = new Date(data.gend);
                     console.log(d1);
                     console.log(d2);
		              var control = new google.visualization.ControlWrapper({
		                 'controlType': 'ChartRangeFilter',
		                 'containerId': 'control',
		                 'options': {
		                   // Filter by the date axis.
		                   
		                   'filterColumnIndex': 0,
		                   'ui': {
		                     'chartType': 'LineChart',
		                     'chartOptions': {
		                       'chartArea': {'width': '90%'},
		                       'hAxis': {'baselineColor': 'none'}
		                     },
		                     // Display a single series that shows the closing value of the stock.
		                     // Thus, this view has two columns: the date (axis) and the stock value (line series).
		                     'chartView': {
		                       'columns': [0, 1]
		                     },
		                     // 1 day in milliseconds = 24 * 60 * 60 * 1000 = 86,400,000
		                     'minRangeSize': 86400000
		                   }
		                 },
		                 // Initial range: 2012-02-09 to 2012-03-20.
                     
		                 'state': {'range': {'start': d1, 'end': d2}}
		               });

                        var chart = new google.visualization.ChartWrapper({
                         'chartType': 'ComboChart',
                         'containerId': 'chart',
                         'options': {
                           // Use the same chart area width as the control for axis alignment.
                           'chartArea': {'height': '80%', 'width': '90%'},
                           'hAxis': {'slantedText': false},
                           'vAxis': {},
                           'legend': {'position': 'none'},
                           seriesType: "line",
                           tooltip: { isHtml: true }
                         },
                         // Convert the first column from 'date' to 'string'.
                         'view': {
                           'columns': [
                             {
                               'calc': function(dataTable, rowIndex) {
                                 return dataTable.getFormattedValue(rowIndex, 0);
                               },
                               'type': 'string'
                             }, 1, 2]
                         }
                       });
						
                       var gdata = new google.visualization.DataTable();
                       $.each(data.cols, function(key,obj) {
                          gdata.addColumn(obj);
                       }); 
                       $.each(data.rows,function(key,obj) {
                        console.log(obj);
                        var pdate = new Date(obj.date);
                          var processedRow = [
                            [pdate, parseFloat(obj.split), obj.athlete, obj.tooltip]
                          ];
                          console.log(processedRow);
                          gdata.addRows(processedRow);
                       });
                       dashboard.bind(control, chart);
                       dashboard.draw(gdata);
                       function resizeHandler () {
                            dashboard.draw(data);
                        }
                        if (window.addEventListener) {
                            window.addEventListener('resize', resizeHandler, false);
                        }
                        else if (window.attachEvent) {
                            window.attachEvent('onresize', resizeHandler);
                        }

            	// end ajax handler
            });
		   

		////////////////
	});
});
</script>
