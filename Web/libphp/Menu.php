<?php
function Menu($role, $page_active): string
{
    $to_echo = "";
    $admin_pages = array("Search" => "search_page.php", "Annotator area" => "AnnotatorArea.php", "Validator Area" => "ValidatorArea.php", "Add genome" => "Add_genome.php", "User Management" => "usermanag.php");
    $validator_pages = array("Search" => "search_page.php", "Annotator area" => "AnnotatorArea.php", "Validator Area" => "ValidatorArea.php");
    $annotator_pages = array("Search" => "search_page.php", "Annotator area" => "AnnotatorArea.php");
    $reader_pages = array("Search" => "search_page.php");
    if ($role == 'Admin') {
        foreach ($admin_pages as $name => $page) {
            if ($page_active == $page) {/*Active page*/
                $to_echo .= "<a class='active' href='$page'>$name</a>";
            } else {
                $to_echo .= "<a href='$page'>$name</a>";
            }
        }
    } else if ($role == 'Validator') {
        foreach ($validator_pages as $name => $page) {
            if ($page_active == $page) {/*Active page*/
                $to_echo .= "<a class='active' href='$page'>$name</a>";
            } else {
                $to_echo .= "<a href='$page'>$name</a>";
            }
        }
    } else if ($role == 'Annotator') {
        foreach ($annotator_pages as $name => $page) {
            if ($page_active == $page) {/*Active page*/
                $to_echo .= "<a class='active' href='$page'>$name</a>";
            } else {
                $to_echo .= "<a href='$page'>$name</a>";
            }
        }
    } else {
        foreach ($reader_pages as $name => $page) {
            if ($page_active == $page) { /*Active page*/
                $to_echo .= "<a class='active' href='$page'>$name</a>";
            } else {
                $to_echo .= "<a href='$page'>$name</a>";
            }
        }
    }
    /*Link to logout ==> redirect to login page*/
    $to_echo .= "<div class='LogOut'><a class='fa fa-sign-out fa-lg' href = 'Logout.php'>Log out</a></div>";
    return $to_echo;
}



