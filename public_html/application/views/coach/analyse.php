<div class="container">
    <div class="row">
      <div class="col-lg-12">
       	<h1>Analyse</h1>
    </div>
</div>
 <div class="row">
      <div class="col-lg-3">
		<p>Graph Options:</p>
		<form id="graphForm" role="form">
		  <div class="form-group" title="Select a type of graph from the dropdown list">
		    <label class="sr-only" for="inputGraphType">Graph Type</label>
		    <select class="form-control" name="inputGraphType" id="inputGraphType">
		    <? foreach($graphs as $key => $graph): ?>
            <option value="<?=$key?>"><?=$graph['name']?></option>
            <? endforeach; ?>
	      </select>
		  </div>
          <div class="form-group" title="Click the dates on the left and right to set a start and end date for the graph">
              <div class="input-daterange input-group input-group-sm" id="datepicker">
                <input type="text" class="form-control" name="inputStart" id="inputStart" value="<?=date("d-m-Y",(time() - (90 * 86400)))?>"/>
                <span class="input-group-addon">to</span>
                <input type="text" class="form-control" name="inputEnd" id="inputEnd" value="<?=date("d-m-Y")?>"/>
            </div>
          </div>
		  <div class="form-group" title="Type in names then select from the drop down">
		    <label class="sr-only" for="inputPeople">Who</label>
		    <input class="form-control"  type="text" name="inputPeople" id="inputPeople" placeholder="Enter names">
		  </div>
		<button type="submit" class="btn btn-default">Load Graph</button>
		</form>		
      </div>

      <div class="col-lg-9">

             <div id="dashboard">
                    <div id="chart" style='width: 100%; height: 600px;'>&nbsp;</div>
                </div>
        
        <div id='table_div'>&nbsp;</div>
	   </div>

</div>	
<script type="text/javascript" src="//www.google.com/jsapi"></script>
<script type="text/javascript">
 
$(document).ready(function() {
    $('.input-daterange').datepicker({
        format: "dd-mm-yyyy",
        endDate: "today",
        todayBtn: "linked",
        todayHighlight: true
    });
     
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
    if($("#inputPeople").val() != "") {	
    		
    		var data = {
    			type: $("#inputGraphType").val(),
    			start: $("#inputStart").val(),
    			end: $("#inputEnd").val(),
    			who: $("#inputPeople").val(),
    		};
    		var types = <?= json_encode($graphs); ?>;
    		var thisType = types[data.type];
    		console.log(thisType);
    		if(typeof thisType.time != undefined) {
    		data[data.length] = { time: thisType.time};
    		}
    		if(typeof thisType.distance != undefined) {
    		data[data.length] = { distance: thisType.distance };
    		}
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
    	
  } else {
    alert("No names entered");
  }
  });
});
initGraphs();
  function initGraphs() {
   
      google.load('visualization', '1.0', {'packages':['corechart','controls','table']});
   //google.setOnLoadCallback();
    
  }
</script>
