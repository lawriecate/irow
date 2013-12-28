<div class="container"> 
  <div class="row">
    <div class="col-lg-12">

        <h1>Manage Clubs</h1>
        <p>
          <input type="text" name="search" id="txtSearch"><button id="btnSearch" class="btn btn-default">Search</button><a href="<?=base_url()?>admin/add_club" class="btn btn-primary">Add Club</a>
        </p>
        <div class="table-responsive">
        <table class="table table-striped" id="userstable">
         <thead> <tr>
	          <th>ID</th> 
	          <th>Name</th>
	          <th>Phone</th>
	          <th>Email</th> 
	          <th>Options</th>
	          </tr> </thead> 
          <tbody> 
            <tr> <td>-</td> <td>-</td> <td>-</td> <td>-</td> <td>-</td></tr> 


           </tbody> </table>
</div>
           <p id="status"></p>
          
      
      </div>
  </div>
</div>
   <script type="text/javascript" charset="utf-8">
                        $(document).ready(function() {
                        		function searchClubs(search) {
                        			$.get('<?=base_url()?>admin/ajax_clubsdata?start=0&len=10&q='+search, function(data) {
                        				$("#userstable > tbody > tr").remove();
                        				$.each(data.items,function(key,obj) {
                        					var row = $("<tr></tr>");
                        					$.each(obj,function(ikey,iobj){
                        						
                        						row.append("<td>" + iobj + "</td>");
                        					});

                        					row.append('<td><a href="<?=base_url()?>admin/edit_club/'+obj[0]+'">Edit</a></td>');

                        					$("#userstable tbody").append(row);

                        				});
                        				$("#status").text(data.display + " of " + data.total + " clubs shown");
                        			});
                        		}

                        		$("#txtSearch").change(function() {
                        			searchClubs($(this).val());
                        		});

                        		$("#btnSearch").click(function() {
                        			searchClubs($("#txtSearch").val());
                        		});

                        	searchClubs("");
                        } );
                </script>