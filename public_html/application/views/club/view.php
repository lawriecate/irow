<div class="jumbotron" style="background-image:url('<?=base_url()?>assets/test.jpg'); background-position:center bottom;background-size:cover;">
  <div class="container" style="height:300px">
     <h1 style="color:#fff;"><?= $club['name']?></h1>
  </div>
</div>
<div class="container">
    <!-- Example row of columns -->
    <div class="row">
        <div class="col-lg-12">
        	<ul>
        		<li><span class="glyphicon glyphicon-map-marker"></span> <?= $club['addr_city'] ?> <?= $club['addr_country'] ?></li>
        		<li><? echo ($club['verified'] == 1 ?  '<span class="glyphicon glyphicon-ok-sign"></span> Verified' : '<span class="glyphicon glyphicon-question-sign"></span> Unverified (request verification)'); ?></li>
        		<li><a href="#" id="toggleMembership"><? if($membership==FALSE) { ?>Not Joined<? } else { ?>Joined<? } ?></a></li>
        		<li>Managers: 
              <ul>
              <? foreach($managers as $manager): ?>
              <li><?= $manager['name'] ?></li>
            <? endforeach; ?>
          </ul>
            </li>
        		<li>Coaches: </li>
            <ul>
              <? foreach($coaches as $coach): ?>
              <li><?= $coach['name'] ?></li>
            <? endforeach; ?>
          </ul>
        	</ul>
         
          
		</div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
  $("#toggleMembership").click(function (){
    $.get('<?=base_url() ?>club/ajax_chmembership?club=<?=$club['ref']?>', function(data) {
      $("#toggleMembership").text(data);
    });
  });
});
</script>