<?php
  class Image {
    
    public $id;
    public $file_location; //url
    public $name;

    public function __construct($id, $file_location, $name) {
      $this->id          = $id;
      $this->name        = $name;
      $this->file_location = $file_location;
    }

    //Find images of a product
    public static function get_product_images($prod_id){
      $db = Db::getInstance();
      $cat_id = intval($cat_id);
      $req = $db->prepare('SELECT * FROM images 
                            INNER JOIN rel_prod_image ON images.id =  rel_prod_image.image_id
                            WHERE rel_prod_image.product_id = :prod_id ');
      $req->execute(array(':prod_id' => $prod_id));

      $list = [];

      foreach($req->fetchAll() as $image) {
        $list[] = new Image ($image['id'],$image['name'],$image['file_location']);
      }
      
      $db = null;
      return $list;
    }

    public static function insert($file_location, $name, $product_id){
      
      $db= Db::getInstance();
      $req= $db->prepare('INSERT IGNORE INTO images (file_location, name)
                          VALUES (:file_location, :name)');
      $vals = array(':name'=>$name, ':file_location'=>$file_location);
      $req->execute($vals);
      $img_id = $db->lastInsertId(); 
      $req= $db->prepare('INSERT IGNORE INTO rel_prod_image (product_id, image_id)
                          VALUES (:product_id, :img_id)');
      $req->execute(array(':product_id' => $product_id, ':img_id' => $img_id));
      

      $db = null;
      return new images($prod_id, $name, $description, $price);
    }

  }
?>