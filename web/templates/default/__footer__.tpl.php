    <div id="footer">
      <span id="footer_time">
        ( output in
        <?php
        $calculation_end = microtime(true);
        $calculation_time = $calculation_end - $this->calculation_start;
        
        echo round($calculation_time, 3);
        ?>
        seconds )
      </span>
      <span id="footer_release"><a href="https://github.com/Unvanquished/unvstats">Unvstats v<?php echo VERSION; ?></a></span><br>
      <span id="footer_credits">derived from <a href="https://github.com/DolceTriade/tremstats">Tremstats Too v2.3.0</a> ~ modified by <a href="https://github.com/ppetr">Petr</a> ~ original by <a href="http://rezyn.mercenariesguild.net">Rezyn</a> ~ and <a href="http://www.dasprids.de" title="DASPRiD's">DASPRiD</a> ~ </span>
    </div>
  </body>
</html>
