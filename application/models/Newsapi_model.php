<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Newsapi_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->config->load('news');
        $this->load->database();
        //$this->load->helper('simple_html_dom');
    }

    public function fetch()
    {   
        $current_time = date('Y-m-d H:i:s z');
        $this->db->select('id, newsapi_cat');
        $categories = $this->db->get('categories')->result();
        foreach ($categories as $category) {
            if ($category->newsapi_cat) {
                $items = $this->_fetch_category($category->newsapi_cat);
                if ($items) {
                    $this->_save_category($items);
                }
            }
        }
        $this->db->insert('execution', array('start' => $current_time));
    }

    public function _fetch_category($category_text)
    {
        if ($this->config->item('newsapi')) {
            $link = $this->config->item('n_url_base') . $category_text . $this->config->item('n_url_param') . $this->config->item('newsapi');
            $context = stream_context_create([
                "http" => [
                    "ignore_errors" => true,
                ],
            ]);
            $result = json_decode(file_get_contents($link, false, $context));
            if ($result) {
                if (property_exists($result, 'articles')) {
                    return $result->articles;
                }
            }
        }
        return false;
    }

    public function _save_category($items)
    {
        foreach ($items as $article) {
            $dblinks = $this->_find_item_from_url($article->url);
            if ($dblinks) {
                foreach ($dblinks as $dblink) {
                    $this->_update_item($dblink->itemId, $article->urlToImage);
                }
            }
        } 
    }

    public function _find_item_from_url($url)
    {
        $this->db->where('url', $url);
        $dblinks = $this->db->get('links');
        if ($dblinks->num_rows() > 0) {
            return $dblinks->result();
        }
        return false;
    }

    public function _update_item($item_id, $url_to_image)
    {
        $update_data = array(
            'media' => $url_to_image,
        );

        $this->db->where('id', $item_id);
        $this->db->update('items', $update_data);
    }

}
