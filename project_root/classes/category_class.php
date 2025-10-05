<?php

require_once '../settings/db_class.php';

/**
 * Category class - handles all category-related database operations
 */
class Category extends db_connection
{
    private $cat_id;
    private $cat_name;

    public function __construct($cat_id = null)
    {
        parent::db_connect();
        if ($cat_id) {
            $this->cat_id = $cat_id;
            $this->loadCategory();
        }
    }

    /**
     * Load category data from database
     */
    private function loadCategory()
    {
        if (!$this->cat_id) {
            return false;
        }
        
        $sql = "SELECT * FROM categories WHERE cat_id = " . $this->cat_id;
        $result = $this->db_fetch_one($sql);
        
        if ($result) {
            $this->cat_name = $result['cat_name'];
            return true;
        }
        return false;
    }

    /**
     * Add new category to database
     */
    public function add_category($name)
    {
        // Check if category name already exists
        if ($this->category_name_exists($name)) {
            return false;
        }
        
        // Escape strings to prevent SQL injection
        $name = mysqli_real_escape_string($this->db_conn(), $name);
        
        // Prepare SQL query
        $sql = "INSERT INTO categories (cat_name) VALUES ('$name')";
        
        if ($this->db_write_query($sql)) {
            return $this->last_insert_id();
        }
        return false;
    }

    /**
     * Get all categories
     */
    public function get_all_categories()
    {
        $sql = "SELECT * FROM categories ORDER BY cat_name ASC";
        return $this->db_fetch_all($sql);
    }

    /**
     * Get category by ID
     */
    public function get_category_by_id($cat_id)
    {
        $cat_id = (int)$cat_id;
        $sql = "SELECT * FROM categories WHERE cat_id = $cat_id";
        return $this->db_fetch_one($sql);
    }

    /**
     * Check if category name exists
     */
    public function category_name_exists($name)
    {
        // Ensure we have a connection
        if (!$this->db_connect()) {
            return false;
        }
        
        $name = mysqli_real_escape_string($this->db_conn(), $name);
        $sql = "SELECT cat_id FROM categories WHERE cat_name = '$name'";
        $result = $this->db_fetch_one($sql);
        
        return ($result !== false && $result !== null && isset($result['cat_id']));
    }

    /**
     * Update category information
     */
    public function edit_category($cat_id, $name)
    {
        // Check if new name already exists (excluding current category)
        if ($this->category_name_exists_except($name, $cat_id)) {
            return false;
        }
        
        $cat_id = (int)$cat_id;
        $name = mysqli_real_escape_string($this->db_conn(), $name);
        
        $sql = "UPDATE categories SET cat_name = '$name' WHERE cat_id = $cat_id";
        
        return $this->db_write_query($sql);
    }

    /**
     * Check if category name exists except for a specific ID
     */
    private function category_name_exists_except($name, $except_id)
    {
        $name = mysqli_real_escape_string($this->db_conn(), $name);
        $except_id = (int)$except_id;
        $sql = "SELECT cat_id FROM categories WHERE cat_name = '$name' AND cat_id != $except_id";
        $result = $this->db_fetch_one($sql);
        
        return ($result !== false && $result !== null && isset($result['cat_id']));
    }

    /**
     * Delete category
     */
    public function delete_category($cat_id)
    {
        // Check if category has associated products
        if ($this->category_has_products($cat_id)) {
            return false; // Cannot delete category with products
        }
        
        $cat_id = (int)$cat_id;
        $sql = "DELETE FROM categories WHERE cat_id = $cat_id";
        return $this->db_write_query($sql);
    }

    /**
     * Check if category has associated products
     */
    private function category_has_products($cat_id)
    {
        $cat_id = (int)$cat_id;
        $sql = "SELECT COUNT(*) as product_count FROM products WHERE product_cat = $cat_id";
        $result = $this->db_fetch_one($sql);
        
        if ($result && $result['product_count'] > 0) {
            return true;
        }
        return false;
    }

    /**
     * Get category count
     */
    public function get_category_count()
    {
        $sql = "SELECT COUNT(*) as total FROM categories";
        $result = $this->db_fetch_one($sql);
        return $result ? $result['total'] : 0;
    }
}