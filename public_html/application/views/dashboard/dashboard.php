<div class="container"> 
  <div class="row">
    <div class="col-lg-12">
      <div class="page-header">
      <h1>Dashboard <small>Welcome <?=$name?></small></h1>
    </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-8">
       <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Recent Activity <small id="raLoadingMsg" class="pull-right">loading graphs...</small></h3>
        </div>
        <div class="panel-body">
            <div class="row">
              <div class="col-xs-12" id="raGraph" style=" background:url('<?=base_url()?>assets/img/tinyloader.gif\');background-repeat:no-repeat ;background-position:center center;">
                 <div id="dashboard">
                    <div id="chart" style='width: 100%; height: 300px;'></div>
                    <div id="control" style='width: 100%; height: 50px;'></div>
                </div>
              </div>
            </div>

          </div>
      </div>     
      
      
    </div>
    <div class="col-lg-4">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Log Activity</h3>
        </div>
        <div class="panel-body logForm">
          <div class="row connecting">
            <div class="col-xs-12">
              <div class="progress progress-striped active">
  <div class="progress-bar"  role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
    <span class="sr-only">connecting...</span>
  </div>
</div>
            </div>
          </div>
            <div class="row stage1 hidden">
              ...
            </div>
            <div class="row stage2 hidden">
              <div class="col-xs-4 text-center">
                <a href="#">Time</a>
              </div>
              <div class="col-xs-4 text-center">
                <a href="#">Distance</a>
              </div>
              <div class="col-xs-4 text-center">
                <a href="#">Intervals</a>
              </div>
              
            </div>
            <div class="row stage3 hidden">
              <div class="col-xs-12">
              <form role="form" id="logForm">
                <input type="hidden" id="logType" name="type" />
                <div class="form-group" id="logDateGroup">
                  <label for="logDate">Date</label>
                  <input type="text" class="form-control" id="logDate" name="date" placeholder="Today" value="<?= date("d-m-Y")?>">
                </div>
                <div class="row">
                    <div class="form-group col-xs-4" id="logTimeGroup">
                      <label for="logTime">Time</label>
                      <input title="Enter time in HH:MM:SS format" type="text" class="form-control" id="logTime" name="time" >
                    </div>
                    <div class="form-group col-xs-4" id="logDistanceGroup">
                      <label for="logDistance">Distance</label>
                      <input title="Enter distance in meters" type="text" class="form-control" id="logDistance" name="distance" >
                    </div>
                    <div class="form-group col-xs-4" id="logSplitGroup">
                      <label for="logSplit">Split</label>
                      <input title="Enter 500m split in HH:MM:SS format" type="text" class="form-control" id="logSplit" name="split">
                    </div>
                </div>
                <div class="row">
                  <div class="form-group col-xs-4" id="logRateGroup">
                    <label for="logRate">Rate</label>
                    <input type="text" class="form-control" id="logRate" name="rate">
                  </div>
                   <div class="form-group col-xs-4" id="logHrGroup">
                    <label for="logHr">Heart Rate</label>
                    <input type="text" class="form-control" id="logHr" name="hr">
                  </div>
                </div>
                <button type="submit" id="logSubmit" class="btn btn-default">Save To Log</button>
              </form>
             </div>
            </div>


            <div class="row stage4 hidden">
              <div class="col-xs-12">
                <p id="finMsg">Congratulations your exercise was saved to your diary.  You can add any extra notes below!</p>
              <form role="form" id="logUpdateForm">
               
                <div class="form-group" id="logNotesGroup">
                  <label for="logNotes">Notes</label>
                  <textarea class="form-control" id="logNotes" name="notes"></textarea>
                </div>
                <button type="submit" id="logUpdateButton" class="btn btn-primary">Save Notes</button>
                <button type="button" id="logResetButton" class="btn btn-default">Add Another</button>
              </form>
             </div>
            </div>

            <div class="row failstage hidden">
              <div class="col-xs-12">
                <div class="alert alert-danger">Oh dear.  We couldn't save your exercise :(.  <a href="#" id="failLink" class="alert-link">Click here to restart</a>.</div>
             
             </div>
            </div>
          </div>
      </div>    
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Personal Bests <small id="graphLoadingSpinner" class="pull-right"  >loading graphs...</small></h3>
        </div>
        <div class="list-group">
              <? foreach($pbs as $key => $pb): ?>
                <a href="#" class="list-group-item pbLink" id="pbLink<?=$key?>">
                  <? if($pb['found'] == TRUE) { ?>
                  <h4 class="list-group-item-heading"><?= $pb['label']?></h4>
                  <p class="list-group-item-text"><strong><?= $pb['split'] ?></strong> / <?= $pb['score']?></p>
                  <? } else { ?>
                  <h4 class="list-group-item-heading text-muted"><?= $pb['label']?> <small>No score found</small></h4>
                  
                  <? } ?>
                </a>
              
            <? endforeach; ?>
        </div>
      </div>  
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Body Tracking <small id="saveMsLoading" class="hidden pull-right">saving...</small></h3>
        </div>
        <div class="panel-body">
            <div class="row">
              <div class="col-xs-4"><span class="dashMs"><input title="Enter your new height measurement" id="saveMsHeight" type="text" maxlength="3" value="<?= $height ?>"><small>m</small></span></br>Height</div>
              <div class="col-xs-4"><span class="dashMs"><input  title="Enter your new arm span measurement" id="saveMsArmSpan" type="text" maxlength="3" value="<?= $armspan ?>"><small>m</small></span></br>Arm Span</div>
              <div class="col-xs-4"><span class="dashMs"><input title="Enter your new weight measurement" id="saveMsWeight" type="text" maxlength="6" value="<?= $weight ?>"><small>kg</small></span></br>Weight</div>
            </div>
            <div class="row">
              <div class="col-xs-12">
               <?/* <button id="saveMsButton" class="btn btn-default">Save</button>*/ ?>
              </div>
            </div>

          </div>
      </div>     
      
    </div>
  </div>
  </div>
 
  <script type="text/javascript">
  $(document).ready(function() {
////////////    MEASUREMENTS UPDATE   ////////////////////////////
/*$("#saveMsButton").click(function() {
  $.get('<?=base_url()?>dashboard/ajax_updatem?t=height&v=' + $("#saveMsHeight").val(), function() {
    //alert('saved');
  });
  $.get('<?=base_url()?>dashboard/ajax_updatem?t=weight&v=' + $("#saveMsWeight").val(), function() {
    //alert('saved');
  });
  $.get('<?=base_url()?>dashboard/ajax_updatem?t=armspan&v=' + $("#saveMsArmSpan").val(), function() {
    //alert('saved');
  });
});*/

$("#saveMsHeight").change(function() {
  $("#saveMsLoading").removeClass("hidden").show();
$.get('<?=base_url()?>dashboard/ajax_updatem?t=height&v=' + $("#saveMsHeight").val(), function() {
    setTimeout(function() { $("#saveMsLoading").hide() },1000);
  });
});
$("#saveMsWeight").change(function() {
   $("#saveMsLoading").removeClass("hidden").show();
$.get('<?=base_url()?>dashboard/ajax_updatem?t=weight&v=' + $("#saveMsWeight").val(), function() {
   setTimeout(function() { $("#saveMsLoading").hide() },1000);
  });
});
$("#saveMsArmSpan").change(function() {
   $("#saveMsLoading").removeClass("hidden").show();
$.get('<?=base_url()?>dashboard/ajax_updatem?t=armspan&v=' + $("#saveMsArmSpan").val(), function() {
    setTimeout(function() { $("#saveMsLoading").hide() },1000);
  });
});
////////////    LOG EXERCISE FORM     ////////////////////////////
      $("#logDate").inputmask("d-m-y"); 
      $("#logRate").inputmask({mask:"9","repeat": 2 ,rightAlignNumerics: false,placeholder: ""} ); 
      /*$("#logSplit").inputmask({mask:"h:s:s.s"});
      $("#logTime").inputmask({mask:"h:s:s.s"});
       $("#logDistance").inputmask({mask:"9","repeat": 6 ,rightAlignNumerics: false,placeholder: "0"});*/
       $("#failLink").click(function() {
        init();
        location.reload();
       });

       var stage=1;
       function init() {
        $("#logForm")[0].reset();
        $("#logUpdateForm")[0].reset();
        $(".logForm > div.row:not(.stage1)").hide();
        $("#logUpdateButton").attr("class","btn btn-primary");
        $("#logUpdateButton").text("Save Notes");
          
        stage = 1;
          $.getJSON('<?=base_url()?>diary/ajax_logetypes',function() {

          }).done(function(data) {
            var stage1 = $(".logForm div.row.stage1");
            stage1.html("");
            $.each(data, function(group, item) {
              var div = $(' <div class="col-xs-3 text-center"></div>');
              var link = $('<a href="#">'+group+'</a>');
              div.append(link);

              link.click(function() {
                showStage2(item);
              });

              stage1.append(div);

            });
            stopLoading();
            $(".logForm div.row.stage1").removeClass("hidden").show();
          }).fail(function() {
            stage = -1;
            showFail();
          })
          ;
        }
        init();

        function showStage2(data) {
          var stage2 = $(".logForm div.row.stage2");
            stage2.html("");
          $.each(data, function(group, item) {
              var div = $(' <div class="col-xs-3 text-center"></div>');
              var link = $('<a href="#">'+item.label+'</a>');
              div.append(link);

              link.click(function() {
                showStage3(item);
              });

              stage2.append(div);

            });
          $(".logForm div.row.stage1").hide();
          $(".logForm div.row.stage2").removeClass("hidden").show();
        }

        function showStage3(type) {
          var stage3 = $(".logForm div.row.stage3");
          
          // filter different fields
          function toggleField(determiner,input) {
            if(determiner == "1") {
              // all good
            } else {
              input.hide();
            }
          }

          if(type.erg_calc == "1") {
            turn_on_erg_calc();
          }

          toggleField(type.show_distance,$("#logDistanceGroup"));
          toggleField(type.show_time,$("#logTimeGroup"));
          toggleField(type.show_split,$("#logSplitGroup"));
          toggleField(type.show_rate,$("#logRateGroup"));
          $("#logType").val(type.value);
          //toggleField(type.show_hr,$("#logHeartRateGroup"));
          
          $(".logForm div.row.stage2").hide();
          $(".logForm div.row.stage3").removeClass("hidden").show();
          $("#logTime").focus();
          
        }

        var currentAcid = null;

        $("#logForm").submit(function() {
          event.preventDefault();
          var eXdata = {
            inputType: $("#logType").val(),
            inputDate: $("#logDate").val(),
            inputDistance: $("#logDistance").val(),
            inputTime: $("#logTime").val().replace("_","0"),
            inputSplit: $("#logSplit").val().replace("_","0"),
            inputRate: $("#logRate").val(),
            inputHr: $("#logHr").val(),
            inputNotes:""
          }
          
          // send to server
          startLoading();
          $.ajax({
            type: "POST",
            datatype: "json",
            url: "diary/ajax_logexercise",
            data: eXdata
          }
            )
            .done(function(data) {

              stopLoading();
              currentAcid = data.ref;
              var label = $('<input id="logLabel" class="form-control inline input-sm" type="text" placeholder="" value="' +data.label +'">');
              label.css('display','inline');
              function resizeLabel(ob) {
                var newWh = ob.val().length * 8;
                if(newWh > 240) { newWh = 240; } // maximum
                if(newWh > 80) {
                  ob.css('width',newWh+'px');
                } else {
                  ob.css('width','80px');
                }
                
              }
              label.keypress(function() {
                resizeLabel($(this));
              });
              $("#finMsg").html('');
              $("#finMsg").append(label);
              resizeLabel(label);
              label.before('Congratulations your ');
              label.after(' was saved to your diary.  You can add any extra notes below!');
              $(".logForm div.row.stage4").removeClass("hidden").show();
            })
            .fail(function() {
              stage = -1;
              showFail();
            })
            .always(function() {
            
          });

        });

        $("#logUpdateForm").submit(function() {
          event.preventDefault();
          if(currentAcid == null) {
            showFail();
          }
          var eXdata = {
            acid: currentAcid,
            inputNotes: $("#logNotes").val(),
            inputLabel: $("#logLabel").val()
          }
          
          // send to server
         // startLoading();
         $("#logUpdateButton").text("Saving...");
          $.ajax({
            type: "POST",
            datatype: "json",
            url: "diary/ajax_updateexercise",
            data: eXdata
          }
            )
            .done(function(data) {

              $("#logUpdateButton").removeClass("btn-primary").addClass("btn-success").html('Saved <span class="glyphicon glyphicon-ok"></span>');;
              //stopLoading();
             // $(".logForm div.row.stage4").removeClass("hidden").show();
            })
            .fail(function() {
              stage = -1;
              showFail();
            })
            .always(function() {
            
          });
        });

        $("#logResetButton").click(function() {
          init();
        })


    function startLoading() {
      $(".logForm > div.row:not(.connecting)").hide();
      $(".logForm div.row.connecting").removeClass("hidden").show();

    }

    function stopLoading() {
      $(".logForm div.row.connecting").hide();

    }

    function showFail() {
      $(".logForm div.row").hide();
      $(".logForm .failstage").removeClass("hidden").show();

    }

    ////*********************************/////////
    /////         split calculator        /////////
function time_to_seconds(a){parts=a.split(":");parts.reverse();total_seconds=raise60=0;for(var b in parts)seconds=parts[b]*Math.pow(60,raise60),total_seconds+=seconds,raise60++;return total_seconds}
function outputSplit(init,longOutput) {
    minutes = Math.floor(init / 60);
    seconds = init - (minutes * 60);
   

    var pad=function(num,field){
        var n = '' + num;
        var w = n.length;
        var l = field.length;
        var pad = w < l ? l-w : 0;
        return field.substr(0,pad) + n;
    };


    pretty = minutes + ":" + seconds.toFixed(1);

    return pretty;
  }

function turn_on_erg_calc()
{
    function validSplit(){return""!=$("#logSplit").val()?!0:!1}function validTime(){return""!=$("#logTime").val()?!0:!1}function validDistance(){return""!=$("#logDistance").val()?!0:!1};

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

      $("#logTime").change(function() {
      recalculate();
      //$("#logRate").focus();
    });

    $("#logDistance").change(function() {
      recalculate();
      //$("#logRate").focus();
    });

    $("#logSplit").change(function( ) {
      recalculate();
    });

    function recalculate()
    {
        // disable time if split entered
        if(validSplit() && validDistance() && validTime()) {
          //$("#inputTime").attr("disabled","disabled");
        // ALL THREE inputs
          
        } else if( validSplit()  && validTime()) {

          $("#logDistance").val(distanceCalc( $("#logTime").val(), $("#logSplit").val() ));
        
        }
      else if( validSplit()  && validDistance()) {
          
        $("#logTime").val( timeCalc(  $("#logDistance").val(),$("#logSplit").val() ));
        }
      else if( validDistance()  && validTime()) {
          
        $("#logSplit").val( splitCalc(  $("#logDistance").val(),$("#logTime").val() ));
          
        }

        
      
    }
}



});



</script>
<script type="text/javascript" src="//www.google.com/jsapi"></script>
<script type="text/javascript">

    

  ////////////////    GRAPHS    ///////////////////////////////////
 initGraphs();
  function initGraphs() {
   
      google.load('visualization', '1.0', {'packages':['corechart','controls']});
   google.setOnLoadCallback(setCharts);
    
  }
     
      function setCharts() {
         

                              function drawChart(divid,type,dislen) {
                                 var server_data = $.ajax({
                                  url: "<?=base_url()?>dashboard/ajax_graphdata?t="+type+"&d="+dislen,
                                  dataType:"json",
                                  async: false
                                  }).responseText;
                                  var data = new google.visualization.DataTable(server_data);

                                // Set chart options
                                var options = {
                                              'width':250,
                                              'height':250,
                                              'legend':'none',
                                              hAxis: {title: 'Time'},
                                              vAxis: {title: 'Score'},
                                              curveType: 'function',
                                              chartArea: {width: '100%', height: '100%'},
                                              hAxis: {textPosition: 'none'},
                                              vAxis: { textPosition:'none'},
                                              tooltip: { isHtml: true },
                                              pointSize: 11,
                                              lineWidth: 3
                                            };
                            
                                // Instantiate and draw our chart, passing in some options.
                                var chart = new google.visualization.ScatterChart(document.getElementById(divid));
                                chart.draw(data, options);
                              }


                        ////////////////////////////////////////////////////////////////////


                        //////////////    POPOVERS    ////////////////////////////////////
                        $(".pbLink").click(function() {
                          return false;
                        });
                            <? foreach($pbs as $key => $pb): 
                            if($pb['found'] == TRUE) { ?>

                            $("#pbLink<?=$key?>").popover({
                              placement: function() {
                                width = $(window).width();
                                if(width < 1200) {
                                  return "top";
                                }
                                return "left";  
                              },
                              html:true,
                              title: "<?= $pb['label']?> Progress",
                              content: '<div id="pbGraph<?=$key?>" style="width:250px; height:250px;background:url(\'<?=base_url()?>assets/img/tinyloader.gif\');background-repeat:no-repeat ;background-position:center center;">...</div>'
                            });
                            $("#pbLink<?=$key?>").on('shown.bs.popover', function () {
                                // draw graph
                                drawChart('pbGraph<?=$key?>',"<?=$pb['type']?>","<?=$pb['dislen']?>");
                              })
                            <?
                            } 
                            endforeach; ?>
                            $('.pbLink').on('click', function (e) {
                                $('.pbLink').not(this).popover('hide');
                            });

                      $("#graphLoadingSpinner").fadeOut();

                      //////////////////////////////////////////////////////


            ///////////////////     RECENT ACTIVITY GRAPH     ///////////////////////////
              var dashboard = new google.visualization.Dashboard(
                   document.getElementById('dashboard'));

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
                 'state': {'range': {'start': new Date(<? $m30 = time() - 86400 * 30; echo date("Y,",$m30),date("m",$m30)-1,date(",d",$m30) ?>), 'end': new Date(<?=date("Y,"),date("m")-1,date(",d")?>)}}
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
                           seriesType: "bars",
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

                        var server_data = $.ajax({
                          url: "<?=base_url()?>dashboard/ajax_radata",
                          dataType:"json",
                          async: false
                        }).responseText;
                       var data = new google.visualization.DataTable(server_data);

                       dashboard.bind(control, chart);
                       dashboard.draw(data);
                       function resizeHandler () {
                            dashboard.draw(data);
                        }
                        if (window.addEventListener) {
                            window.addEventListener('resize', resizeHandler, false);
                        }
                        else if (window.attachEvent) {
                            window.attachEvent('onresize', resizeHandler);
                        }

/*


            var server_data = $.ajax({
                                  url: "<?=base_url()?>dashboard/ajax_radata",
                                  dataType:"json",
                                  async: false
                                  }).responseText;
                                  var data = new google.visualization.DataTable(server_data);

                                // Set chart options
                                var options = {
                                              hAxis: {title: 'Time'},
                                              vAxis: {title: 'Score'},
                                               tooltip: { isHtml: true },
                                              curveType: 'function',
                                              seriesType: "bars",
                                              series: {2: {type: "line"}}
                                            };
                            
                                // Instantiate and draw our chart, passing in some options.
                                var chart = new google.visualization.ComboChart(document.getElementById("raGraph"));
                                chart.draw(data, options);*/
                                $("#raLoadingMsg").fadeOut();

            /////////////////////////////////////////////////////////////////////////////
      }


  </script>