<div class="container">
    <!-- Example row of columns -->
    <div class="row">
        <div class="col-lg-12">
        	<h1>Invitations</h1>
        	<p>When you log an activity using a name that isn't registered to your club, a profile will automatically be created so you can keep track of the same person.  
        		You can invite them to take possesion of their profile by entering their email next to their name, and pressing 'Invite'</p>
        	<? /* <p>Invite a new user:</p> */ ?>

        	<? /*<p>Placeholder users <small>(names you've already entered data for)</small></p>*/ ?>
			<table class="table">
				<thead>
					<th>Name</th>
					<th>Email</th>
					<th>Status</th>
				</thead>
				<tbody>
					<? foreach($invitations as $invite): ?>
					<tr>
					<td><input type="hidden" name="inputName" class="inputId" id="inputName<?=$invite['id']?>" value="<?=$invite['id']?>"/><?=$invite['name']?></td>
					<td><input type="email" name="inputEmail" class="inputEmail form-control" id="inputEmail<?=$invite['email']?>" placeholder="Enter email"></td>
					<td><a class="btn btn-primary inviteButton">Invite</a></td>
				</tr>
				<? endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	$(".inviteButton").click(function() {
		row = $(this).closest("tr");
		button = $(this);
		id = row.find(".inputId").val();
		email = row.find(".inputEmail").val();
		$.post("<?=base_url()?>coach/ajax_invite",{uid: id, email: email}).done(function(data) {
			if(data==true) {
				button.text("Sent");
				button.attr("disabled","disabled");
				button.removeClass("btn-primary").addClass("btn-success");
			} else {
				button.text("Error");
				button.removeClass("btn-primary").addClass("btn-danger");
			}
		});
	});
});
</script>