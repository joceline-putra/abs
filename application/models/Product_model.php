<?php 
/* 
    @Author: Yoceline Witaya 
*/ 
class Product_model extends CI_Model{
    function __construct(){
        parent::__construct();
    }
    
    function set_params($params){
        if ($params) {
            foreach ($params as $k => $v) {
                $this->db->where($k, $v);
            }
        }
    }

    function set_search($search){
        if ($search) {
            $n = 0;
            $this->db->group_start();
            foreach ($search as $key => $val) {
                if ($n == 0) {
                    $this->db->like($key, $val);
                } else {
                    $this->db->or_like($key, $val);
                }
                $n++;
            }
            $this->db->group_end();
        }
    }

    function set_join(){
        $this->db->join('categories','product_category_id=category_id','left');
    }

    function set_join_item(){
        /* $this->db->join('','','left'); */
    }

    function set_select(){
        $this->db->select("*");
    }

    function set_select_item(){
        $this->db->select("*");
    }

    function get_all_product($params = null, $search = null, $limit = null, $start = null, $order = null, $dir = null) {
        $this->set_select();
        $this->set_params($params);
        $this->set_search($search);
        $this->set_join();

        if ($order) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('product_id', "asc");
        }

        // if ($limit) {
            $this->db->limit($limit, $start);            
        // }
        
        return $this->db->get('products')->result_array();
    }  
    function get_all_product_count($params,$search){
        $this->db->from('products');
        $this->set_params($params);
        $this->set_search($search);
        $this->set_join();        
        return $this->db->count_all_results();
    }
    function get_all_product_item($params = null, $search = null, $limit = null, $start = null, $order = null, $dir = null) {
        $this->set_select_item();
        $this->set_params($params);
        $this->set_search($search);
        $this->set_join_item();

        if ($order) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('product_item_id', "asc");
        }

        if ($limit) {
            $this->db->limit($limit, $start);
        }
        
        return $this->db->get('product_items')->result_array();
    }  
    function get_all_product_item_count($params,$search){
        $this->db->from('product_items');
        $this->set_params($params);
        $this->set_search($search);
        return $this->db->count_all_results();
    }    

    /* 
        ================
        CRUD Product
        ================
    */        
    
    /* function to add new product */
    function add_product($params){
        $this->db->insert('products',$params);
        return $this->db->insert_id();
    }
    
    /* function to get product by id */
    function get_product($id){
        $this->set_select();
        $this->set_join();
        return $this->db->get_where('products',array('product_id'=>$id))->row_array();
    }
    function get_product_custom($where){
        $this->set_select();
        $this->set_join();
        return $this->db->get_where('products',$where)->row_array();
    }
    function get_product_custom_result($where){
        $this->set_select();
        $this->set_join();
        return $this->db->get_where('products',$where)->result_array();
    }

    /* function to update product */
    function update_product($id,$params){
        $this->db->where('product_id',$id);
        return $this->db->update('products',$params);
    }
    function update_product_custom($where,$params){
        $this->db->where($where);
        return $this->db->update('products',$params);
    }

    /* function to delete product */
    function delete_product($id){
        return $this->db->delete('products',array('product_id'=>$id));
    }
    function delete_product_custom($where){
        return $this->db->delete('products',$where);
    }

    /* function to check data exists product */
    function check_data_exist($params){
        $this->db->where($params);
        $query = $this->db->get('products');
        if ($query->num_rows() > 0){
            return true;
        }
        else{
            return false;
        }
    }
    /* function to check data exists product of two condition */
    function check_data_exist_two_condition($where_not_in,$where_exist){
        if ($where_not_in) {
            foreach ($where_not_in as $k => $v) {
                $this->db->where($k.' !=', $v);
            }
        }
        if ($where_exist) {
            $n = 0;
            $this->db->group_start();
            foreach($where_exist as $key => $val) {
                if ($n == 0) {
                    $this->db->where($key, $val);
                } else {
                    $this->db->where($key, $val);
                }
                $n++;
            }
            $this->db->group_end();
        }
        $this->db->limit(1,0);
        $query = $this->db->get('products');
        if ($query->num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }
    
    /* 
        ================
        CRUD Product ITEM
        ================
    */
    
    /* function to add new product items */
    function add_product_item($params){
        $this->db->insert('product_items',$params);
        return $this->db->insert_id();
    }
    
    /* function to get product items by id */
    function get_product_item($id){
        $this->set_select_item();
        $this->set_join_item();
        return $this->db->get_where('product_items',array('product_item_id'=>$id))->row_array();
    }
    function get_product_item_custom($where){
        $this->set_select_item();
        $this->set_join_item();
        return $this->db->get_where('product_items',$where)->row_array();
    }
    function get_product_item_custom_result($where){
        $this->set_select_item();
        $this->set_join_item();
        return $this->db->get_where('product_items',$where)->result_array();
    }

    /* function to update product items */
    function update_product_item($id,$params){
        $this->db->where('product_item_id',$id);
        return $this->db->update('product_items',$params);
    }
    function update_product_item_custom($where,$params){
        $this->db->where($where);
        return $this->db->update('product_items',$params);
    }    
    
    /* function to delete product items */
    function delete_product_item($id){
        return $this->db->delete('product_items',array('product_item_id'=>$id));
    }
    function delete_product_item_custom($where){
        return $this->db->delete('product_items',$where);
    }

    /* function to check data exists product_items */
    function check_data_exist_items($params){
        $this->db->where($params);
        $query = $this->db->get('products_items');
        if ($query->num_rows() > 0){
            return true;
        }
        else{
            return false;
        }
    }
    /* function to check data exists product of two condition */
    function check_data_exist_items_two_condition($where_not_in,$where_exist){
        if ($where_not_in) {
            foreach ($where_not_in as $k => $v) {
                $this->db->where($k.' !=', $v);
            }
        }
        if ($where_exist) {
            $n = 0;
            $this->db->group_start();
            foreach($where_exist as $key => $val) {
                if ($n == 0) {
                    $this->db->where($key, $val);
                } else {
                    $this->db->where($key, $val);
                }
                $n++;
            }
            $this->db->group_end();
        }
        $this->db->limit(1,0);
        $query = $this->db->get('products_items');
        if ($query->num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }
}
?>