<div class="container">
    <div class="row">
        <div class="col-lg-12">
          <h1><?=$person['name']?></h1>
          <p>Age: <? $bday = new DateTime($person['dob']); $today = new DateTime(); $diff = $today->diff($bday); echo $diff->y;?> years old</p>
          <? if(count($memberships)>0) { ?>
          <p>Member of: </p>
          <ul>
          	<? foreach($memberships as $membership): ?>
          	<li><?= $membership['name'] ?></li>
          	<? endforeach; ?>
          </ul>
          <? } ?>
        </div>
    </div>
</div>