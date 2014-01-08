<div class="container">
  <div class="row">
  <h2>Log Exercise</h2>
  <p>Please select the type of exercise, then fill in details for each person</p>
</div>
<div class="row">
  <div class="col-lg-9">
<form>
    
      <div class="row">
        <div class="alert alert-danger alert-dismissable hidden" id="connWarning">
          <strong>Warning!</strong> Connection problem detected</br>
          Please open a new tab or window and verify your internet connection
        </div>
        <div class="form-group col-xs-3 ">

          <label class="control-label" for="inputEmail">Type</label>
          <select class="form-control" id="inputType">
            <? foreach($types as $group => $subtypes): ?>
            <optgroup label="<?=$group?>">
              <? foreach($subtypes as $subtype): ?>
              <option value="<?= $subtype['value'] ?>"><?= $group ?> <?= $subtype['label'] ?></option>
              <? endforeach; ?>
            </optgroup>
          <? endforeach; ?>
            
          </select>
        </div>
        <div class="form-group col-xs-3">
          <label class="control-label" for="inputDate">Date</label>
          <input type="text" class="form-control" name="date" id="inputDate" value="<?=date("d-m-Y")?>" >
        </div>
      
      </div>
      <div class="row">
        
      </div>
        
        
      </form>
  </div>
</div>
<div class="row">
  <div id="inputBigTable">
  <table class="table table-striped row">
        <thead>
          <tr>
            <th class="tf tname" width="16%">Name
              </br>
              
            </th>
            <th class="tf tsplit" width="16%">Split (HH:MM:SS.SS)
            </br>
              <div class="input-group" data-toggle="tooltip" title="Fill in the split then press down to copy it to all the rows">
              <input type="text" class="form-control" name="splitA" id="inputSplitCopy" >
              <span class="input-group-btn">
              <button id="inputSplitCopyBtn" class="btn btn-default" type="button"><span class="glyphicon glyphicon-arrow-down"></span></button>
                  </span>
              </div></th>
            <th class="tf ttime" width="16%">
              Time (HH:MM:SS.SS)
              </br>
              <div class="input-group">
              <input type="text" class="form-control" name="timeA" id="inputTimeCopy" >
              <span class="input-group-btn">
              <button id="inputTimeCopyBtn" class="btn btn-default" type="button"><span class="glyphicon glyphicon-arrow-down"></span></button>
                  </span>
              </div>
            </th>
            <th class="tf tdistance" width="16%">
              Distance (m)
               </br>
                <div class="input-group">
                <input type="text" class="form-control" name="distanceA" id="inputDistanceCopy" >
                 <span class="input-group-btn">
                      <button id="inputDistanceCopyBtn" class="btn btn-default" type="button"><span class="glyphicon glyphicon-arrow-down"></span></button>
                    </span>
              </div>
            </th>
            <th class="tf trate" width="16%">Rate (spm)
            </br>
              <div class="input-group">
              <input type="text" class="form-control" name="timeA" id="inputRateCopy" >
              <span class="input-group-btn">
              <button id="inputRateCopyBtn" class="btn btn-default" type="button"><span class="glyphicon glyphicon-arrow-down"></span></button>
                  </span>
              </div></th>
            <th class="tf tnotes"  width="16%">Notes
            </br>
              <div class="input-group">
              <input type="text" class="form-control" name="timeA" id="inputNotesCopy" >
              <span class="input-group-btn">
              <button id="inputNotesAddBtn" class="btn btn-default" type="button"><span class="glyphicon glyphicon-plus"></span></button>
              <button id="inputNotesCopyBtn" class="btn btn-default" type="button"><span class="glyphicon glyphicon-arrow-down"></span></button>
                  </span>
              </div></th>
          </tr>
            
        </thead>
        <tbody>
          <?/*<tr>
            <td class="tf tname"><input class="form-control nameselector" type="text" /></td>
            <td class="tf tsplit"><input class="form-control" type="text" /></td>
             <td class="tf ttime"><input class="form-control" type="text" /></td>
            <td class="tf tdistance"><input class="form-control" type="text" /></td>
            <td class="tf trate"><input class="form-control" type="text" /></td>
            <td class="tf tnotes"><textarea class="form-control" rows="2"></textarea></td>
          </tr>*/?>
          
        </tbody>
      </table>
</div>
      <button id="addRow" type="button" class="btn btn-default">Add Row</button>  <button id="saveAllBtn" type="submit" class="btn btn-primary">Save</button>
  </div>
</div>
  <script type="text/javascript">
  $(document).ready(function() {
    $("#inputType").change(function() {
       var type = $(this).val();
        applyType(type);
        
    });

    $("#inputTimeCopyBtn").click(function() {
      $(".ttime input").val($("#inputTimeCopy").val());
    });

    $("#inputDistanceCopyBtn").click(function() {
      $(".tdistance input").val($("#inputDistanceCopy").val());
    });
     $("#inputRateCopyBtn").click(function() {
      $(".trate input").val($("#inputRateCopy").val());
    });
      $("#inputSplitCopyBtn").click(function() {
      $(".tsplit input").val($("#inputSplitCopy").val());
    });
    $("#inputNotesCopyBtn").click(function() {
      $(".tnotes textarea").val($("#inputNotesCopy").val());
    });
     $("#inputNotesAddBtn").click(function() {
      $(".tnotes textarea").each(function(index) {
        if($(this).val() == "" ) {
          $(this).val($(this).val() + $("#inputNotesCopy").val());
        } else {
          $(this).val($(this).val() + "\n" + $("#inputNotesCopy").val());
        }
        
      });
      
    });


    function applyType(type) {

        
        <? /*foreach($types as $group => $subtypes): ?>
          <? foreach($subtypes as $subtype): ?>
          <? if($subtype['erg_primary'] != null) { ?>
  if(type == "<?=$subtype['value']?>") {
            $("#inputPrimaryLbl").text('<?=ucfirst($subtype['erg_primary'])?>');
            $("#inputPrimaryDiv").removeClass("hidden").show();
            /*$("#inputPrimary").off("change");
            $("#inputPrimary").change(function() {
              $(".t<?=$subtype['erg_primary']?> input").val($(this).val());
            });
            $("#inputPrimaryBtn").off("click");
            $("#inputPrimaryBtn").click(function() {
              $(".t<?=$subtype['erg_primary']?> input").val($("#inputPrimary").val());
            });
          }
          <? } else {  ?>
          if(type == "<?=$subtype['value']?>") {
            $("#inputPrimaryDiv").hide();
          }
            <? } ?>
          <? endforeach; ?>
        <? endforeach; */ ?>

        loadTable(type);
    }

    var fields = [ 'name','time','distance','split','rate','notes' ];
    function loadTable(type) {
      $("#inputBigTable").slideUp(function() {
          $.get('<?=base_url()?>coach/ajax_getfields?type='+type,function(newfields) {
           
            fields = newfields;
            applyFields();
             $("#inputBigTable").slideDown();
          });
        });
    }

    function applyFields() {
      table = $("#inputBigTable table");
      theadtr=  $("#inputBigTable table thead tr");
     
      tbody=  $("#inputBigTable table tbody ");
      $(".tf").not(".tname").hide();
      $.each(fields,function(key,field) {
        $(".t"+field).show();
      });


           
    }

    function init() {

      $("#inputBigTable table tbody tr td").off('keydown');
      $("#inputBigTable table tbody tr:last td:last").keydown(function(event) {
          var index = $(this).parent().index();
         if ( event.which == 9 ) {
           event.preventDefault();
           addRow();

          }

      });
      
     
      applyFields();

       $('#inputBigTable table').enableCellNavigation();
    }

    var rowIndex = 0;
    loadTable('ergt');
    
    addRow();
    //init();
    $("#inputDate").inputmask("d-m-y"); 
    /*$("#inputRateCopy").inputmask({mask:"9","repeat": 2 ,rightAlignNumerics: false,placeholder: ""} ); 
    $("#inputSplitCopy").inputmask({mask:"s:s.s"});
    $("#inputTimeCopy").inputmask({mask:"h:s:s.s"});
    $("#inputDistanceCopy").inputmask({mask:"9","repeat": 6 ,rightAlignNumerics: false,placeholder: "0"});
    */
    $('#inputType').selectize();

      
    $("#inputType").focus();

    
    function addRow() {
      tbody=  $("#inputBigTable table tbody ");
      tr = $(' <tr> ' +
        ' <td class="tf tname"><input name="ac['+rowIndex+'][person]" class="form-control nameselector" type="text" /></td>   ' +
        ' <td class="tf tsplit"><input name="ac['+rowIndex+'][split]" class="form-control" type="text" /></td>       '+
        ' <td class="tf ttime"><input name="ac['+rowIndex+'][time]" class="form-control" type="text" /></td>    ' +
        ' <td class="tf tdistance"><input name="ac['+rowIndex+'][distance]" class="form-control" type="text" /></td>    ' +
        ' <td class="tf trate"><input name="ac['+rowIndex+'][rate]"  class="form-control" type="text" /></td>     ' +
        ' <td class="tf tnotes"><textarea name="ac['+rowIndex+'][notes]"  class="form-control" rows="2"></textarea></td>'+
        '</tr>');
      rowIndex = rowIndex + 1;

      tbody.append(tr);
      
    $(".trate input").inputmask({mask:"9","repeat": 2 ,rightAlignNumerics: false,placeholder: ""} ); 
    //$(".tsplit input").inputmask({mask:"s:s.s"});
    //$(".ttime input").inputmask({mask:"h:s:s.s"});
    //$(".tdistance input").inputmask({mask:"9","repeat": 6 ,numericInput: true,placeholder: "0"});

    turn_on_erg_calc(tr.find(".ttime input"),tr.find(".tdistance input"),tr.find(".tsplit input"));

      
      tr.find('input.nameselector').selectize({
                valueField: 'user_id',
                labelField: 'name',
                searchField: 'name',
                maxItems: 1,
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
                <? /* create: function(input) {
                    $.ajax({
                        url: '<?=base_url()?>coach/ajax_nameadd',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            name: input
                        },
                        error: function() {
                            return false;
                        },
                        success: function(res) {
                          if(res.user_id != "") {
                            return res;
                          } else {
                            alert('failure');
                            return false;
                          }
                        }
                    });
                } */ ?>
                create: true
            });
      tr.find('input:first').focus();

      init();
    }

    

    $("#addRow").click(function() {
       addRow();
    });

    $("#saveAllBtn").click(function() {
      saveAll();
    });

    function isValid(data) {
      var valid = true;
      $.each(data,function(key,obj) {
  
        if(obj.name == "person") {
          if(obj.value == "") {
            valid= false;
          }
        }
      });
      return valid;
    }

    function saveAll() {
      $("#inputBigTable table tbody tr").each(function(index) {
        var row = $(this);

        var data =row.find('input:visible').serializeArray();

        data.push({ name: "date", value: $("#inputDate").val() });
        data.push({ name: "type", value: $("#inputType").val() });
        data.push({ name: "person", value: $(this).find('.selectized').val() });
        
        if(isValid(data)) {
          row.find('input,textarea').attr("disabled","disabled");
         // row.find('.tname input').selectize.disable();
          $.ajax({
            url: '<?=base_url()?>coach/ajax_saveactivity',
            data: data,
            type: 'POST'
          }).done(function() {
            row.addClass("success");
            row.find('td').each(function() {
              $(this).wrapInner('<div></div>');
            });
            row.find('td div').slideUp(function() {
              row.fadeOut();
			  row.remove();
            });
          }).fail(function() {
            row.addClass("danger");
          });
        } else {
          row.addClass("danger");
        }
      });
    }

      ////*********************************/////////
    /////         split calculator        /////////
function time_to_seconds(a){parts=a.split(":");parts.reverse();total_seconds=raise60=0;for(var b in parts)seconds=parts[b]*Math.pow(60,raise60),total_seconds+=seconds,raise60++;return total_seconds}
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
      pretty = pad(hours,"0") + ":"  + (minutes) + ":" + combined_seconds;
    }

    return pretty;
  }

function turn_on_erg_calc(timeObj,distanceObj,splitObj)
{
    function validSplit(){return""!=splitObj.val()?!0:!1}function validTime(){return""!=timeObj.val()?!0:!1}function validDistance(){return""!=distanceObj.val()?!0:!1};

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

      timeObj.change(function() {
      recalculate();
      //$("#logRate").focus();
    });

    distanceObj.change(function() {
      recalculate();
      //$("#logRate").focus();
    });

    splitObj.change(function( ) {
      recalculate();
    });

    function recalculate()
    {
        // disable time if split entered
        if(validSplit() && validDistance() && validTime()) {
          //$("#inputTime").attr("disabled","disabled");
        // ALL THREE inputs
          
        } else if( validSplit()  && validTime()) {

          distanceObj.val(distanceCalc( timeObj.val(), splitObj.val() ));
        
        }
      else if( validSplit()  && validDistance()) {
          
        timeObj.val( timeCalc( distanceObj.val(),splitObj.val() ));
        }
      else if( validDistance()  && validTime()) {
          
        splitObj.val( splitCalc( distanceObj.val(),timeObj.val() ));
          
        }

        
      
    }
}
//////////////////////////////////////////////////

    // connection checks
    function checkConnection() {
      $.ajax({
        url: "<?=base_url()?>coach/ajax_conn",
        cache: false
      }).always(function() {
       
        setTimeout(checkConnection,30000);

      }).done(function(data) {
        if(data != true) {
        $("#connWarning").removeClass("hidden").show();
      }
      })
      .fail(function() {
         $("#connWarning").removeClass("hidden").show();
       });
    }
    checkConnection();
  });

  </script>