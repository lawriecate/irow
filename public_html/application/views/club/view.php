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
        		<li>Not Joined</li>
        		<li>Managers: </li>
        		<li>Coaches: </li>
        	</ul>
         
          
		</div>
	</div>
</div>