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
            $header = $this->_get_header($item->links);
            $item->title = $header['title'];
            $item->excerpt = $header['excerpt'];
            $item->pubDate = $item->pubDate . ' GMT';
            array_push($result, $item);
        }
        return $result;
    }

    public function _get_header($links)
    {
        $tmp_links = $links;
        if (count($tmp_links) > 1) {
            usort($tmp_links, array($this, '_cmp_excerpt_desc'));
        }
        $header = array(
            'title' => '',
            'excerpt' => $tmp_links[0]->excerpt,
        );
        if (count($tmp_links) > 1) {
            usort($tmp_links, array($this, '_cmp_title_desc'));
        }
        if (strlen($header['excerpt']) > 0){
            $header['title'] = $tmp_links[0]->title;
        } else {
            if(count($tmp_links) > 1) {
                $header['excerpt'] = $tmp_links[0]->title;
                $header['title'] = $tmp_links[1]->title;
            } else {
                $header['excerpt'] = '';
                $header['title'] = $tmp_links[0]->title;
            }
        }
        return $header;
    }

    public function _cmp_title_desc($a, $b)
    {
        if (strlen($a->title) === strlen($b->title)) {
            return 0;
        } elseif (strlen($b->title) > strlen($a->title)) {
            return 1;
        } else {
            return -1;
        }
    }

    public function _cmp_excerpt_desc($a, $b)
    {
        if (strlen($a->excerpt) === strlen($b->excerpt)) {
            return 0;
        } elseif (strlen($b->excerpt) > strlen($a->excerpt)) {
            return 1;
        } else {
            return -1;
        }
    }

}
