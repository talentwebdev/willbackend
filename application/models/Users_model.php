<?php

class Users_model extends CI_Model  
{
    public static $table = "users"; 

    public function insert_entry()
    {
        
        $this->name = $_POST['name'];
        $this->surname = $_POST['surname'];
        $this->id_number = $_POST['id_number'];
        $this->email = $_POST['email'];
        $this->password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        if(count($this->get_entry()))
        {
            return null;
        }
        $this->db->insert("users", $this);
        $this->id = $this->db->insert_id();
        return $this;
        
    }

    public function get_entry()
    {
        $query = $this->db->where('email', $_POST['email'])->get("users");
        return $query->result();
    }
}