<hr>

      <footer>
        <p>&copy; LGC 2013 <? if($this->l_auth->logged_in()) { ?>- <a href="<?= site_url('logout') ?>">Log Out</a> <? } ?>- Tel: <a href="tel:00441315104769">01315104769</a></p>
      </footer>
    </div> <!-- /container -->      <? /*  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="assets/js/vendor/jquery-1.10.1.min.js"><\/script>')</script>
*/ ?>
        
        <script src="<?php echo base_url(); ?>assets/js/vendor/bootstrap.min.js"></script>

        <script src="<?php echo base_url(); ?>assets/js/plugins.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/main.js"></script>
        <script>
        /*
        var ctx = document.getElementById("dashChart").getContext("2d");
            var data = {
                labels : ["January","February","March","April","May","June","July"],
                datasets : [
                    {
                        fillColor : "rgba(220,220,220,0.5)",
                        strokeColor : "rgba(220,220,220,1)",
                        data : [65,59,90,81,56,55,40]
                    },
                    {
                        fillColor : "rgba(151,187,205,0.5)",
                        strokeColor : "rgba(151,187,205,1)",
                        data : [28,48,40,19,96,27,100]
                    }
                ]
            }
            var myNewChart = new Chart(ctx).Bar(data);*/
        </script>

        <script>
            var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
            (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
            g.src='//www.google-analytics.com/ga.js';
            s.parentNode.insertBefore(g,s)}(document,'script'));
        </script>
    </body>
</html>