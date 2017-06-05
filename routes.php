<?php
  function call($controller, $action) {
    // require the file that matches the controller name
    require_once('controllers/' . $controller . '_controller.php');

    // create a new instance of the needed controller
    //require_once('Models/image.php');
    //require_once('Models/product.php');
      
      require_once('Models/category.php');
      require_once('Models/product.php');
      $controller = new AdminController();
    
    // call the action
    $controller->{ $action }();
  }

  // just a list of the controllers we have and their actions
  // we consider those "allowed" values
  $controllers = array('admin' => ['home',
                                   'error',
                                   'display_category',
                                   'display_product',
                                   'edit_category',
                                   'edit_product',
                                   'delete_category',
                                   'delete_product']);

  // check that the requested controller and action are both allowed
  // if someone tries to access something else he will be redirected to the error action of the admin controller
  if (array_key_exists($controller, $controllers)) {
    if (in_array($action, $controllers[$controller])) {
      call($controller, $action);
    } else {
      call('admin', 'home');
    }
  } else {
    call('admin', 'error');
  }
?>