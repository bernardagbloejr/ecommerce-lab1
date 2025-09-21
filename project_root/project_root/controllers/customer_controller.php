<?php

require_once '../classes/customer_class.php';

/**
 * Register customer controller function
 */
function register_customer_ctr($name, $email, $password, $country, $city, $contact, $user_role = 2)
{
    $customer = new Customer();
    
    // Add customer
    $customer_id = $customer->add_customer($name, $email, $password, $country, $city, $contact, $user_role);
    
    if ($customer_id) {
        return array('status' => 'success', 'customer_id' => $customer_id);
    }
    
    return array('status' => 'error', 'message' => 'Failed to register customer or email already exists');
}

/**
 * Get customer by email controller function
 */
function get_customer_by_email_ctr($email)
{
    $customer = new Customer();
    return $customer->get_customer_by_email($email);
}

/**
 * Login customer controller function
 */
function login_customer_ctr($email, $password)
{
    $customer = new Customer();
    $result = $customer->verify_password($email, $password);
    
    if ($result) {
        return array('status' => 'success', 'customer' => $result);
    }
    
    return array('status' => 'error', 'message' => 'Invalid email or password');
}

/**
 * Check if email exists controller function
 */
function check_email_exists_ctr($email)
{
    $customer = new Customer();
    return $customer->email_exists($email);
}