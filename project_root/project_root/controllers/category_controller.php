<?php

require_once '../classes/category_class.php';

/**
 * Add category controller function
 */
function add_category_ctr($name)
{
    $category = new Category();
    
    // Add category
    $category_id = $category->add_category($name);
    
    if ($category_id) {
        return array('status' => 'success', 'category_id' => $category_id, 'message' => 'Category added successfully');
    }
    
    return array('status' => 'error', 'message' => 'Failed to add category or category name already exists');
}

/**
 * Get all categories controller function
 */
function get_all_categories_ctr()
{
    $category = new Category();
    $categories = $category->get_all_categories();
    
    if ($categories !== false) {
        return array('status' => 'success', 'categories' => $categories);
    }
    
    return array('status' => 'error', 'message' => 'Failed to fetch categories');
}

/**
 * Get category by ID controller function
 */
function get_category_by_id_ctr($cat_id)
{
    $category = new Category();
    $result = $category->get_category_by_id($cat_id);
    
    if ($result) {
        return array('status' => 'success', 'category' => $result);
    }
    
    return array('status' => 'error', 'message' => 'Category not found');
}

/**
 * Update category controller function
 */
function update_category_ctr($cat_id, $name)
{
    // Input validation
    if (empty($name) || $cat_id <= 0) {
        return array('status' => 'error', 'message' => 'Invalid category data provided');
    }
    
    $category = new Category();
    
    // Check if category exists first
    $existing_category = $category->get_category_by_id($cat_id);
    if (!$existing_category) {
        return array('status' => 'error', 'message' => 'Category not found');
    }
    
    // Attempt to update
    $result = $category->edit_category($cat_id, $name);
    
    if ($result) {
        return array('status' => 'success', 'message' => 'Category updated successfully');
    }
    
    return array('status' => 'error', 'message' => 'Failed to update category. Name may already exist');
}

/**
 * Delete category controller function
 */
function delete_category_ctr($cat_id)
{
    $category = new Category();
    $result = $category->delete_category($cat_id);
    
    if ($result) {
        return array('status' => 'success', 'message' => 'Category deleted successfully');
    }
    
    return array('status' => 'error', 'message' => 'Failed to delete category. It may have associated products');
}

/**
 * Get category count controller function
 */
function get_category_count_ctr()
{
    $category = new Category();
    return $category->get_category_count();
}