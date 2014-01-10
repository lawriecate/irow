<div class="container"> 
  <!-- Example row of columns -->
  <style type="text/css">

      	.group .excard a {
      		display: block;
			width: 140px;
      		height: 140px;
      		float: left;
      	}
      	.group .excard a:hover {
      		background-color: #ccc;
      	}
      	.ir-dtsr {
      		display: none;
      	}
        .graph {
          width: 100%;
          background: #ccc;
          height: 200px;
          border-radius: 2px;

        }
        #graphs {
          background:  grey;
          height: 200px;
        }
      </style>
  <div class="row">
    <div class="col-lg-12">
      <div id="graphs" class="hidden">
       &nbsp;

      </div>
          <small class="hidden">[White] 2K average [Red] &frac12; hour average</small>
      <ol class="breadcrumb" id="diary_breadcrumb">
        <li class="active"><a href="#life">Diary</a></li>
        
      </ol>
      <div id="diary_container">
        <p>...</p>
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
        &nbsp;
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
          <div class="row">
              <div class="form-group ir-dtsr col-md-3">
                <label for="inputSplit">Split (MM:SS.SS)</label>
                <input type="text" class="form-control" id="inputSplit" name="inputSplit" placeholder="Enter 500m split">
              </div>
              <div class="form-group ir-dtsr ir-dt timeG col-md-3">
                <label for="inputTime">Time (HH:MM:SS.SS)</label>
                <input type="text" class="form-control" id="inputTime" name="inputTime" placeholder="Enter time">
              </div>
              <div class="form-group ir-dtsr ir-dt distanceG col-md-3">
                <label for="inputDistance">Distance (Metres)</label>
                <input type="text" class="form-control" id="inputDistance" name="inputDistance" placeholder="Enter distance">
              </div>
              <div class="form-group ir-dtsr col-md-3">
                <label for="inputRate">Rate (strokes per minute)</label>
                <input type="text" class="form-control" id="inputRate" name="inputRate" placeholder="Enter rate">
              </div>
          </div>
          
          <div class="form-group ir-dtsr ir-dt ir-n">
            <label for="inputNotes">Notes</label>
            <textarea class="form-control" name="inputNotes" id="inputNotes"></textarea>
          </div>
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="saveButton">Save</button>
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

     $("#inputDate").inputmask("d-m-y"); 
      $("#inputRate").inputmask({mask:"9","repeat": 2 ,rightAlignNumerics: false,placeholder: ""} ); 
     $("#inputSplit").inputmask({mask:"9:99.9", greedy: false,numericInput: true, rightAlignNumerics: false,placeholder: "  "});
      $("#inputTime").inputmask({mask:"99:99.9", greedy: false,numericInput: true ,rightAlignNumerics: false,placeholder: "  "});
       $("#inputDistance").inputmask({mask:"9","repeat": 6 ,rightAlignNumerics: false,placeholder: ""});

    $('#newWorkoutModal').on('show.bs.modal', function(e) {
		
	  	typeChange(0);
      $("#newWorkoutModal form")[0].reset();
      $("#addExContainer").show();
      $("#saveButton").show();
      $("#statusMsg").html("&nbsp;");

    });

  $('#newWorkoutModal').on('shown.bs.modal', function(e) {

      $("#inputType").focus();
    });


  	typecode = 1;
  	function switchInputs(code,effects) {
  	//	$(".ir-dtsr").slideUp();
  		$(".ir-dtsr input").removeAttr("disabled");
  		switch (code) {
  			case 1:
  				$(".ir-dtsr").slideDown(effects);
  				$("#inputSplit").focus();
  				break;
  			case 2:
  				$(".ir-dt").slideDown(effects);
  				$("#inputTime").focus();
  				break;
  			case 3:
  				$(".ir-n").slideDown(effects);
  				$("#inputNotes").focus();
  				break;
  		}

  	}
	
	function typeChange(effects) {
		if(typeof(effects)==='undefined') effects = 400;
		type = $("#inputType").val();
	  
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
	  			$(".ir-dtsr").slideUp(effects,function() {
	  			if(type == "ergd" || type == "waterd") {
		  			$(".timeG").before($(".distanceG"));
		  			

		  		} else if(type == "ergt" || type == "watert") {
		  			$(".distanceG").before($(".timeG"));
		  			
		  		} else {
		  			
		  		}
	  			switchInputs(<?= $typecode ?>,effects);
				});
	  			break;
	  		<?
	  			endforeach;
	  		endforeach;
	  		?>

	  	}
	}

  	$("#inputType").change(function() {
	  	typeChange();
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
	
	function splitCalc(distance,time) {
  		seconds = (500 * time_to_seconds(time)) / distance;
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
	  		if(validSplit() && validDistance() && validTime()) {
	  			//$("#inputTime").attr("disabled","disabled");
				// ALL THREE inputs
	  			
	  		} else if( validSplit()  && validTime()) {

		  		$("#inputDistance").val(distanceCalc(	$("#inputTime").val(), $("#inputSplit").val() ));
		  	
	  		}
			else if( validSplit()  && validDistance()) {
		  		
				$("#inputTime").val( timeCalc(	$("#inputDistance").inputmask('unmaskedvalue'),$("#inputSplit").val() ));
	  		}
			else if( validDistance()  && validTime()) {
		  		
				$("#inputSplit").val( splitCalc(	$("#inputDistance").inputmask('unmaskedvalue'),$("#inputTime").val() ));
		  		
	  		}

	  		
	  	}
  	}

  	//////////////////////////////////////////////////////////
  	// view exercise code

    function showModal(injectLink) {
      $.get(injectLink,function(page) {
        $("#viewWorkoutModal .modal-body").html(page);
        $("#viewWorkoutModal").modal().show();
      });
      
    }

function updateView(hash) {
      
        req = hash.substring(1);
        
        $.ajax({
          type: "GET",
          url: "<?= base_url() ?>diary/ajax_diary_view",
          data:{tag: req}
        }
          )
          .done(  function(data) {
          if(data != false) {





            //console.log(data);
              var view = $('<div class="view"></div>');
              var viewOuter = $("<h2></h2>").text(data.title);
              view.append(viewOuter);
              /*if(data.return instanceof Object) {
                var returnLink = $('<a href="'+data.return.link+'">'+data.return.title+'</a>');
                returnLink.click(function() {

                  updateView(data.return.link);
                });
                  returnLink.insertBefore(viewOuter);

              }*/

              if(data.breadcrumb instanceof Object) {
                $("#diary_breadcrumb").html('');
                $.each(data.breadcrumb,function(key,crumb) {
                  var li = $('<li></li>');
                  var link = $('<a href="'+crumb.href+'">'+crumb.t+'</a>');
                  li.append(link);
                  link.click(function() {
                    updateView(crumb.href)
                  });
                  $("#diary_breadcrumb").append(li);
                });
                $("#diary_breadcrumb li:last").addClass("active");
                $("#diary_breadcrumb li:last").text($("#diary_breadcrumb li:last").text());
              }
              var viewList = $('<div class="group"></div>');
              view.append(viewList);
              $.each(data.items,function(key, obj) {
                
                var itemOuter = $('<div class="excard"></div>');
                
                viewList.append(itemOuter);
                var itemLink = $('<a href="'+obj.link+'"></a>').append('<h3>'+obj.title+'</h3>').append('<p>'+obj.label+'</p>');
                if(typeof obj.back != undefined) {
                  itemLink.css('background',obj.back);
                }
                itemOuter.hide();
                itemOuter.append(itemLink);
                setTimeout(function() { itemOuter.fadeIn(); },20*key);

                itemLink.click(function() {
                  
                  if(obj.link_modal == true) {
                    showModal(obj.link);
                    event.preventDefault();
                  } else {
                    updateView(obj.link);
                  }
                });
                
              });


              if(typeof data.showAdd != undefined && data.showAdd == true) {
                 var addLink = $('<div class="excard"> </div>');
                 var addAnchor = $('<a href="#newWorkoutModal" data-toggle="modal" ><button type="button" class="btn btn-default btn-lg"> <span class="glyphicon glyphicon-add"></span> Add </button> </a> ');
                 addLink.append(addAnchor);
                 addAnchor.click(function() {

                  $("#inputDate").attr("value",data.showAddDate);

                 });
                viewList.append(addLink);
              }

              $("#diary_container").html("");
              $("#diary_container").append(view);

              // GRAPH VIEW

              if(data.graphs != false) {
                $("#graphs").html("");
                $.each(data.graphs, function(key,gdata) {
                  var graph = $('<canvas id="dashChart'+key+'" class="graph"></canvas>');
                  $("#graphs").append(graph);
                  //var gdata = graph;
                  //console.log(graph);

                  var options = {animation : true};

                  //Get the context of the canvas element we want to select
                  var c = $('#dashChart'+key);
                  var ct = c.get(0).getContext('2d');
                  var ctx = document.getElementById("dashChart"+key).getContext("2d");
                  /*************************************************************************/

                  //Run function when window resizes
                  $(window).resize(respondCanvas);
                  
                  function respondCanvas() {
                      c.attr('width', jQuery("#dashChart"+key).width());
                      c.attr('height', jQuery("#dashChart"+key).height());
                      //Call a function to redraw other content (texts, images etc)
                      myNewChart = new Chart(ct).Line(gdata, options);
                  }

                  //Initial call 
                  respondCanvas();
                });
              
                  
              } else {
                $("#graphs").html('<div style="height:200px"><p>No graph avaliable</p></div>');
              }



            } else {
              $("#diary_container").html("<h1>Error loading page</h1>");
            }

          })
          .fail(function() {
          $("#diary_container").html("<h1>System error, please try refreshing page</h1>");
          })
          .always(function() {
          
        });
     }
     if(window.location.hash == "" ) {
      var currentTime = new Date();
      var month = currentTime.getMonth() + 1;
      var day = currentTime.getDate();
      var year = currentTime.getFullYear();
        window.location.hash = "#day_" + year + "_" + month + "_" + day;
      }
      updateView(window.location.hash);
            

      $(window).bind( 'hashchange', function(e) {
        updateView(window.location.hash);
       });
  
	/////////////////////////////////
	
	////////////////////////////
	// exercise submit ajaxify
	$("#addexForm").submit(function(event) {
		$("#addExContainer").slideUp();
    $("#statusMsg").text("Please wait...");
    $("#saveButton").fadeOut();
   
		//$("#newWorkoutModal #statusMsg").html("<h2>Saving...</h2>");
		$.ajax({
			type: "POST",
			url: "diary/ajax_logexercise",
			data: $( this ).serialize() 
		}
			)
		  .done(function() {
		    $("#newWorkoutModal").modal('hide');
        updateView(window.location.hash);
			//location.reload();
		  })
		  .fail(function() {
			//alert( "error" );
		  })
		  .always(function() {
			
		});
		
  		event.preventDefault();
	});
	//////////////////////////////////
  
  // diary navigation
	$(function(){
		  
		 	});
	
	

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

