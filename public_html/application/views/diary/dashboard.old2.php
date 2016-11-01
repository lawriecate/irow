<div class="container"> 
  <!-- Example row of columns -->
  <style type="text/css">
      	.group .excard a {
      		display: block;
			width: 110px;
      		height: 100px;
      		float: left;
      	}
      	.group .excard a:hover {
      		background-color: #ccc;
      	}
      	.ir-dtsr {
      		display: none;
      	}
      </style>
  <div class="row">
    <div class="col-lg-12">
      <h2>Diary</h2>
      <div id="diary_container">
        <p>loading...</p>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->

<div class="modal fade" id="viewWorkoutModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button id="showAddForm" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">View Log</h4>
      </div>
      <div class="modal-body">
        <p>RECORD:</p>
        <p></p>
      </div>
    </div>
    <!-- /.modal-content --> 
  </div>
  <!-- /.modal-dialog --> 
</div>
<!-- /.modal -->

<div class="modal fade" id="newWorkoutModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button id="showAddForm" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">New Workout</h4>
      </div>
      <div class="modal-body">
      <div id="statusMsg">&nbsp;</div>
        <div id="addExContainer">
          <p>Select an exercise:</p>
          <?php echo form_open('diary/add',array('role'=>'form','id'=>'addexForm')); ?>
          <div class="form-group">
            <label for="inputTime">Date</label>
            <input type="text" class="form-control" id="inputDate" name="inputDate" value="<?= date("d-m-Y")?>">
          </div>
          <div class="form-group ">
            <label class="control-label" for="inputType">Type</label>
            <select class="form-control" id="inputType" name="inputType">
              <? foreach($types as $key => $group) { ?>
              <optgroup label="<?=$key?>">
              <? foreach($group as $item): ?>
              <option value="<?= $item['value'] ?>">
              <?= $item['label']?>
              </option>
              <? endforeach; ?>
              </optgroup>
              <? } ?>
            </select>
          </div>
          <div class="form-group ir-dtsr">
            <label for="inputSplit">Split (MM:SS.SS)</label>
            <input type="text" class="form-control" id="inputSplit" name="inputSplit" placeholder="Enter 500m split">
          </div>
          <div class="form-group ir-dtsr ir-dt timeG">
            <label for="inputTime">Time (HH:MM:SS.SS)</label>
            <input type="text" class="form-control" id="inputTime" name="inputTime" placeholder="Enter time">
          </div>
          <div class="form-group ir-dtsr ir-dt distanceG">
            <label for="inputDistance">Distance (Metres)</label>
            <input type="text" class="form-control" id="inputDistance" name="inputDistance" placeholder="Enter distance">
          </div>
          <div class="form-group ir-dtsr">
            <label for="inputRate">Rate (strokes per minute)</label>
            <input type="text" class="form-control" id="inputRate" name="inputRate" placeholder="Enter rate">
          </div>
          <div class="form-group ir-dtsr ir-dt ir-n">
            <label for="inputNotes">Notes</label>
            <textarea class="form-control" name="inputNotes" id="inputNotes"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
        </form>
     	</div>
    </div>
    <!-- /.modal-content --> 
  </div>
  <!-- /.modal-dialog --> 
</div>
<!-- /.modal --> 
<script type="text/javascript">
  $(document).ready(function() {
  	typecode = 1;
  	function switchInputs(code) {
  	//	$(".ir-dtsr").slideUp();
  		$(".ir-dtsr input").removeAttr("disabled");
  		switch (code) {
  			case 1:
  				$(".ir-dtsr").slideDown();
  				$("#inputSplit").focus();
  				break;
  			case 2:
  				$(".ir-dt").slideDown();
  				$("#inputTime").focus();
  				break;
  			case 3:
  				$(".ir-n").slideDown();
  				$("#inputNotes").focus();
  				break;
  		}

  	}

  	$("#inputType").change(function() {
	  	type = $("#inputType").val();
	  	lock = 0;
	  	switch(type) {
	  		<?
	  		foreach($types as $key => $group):
	  			foreach($group as $item):
	  				$typecode = 3;
	  				if($item['data'] == "comp_dtsr") {
	  					$typecode = 1;
	  				} 
	  				elseif($item['data'] == "comp_dt") {
	  					$typecode = 2;
	  				}
	  		?>
	  		case "<?= $item['value'] ?>":
	  			typecode = <?= $typecode ?>;
	  			$(".ir-dtsr").slideUp();
	  			if(type == "ergd" || type == "waterd") {
		  			$(".timeG").before($(".distanceG"));
		  			lock = 1;

		  		} else if(type == "ergt" || type == "watert") {
		  			$(".distanceG").before($(".timeG"));
		  			lock = 2;
		  		} else {
		  			lock = 0;
		  		}
	  			switchInputs(<?= $typecode ?>);
	  			break;
	  		<?
	  			endforeach;
	  		endforeach;
	  		?>

	  	}
	  });

  	// calculate time/distance/split triangle for rowing exercises
  	function validSplit() {
  		if($("#inputSplit").val() != "") {
  			return true;
  		} else {
  			return false;
  		}
  	}

  	function validTime() {
  		if($("#inputTime").val() != "") {
  			return true;
  		} else {
  			return false;
  		}
  	}

  	function validDistance() {
  		if($("#inputDistance").val() != "") {
  			return true;
  		} else {
  			return false;
  		}
  	}

  	function distanceCalc(time,split) {
  		return Math.floor(500 * (time_to_seconds(time) / time_to_seconds(split)) );
  	}

  	function timeCalc(distance,split) {
  		seconds = distance / 500 * time_to_seconds(split);
  		return outputSplit(seconds);
  	}

	function time_to_seconds(time) {
		parts = time.split(':');
		parts.reverse();
		raise60 = 0;
		total_seconds = 0;
		for(var key in parts) {
			var part = parts[key];
			//echo "$part x 60 ^ $raise60<br>";
		
			seconds = part * Math.pow(60,raise60);
			
			total_seconds += seconds;
			raise60++;
		}
		return total_seconds;
	}

function outputSplit(init,longOutput) {
hours = Math.floor(init / 3600);
		minutes = Math.floor((init / 60) % 60);
		seconds = init % 60;

		var pad=function(num,field){
		    var n = '' + num;
		    var w = n.length;
		    var l = field.length;
		    var pad = w < l ? l-w : 0;
		    return field.substr(0,pad) + n;
		};

		if(init.toString().indexOf(".") != -1) {
			fractional =  init.toString().substr(init.toString().indexOf("."));
		} else {
			fractional = ".0";
		}
		
		seconds = seconds.toString().substr(0,seconds.toString().indexOf("."));
		combined_seconds =  pad(seconds,"0") + fractional.substr(0,2);
		
		if(seconds < 10) {
			second_pad = "0";
		} else {
			second_pad = "";
		}

		pretty = minutes + ":" + second_pad + combined_seconds;

		

		if(longOutput == true) {
			pretty = pad(hours,"00") + ":"  + pad(minutes,"00") + ":" + second_pad + combined_seconds;
		}

		return pretty;
	}

  	$("#inputTime").change(function() {
  		recalculate();
  		$("#inputRate").focus();
  	});

  	$("#inputDistance").change(function() {
  		recalculate();
  		$("#inputRate").focus();
  	});

  	$("#inputSplit").change(function(	) {
  		recalculate();
  	});

  	function recalculate()
  	{
  		if(typecode == 1) {
	  		// disable time if split entered
	  		if(lock == 1 && validDistance()) {
	  			//$("#inputTime").attr("disabled","disabled");

	  			$("#inputTime").val( timeCalc(	$("#inputDistance").val(),$("#inputSplit").val() ));
	  		} else if( lock ==2  && validTime()) {
		  		//$("#inputDistance").attr("disabled","disabled");

		  		$("#inputDistance").val(distanceCalc(	$("#inputTime").val(), $("#inputSplit").val() ));
		  	
	  		}

	  		
	  	}
  	}

  	//////////////////////////////////////////////////////////
  	// view exercise code

	/////////////////////////////////
	
	////////////////////////////
	// exercise submit ajaxify
	$("#addexForm").submit(function(event) {
		$("#addExContainer").slideUp();
		$("#newWorkoutModal #statusMsg").html("<h2>Saving...</h2>");
		$.ajax({
			type: "POST",
			url: "diary/ajax_logexercise",
			data: $( this ).serialize() 
		}
			)
		  .done(function() {
			//$("#newWorkoutModal").modal('hide');
			location.reload();
		  })
		  .fail(function() {
			alert( "error" );
		  })
		  .always(function() {
			
		});
		
  		event.preventDefault();
	});
	//////////////////////////////////
  
  // page load first data
  var yearVI;
  var monthVI;

  $.ajax({
      type: "GET",
      url: "<?= base_url() ?>diary/ajax_diary_totals"
    }
      )
      .done(  function(data) {
        $("#diary_container").html("");
        getLifetimeView(data);
      })
      .fail(function() {
      alert( "error" );
      })
      .always(function() {
      
    });

  function getLifetimeView(data) {
    var lifetimeView = $('<div class="view"></div>');
    $("#diary_container").append(lifetimeView);
    var yearList = $('<div class="group"></div>');
    lifetimeView.append(yearList);
    
      $.each(data,function(year, months) {
          var yearOuter = $('<div class="excard"></div>');
          yearList.append(yearOuter);
          var yearAnchor = $('<a href="#dy_'+year+'"></a>').append('<h3>'+year+'</h3>').append('<p></p>');
          yearOuter.append(yearAnchor);
          yearAnchor.click(function() {
            var yearView = getYearView(year,lifetimeView);
            
          });
          
        });

  }

  function getYearView(year,lifetimeView) {
      var yearView = $('<div class="view"></div>');
      var yearOuter = $("<h2></h2>");
      yearView.append(yearOuter);

      var returnLink = $('<a href="#">Go back to life view</a>');
      returnLink.click(function() {

        switchViews(lifetimeView);
        yearView.remove();
      });
      returnLink.insertBefore(yearOuter);

      var yearAnchor = yearOuter.append('<a href="#dy_'+year+'">'+year+'</a>');
      yearAnchor.click(function() {
        //alert('test');
      });

      var monthList = $('<div class="group"></div>');
      yearView.append(monthList);
      //$.each(months,function(j,month) {
      //  addMonthLink(year,month,yearView);
      //}); 
      for (i=1; i<=12; i++) {
        addMonthLink(year,i,monthList,yearView);
      }
      $("#diary_container").append(yearView);
      switchViews(yearView);
    	return yearView;

  }
    
  function addMonthLink(year,month,yearViewMonthList,yearView) {  // function that adds object to represent a month 
      //$("#diary_container").append('<h3><a href="#">' + year + ": " + month + "</a></h3>");
      var monthOuter = $('<div class="excard"></div>');
		  //yearView.append(monthOuter);
      // var monthAnchor = monthOuter.append('<a href="#">'+year+' - '+month+'</a>');
       var monthAnchor = $('<a href="#dy_'+year+'_'+month+'"></a>').append('<h3>'+month+'</h3>').append('<p>Act: '+year+'</p>');
      monthAnchor.click(function() {
      //alert('loading... ' + year + '/' + month);

      // loads the data which tells the month if exercises are avaliable on each day
      $.get('<?= base_url() ?>diary/ajax_diary_days', { year: year, month: month }, function( data) {
      		switchViews(getMonthView(year,month,data,yearView));
		  });
      });
      monthOuter.append(monthAnchor);
      yearViewMonthList.append(monthOuter);
    }
	
	function daysInMonth(month,year) {
   		return new Date(year, month, 0).getDate();
	}
	
	function getMonthView(year,month,data,parentView) {
    

		var monthView = $('<div class="view"></div>');
		var monthHeader = $("<h3>Month View: " + year + "-" + month + "</h3>");
    
		monthView.append(monthHeader);

    var returnLink = $('<a href="#dy_'+year+'">Go back to ' + year + '</a>');
    returnLink.click(function() {

      switchViews(parentView);
      monthView.remove();
    });
    returnLink.insertBefore(monthHeader);

		var listContainer = $('<div class="group"></div>');
		monthView.append(listContainer);
		/*$.each(data,function(key,obj) {
			var day = obj.day
			var noact = obj.no_activities;
			var listObject= $('<div class="excard"></div>');
			var dayLink = $('<a href="#"></a>').append('<h3>'+day+'</h3>').append('<p>Act:'+noact+'</p>');
			listObject.append(dayLink);
			listContainer.append(listObject);
		});*/ 
		for (i = 1; i <= daysInMonth(month,year); i++) {
			var day = i;
			var noact;

			if (data[day] > 0) {
				noact = data[day];
			} else {
				noact = 0;
			}
			var listObject= $('<div class="excard"></div>');
			var dayLink = $('<a href="#dy_'+year+'_'+month+'_'+day+'"></a>');
      dayLink.click([day],function(e) {
        switchViews(getDayView(year,month,e.data[0],monthView));
      });
      dayLink.append('<h3>'+day+'</h3>').append('<p>Act: '+noact+'</p>');
      

			listObject.append(dayLink);
      
			listContainer.append(listObject);
		}
		
		$("#diary_container").append(monthView);
    return monthView;
 	}

  function getDayView(year,month,day,parentView) {
    var dayView = $('<div class="view"></div>');
    var dayHeader = $("<h3>Day View: "+year+"-"+month+"-"+day+"</h3>");
    dayView.append(dayHeader);

    var returnLink = $('<a href="#dy_'+year+'_'+month+'">Go back to ' + month + '</a>');
    returnLink.click(function() {

      switchViews(parentView);
      monthView.remove();
    });
    returnLink.insertBefore(dayHeader);

    var listContainer = $('<div class="group"></div>');
    dayView.append(listContainer);

      $.get('<?= base_url() ?>diary/ajax_diary_daylist', { year: year, month: month, day: day }, function( data) {
          $.each(data, function(key,activity) {

               var listObject= $('<div class="excard"></div>');
                var exLink = $('<a href="#"></a>').append('<h3>'+activity.label+'</h3>').append('<p>Act: '+activity.avg_split+'</p>');
                listObject.append(exLink);
                listContainer.append(listObject);
          });
          var addLink = $('<div class="excard"> <a href="#newWorkoutModal" data-toggle="modal" ><button type="button" class="btn btn-default btn-lg"> <span class="glyphicon glyphicon-add"></span> Add </button> </a> </div>')
             listContainer.append(addLink);
      });

   

      $("#diary_container").append(dayView);
    return dayView;
  }

  function switchViews(current) {
    $(".view").not(current).slideUp();
    current.slideDown();
  }


  /*
  <h3>This Week</h3>
        <div class="group">
          <? foreach($this_week as $activity): ?>
          <div class="excard"> <a href="<?=base_url()?>diary/view/<?=$activity['ref']?>/" data-target="#viewWorkoutModal" data-toggle="modal" >
            <h3>
              <?= $activity['label']?>
            </h3>
            <p>
              <?= date("D j", strtotime($activity['sort_time'])) ?>
            </p>
            </a> </div>
          <? endforeach; ?>
          <div class="excard"> <a href="#newWorkoutModal" data-toggle="modal" >
            <button type="button" class="btn btn-default btn-lg"> <span class="glyphicon glyphicon-add"></span> Add </button>
            </a> </div>
        </div>*/

        /////////////////////////

  });
  </script> 
