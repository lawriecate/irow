<hr>

      <footer>
        <p>&copy; LGC 2013 <? if($this->l_auth->logged_in()) { ?>- <a href="<?= site_url('logout') ?>">Log Out</a> <? } ?>- Tel: <a href="tel:00441315104769">01315104769</a></p>
      </footer>
    </div> <!-- /container -->      <? /*  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="assets/js/vendor/jquery-1.10.1.min.js"><\/script>')</script>
*/ ?>
        
       
        <? if(!isset($notracking)) { ?>
        <script>
            var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
            (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
            g.src='//www.google-analytics.com/ga.js';
            s.parentNode.insertBefore(g,s)}(document,'script'));
        </script>
        <? } ?>
    </body>
</html>