<?php

class Test_model extends CI_Model
{
    /**
     *  Insert
     *      Insert callback response body to database
     *
     * @param array() $data Array of data
     *                  e.g ['response_body'=>'Message from MPESA']
     *
     * @return int ID of inserted row
     */
    public function insert($data)
    {
        $this->db->insert('callback', $data);
        return $this->db->insert_id();
    }
}
