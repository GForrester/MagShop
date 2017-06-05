<?php
  class AdminController {
    public $cat_list = array();

    public function __construct(){
      foreach (Category::all() as $category) {
        $this->cat_list[$category->id]=$category->name;
      }
      //print_r($cat_list);
    }

    public function home() {
      $categories = Category::all();
      require_once('views/admin/category.php');
    }

    public function display_category($category_slug = null){
      if($category_slug === null) $category_slug = $_REQUEST['category'];
      $q_category = Category::find($category_slug);
      $categories = Category::find_sub_cats($q_category->id);
      array_unshift($categories, $q_category);
      $cat_ids = [];//For searching product
      foreach ($categories as $category) {
        $cat_ids[] = $category->id;
      }

      if(isset($_REQUEST['deep']) && $_REQUEST['deep'] != false )
      {
        $products = Product::find_subcat_products($cat_ids);
      } 
      else {
        $products = Product::find_cat_products($q_category->id);
        $categories = array($categories[0]);
      }
      //print_r($products);
      require_once('views/admin/category.php');
    }

    public function display_product($product_id = null){
        $product = Product::find($_REQUEST['product']);
        require_once('views/admin/product.php');
    }

    public function edit_category(){
      $new_slug = Category::slugify($_REQUEST['name']);
      if(isset($_REQUEST['id'])){
        $updated_category = Category::update($_REQUEST['id'], $_REQUEST['name'], $new_slug, $_REQUEST['parent_id']);
      }
      else {
        $new_category = Category::insert($_REQUEST['name'],$new_slug,$_REQUEST['parent_id']);
      }
      $this->home();
    }

    public function edit_product($product_id = null){
      if(isset($_REQUEST['id']) ){
        $product = Product::update($_REQUEST['id'],$_REQUEST['name'],$_REQUEST['description'],$_REQUEST['price'],$_REQUEST['parent_id']);
      } else {
        $product = Product::insert($_REQUEST['name'],'',$_REQUEST['price'],$_REQUEST['parent_id']);
      }
      require_once('views/admin/product.php');
    }

    public function delete_category(){
      Category::remove($_REQUEST['id'], $_REQUEST['parent_id']);
      $this->home();
    }

    public function delete_product(){
     Product::remove($_REQUEST['id']);
     $this->display_category($_REQUEST['category_id']); 
    }


    public function error() {
      require_once('views/admin/error.php');
    }
  }
?>