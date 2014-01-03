<div class="container"> 
  <div class="row">
    <div class="col-lg-12">
      <div class="page-header"><h1>All Clubs</h1></div>

			<ul>
			<? foreach($clubs as $club): ?>
			<li><a href="<?=base_url()?>club/profile/<?=$club['ref']?>"><?=$club['name']?></a></li>
			<? endforeach; ?>
			</ul>
		</div>
	</div>
</div>