<?php

class Notification_model extends CI_Model  
{
    public function add_entry($title, $content)
    {
        $this->title = $title;
        $this->content = $content;
        $this->db->insert("notification", $this);
        $this->id = $this->db->insert_id();
        return $this;
    }

    public function fetch_entry($user_id)
    {
        $notifications = $this->db->get('notification')->result();

        for($i = 0 ; $i < count($notifications) ; $i++)
        {
            $array = array('user_id' => $user_id, 'notification_id' => $notifications[$i]->id);
            $read = $this->db->like($array)->get("notification_read")->result();
            $notifications[$i]->read = (count($read) == 0 ? false : true);
        }

        return $notifications;
    }

    public function read_entry($user_id, $notification_id)
    {
        $this->user_id = $user_id;
        $this->notification_id = $notification_id;
        $items = $this->db->where(array('user_id' => $user_id, 'notification_id' => $notification_id))->get('notification_read')->result();
        if(count($items) == 0)
        {
            $this->db->insert("notification_read", $this);
            $this->id = $this->db->insert_id();
        }
        
        return $this;
    }

}