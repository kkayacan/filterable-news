<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Google_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->config->load('news');
        $this->load->database();
        $this->load->helper('simple_html_dom');
    }

    public function fetch()
    {

        set_time_limit(300);
        $result = [];

        $current_time = date('Y-m-d H:i:s z');

        $this->db->select('id, text');
        $categories = $this->db->get('categories')->result();

        $this->db->select('text');
        $this->db->order_by('CHAR_LENGTH(text) DESC');
        $redundant_texts = $this->db->get('redundant')->result();

        foreach ($categories as $category) {
            $items = $this->_fetch_category($category->text);
            $this->_save_category($category->id, $items, $redundant_texts, $current_time);
        }

        $this->db->insert('execution', array('start' => $current_time));

        array_push($result, array('message' => 'done'));
        return $result;
    }

    public function _fetch_category($category_text)
    {
        $link = $this->config->item('url_base') . $category_text . $this->config->item('url_param');
        $xml_string = file_get_contents($link);
        $xml = new SimpleXMLElement($xml_string);
        return $xml->channel->item;
    }

    public function _save_category($category_id, $items, $redundant_texts, $current_time)
    {
        foreach ($items as $item) {

            $pub_date_object = DateTime::createFromFormat(DateTimeInterface::RSS, (string) $item->pubDate);
            $pub_date = $pub_date_object->format('Y-m-d H:i:s z');

            if ($item->children('media', true)) {
                $media = $item->children('media', true)->content->attributes();
            } else {
                $media = '';
            }

            $raw_links = $this->_build_link_list((string) $item->description);
            $links = $this->_truncate_redundant($raw_links, $redundant_texts);

            $dbitem = $this->_find_item_from_url($pub_date, $links);

            if ($dbitem) {
                $this->_update_item($dbitem, $media, $current_time);
                $this->_update_item_category($dbitem->id, $category_id);
                $this->_update_links($links, $dbitem->id, $category_id);
            } else {
                $item_id = $this->_insert_item($pub_date, $media, $current_time);
                $this->_insert_item_category($item_id, $category_id);
                $this->_insert_links($links, $item_id, $category_id);
            }
        }
    }

    public function _build_link_list($description)
    {
        $dom = str_get_html($description);
        $links = $dom->find('li');
        if ($links) {
            return $this->_parse_link_list($links);
        } else {
            return $this->_parse_single_link($dom);
        }
    }

    public function _truncate_redundant($raw_links, $redundant_texts)
    {
        $links = $raw_links;
        foreach ($links as $link) {
            foreach ($redundant_texts as $redundant) {
                $link['title'] = str_replace($redundant->text, '', $link['title']);
            }
        }
        return $links;
    }

    public function _parse_link_list($html_links)
    {
        $links = [];
        foreach ($html_links as $html_link) {
            if (!$html_link->find('strong')) {
                $link = array(
                    'source' => $html_link->find('font', 0)->innertext,
                    'url' => $this->_get_redirected_url($html_link->find('a', 0)->href), //$html_link->find('a', 0)->href,
                    'title' => $html_link->find('a', 0)->innertext,
                    'excerpt' => '');
                array_push($links, $link);
            }
        }
        return $links;
    }

    public function _parse_single_link($html_link)
    {
        $links = [];
        $excerpt = '';
        if ($html_link->find('p', 0)){
            $excerpt = $html_link->find('p', 0)->innertext;
        }
        $link = array(
            'source' => $html_link->find('font', 0)->innertext,
            'url' => $this->_get_redirected_url($html_link->find('a', 0)->href), //$html_link->find('a', 0)->href,
            'title' => $html_link->find('a', 0)->innertext,
            'excerpt' => $excerpt);
        array_push($links, $link);
        return $links;
    }

    public function _get_redirected_url($source_url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $source_url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Must be set to true so that PHP follows any "Location:" header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $a = curl_exec($ch); // $a will contain all headers

        $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL); // This is what you need, it will return you the last effective URL

        // Uncomment to see all headers
        /*
        echo "<pre>";
        print_r($a);echo"<br>";
        echo "</pre>";
        */

        return $url; // Voila
    }

    public function _find_item_from_url($pub_date, $links)
    {
        $this->db->select('id, media');
        $this->db->where('pubDate', $pub_date);
        $dbitems = $this->db->get('items');

        if ($dbitems->num_rows() > 0) {
            foreach ($dbitems->result() as $dbitem) {
                foreach ($links as $link) {
                    $this->db->where('itemId', $dbitem->id);
                    $this->db->where('url', $link['url']);
                    $dblink = $this->db->get('links');
                    if ($dblink->num_rows() > 0) {
                        return $dbitem;
                    }
                }
            }
        }
        return false;
    }

    public function _update_item($dbitem, $media_content, $current_time)
    {
        if ($media_content) {
            $media = $media_content;
        } else {
            $media = $dbitem->media;
        }
        $update_data = array(
            'media' => $media,
            'lastSeen' => $current_time,
        );

        $this->db->where('id', $dbitem->id);
        $this->db->update('items', $update_data);
    }

    public function _update_item_category($item_id, $category_id)
    {
        $this->db->where('itemId', $item_id);
        $this->db->where('categoryId', $category_id);
        $dblink = $this->db->get('item_categories');
        if ($dblink->num_rows() == 0) {
            $this->_insert_item_category($item_id, $category_id);
        }
    }

    public function _update_links($links, $item_id, $category_id)
    {
        foreach ($links as $link) {
            $this->db->where('itemId', $item_id);
            $this->db->where('url', $link['url']);
            $dblink = $this->db->get('links');
            if ($dblink->num_rows() == 0) {
                $insert_data = array(
                    'itemId' => $item_id,
                    'url' => $link['url'],
                    'title' => $link['title'],
                    'excerpt' => $link['excerpt'],
                    'sourceId' => $this->_get_source_id($link['source'], $category_id),
                    'sourceAlias' => $link['source'],
                );
                $this->db->insert('links', $insert_data);
            }
        }
    }

    public function _insert_item($pub_date, $media, $current_time)
    {
        $insert_data = array(
            'pubDate' => $pub_date,
            'media' => $media,
            'firstSeen' => $current_time,
            'lastSeen' => $current_time,
        );
        $this->db->insert('items', $insert_data);
        return $this->db->insert_id();
    }

    public function _insert_item_category($item_id, $category_id)
    {
        $insert_data = array(
            'itemId' => $item_id,
            'categoryId' => $category_id,
        );
        $this->db->insert('item_categories', $insert_data);
    }

    public function _insert_links($links, $item_id, $category_id)
    {
        foreach ($links as $link) {
            $insert_data = array(
                'itemId' => $item_id,
                'url' => $link['url'],
                'title' => $link['title'],
                'excerpt' => $link['excerpt'],
                'sourceId' => $this->_get_source_id($link['source'], $category_id),
                'sourceAlias' => $link['source'],
            );
            $this->db->insert('links', $insert_data);
        }
    }

    public function _get_source_id($alias, $category_id)
    {
        $this->db->select('sourceId');
        $this->db->where('text', $alias);
        $dbalias = $this->db->get('aliases');
        if ($dbalias->num_rows() > 0) {
            $this->_update_source($dbalias->row()->sourceId, $category_id);
            return $dbalias->row()->sourceId;
        } else {
            return $this->_insert_source($alias, $category_id);
        }
    }

    public function _update_source($source_id, $category_id)
    {
        $this->db->where('sourceId', $source_id);
        $this->db->where('categoryId', $category_id);
        $source_category = $this->db->get('source_categories');
        if ($source_category->num_rows() == 0) {
            $insert_data = array(
                'sourceId' => $source_id,
                'categoryId' => $category_id,
            );
            $this->db->insert('source_categories', $insert_data);
        }
    }

    public function _insert_source($alias, $category_id)
    {
        $source_data = array(
            'groupId' => 0,
        );
        $this->db->insert('sources', $source_data);
        $source_id = $this->db->insert_id();

        $alias_data = array(
            'sourceId' => $source_id,
            'text' => $alias,
        );
        $this->db->insert('aliases', $alias_data);

        $source_cat_data = array(
            'sourceId' => $source_id,
            'categoryId' => $category_id,
        );
        $this->db->insert('source_categories', $source_cat_data);

        return $source_id;
    }

}
