<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Report_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get($hours)
    {

        $result = [];

        $date_time = new DateTime();
        $date_time->modify('-' . $hours . ' hours');
        $start_time = $date_time->format('Y-m-d H:i:s');

        $this->db->select('id, pubDate, media');
        $this->db->where('pubDate >=', $start_time);
        $items = $this->db->get('items');

        foreach ($items->result() as $item) {
            $this->db->select('categories.text');
            $this->db->from('categories');
            $this->db->join('item_categories', 'item_categories.categoryId = categories.id');
            $this->db->where('item_categories.itemId', $item->id);
            $item->categories = $this->db->get()->result();
            $this->db->select('url, title, excerpt, sourceAlias');
            $this->db->where('itemId', $item->id);
            $item->links = $this->db->get('links')->result();
            $item->sourceCount = count($item->links);
            array_push($result, $item);
        }
        return $result;
    }

}
