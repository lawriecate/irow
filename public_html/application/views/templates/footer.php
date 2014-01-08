<hr>

      <footer>
        <p>&copy; LGC 2013 <? if($this->l_auth->logged_in()) { ?>- <a href="<?= site_url('logout') ?>">Log Out</a> <? } ?> - <a href="http://irow.uservoice.com" target="_blank">Help &amp; Support</a> <? /*- Tel: <a href="tel:00441315104769">01315104769</a> - <a href="<?=base_url()?>terms.html" target="_blank">Terms Of Service</a> - <a href="<?=base_url()?>privacy.html" target="_blank">Privacy Policy</a>*/?></p>
      </footer>
    </div> <!-- /container -->      <? /*  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="assets/js/vendor/jquery-1.10.1.min.js"><\/script>')</script>
*/ ?>
        
       
        <? if(!isset($notracking)) { ?>
        <script>
          (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
          m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
          })(window,document,'script','//stats.g.doubleclick.net/dc.js','ga');

          ga('create', 'UA-6840885-4', 'irow.com');
          ga('send', 'pageview');

        </script>
        <? } ?>
        <script>
// Include the UserVoice JavaScript SDK (only needed once on a page)
UserVoice=window.UserVoice||[];(function(){var uv=document.createElement('script');uv.type='text/javascript';uv.async=true;uv.src='//widget.uservoice.com/K18b8tkbZwKNUMLjbYGhAA.js';var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(uv,s)})();

//
// UserVoice Javascript SDK developer documentation:
// https://www.uservoice.com/o/javascript-sdk
//

// Set colors
UserVoice.push(['set', {
  accent_color: '#448dd6',
  trigger_color: 'white',
  trigger_background_color: 'rgba(46, 49, 51, 0.6)'
}]);

// Identify the user and pass traits
// To enable, replace sample data with actual user traits and uncomment the line
<? 
if($this->l_auth->logged_in()) {
$user = $this->user_model->get_by_id($this->l_auth->current_user_id()); 
  ?>
UserVoice.push(['identify', {
  email:      '<?=$user['email']?>', // User’s email address
  name:       '<?=$user['name']?>', // User’s real name
  created_at: '<?=strtotime($user['signup'])?>', // Unix timestamp for the date the user signed up
  id:         '<?=$user['id']?>', // Optional: Unique id of the user (if set, this should not change)
  //type:       'Owner', // Optional: segment your users by type
  //account: {
  //  id:           123, // Optional: associate multiple users with a single account
  //  name:         'Acme, Co.', // Account name
  //  created_at:   1364406966, // Unix timestamp for the date the account was created
  //  monthly_rate: 9.99, // Decimal; monthly rate of the account
  //  ltv:          1495.00, // Decimal; lifetime value of the account
  //  plan:         'Enhanced' // Plan name for the account
  //}
}]);
<? } ?>
// Add default trigger to the bottom-right corner of the window:
UserVoice.push(['addTrigger', { mode: 'contact', trigger_position: 'bottom-right' }]);

// Or, use your own custom trigger:
//UserVoice.push(['addTrigger', '#id', { mode: 'contact' }]);

// Autoprompt for Satisfaction and SmartVote (only displayed under certain conditions)
UserVoice.push(['autoprompt', {}]);
</script>
    </body>
</html>