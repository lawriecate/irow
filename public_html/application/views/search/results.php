<div class="container"> 
  <div class="row">
    <div class="col-lg-12">
      <h2>Search Results</h2>
    <? foreach($results as $category => $items): ?>
      <h3><?= $category ?></h3>
      <? 
      if(count($items) > 0) {
      foreach($items as $item): ?>
      <div class="row">
        <div class="col-xs-6 col-md-3">
          <a href="<?= $item['url'] ?>" class="thumbnail">
            <?= preg_replace('/'.$query.'/i', '<strong>$0</strong>',$item['name']); ?>
          </a>
        </div>
      </div>
      <? endforeach; ?>
    <? 
  } else {
    ?>
    <h4>No results</h4>
<? } 

endforeach; 
?>

    </div>
  </div>
  </div>