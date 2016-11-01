<div class="container"> 
  <div class="row">
    <div class="col-lg-12">

        <h1>Manage Users</h1>
        <p>
          <input type="text" name="search" id="txtSearch"><button id="btnSearch">Search</button>
        </p>
        <div class="table-responsive">
        <table class="table table-striped" id="userstable">
         <thead> <tr>
	          <th>ID</th> 
	          <th>Name</th>
	          <th>Email</th>
	          <th>DOB</th> 
	          <th>Admin</th> 
	          <th>Club</th> 
	          <th>Options</th>
	          </tr> </thead> 
          <tbody> 
            <tr> <td>-</td> <td>-</td> <td>-</td> <td>-</td> <td>-</td> <td>-</td> <td>-</td></tr> 


           </tbody> </table>
        </div>
           <p id="status"></p>
           <? /*<ul class="pagination">
  <li><a href="#">&laquo;</a></li>
  <li><a href="#">1</a></li>
  <li><a href="#">2</a></li>
  <li><a href="#">3</a></li>
  <li><a href="#">4</a></li>
  <li><a href="#">5</a></li>
  <li><a href="#">&raquo;</a></li>
</ul>*/ ?>
      
      </div>
  </div>
</div>
   <script type="text/javascript" charset="utf-8">
                        $(document).ready(function() {
                               /* $('#userstable').dataTable({
							        "bProcessing": true,
							        "bServerSide": true,
							        "sAjaxSource": "<?=base_url()?>admin/ajax_usersdata"
							    });*/

                        		function searchUsers(search) {
                        			$.get('<?=base_url()?>admin/ajax_usersdata?start=0&len=50&q='+search, function(data) {
                        				$("#userstable > tbody > tr").remove();
                        				$.each(data.items,function(key,obj) {
                        					var row = $("<tr></tr>");
                        					$.each(obj,function(ikey,iobj){
                        						
                        						row.append("<td>" + iobj + "</td>");
                        					});

                        					row.append('<td><a href="<?=base_url()?>admin/edit_user/'+obj[0]+'">Edit</a> :: <a href="<?=base_url()?>admin/getauth_user/'+obj[0]+'">Authenticate</a></td>');

                        					$("#userstable tbody").append(row);

                        				});
                        				$("#status").text(data.display + " of " + data.total + " users shown");
                        			});
                        		}

                        		$("#txtSearch").change(function() {
                        			searchUsers($(this).val());
                        		});

                        		$("#btnSearch").click(function() {
                        			searchUsers($("#txtSearch").val());
                        		});

                        	searchUsers("");
                        } );
                </script>