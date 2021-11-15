<?php
function create_pagination($nr_results,$perpage,$currentPage){
    // build array containing links to all pages
    $tmp = [];
    for($p=1, $i=0; $i < $nr_results; $p++, $i += $perpage) {
        if($currentPage == $p) {
            // current page shown as bold, no link
            $tmp[] = "<b>".$p."</b>";
        } else {
            $tmp[] = "<a href=\"?page=".$p."\">".$p."</a>";
        }
    }

// thin out the links
    for($i = count($tmp) - 3; $i > 1; $i--) {
        if(abs($currentPage - $i - 1) > 2) {
            unset($tmp[$i]);
        }
    }

// display page navigation if data covers more than one page
    if(count($tmp) > 1) {
        echo "<p>";

        if($currentPage > 1) {
            // display 'Prev' link
            echo "<a href=\"?page=" . ($currentPage - 1) . "\">&laquo; Prev</a> | ";
        } else {
            echo "Page ";
        }

        $lastlink = 0;
        foreach($tmp as $i => $link) {
            if($i > $lastlink + 1) {
                echo " ... "; // where one or more links have been omitted
            } elseif($i) {
                echo " | ";
            }
            echo $link;
            $lastlink = $i;
        }

        if($currentPage <= $lastlink) {
            // display 'Next' link
            echo " | <a href=\"?page=" . ($currentPage + 1) . "\">Next &raquo;</a>";
        }

        echo "</p>\n\n";
    }
}
?>
