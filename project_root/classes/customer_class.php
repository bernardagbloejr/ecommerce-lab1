<?php

require_once '../settings/db_class.php';

/**
 * Customer class - handles all customer-related database operations
 */
class Customer extends db_connection
{
    private $customer_id;
    private $customer_name;
    private $customer_email;
    private $customer_pass;
    private $customer_country;
    private $customer_city;
    private $customer_contact;
    private $customer_image;
    private $user_role;

    public function __construct($customer_id = null)
    {
        parent::db_connect();
        if ($customer_id) {
            $this->customer_id = $customer_id;
            $this->loadCustomer();
        }
    }

    /**
     * Load customer data from database
     */
    private function loadCustomer()
    {
        if (!$this->customer_id) {
            return false;
        }
        
        $sql = "SELECT * FROM customer WHERE customer_id = " . $this->customer_id;
        $result = $this->db_fetch_one($sql);
        
        if ($result) {
            $this->customer_name = $result['customer_name'];
            $this->customer_email = $result['customer_email'];
            $this->customer_country = $result['customer_country'];
            $this->customer_city = $result['customer_city'];
            $this->customer_contact = $result['customer_contact'];
            $this->customer_image = $result['customer_image'];
            $this->user_role = $result['user_role'];
            return true;
        }
        return false;
    }

    /**
     * Add new customer to database
     */
    public function add_customer($name, $email, $password, $country, $city, $contact, $user_role = 2)
    {
        // Check if email already exists
        if ($this->email_exists($email)) {
            return false;
        }
        
        // Encrypt password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Escape strings to prevent SQL injection
        $name = mysqli_real_escape_string($this->db_conn(), $name);
        $email = mysqli_real_escape_string($this->db_conn(), $email);
        $country = mysqli_real_escape_string($this->db_conn(), $country);
        $city = mysqli_real_escape_string($this->db_conn(), $city);
        $contact = mysqli_real_escape_string($this->db_conn(), $contact);
        
        // Prepare SQL query
        $sql = "INSERT INTO customer (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, user_role) 
                VALUES ('$name', '$email', '$hashed_password', '$country', '$city', '$contact', $user_role)";
        
        if ($this->db_write_query($sql)) {
            return $this->last_insert_id();
        }
        return false;
    }

    /**
     * Get customer by email
     */
    public function get_customer_by_email($email)
    {
        $email = mysqli_real_escape_string($this->db_conn(), $email);
        $sql = "SELECT * FROM customer WHERE customer_email = '$email'";
        return $this->db_fetch_one($sql);
    }

    /**
     * Check if email exists
     */
    public function email_exists($email)
    {
        $email = mysqli_real_escape_string($this->db_conn(), $email);
        $sql = "SELECT customer_id FROM customer WHERE customer_email = '$email'";
        $result = $this->db_fetch_one($sql);
        return $result !== false;
    }

    /**
     * Verify customer password for login
     */
    public function verify_password($email, $password)
    {
        $customer = $this->get_customer_by_email($email);
        if ($customer && password_verify($password, $customer['customer_pass'])) {
            return $customer;
        }
        return false;
    }

    /**
     * Edit customer information
     */
    public function edit_customer($customer_id, $name, $email, $country, $city, $contact)
    {
        $name = mysqli_real_escape_string($this->db_conn(), $name);
        $email = mysqli_real_escape_string($this->db_conn(), $email);
        $country = mysqli_real_escape_string($this->db_conn(), $country);
        $city = mysqli_real_escape_string($this->db_conn(), $city);
        $contact = mysqli_real_escape_string($this->db_conn(), $contact);
        
        $sql = "UPDATE customer 
                SET customer_name = '$name', 
                    customer_email = '$email', 
                    customer_country = '$country', 
                    customer_city = '$city', 
                    customer_contact = '$contact' 
                WHERE customer_id = $customer_id";
        
        return $this->db_write_query($sql);
    }

    /**
     * Delete customer
     */
    public function delete_customer($customer_id)
    {
        $sql = "DELETE FROM customer WHERE customer_id = $customer_id";
        return $this->db_write_query($sql);
    }

    /**
     * Update customer image
     */
    public function update_customer_image($customer_id, $image_path)
    {
        $image_path = mysqli_real_escape_string($this->db_conn(), $image_path);
        $sql = "UPDATE customer SET customer_image = '$image_path' WHERE customer_id = $customer_id";
        return $this->db_write_query($sql);
    }
}
