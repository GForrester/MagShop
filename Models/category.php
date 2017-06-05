<?php
  class Category {
    
    public $id;
    public $name;
    public $parent_id; //id of category it is a subcategory of
    public $slug;

    public function __construct($id, $name, $parent_id = null, $slug) {
      $this->id         = $id;
      $this->name       = $name;
      $this->parent_id  = $parent_id;
      $this->slug       = $slug;
    }

    public static function slugify($name) {
      $db = Db::getInstance();
      $slug = preg_replace("/-$/","",preg_replace('/[^a-z0-9]+/i', "-", strtolower($name)));

      $query = "SELECT COUNT(*) AS NumHits FROM categories WHERE name LIKE '$slug%'";
      $req = $db->prepare($query);
      $req->execute([$slug]);
      
      $row = $req->fetchColumn();
      
      $numHits = (!isset($row['NumHits']) ? $row['NumHits'] : $row );

      return ($numHits > 0) ? ($slug . '-' . $numHits) : $slug;
    }

    public static function all() {
      $list = [];
      $db = Db::getInstance();
      $req = $db->query('SELECT * FROM categories');

      // we create a list of category objects from the database results
      foreach($req->fetchAll() as $category) {
        $list[] = new Category ($category['id'],$category['name'],$category['parent_id'],$category['slug']);
      }

      
      $db = null;
      return $list;
    }

    //Returns a category
    public static function find($id_slug) { 
      $match = 'slug';
      // check if searching by slug or by id
      if (preg_match('/^[0-9]+$/', $id_slug) == 1) {
        $match = 'id';
        $id_slug = intval($id_slug);
      }
      $db = Db::getInstance();
      $req = $db->prepare('SELECT * FROM categories WHERE '.$match.' =  "'.$id_slug.'"');

      $req->execute();
      $category = $req->fetch();
      
      $db = null;
      return new category($category['id'], $category['name'], $category['parent_id'], $category['slug']);
    }

    //Returns array of all subcategories
    public static function find_sub_cats($id) {
      $db = Db::getInstance();
      $id = intval($id);
      $req = $db->prepare('SELECT  id, name,
                            parent_id, slug
                        FROM    (SELECT * FROM categories
                                 ORDER BY id, parent_id) cats_sorted,
                                (SELECT @pv := '.$id.') INITIALISATION
                        WHERE   FIND_IN_SET(parent_id, @pv) > 0
                        AND     @pv := CONCAT(@pv, \',\', id)');
      $req->execute();

      $list = [];

      foreach($req->fetchAll() as $category) {
        $list[] = new Category ($category['id'],$category['name'],$category['parent_id'],$category['slug']);
      }
      
      $db = null;
      return $list;
    }

    //add new category
    public static function insert($name, $slug, $parent_id = null){
      
      print_r($parent_id);
      $db= Db::getInstance();
      $req= $db->prepare('INSERT IGNORE INTO categories (name, '.($parent_id != null ? 'parent_id,' : '').'slug) VALUES (:name, '.($parent_id != null ? ':parent_id,' : '').':slug)');
      $vals = array(':name' => $name, ':slug' => $slug);
      if($parent_id != null ) $vals[':parent_id']=$parent_id;
      $req->execute($vals);
      $id = $db->lastInsertId(); 
      $db = null;
      return new Category($id, $name, $parent_id, $slug);
    }

    //update category
    public static function update($id, $name, $slug, $parent_id = null){
    
      $db= Db::getInstance();
      $req= $db->prepare("UPDATE categories 
                            SET   name      = :name,
                                  slug      = :slug,
                                  parent_id = :parent_id
                            WHERE id        = :id");
      $vals = array(':id'=>$id,':name' => $name, ':slug' => $slug, ':parent_id' => $parent_id);
      $req->execute($vals);

      $db = null;
      return new Category($id, $name, $parent_id, $slug);
    }

    //Remove category and references to it
    public static function remove($id,$parent_id = null){
      $db= Db::getInstance();

      //updates current references to category to the current category's parent if it exists
      $req= $db->prepare("UPDATE categories AS c, rel_prod_cat AS rpc
                            SET   c.parent_id = :parent_id,
                               rpc.category_id  = :parent_id
                            WHERE c.parent_id   = :id
                            OR  rpc.category_id = :id");
      $vals = array(':id'=>$id,':parent_id' => $parent_id);
      $req->execute($vals);
      
      $req= $db->prepare("DELETE FROM categories 
                            WHERE id  = :id");
      $vals = array(':id'=>$id);
      $req->execute($vals);
      $db = null;
    }

  }
?>