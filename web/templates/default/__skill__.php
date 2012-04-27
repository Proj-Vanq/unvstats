<?php
    function skill($skill, $sigma) {
        if (($skill > 0) && ($sigma > 0)) {
            echo round($skill, 1);
            echo " (";
            echo round($sigma, 1);
            echo ")";
        } else
            echo "<span class='n-a'></span>";
    }

    function skillTD($skill, $sigma) {
        if (($skill > 0) && ($sigma > 0)) {
            echo '<td class="skill" title="uncertainity: ';
            echo round($sigma, 1);
            echo '">';
            echo round($skill, 1);
            echo '</td>';
        } else
            echo "<td class='n-a'></td>";
    }
?>
