<?php
require_once('includes/connection.php');
$controller = 'admin';
$action     = 'home';
if (isset($_REQUEST['action'])) {
	$action = $_REQUEST['action'];
} else if (isset($_REQUEST['category']) && $_REQUEST['category'] != null) {
	$action  = 'display_category';

} else if (isset($_REQUEST['product'])) {

	$action  = 'display_product';

} else if (isset($_REQUEST['prod_or_cat'])) {

	$action  = 'find_prod_or_cat';
} else  {
	$action = 'home';
}

require_once('views/layout.php');
?>
