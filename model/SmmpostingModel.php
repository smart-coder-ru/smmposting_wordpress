<?php
class SmmpostingModel
{
    private $db;
    private $wpdb;
    public function __construct(){
        $this->setDb();
    }
    public function setDb() {
        $this->db = new SMMP_DB();
        global $wpdb;
        $this->wpdb = $wpdb;
    }
    public function getSetting($option_name) {
        $sql = "SELECT * FROM ". $this->wpdb->base_prefix ."options WHERE option_name = '" . $this->db->escape($option_name) . "'";
        $query = $this->db->query($sql);

        if (!empty($query->row)) {
            if (isset($query->row['option_value'])) {
                return json_decode($query->row['option_value'], true);
            }
        }

        return [];

    }
    public function editSetting($option_name, $data)
    {
        $config = $this->getSetting($option_name);

        if (empty($config)) {
            $sql = "INSERT INTO ". $this->wpdb->base_prefix ."options
								SET 
									option_name = '" . $this->db->escape($option_name) . "',
									autoload = 'yes',
                                    option_value = '" . $this->db->escape(json_encode($data)) . "'
								";
            $query = $this->db->query($sql);
        } else {
            $sql = "UPDATE ". $this->wpdb->base_prefix ."options
								SET 
									option_name = '" . $this->db->escape($option_name) . "',
									autoload = 'yes',
                                    option_value = '" . $this->db->escape(json_encode($data)) . "'
								WHERE
									option_name = '" . $this->db->escape($option_name) . "'
								";
            $query = $this->db->query($sql);
        }



    }
}
