<?php
  class Product {
    
    public $id;
    public $name;
    public $description;
    public $price;
    public $images;
    public $category_id;

    public function __construct($id, $name, $description, $price, $images=null, $category_id) {
      $this->id          = $id;
      $this->name        = $name;
      $this->description = $description;
      $this->price       = $price;
      $this->images   = $images;
      $this->category_id = $category_id;
    }

    public static function slugify($name) {
      $db = Db::getInstance();
      $slug = preg_replace("/-$/","",preg_replace('/[^a-z0-9]+/i', "-", strtolower($name)));

      $query = "SELECT COUNT(*) AS NumHits FROM products WHERE name LIKE '$slug%'";
      $req = $db->prepare($query);
      $req->execute([$slug]);
      
      $row = $req->fetchColumn();
      
      $numHits = ($row == '0' ? $row : $row['NumHits'] );

      return ($numHits > 0) ? ($slug . '-' . $numHits) : $slug;
    }

    public static function all() {
      $list = [];
      $db = Db::getInstance();
      $req = $db->query('SELECT * FROM products');

      // we create a list of product objects from the database results
      foreach($req->fetchAll() as $product) {
        $list[] = new Product ($product['id'],$product['name'],$product['description'],$product['price']);
      }

      
      $db = null;
      return $list;
    }

    //Returns a product
    public static function find($id) { 
      $db = Db::getInstance();
      $req = $db->prepare('SELECT products.*, COUNT(rel_prod_image.id) AS "images", rel_prod_cat.category_id
                            FROM products 
                            INNER JOIN rel_prod_cat ON rel_prod_cat.product_id = products.id
                            LEFT JOIN rel_prod_image ON products.id =  rel_prod_image.product_id
                            WHERE rel_prod_cat.product_id = '.$id.' 
                            GROUP BY products.id');

      $req->execute();
      $product = $req->fetch();
      
      $db = null;
      return new Product ($product['id'],$product['name'],$product['description'],$product['price'],$product['images'],$product['category_id']);
    }

    //Returns array of products from a single category, shallow
    public static function find_cat_products($cat_id) {
      $db = Db::getInstance();
      $cat_id = intval($cat_id);
      $req = $db->prepare('SELECT products.*, COUNT(rel_prod_image.id) AS "images", rel_prod_cat.category_id
                            FROM products 
                            INNER JOIN rel_prod_cat ON rel_prod_cat.product_id = products.id
                            LEFT JOIN rel_prod_image ON products.id =  rel_prod_image.product_id
                            WHERE rel_prod_cat.category_id = :cat_id 
                            GROUP BY products.id');
      $req->execute(array(':cat_id' => $cat_id));

      $list = [];

      foreach($req->fetchAll() as $product) {
        $list[] = new Product ($product['id'],$product['name'],$product['description'],$product['price'], $product['images'], $product['category_id']);
      }
      
      $db = null;
      return $list;
    }

    //Returns array of products from a heirarchy of categories, deep
    public static function find_subcat_products($cat_ids) {
      $db = Db::getInstance();
      $questionmarks = str_repeat("?,", count($cat_ids)-1) . "?";

      $req = $db->prepare('SELECT products.*, COUNT(rel_prod_image.id) AS "images", rel_prod_cat.category_id
                            FROM products 
                            INNER JOIN rel_prod_cat ON rel_prod_cat.product_id = products.id
                            LEFT JOIN rel_prod_image ON products.id =  rel_prod_image.product_id
                            WHERE rel_prod_cat.category_id IN ('.$questionmarks.') 
                            GROUP BY products.id');
      $req->execute($cat_ids);

      $list = [];

      foreach($req->fetchAll() as $product) {
        $list[] = new Product ($product['id'],$product['name'],$product['description'],$product['price'], $product['images'], $product['category_id']);
      }
      
      $db = null;
      return $list;
    }

    //add new product
    public static function insert($name, $description, $price, $category_id){
      
      $db= Db::getInstance();
      $req= $db->prepare('INSERT IGNORE INTO products (name, description, price)
                          VALUES (:name, :description, :price)');
      $vals = array(':name'=>$name, ':description'=>$description, ':price'=>$price);
      $req->execute($vals);
      $prod_id = $db->lastInsertId(); 
      $req= $db->prepare('INSERT IGNORE INTO rel_prod_cat (product_id, category_id)
                          VALUES (:product_id, :category_id)');
      $req->execute(array(':product_id' => $prod_id, ':category_id' => $category_id));
      

      $db = null;
      return new Product($prod_id, $name, $description, $price, null, $category_id);
    }

    //update product
    public static function update($id, $name, $description, $price, $cat_id){
    
      $db= Db::getInstance();
      $req= $db->prepare("UPDATE products  
                            SET   products.name        = :name,
                                  products.description = :description,
                                  products.price       = :price
                            WHERE products.id         = :id");
      $vals = array(':id'=>$id,':name' => $name, ':description' => $description, ':price' => $price);
      $req->execute($vals);
      $db = null;
      Product::update_prod_cat($id,$cat_id);
      return new Product($id, $name, $description, $price, null, $cat_id);
    }

    //Update product categories
    public static function update_prod_cat($id, $cat_id){
    
      $db= Db::getInstance();
      $req= $db->prepare("DELETE FROM rel_prod_cat 
                          WHERE product_id = :id");
      $vals = array(':id'=>$id);
      $req->execute($vals);


      $req= $db->prepare("INSERT IGNORE INTO rel_prod_cat (product_id, category_id)
                          VALUES ($id, $cat_id)");
      $req->execute();

      $db = null;
    }

    //Remove product and references to in relational tables
    public static function remove($id){
      $db= Db::getInstance();

      $req= $db->prepare("DELETE FROM products 
                            WHERE id  = :id");
      $vals = array(':id'=>$id);
      $req->execute($vals);
      $db = null;
    }

  }
?>