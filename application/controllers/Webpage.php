<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Webpage extends MY_Controller{

    public $folder_upload = 'upload/news/';
    public $allowed_types = 'jpg|png|jpeg|mp4';
    public $image_width   = 250;
    public $image_height  = 250;
    public $allowed_file_size     = 5000; // 5 MB -> 5000 KB
    
    function __construct(){
        parent::__construct();
        if(!$this->is_logged_in()){
            redirect(base_url("login"));
        }
        /*
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->model('User_model');
        $this->load->model('Webpage_model');
        */
        $this->load->model('Branch_model');
        $this->load->model('User_model');
        $this->load->model('News_model');
        $this->load->model('Kategori_model');  
        $this->load->model('Faq_model');                

        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->folder_upload = 'upload/branch/';
        $this->allowed_types = 'jpg|png|jpeg|mp4';
        $this->image_width   = 480;
        $this->image_height  = 120;
        $this->allowed_file_size     = 1024; // 5 MB -> 5000 KB        
    }
    function index(){
        if ($this->input->post()) {    
            $return = new \stdClass();
            $return->status = 0;
            $return->message = '';
            $return->result = '';

            $upload_directory = $this->folder_upload;     
            $upload_path_directory = $upload_directory;

            $data['session'] = $this->session->userdata();  
            $session_user_id = !empty($data['session']['user_data']['user_id']) ? $data['session']['user_data']['user_id'] : null;

            $post = $this->input->post();
            $get  = $this->input->get();
            $action = !empty($this->input->post('action')) ? $this->input->post('action') : false;
            
            switch($action){
                case "load":
                    $columns = array(
                        '0' => 'name_id',
                        '1' => 'name_name'
                    );

                    $limit     = !empty($post['length']) ? $post['length'] : 10;
                    $start     = !empty($post['start']) ? $post['start'] : 0;
                    $order     = !empty($post['order']) ? $columns[$post['order'][0]['column']] : $columns[0];
                    $dir       = !empty($post['order'][0]['dir']) ? $post['order'][0]['dir'] : "asc";
                    
                    $search    = [];
                    if(!empty($post['search']['value'])) {
                        $s = $post['search']['value'];
                        foreach ($columns as $v) {
                            $search[$v] = $s;
                        }
                    }

                    $params = array();
                    
                    /* If Form Mode Transaction CRUD not Master CRUD
                    !empty($post['date_start']) ? $params['name_date >'] = date('Y-m-d H:i:s', strtotime($post['date_start'].' 23:59:59')) : $params;
                    !empty($post['date_end']) ? $params['name_date <'] = date('Y-m-d H:i:s', strtotime($post['date_end'].' 23:59:59')) : $params;
                    */

                    //Default Params for Master CRUD Form
                    $params['name_id']   = !empty($post['name_id']) ? $post['name_id'] : $params;
                    $params['name_name'] = !empty($post['name_name']) ? $post['name_name'] : $params;

                    /*
                        if($post['other_column'] && $post['other_column'] > 0) {
                            $params['other_column'] = $post['other_column'];
                        }
                        if($post['filter_type'] !== "All") {
                            $params['name_type'] = $post['filter_type'];
                        }
                    */
                    
                    $get_count = $this->Webpage_model->get_all_name_count($params, $search);
                    if($get_count > 0){
                        $get_data = $this->Webpage_model->get_all_name($params, $search, $limit, $start, $order, $dir);
                        $return->total_records   = $get_count;
                        $return->status          = 1; 
                        $return->result          = $get_data;
                    }else{
                        $return->total_records   = 0;
                        $return->result          = [];
                    }
                    $return->message             = 'Load '.$return->total_records.' data';
                    $return->recordsTotal        = $return->total_records;
                    $return->recordsFiltered     = $return->total_records;
                    break;
                case "create_update":
                    $this->form_validation->set_rules('name_id', 'name_id', 'required');
                    $this->form_validation->set_message('required', '{field} wajib diisi');
                    if ($this->form_validation->run() == FALSE){
                        $return->message = validation_errors();
                    }else{

                        if(intval($post['name_id']) > 0){ /* Update if Exist */ // if( (!empty($post['name_session'])) && (strlen($post['name_session']) > 10) ){ /* Update if Exist */

                            /* Check Existing Data */
                            $where_not = [
                                'name_id' => intval($post['name_id']),
                            ];
                            $where_new = [
                                'name_name' => $name_name
                            ];
                            $check_exists = $this->Webpage_model->check_data_exist_two_condition($where_not,$where_new);

                            /* Continue Update if not exist */
                            if(!$check_exists){
                                $where = array(
                                    'name_id' => intval($post['name_id']),
                                );
                                $params = array(
                                    'name_name' => $name_name,
                                    'name_flag' => !empty($post['name_flag']) ? $post['name_flag'] : 0
                                );
                                $update = $this->Webpage_model->update_name_custom($where,$params);
                                if($update){
                                    $get_name = $this->Webpage_model->get_name_custom($where);
                                    $return->status  = 1;
                                    $return->message = 'Berhasil memperbarui '.$name_name;
                                    $return->result= array(
                                        'name_id' => $update,
                                        'name_name' => $get_name['name_name'],
                                        'name_session' => $get_name['name_session']
                                    );
                                }else{
                                    $return->message = 'Gagal memperbarui '.$name_name;
                                }
                            }else{
                                $return->message = 'Data sudah digunakan';
                            }
                        }else{ /* Save New Data */

                            /* Check Existing Data */
                            $params_check = [
                                'name_name' => $name_name
                            ];
                            $check_exists = $this->Webpage_model->check_data_exist($params_check);

                            /* Continue Save if not exist */
                            if(!$check_exists){
                                $name_session = strtoupper(substr(hash('sha256', date_timestamp_get(date_create())),0,20));
                                $params = array(
                                    'name_session' => $name_session,
                                    'name_name' => $name_name,
                                    'name_flag' => !empty($post['name_flag']) ? $post['name_flag'] : 0
                                );
                                $create = $this->Webpage_model->add_name($params);
                                if($create){
                                    $get_name = $this->Webpage_model->get_name($create);
                                    $return->status  = 1;
                                    $return->message = 'Berhasil menambahkan '.$name_name;
                                    $return->result= array(
                                        'name_id' => $create,
                                        'name_name' => $get_name['name_name'],
                                        'name_session' => $get_name['name_session']
                                    );
                                }else{
                                    $return->message = 'Gagal menambahkan '.$name_name;
                                }
                            }else{
                                $return->message = 'Data sudah ada';
                            }
                        }
                    }
                    break;
                case "create":
                    // $data = base64_decode($post); $data = json_decode($post, TRUE);
                    $this->form_validation->set_rules('name_name', 'name_name', 'required');
                    $this->form_validation->set_message('required', '{field} wajib diisi');
                    if ($this->form_validation->run() == FALSE){
                        $return->message = validation_errors();
                    }else{

                        $name_name = !empty($post['name_name']) ? $post['name_name'] : null;
                        $name_flag = !empty($post['name_flag']) ? $post['name_flag'] : 0;
                        $name_session = strtoupper(substr(hash('sha256', date_timestamp_get(date_create())),0,20));

                        $params = array(
                            'name_name' => $name_name,
                            'name_flag' => $name_flag
                        );

                        //Check Data Exist
                        $params_check = array(
                            'name_name' => $name_name
                        );
                        $check_exists = $this->Webpage_model->check_data_exist($params_check);
                        if(!$check_exists){

                            $set_data=$this->Webpage_model->add_name($params);
                            if($set_data){

                                $name_id = $set_data;
                                $data = $this->Webpage_model->get_name($name_id);

                                // Image Save Upload
                                $post_files = !empty($_FILES) ? $_FILES['files'] : "";
                                if(!empty($post_files)){
                                    //Save Image if Exist
                                    $config['image_library'] = 'gd2';
                                    $config['upload_path'] = $upload_path_directory;
                                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                                    $this->upload->initialize($config);
                                    if ($this->upload->do_upload('files')) {
                                        $upload = $this->upload->data();
                                        $raw_photo = time() . $upload['file_ext'];
                                        $old_name = $upload['full_path'];
                                        $new_name = $upload_path_directory . $raw_photo;
                                        if (rename($old_name, $new_name)) {
                                            $compress['image_library'] = 'gd2';
                                            $compress['source_image'] = $upload_path_directory . $raw_photo;
                                            $compress['create_thumb'] = FALSE;
                                            $compress['maintain_ratio'] = TRUE;
                                            $compress['width'] = $this->image_width;
                                            $compress['height'] = $this->image_height;
                                            $compress['new_image'] = $upload_path_directory . $raw_photo;
                                            $this->load->library('image_lib', $compress);
                                            $this->image_lib->resize();

                                            if ($data && $data['name_id']) {
                                                $params_image = array(
                                                    'name_image' => $upload_directory . $raw_photo
                                                );
                                                if (!empty($data['name_image'])) {
                                                    if (file_exists($upload_path_directory . $data['name_image'])) {
                                                        unlink($upload_path_directory . $data['name_image']);
                                                    }
                                                }
                                                $stat = $this->Webpage_model->update_name_custom(array('name_id' => $set_data), $params_image);
                                            }
                                        }
                                    }
                                }
                                //End of Save Image

                                //Croppie Upload Image
                                $post_upload = !empty($this->input->post('upload1')) ? $this->input->post('upload1') : "";
                                if(!empty($post_upload)){
                                    $upload_process = $this->file_upload_image($this->folder_upload,$post_upload);
                                    if($upload_process->status == 1){
                                        if ($data && $data['name_id']) {
                                            $params_image = array(
                                                'name_url' => $upload_process->result['file_location']
                                            );
                                            if (!empty($data['name_url'])) {
                                                if (file_exists($upload_path_directory . $data['name_url'])) {
                                                    unlink($upload_path_directory . $data['name_url']);
                                                }
                                            }
                                            $stat = $this->Webpage_model->update_name_custom(array('name_id' => $set_data), $params_image);
                                        }
                                    }else{
                                        $return->message = 'Fungsi Gambar gagal';
                                    }
                                }
                                //End of Croppie

                                $return->status=1;
                                $return->message='Berhasil menambahkan '.$post['name_name'];
                                $return->result= array(
                                    'id' => $set_data,
                                    'name' => $post['name_name'],
                                    'session' => $name_session
                                ); 
                            }else{
                                $return->message='Gagal menambahkan '.$post['name_name'];
                            }
                        }else{
                            $return->message='Data sudah ada';
                        }
                    }
                    break;
                case "read":
                    $this->form_validation->set_rules('name_id', 'name_id', 'required');
                    if ($this->form_validation->run() == FALSE){
                        $return->message = validation_errors();
                    }else{                
                        $name_id   = !empty($post['name_id']) ? $post['name_id'] : 0;
                        if(intval(strlen($name_id)) > 0){        
                            $datas = $this->Webpage_model->get_name($name_id);
                            if($datas){
                                $return->status=1;
                                $return->message='Berhasil mendapatkan data';
                                $return->result=$datas;
                            }else{
                                $return->message = 'Data tidak ditemukan';
                            }
                        }else{
                            $return->message='Data tidak ada';
                        }
                    }
                    break;
                case "update":
                    $this->form_validation->set_rules('name_id', 'name_id', 'required');
                    $this->form_validation->set_message('required', '{field} tidak ditemukan');
                    if ($this->form_validation->run() == FALSE){
                        $return->message = validation_errors();
                    }else{
                        $name_id = !empty($post['name_id']) ? $post['name_id'] : $post['name_id'];
                        $name_name = !empty($post['name_name']) ? $post['name_name'] : $post['name_name'];
                        $name_flag = !empty($post['name_flag']) ? $post['name_flag'] : $post['name_flag'];

                        if(strlen($name_id) > 1){
                            $params = array(
                                'name_name' => $name_name,
                                'name_date_updated' => date("YmdHis"),
                                'name_flag' => $name_flag
                            );

                            /*
                            if(!empty($data['password'])){
                                $params['password'] = md5($data['password']);
                            }
                            */
                           
                            $set_update=$this->Webpage_model->update_name($name_id,$params);
                            if($set_update){
                                
                                $get_data = $this->Webpage_model->get_name($name_id);
                                    
                                //Update Image if Exist
                                $post_files = !empty($_FILES) ? $_FILES['files'] : "";
                                if(!empty($post_files)){
                                    $config['image_library'] = 'gd2';
                                    $config['upload_path'] = $upload_path_directory;
                                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                                    $this->upload->initialize($config);
                                    if ($this->upload->do_upload('files')) {
                                        $upload = $this->upload->data();
                                        $raw_photo = time() . $upload['file_ext'];
                                        $old_name = $upload['full_path'];
                                        $new_name = $upload_path_directory . $raw_photo;
                                        if (rename($old_name, $new_name)) {
                                            $compress['image_library'] = 'gd2';
                                            $compress['source_image'] = $upload_path_directory . $raw_photo;
                                            $compress['create_thumb'] = FALSE;
                                            $compress['maintain_ratio'] = TRUE;
                                            $compress['width'] = $this->image_width;
                                            $compress['height'] = $this->image_height;
                                            $compress['new_image'] = $upload_path_directory . $raw_photo;
                                            $this->load->library('image_lib', $compress);
                                            $this->image_lib->resize();
                                            if ($get_data) {
                                                $params_image = array(
                                                    'name_image' => base_url($upload_directory) . $raw_photo
                                                );
                                                if (!empty($get_data['name_image'])) {
                                                    $file = FCPATH.$this->folder_upload.$get_data['name_image'];
                                                    if (file_exists($file)) {
                                                        unlink($file);
                                                    }
                                                }
                                                $stat = $this->Webpage_model->update_name_custom(array('name_id' => $name_id), $params_image);
                                            }
                                        }
                                    }
                                }
                                //End of Save Image

                                $return->status  = 1;
                                $return->message = 'Berhasil memperbarui '.$name_name;
                            }else{
                                $return->message='Gagal memperbarui '.$name_name;
                            }   
                        }else{
                            $return->message = "Gagal memperbarui";
                        } 
                    }
                    break;
                case "delete":
                    $this->form_validation->set_rules('name_id', 'name_id', 'required');
                    if ($this->form_validation->run() == FALSE){
                        $return->message = validation_errors();
                    }else{
                        $name_id   = !empty($post['name_id']) ? $post['name_id'] : 0;
                        $name_name = !empty($post['name_name']) ? $post['name_name'] : null;

                        if(strlen($name_id) > 0){
                            $get_data=$this->Webpage_model->get_name($name_id);
                            // $set_data=$this->Webpage_model->delete_name($name_id);
                            $set_data = $this->Webpage_model->update_name_custom(array('name_id'=>$name_id),array('name_flag'=>4));                
                            if($set_data){
                                /*
                                $file = FCPATH.$this->folder_upload.$get_data['name_image'];
                                if (file_exists($file)) {
                                    unlink($file);
                                }
                                */
                                $return->status=1;
                                $return->message='Berhasil menghapus '.$name_name;
                            }else{
                                $return->message='Gagal menghapus '.$name_name;
                            } 
                        }else{
                            $return->message='Data tidak ditemukan';
                        }
                    }
                    break;
                case "update_flag":
                    $this->form_validation->set_rules('name_id', 'name_id', 'required');
                    $this->form_validation->set_rules('name_flag', 'name_flag', 'required');
                    $this->form_validation->set_message('required', '{field} wajib diisi');
                    if($this->form_validation->run() == FALSE){
                        $return->message = validation_errors();
                    }else{
                        $name_id = !empty($post['name_id']) ? $post['name_id'] : 0;
                        if(strlen(intval($name_id)) > 1){
                            
                            $params = array(
                                'name_flag' => !empty($post['name_flag']) ? intval($post['name_flag']) : 0,
                            );
                            
                            $where = array(
                                'name_id' => !empty($post['name_id']) ? intval($post['name_id']) : 0,
                            );
                            
                            if($post['name_flag']== 0){
                                $set_msg = 'nonaktifkan';
                            }else if($post['name_flag']== 1){
                                $set_msg = 'mengaktifkan';
                            }else if($post['name_flag']== 4){
                                $set_msg = 'menghapus';
                            }else{
                                $set_msg = 'mendapatkan data';
                            }

                            if($post['name_flag'] == 4){
                                $params['name_url'] = null;
                            }

                            $get_data = $this->Webpage_model->get_name_custom($where);
                            if($get_data){
                                $set_update=$this->Webpage_model->update_name_custom($where,$params);
                                if($set_update){
                                    if($post['name_flag'] == 4){
                                        /*
                                        $file = FCPATH.$this->folder_upload.$get_data['name_image'];
                                        if (file_exists($file)) {
                                            unlink($file);
                                        }
                                        */
                                    }
                                    $return->status  = 1;
                                    $return->message = 'Berhasil '.$set_msg.' '.$get_data['name_name'];
                                }else{
                                    $return->message='Gagal '.$set_msg;
                                }
                            }else{
                                $return->message='Gagal mendapatkan data';
                            }   
                        }else{
                            $return->message = 'Tidak ada data';
                        } 
                    }
                    break;
                case "create_name_item":
                    // $data = base64_decode($post);
                    // $data = json_decode($post, TRUE);

                    $this->form_validation->set_rules('name_item_name', 'name_item_name', 'required');
                    $this->form_validation->set_message('required', '{field} wajib diisi');
                    if ($this->form_validation->run() == FALSE){
                        $return->message = validation_errors();
                    }else{

                        $name_item_name = !empty($post['name_item_name']) ? $post['name_item_name'] : null;
                        $name_item_flag = !empty($post['name_item_flag']) ? $post['name_item_flag'] : 0;
                        $name_item_session = strtoupper(substr(hash('sha256', date_timestamp_get(date_create())),0,20));

                        $params = array(
                            'name_item_name' => $name_item_name,
                            'name_item_flag' => $name_item_flag
                        );

                        //Check Data Exist
                        $params_check = array(
                            'name_item_name' => $name_item_name
                        );
                        $check_exists = $this->Webpage_model->check_data_exist_name_item($params_check);
                        if(!$check_exists){

                            $set_data=$this->Webpage_model->add_name_item($params);
                            if($set_data){

                                $name_item_id = $set_data;
                                $data = $this->Webpage_model->get_name_item($name_item_id);
                                $return->status=1;
                                $return->message='Berhasil menambahkan '.$post['name_item_name'];
                                $return->result= array(
                                    'id' => $set_data,
                                    'name' => $post['name_item_name'],
                                    'session' => $name_item_session
                                ); 
                            }else{
                                $return->message='Gagal menambahkan '.$post['name_item_name'];
                            }
                        }else{
                            $return->message='Data sudah ada';
                        }
                    }
                    break;
                case "read_name_item":
                    $this->form_validation->set_rules('name_item_id', 'name_item_id', 'required');
                    if ($this->form_validation->run() == FALSE){
                        $return->message = validation_errors();
                    }else{                
                        $name_item_id   = !empty($post['name_item_id']) ? $post['name_item_id'] : 0;
                        if(intval(strlen($name_item_id)) > 0){        
                            $datas = $this->Webpage_model->get_name_item($name_item_id);
                            if($datas){
                                $return->status=1;
                                $return->message='Berhasil mendapatkan data';
                                $return->result=$datas;
                            }else{
                                $return->message = 'Data tidak ditemukan';
                            }
                        }else{
                            $return->message='Data tidak ada';
                        }
                    }
                    break;
                case "update_name_item":
                    $this->form_validation->set_rules('name_item_id', 'name_item_id', 'required');
                    $this->form_validation->set_message('required', '{field} tidak ditemukan');
                    if ($this->form_validation->run() == FALSE){
                        $return->message = validation_errors();
                    }else{
                        $name_item_id = !empty($post['name_item_id']) ? $post['name_item_id'] : $post['name_item_id'];
                        $name_item_name = !empty($post['name_item_name']) ? $post['name_item_name'] : $post['name_item_name'];
                        $name_item_flag = !empty($post['name_item_flag']) ? $post['name_item_flag'] : $post['name_item_flag'];

                        if(strlen($name_item_id) > 0){
                            $params = array(
                                'name_item_name' => $name_item_name,
                                'name_item_date_updated' => date("YmdHis"),
                                'name_item_flag' => $name_item_flag
                            );
                           
                            $set_update=$this->Webpage_model->update_name_item($name_item_id,$params);
                            if($set_update){
                                $get_data = $this->Webpage_model->get_name_item($name_item_id);
                                $return->status  = 1;
                                $return->message = 'Berhasil memperbarui '.$name_item_name;
                            }else{
                                $return->message='Gagal memperbarui '.$name_item_name;
                            }   
                        }else{
                            $return->message = "Gagal memperbarui";
                        } 
                    }
                    break;
                case "update_name_item_flag":
                    $this->form_validation->set_rules('name_item_id', 'name_item_id', 'required');
                    $this->form_validation->set_rules('name_item_flag', 'name_item_flag', 'required');
                    $this->form_validation->set_message('required', '{field} wajib diisi');
                    if($this->form_validation->run() == FALSE){
                        $return->message = validation_errors();
                    }else{
                        $name_item_id = !empty($post['name_item_id']) ? $post['name_item_id'] : 0;
                        if(strlen(intval($name_item_id)) > 0){
                            
                            $params = array(
                                'name_item_flag' => !empty($post['name_item_flag']) ? intval($post['name_item_flag']) : 0,
                            );
                            
                            $where = array(
                                'name_item_id' => !empty($post['name_item_id']) ? intval($post['name_item_id']) : 0,
                            );
                            
                            if($post['name_item_flag']== 0){
                                $set_msg = 'nonaktifkan';
                            }else if($post['name_item_flag']== 1){
                                $set_msg = 'mengaktifkan';
                            }else if($post['name_item_flag']== 4){
                                $set_msg = 'menghapus';
                            }else{
                                $set_msg = 'mendapatkan data';
                            }

                            $set_update=$this->Webpage_model->update_name_item_custom($where,$params);
                            if($set_update){
                                $get_data = $this->Webpage_model->get_name_item_custom($where);
                                $return->status  = 1;
                                $return->message = 'Berhasil '.$set_msg.' '.$get_data['name_item_name'];
                            }else{
                                $return->message='Gagal '.$set_msg;
                            }   
                        }else{
                            $return->message = 'Gagal mendapatkan data';
                        } 
                    }
                    break;
                case "delete_name_item":
                    $this->form_validation->set_rules('name_item_id', 'name_item_id', 'required');
                    if ($this->form_validation->run() == FALSE){
                        $return->message = validation_errors();
                    }else{
                        $name_item_id   = !empty($post['name_item_id']) ? $post['name_item_id'] : 0;
                        $name_item_name = !empty($post['name_item_name']) ? $post['name_item_name'] : null;                                

                        if(strlen($name_item_id) > 0){
                            $get_data=$this->Webpage_model->get_name_item($name_item_id);
                            // $set_data=$this->Webpage_model->delete_name_item($name_item_id);
                            $set_data = $this->Webpage_model->update_name_item_custom(array('name_item_id'=>$name_item_id),array('name_item_flag'=>4));                
                            if($set_data){
                                /*
                                if (file_exists($get_data['name_item_image'])) {
                                    unlink($get_data['name_item_image']);
                                } 
                                */
                                $return->status=1;
                                $return->message='Berhasil menghapus '.$name_item_name;
                            }else{
                                $return->message='Gagal menghapus '.$name_item_name;
                            } 
                        }else{
                            $return->message='Data tidak ditemukan';
                        }
                    }
                    break;
                case "load_name_item":
                    $columns = array(
                        '0' => 'name_item_id',
                        '1' => 'name_item_name'
                    );

                    $limit     = !empty($post['length']) ? $post['length'] : 10;
                    $start     = !empty($post['start']) ? $post['start'] : 0;
                    $order     = !empty($post['order']) ? $columns[$post['order'][0]['column']] : $columns[0];
                    $dir       = !empty($post['order'][0]['dir']) ? $post['order'][0]['dir'] : "asc";
                    
                    $search    = [];
                    if(!empty($post['search']['value'])) {
                        $s = $post['search']['value'];
                        foreach ($columns as $v) {
                            $search[$v] = $s;
                        }
                    }

                    $params = array();

                    //Default Params for Master CRUD Form
                    $params['name_item_id']   = !empty($post['name_item_id']) ? $post['name_item_id'] : $params;
                    $params['name_item_name'] = !empty($post['name_item_name']) ? $post['name_item_name'] : $params;

                    /*
                    if($post['other_item_column'] && $post['other_item_column'] > 0) {
                        $params['other_item_column'] = $post['other_item_column'];
                    }
                    */
                    
                    $get_count = $this->Webpage_model->get_all_name_item_count($params, $search);
                    if($get_count > 0){
                        $get_data = $this->Webpage_model->get_all_name_item($params, $search, $limit, $start, $order, $dir);
                        $return->total_records   = $get_count;
                        $return->status          = 1; 
                        $return->result          = $get_data;
                    }else{
                        $return->total_records   = 0;
                        $return->result          = [];
                    }
                    $return->message             = 'Load '.$return->total_records.' data';
                    $return->recordsTotal        = $return->total_records;
                    $return->recordsFiltered     = $return->total_records;
                    break;
                case "load_name_item_2":
                    $params = array(); $total  = 0;
                    $this->form_validation->set_rules('name_item_name_id', 'name_item_name_id', 'required');
                    if ($this->form_validation->run() == FALSE){
                        $return->message = validation_errors();
                    }else{
                        $name_item_name_id   = !empty($post['name_item_name_id']) ? $post['name_item_name_id'] : 0;
                        if(intval(strlen($name_item_name_id)) > 0){
                            $params = array(
                                'name_item_name_id' => $name_item_name_id
                            );
                            $search = null;
                            $start  = null;
                            $limit  = null;
                            $order  = "name_item_id";
                            $dir    = "asc";
                            $get_data = $this->Webpage_model->get_all_name_item($params, $search, $limit, $start, $order, $dir);
                            if($get_data){
                                $total = count($get_data);
                                $return->status=1;
                                $return->message='Berhasil mendapatkan data';
                                $return->result=$get_data;
                            }else{
                                $return->message = 'Data tidak ditemukan';
                            }
                        }else{
                            $return->message='Data tidak ada';
                        }
                    }
                    $return->params          =$params;
                    $return->total_records   = $total;
                    $return->recordsTotal    = $total;
                    $return->recordsFiltered = $total;
                    break;
                default:
                    $return->message='No Action';
                    break; 
            }
            echo json_encode($return);
        }else{
            // Default First Date & End Date of Current Month
            $firstdate = new DateTime('first day of this month');
            $firstdateofmonth = $firstdate->format('d-m-Y');

            $data['session'] = $this->session->userdata();  
            $session_user_id = !empty($data['session']['user_data']['user_id']) ? $data['session']['user_data']['user_id'] : null;

            $data['first_date'] = $firstdateofmonth;
            $data['end_date'] = date("d-m-Y");
            $data['hour'] = date("H:i");
            $data['theme'] = $this->User_model->get_user($data['session']['user_data']['user_id']);

            $data['image_width'] = intval($this->image_width);
            $data['image_height'] = intval($this->image_height);
            /*
            // Reference Model
            $this->load->model('Reference_model');
            $data['reference'] = $this->Reference_model->get_all_reference();
            */

            $data['title'] = 'Webpage';
            $data['_view'] = 'layouts/admin/menu/folder/name';
            $this->load->view('layouts/admin/index',$data);
            $this->load->view('layouts/admin/menu/folder/name_js.php',$data);
        }
    }
    function menu(){
        $data['session'] = $this->session->userdata();     
        $data['theme'] = $this->User_model->get_user($data['session']['user_data']['user_id']);

        $data['allowed_file_type'] = $this->allowed_types;
        $data['allowed_file_size'] = $this->allowed_file_size;

            $data['title'] = 'Halaman';
            $data['_view'] = 'layouts/admin/menu/webpage/menu';
            $file_js = 'layouts/admin/menu/webpage/menu_js.php';
        
        $data['image_width'] = intval($this->image_width);
        $data['image_height'] = intval($this->image_height);

        //Date First of the month
        $firstdate = new DateTime('first day of this month');
        $firstdateofmonth = $firstdate->format('Y-m-d');

        //Date Now
        $datenow =date("Y-m-d"); 
        $data['first_date'] = $firstdateofmonth;
        $data['end_date'] = $datenow;
        
        $this->load->view('layouts/admin/index',$data);
        $this->load->view($file_js,$data);
    }
    function contact(){
        if ($this->input->post()) {    

            $return = new \stdClass();
            $return->status = 0;
            $return->message = '';
            $return->result = '';

            $post = $this->input->post();
            $action = !empty($this->input->post('action')) ? $this->input->post('action') : false;
            
            switch($action){
                case "read":
                    $return->result = $this->Branch_model->get_branch($post['id']);
                    $return->status = 1;
                    $return->message = 'Mengambil data';                
                    break;    
                case "update":
                    $set_json = [
                        'header_title' => !empty($post['header_title']) ? $post['header_title'] : null, 
                        'working_hour' => !empty($post['working_hour']) ? $post['working_hour'] : null,
                        'social_media' => [
                            ['name' => 'facebook', 'link' => !empty($post['facebook']) ? $post['facebook'] : null],
                            ['name' => 'instagram', 'link' => !empty($post['instagram']) ? $post['instagram'] : null],
                            ['name' => 'youtube', 'link' => !empty($post['youtube']) ? $post['youtube'] : null],
                            ['name' => 'twitter', 'link' => !empty($post['twitter']) ? $post['twitter'] : null],
                            ['name' => 'tiktok', 'link' => !empty($post['tiktok']) ? $post['tiktok'] : null],
                        ],
                        'map' => [
                            'longitude' => !empty($post['longitude']) ? $post['longitude'] : null,
                            'latitude' => !empty($post['latitude']) ? $post['latitude'] : null,
                            'link' => !empty($post['link']) ? $post['link'] : null
                        ]                                                                                                                        
                    ];
                    $params = [
                        'branch_name' => $post['nama'],
                        'branch_phone_1' => $post['telepon_1'],
                        'branch_email_1' => $post['email_1'],
                        'branch_phone_2' => $post['telepon_2'],
                        'branch_email_2' => $post['email_2'],                        
                        'branch_address' => $post['alamat'],
                        'branch_province_id' => $post['provinsi'],
                        'branch_city_id' => $post['kota'],
                        // 'branch_village_id' => $post['kecamatan'],
                        'branch_note' => json_encode($set_json)                        
                    ];
                    // var_dump($params);die;
                    $get_data = $this->Branch_model->get_branch($post['id']);
                    
                    //Croppie Upload Image
                    $post_upload = !empty($this->input->post('upload1')) ? $this->input->post('upload1') : "";
                    if(strlen($post_upload) > 10){
                        $upload_process = $this->file_upload_image($this->folder_upload,$post_upload);
                        if($upload_process->status == 1){
                            $params['branch_logo'] = $upload_process->result['file_location'];
                            $params['branch_logo_sidebar'] = $upload_process->result['file_location']; 

                            if($get_data['branch_logo'] !== 'upload/branch/default_logo.png'){
                                if (!empty($get_data['branch_logo'])) {
                                    if (file_exists(FCPATH . $get_data['branch_logo'])) {
                                        unlink(FCPATH . $get_data['branch_logo']);
                                    }
                                }
                            }
                        }else{
                            $return->message = 'Fungsi Gambar gagal';
                        }
                    }
                    //End of Croppie          
                                        
                    $this->Branch_model->update_branch($post['id'],$params);
                    $return->status = 1;
                    $return->message = 'Berhasil memperbarui';
                    break;
            }
            $return->action = $action;
            echo json_encode($return);
        }else{
            $return = new \stdClass();
            $return->status = 0;
            $return->message = '';
            $return->result = '';        
            $data['session'] = $this->session->userdata();     
            $data['theme'] = $this->User_model->get_user($data['session']['user_data']['user_id']);

            $data['allowed_file_type'] = $this->allowed_types;
            $data['allowed_file_size'] = $this->allowed_file_size;

                $data['title'] = 'Kontak';
                $data['_view'] = 'layouts/admin/menu/webpage/web_contact';
                $file_js = 'layouts/admin/menu/webpage/web_contact_js.php';
            
            $data['image_width'] = intval($this->image_width);
            $data['image_height'] = intval($this->image_height);

            //Date First of the month
            $firstdate = new DateTime('first day of this month');
            $firstdateofmonth = $firstdate->format('Y-m-d');

            //Date Now
            $datenow =date("Y-m-d"); 
            $data['first_date'] = $firstdateofmonth;
            $data['end_date'] = $datenow;
            
            $this->load->view('layouts/admin/index',$data);
            $this->load->view($file_js,$data);
        }
    }    
    function faq(){
        if ($this->input->post()) {    
            $return = new \stdClass();
            $return->status = 0;
            $return->message = '';
            $return->result = '';

            $upload_directory = $this->folder_upload;     
            $upload_path_directory = $upload_directory;

            $data['session'] = $this->session->userdata();  
            $session_user_id = !empty($data['session']['user_data']['user_id']) ? $data['session']['user_data']['user_id'] : null;

            $post = $this->input->post();
            $get  = $this->input->get();
            $action = !empty($this->input->post('action')) ? $this->input->post('action') : false;
            
            switch($action){
                case "load":
                    $columns = array(
                        '0' => 'faq_id',
                        '1' => 'faq_question',
                        '2' => 'faq_answer'
                    );

                    $limit     = !empty($post['length']) ? $post['length'] : 10;
                    $start     = !empty($post['start']) ? $post['start'] : 0;
                    $order     = !empty($post['order']) ? $columns[$post['order'][0]['column']] : $columns[0];
                    $dir       = !empty($post['order'][0]['dir']) ? $post['order'][0]['dir'] : "asc";
                    
                    $search    = [];
                    if(!empty($post['search']['value'])) {
                        $s = $post['search']['value'];
                        foreach ($columns as $v) {
                            $search[$v] = $s;
                        }
                    }

                    $params = array();
                    
                    /* If Form Mode Transaction CRUD not Master CRUD
                    !empty($post['date_start']) ? $params['faq_date >'] = date('Y-m-d H:i:s', strtotime($post['date_start'].' 23:59:59')) : $params;
                    !empty($post['date_end']) ? $params['faq_date <'] = date('Y-m-d H:i:s', strtotime($post['date_end'].' 23:59:59')) : $params;
                    */

                    //Default Params for Master CRUD Form
                    // $params['faq_id']   = !empty($post['faq_id']) ? $post['faq_id'] : $params;
                    // $params['faq_name'] = !empty($post['faq_name']) ? $post['faq_name'] : $params;

                    /*
                        if($post['other_column'] && $post['other_column'] > 0) {
                            $params['other_column'] = $post['other_column'];
                        }
                        if($post['filter_type'] !== "All") {
                            $params['faq_type'] = $post['filter_type'];
                        }
                    */
                    
                    $get_count = $this->Faq_model->get_all_faq_count($params, $search);
                    if($get_count > 0){
                        $get_data = $this->Faq_model->get_all_faq($params, $search, $limit, $start, $order, $dir);
                        $return->total_records   = $get_count;
                        $return->status          = 1; 
                        $return->result          = $get_data;
                    }else{
                        $return->total_records   = 0;
                        $return->result          = [];
                    }
                    $return->message             = 'Load '.$return->total_records.' data';
                    $return->recordsTotal        = $return->total_records;
                    $return->recordsFiltered     = $return->total_records;
                    break;
                case "create_update":
                    $this->form_validation->set_rules('faq_question', 'faq_question', 'required');
                    $this->form_validation->set_rules('faq_answer', 'faq_answer', 'required');
                    $this->form_validation->set_message('required', '{field} wajib diisi');
                    if ($this->form_validation->run() == FALSE){
                        $return->message = validation_errors();
                    }else{

                        if(intval($post['faq_id']) > 0){ /* Update if Exist */ // if( (!empty($post['faq_session'])) && (strlen($post['faq_session']) > 10) ){ /* Update if Exist */

                            /* Check Existing Data */
                            $where_not = [
                                'faq_id' => intval($post['faq_id']),
                            ];
                            $where_new = [
                                'faq_question' => $post['faq_question']
                            ];
                            $check_exists = $this->Faq_model->check_data_exist_two_condition($where_not,$where_new);

                            /* Continue Update if not exist */
                            if(!$check_exists){
                                $where = array(
                                    'faq_id' => intval($post['faq_id']),
                                );
                                $params = array(
                                    'faq_question' => $post['faq_question'],
                                    'faq_answer' => $post['faq_answer'],                                    
                                    'faq_flag' => !empty($post['faq_flag']) ? $post['faq_flag'] : 0
                                );
                                $update = $this->Faq_model->update_faq_custom($where,$params);
                                if($update){
                                    $get_faq = $this->Faq_model->get_faq_custom($where);
                                    $return->status  = 1;
                                    $return->message = 'Berhasil memperbarui '.$post['faq_question'];
                                    $return->result= array(
                                        'faq_id' => $update,
                                        'faq_name' => $get_faq['faq_question']
                                    );
                                }else{
                                    $return->message = 'Gagal memperbarui ';
                                }
                            }else{
                                $return->message = 'Data sudah digunakan';
                            }
                        }else{ /* Save New Data */

                            /* Check Existing Data */
                            $params_check = [
                                'faq_question' => $post['faq_question']
                            ];
                            $check_exists = $this->Faq_model->check_data_exist($params_check);

                            /* Continue Save if not exist */
                            if(!$check_exists){
                                // $faq_session = strtoupper(substr(hash('sha256', date_timestamp_get(date_create())),0,20));
                                $params = array(
                                    'faq_question' => $post['faq_question'],
                                    'faq_answer' => $post['faq_answer'],                                    
                                    'faq_flag' => !empty($post['faq_flag']) ? $post['faq_flag'] : 0
                                );
                                $create = $this->Faq_model->add_faq($params);
                                if($create){
                                    $get_faq = $this->Faq_model->get_faq($create);
                                    $return->status  = 1;
                                    $return->message = 'Berhasil menambahkan '.$post['faq_question'];
                                    $return->result= array(
                                        'faq_id' => $create,
                                        'faq_name' => $get_faq['faq_question']
                                    );
                                }else{
                                    $return->message = 'Gagal menambahkan';
                                }
                            }else{
                                $return->message = 'Data sudah ada';
                            }
                        }
                    }
                    break;
                case "read":
                    $this->form_validation->set_rules('faq_id', 'faq_id', 'required');
                    if ($this->form_validation->run() == FALSE){
                        $return->message = validation_errors();
                    }else{                
                        $faq_id   = !empty($post['faq_id']) ? $post['faq_id'] : 0;
                        if(intval(strlen($faq_id)) > 0){        
                            $datas = $this->Faq_model->get_faq($faq_id);
                            if($datas){
                                $return->status=1;
                                $return->message='Berhasil mendapatkan data';
                                $return->result=$datas;
                            }else{
                                $return->message = 'Data tidak ditemukan';
                            }
                        }else{
                            $return->message='Data tidak ada';
                        }
                    }
                    break;
                case "delete":
                    $this->form_validation->set_rules('faq_id', 'faq_id', 'required');
                    if ($this->form_validation->run() == FALSE){
                        $return->message = validation_errors();
                    }else{
                        $faq_id   = !empty($post['faq_id']) ? $post['faq_id'] : 0;
                        $faq_name = !empty($post['faq_name']) ? $post['faq_name'] : null;

                        if(strlen($faq_id) > 0){
                            $get_data=$this->Faq_model->get_faq($faq_id);
                            // $set_data=$this->Faq_model->delete_faq($faq_id);
                            $set_data = $this->Faq_model->update_faq_custom(array('faq_id'=>$faq_id),array('faq_flag'=>4));                
                            if($set_data){
                                /*
                                $file = FCPATH.$this->folder_upload.$get_data['faq_image'];
                                if (file_exists($file)) {
                                    unlink($file);
                                }
                                */
                                $return->status=1;
                                $return->message='Berhasil menghapus '.$faq_name;
                            }else{
                                $return->message='Gagal menghapus '.$faq_name;
                            } 
                        }else{
                            $return->message='Data tidak ditemukan';
                        }
                    }
                    break;
                case "update_flag":
                    $this->form_validation->set_rules('faq_id', 'faq_id', 'required');
                    $this->form_validation->set_rules('faq_flag', 'faq_flag', 'required');
                    $this->form_validation->set_message('required', '{field} wajib diisi');
                    if($this->form_validation->run() == FALSE){
                        $return->message = validation_errors();
                    }else{
                        $faq_id = !empty($post['faq_id']) ? $post['faq_id'] : 0;
                        if(intval($faq_id) > 0){
                            
                            $params = array(
                                'faq_flag' => !empty($post['faq_flag']) ? intval($post['faq_flag']) : 0,
                            );
                            
                            $where = array(
                                'faq_id' => !empty($post['faq_id']) ? intval($post['faq_id']) : 0,
                            );
                            
                            if($post['faq_flag']== 0){
                                $set_msg = 'nonaktifkan';
                            }else if($post['faq_flag']== 1){
                                $set_msg = 'mengaktifkan';
                            }else if($post['faq_flag']== 4){
                                $set_msg = 'menghapus';
                            }else{
                                $set_msg = 'mendapatkan data';
                            }

                            $get_data = $this->Faq_model->get_faq_custom($where);
                            if($get_data){
                                $set_update=$this->Faq_model->update_faq_custom($where,$params);
                                if($set_update){
                                    if($post['faq_flag'] == 4){
                                        /*
                                        $file = FCPATH.$this->folder_upload.$get_data['faq_image'];
                                        if (file_exists($file)) {
                                            unlink($file);
                                        }
                                        */
                                    }
                                    $return->status  = 1;
                                    $return->message = 'Berhasil '.$set_msg;
                                }else{
                                    $return->message='Gagal '.$set_msg;
                                }
                            }else{
                                $return->message='Gagal mendapatkan data';
                            }   
                        }else{
                            $return->message = 'Tidak ada data';
                        } 
                    }
                    break;
                default:
                    $return->message='No Action';
                    break; 
            }
            echo json_encode($return);
        }else{
            // Default First Date & End Date of Current Month
            $firstdate = new DateTime('first day of this month');
            $firstdateofmonth = $firstdate->format('d-m-Y');

            $data['session'] = $this->session->userdata();  
            $session_user_id = !empty($data['session']['user_data']['user_id']) ? $data['session']['user_data']['user_id'] : null;

            $data['first_date'] = $firstdateofmonth;
            $data['end_date'] = date("d-m-Y");
            $data['hour'] = date("H:i");
            $data['theme'] = $this->User_model->get_user($data['session']['user_data']['user_id']);

            $data['image_width'] = intval($this->image_width);
            $data['image_height'] = intval($this->image_height);
            /*
            // Reference Model
            $this->load->model('Reference_model');
            $data['reference'] = $this->Reference_model->get_all_reference();
            */

            $data['title'] = 'Faq';
            $data['_view'] = 'layouts/admin/menu/webpage/faq';
            $this->load->view('layouts/admin/index',$data);
            $this->load->view('layouts/admin/menu/webpage/faq_js.php',$data);
        }
    }    
    function appointment(){
        $this->load->model('Order_model');          
        if ($this->input->post()) {    
            $return = new \stdClass();
            $return->status = 0;
            $return->message = '';
            $return->result = '';

            $upload_directory = $this->folder_upload;     
            $upload_path_directory = $upload_directory;

            $data['session'] = $this->session->userdata();  
            $session_user_id = !empty($data['session']['user_data']['user_id']) ? $data['session']['user_data']['user_id'] : null;

            $post = $this->input->post();
            $get  = $this->input->get();
            $action = !empty($this->input->post('action')) ? $this->input->post('action') : false;
            
            switch($action){
                case "load":
                    $columns = array(
                        '0' => 'order_item_start_date',
                        '1' => 'order_number',
                        '2' => 'order_contact_name',
                        '3' => 'order_contact_phone',
                        '4' => 'product_name',
                        '5' => 'order_total'
                    );

                    $limit     = !empty($post['length']) ? $post['length'] : 10;
                    $start     = !empty($post['start']) ? $post['start'] : 0;
                    $order     = !empty($post['order']) ? $columns[$post['order'][0]['column']] : $columns[0];
                    $dir       = !empty($post['order'][0]['dir']) ? $post['order'][0]['dir'] : "asc";
                    
                    $search    = [];
                    if(!empty($post['search']['value'])) {
                        $s = $post['search']['value'];
                        foreach ($columns as $v) {
                            $search[$v] = $s;
                        }
                    }

                    $params = array();
                    $params['order_type'] = 333;
                    /* If Form Mode Transaction CRUD not Master CRUD */
                    !empty($post['date_start']) ? $params['order_item_start_date >'] = date('Y-m-d H:i:s', strtotime($post['date_start'].' 00:00:00')) : $params;
                    !empty($post['date_end']) ? $params['order_item_start_date <'] = date('Y-m-d H:i:s', strtotime($post['date_end'].' 23:59:59')) : $params;

                    //Default Params for Master CRUD Form
                    // $params['faq_id']   = !empty($post['faq_id']) ? $post['faq_id'] : $params;
                    // $params['faq_name'] = !empty($post['faq_name']) ? $post['faq_name'] : $params;

                    /*
                        if($post['other_column'] && $post['other_column'] > 0) {
                            $params['other_column'] = $post['other_column'];
                        }
                    */
                    if($post['filter_flag'] !== "All") {
                        $params['order_flag'] = $post['filter_flag'];
                    }

                    if($post['filter_paid_flag'] !== "All") {
                        $params['order_paid'] = $post['filter_paid_flag'];
                    }                    

                    $get_count = $this->Order_model->get_all_orders_and_orders_items_count($params, $search);
                    if($get_count > 0){
                        $get_data = $this->Order_model->get_all_orders_and_orders_items($params, $search, $limit, $start, $order, $dir);
                        $return->total_records   = $get_count;
                        $return->status          = 1; 
                        $return->result          = $get_data;
                    }else{
                        $return->total_records   = 0;
                        $return->result          = [];
                    }
                    $return->params = $params;
                    $return->message             = 'Load '.$return->total_records.' data';
                    $return->recordsTotal        = $return->total_records;
                    $return->recordsFiltered     = $return->total_records;
                    break;
                case "create_update": die;
                    $this->form_validation->set_rules('faq_question', 'faq_question', 'required');
                    $this->form_validation->set_rules('faq_answer', 'faq_answer', 'required');
                    $this->form_validation->set_message('required', '{field} wajib diisi');
                    if ($this->form_validation->run() == FALSE){
                        $return->message = validation_errors();
                    }else{

                        if(intval($post['faq_id']) > 0){ /* Update if Exist */ // if( (!empty($post['faq_session'])) && (strlen($post['faq_session']) > 10) ){ /* Update if Exist */

                            /* Check Existing Data */
                            $where_not = [
                                'faq_id' => intval($post['faq_id']),
                            ];
                            $where_new = [
                                'faq_question' => $post['faq_question']
                            ];
                            $check_exists = $this->Faq_model->check_data_exist_two_condition($where_not,$where_new);

                            /* Continue Update if not exist */
                            if(!$check_exists){
                                $where = array(
                                    'faq_id' => intval($post['faq_id']),
                                );
                                $params = array(
                                    'faq_question' => $post['faq_question'],
                                    'faq_answer' => $post['faq_answer'],                                    
                                    'faq_flag' => !empty($post['faq_flag']) ? $post['faq_flag'] : 0
                                );
                                $update = $this->Faq_model->update_faq_custom($where,$params);
                                if($update){
                                    $get_faq = $this->Faq_model->get_faq_custom($where);
                                    $return->status  = 1;
                                    $return->message = 'Berhasil memperbarui '.$post['faq_question'];
                                    $return->result= array(
                                        'faq_id' => $update,
                                        'faq_name' => $get_faq['faq_question']
                                    );
                                }else{
                                    $return->message = 'Gagal memperbarui ';
                                }
                            }else{
                                $return->message = 'Data sudah digunakan';
                            }
                        }else{ /* Save New Data */

                            /* Check Existing Data */
                            $params_check = [
                                'faq_question' => $post['faq_question']
                            ];
                            $check_exists = $this->Faq_model->check_data_exist($params_check);

                            /* Continue Save if not exist */
                            if(!$check_exists){
                                // $faq_session = strtoupper(substr(hash('sha256', date_timestamp_get(date_create())),0,20));
                                $params = array(
                                    'faq_question' => $post['faq_question'],
                                    'faq_answer' => $post['faq_answer'],                                    
                                    'faq_flag' => !empty($post['faq_flag']) ? $post['faq_flag'] : 0
                                );
                                $create = $this->Faq_model->add_faq($params);
                                if($create){
                                    $get_faq = $this->Faq_model->get_faq($create);
                                    $return->status  = 1;
                                    $return->message = 'Berhasil menambahkan '.$post['faq_question'];
                                    $return->result= array(
                                        'faq_id' => $create,
                                        'faq_name' => $get_faq['faq_question']
                                    );
                                }else{
                                    $return->message = 'Gagal menambahkan';
                                }
                            }else{
                                $return->message = 'Data sudah ada';
                            }
                        }
                    }
                    break;
                case "read":
                    $this->form_validation->set_rules('order_id', 'order_id', 'required');
                    if ($this->form_validation->run() == FALSE){
                        $return->message = validation_errors();
                    }else{                
                        $order_id   = !empty($post['order_id']) ? $post['order_id'] : 0;
                        if(intval(strlen($order_id)) > 0){        
                            $datas = $this->Order_model->get_order_item_custom(['order_item_order_id'=>$order_id]);
                            if($datas){
                                $return->status=1;
                                $return->message='Berhasil mendapatkan data';
                                $return->result=$datas;
                            }else{
                                $return->message = 'Data tidak ditemukan';
                            }
                        }else{
                            $return->message='Data tidak ada';
                        }
                    }
                    break;
                case "delete":
                    $this->form_validation->set_rules('order_id', 'order_id', 'required');
                    if ($this->form_validation->run() == FALSE){
                        $return->message = validation_errors();
                    }else{
                        $order_id   = !empty($post['order_id']) ? $post['order_id'] : 0;
                        $order_number = !empty($post['order_number']) ? $post['order_number'] : null;

                        if(strlen($faq_id) > 0){
                            $get_data=$this->Order_model->get_order($order_id);
                            // $set_data=$this->Order_model->delete_order($order_id);
                            $set_data = $this->Order_model->update_order_custom(array('order_id'=>$order_id),array('order_flag'=>4));                
                            if($set_data){
                                /*
                                $file = FCPATH.$this->folder_upload.$get_data['order_image'];
                                if (file_exists($file)) {
                                    unlink($file);
                                }
                                */
                                $return->status=1;
                                $return->message='Berhasil menghapus '.$order_name;
                            }else{
                                $return->message='Gagal menghapus '.$order_name;
                            } 
                        }else{
                            $return->message='Data tidak ditemukan';
                        }
                    }
                    break;
                case "update_flag":
                    $this->form_validation->set_rules('order_id', 'order_id', 'required');
                    $this->form_validation->set_rules('order_flag', 'order_flag', 'required');
                    $this->form_validation->set_message('required', '{field} wajib diisi');
                    if($this->form_validation->run() == FALSE){
                        $return->message = validation_errors();
                    }else{
                        $order_id = !empty($post['order_id']) ? $post['order_id'] : 0;
                        if(intval($order_id) > 0){
                            
                            $params = array(
                                'order_flag' => !empty($post['order_flag']) ? intval($post['order_flag']) : 0,
                            );
                            
                            $where = array(
                                'order_id' => !empty($post['order_id']) ? intval($post['order_id']) : 0,
                            );
                            
                            $get_data = $this->Order_model->get_order_custom($where);
                            if($get_data){
                                $set_update=$this->Order_model->update_order_custom($where,$params);
                                if($set_update){
                                    if($post['order_flag'] == 4){
                                        /*
                                        $file = FCPATH.$this->folder_upload.$get_data['order_image'];
                                        if (file_exists($file)) {
                                            unlink($file);
                                        }
                                        */
                                    }
                                    $return->status  = 1;
                                    $return->message = 'Berhasil';
                                }else{
                                    $return->message='Gagal';
                                }
                            }else{
                                $return->message='Gagal mendapatkan data';
                            }   
                        }else{
                            $return->message = 'Tidak ada data';
                        } 
                    }
                    break;
                case "update_paid_flag":
                    $this->form_validation->set_rules('order_id', 'order_id', 'required');
                    $this->form_validation->set_rules('order_paid_flag', 'order_paid_flag', 'required');
                    $this->form_validation->set_message('required', '{field} wajib diisi');
                    if($this->form_validation->run() == FALSE){
                        $return->message = validation_errors();
                    }else{
                        $order_id = !empty($post['order_id']) ? $post['order_id'] : 0;
                        if(intval($order_id) > 0){
                            
                            $params = array(
                                'order_paid' => !empty($post['order_paid_flag']) ? intval($post['order_paid_flag']) : 0,
                            );
                            
                            $where = array(
                                'order_id' => !empty($post['order_id']) ? intval($post['order_id']) : 0,
                            );
                            
                            $get_data = $this->Order_model->get_order_custom($where);
                            if($get_data){
                                
                                if($post['order_paid_flag'] == 1){
                                    $params['order_total_paid'] = $get_data['order_total'];
                                }else{
                                    $params['order_total_paid'] = 0;
                                }

                                $set_update=$this->Order_model->update_order_custom($where,$params);
                                if($set_update){
                                    // if($post['order_paid'] == 4){
                                        /*
                                        $file = FCPATH.$this->folder_upload.$get_data['order_image'];
                                        if (file_exists($file)) {
                                            unlink($file);
                                        }
                                        */
                                    // }
                                    $return->status  = 1;
                                    $return->message = 'Berhasil';
                                }else{
                                    $return->message='Gagal';
                                }
                            }else{
                                $return->message='Gagal mendapatkan data';
                            }   
                        }else{
                            $return->message = 'Tidak ada data';
                        } 
                    }
                    break;
                    default:
                    $return->message='No Action';
                    break; 
            }
            echo json_encode($return);
        }else{
            // Default First Date & End Date of Current Month
            $firstdate = new DateTime('first day of this month');
            $firstdateofmonth = $firstdate->format('d-m-Y');

            $data['session'] = $this->session->userdata();  
            $session_user_id = !empty($data['session']['user_data']['user_id']) ? $data['session']['user_data']['user_id'] : null;

            $data['first_date'] = $firstdateofmonth;
            $data['end_date'] = date("d-m-Y");
            $data['hour'] = date("H:i");
            $data['theme'] = $this->User_model->get_user($data['session']['user_data']['user_id']);

            $data['image_width'] = intval($this->image_width);
            $data['image_height'] = intval($this->image_height);

            $data['title'] = 'Appointment';
            $data['_view'] = 'layouts/admin/menu/webpage/appointment';
            $this->load->view('layouts/admin/index',$data);
            $this->load->view('layouts/admin/menu/webpage/appointment_js.php',$data);
        }
    }   
    function user(){
        if ($this->input->post()) {    
            $return = new \stdClass();
            $return->status = 0;
            $return->message = '';
            $return->result = '';

            $upload_directory = $this->folder_upload;     
            $upload_path_directory = $upload_directory;

            $data['session'] = $this->session->userdata();  
            $session_user_id = !empty($data['session']['user_data']['user_id']) ? $data['session']['user_data']['user_id'] : null;

            $post = $this->input->post();
            $get  = $this->input->get();
            $action = !empty($this->input->post('action')) ? $this->input->post('action') : false;
            
            switch($action){
                case "load":
                    $columns = array(
                        '0' => 'user_username',
                        '1' => 'user_phone_1',
                        '3' => 'user_date_last_login'
                    );

                    $limit     = !empty($post['length']) ? $post['length'] : 10;
                    $start     = !empty($post['start']) ? $post['start'] : 0;
                    $order     = !empty($post['order']) ? $columns[$post['order'][0]['column']] : $columns[0];
                    $dir       = !empty($post['order'][0]['dir']) ? $post['order'][0]['dir'] : "asc";
                    
                    $search    = [];
                    if(!empty($post['search']['value'])) {
                        $s = $post['search']['value'];
                        foreach ($columns as $v) {
                            $search[$v] = $s;
                        }
                    }

                    $params = array();
                    
                    /* If Form Mode Transaction CRUD not Master CRUD
                    !empty($post['date_start']) ? $params['user_date >'] = date('Y-m-d H:i:s', strtotime($post['date_start'].' 23:59:59')) : $params;
                    !empty($post['date_end']) ? $params['user_date <'] = date('Y-m-d H:i:s', strtotime($post['date_end'].' 23:59:59')) : $params;
                    */

                    //Default Params for Master CRUD Form
                    // $params['user_id']   = !empty($post['user_id']) ? $post['user_id'] : $params;
                    // $params['user_name'] = !empty($post['user_name']) ? $post['user_name'] : $params;

                        if($post['filter_group'] && $post['filter_group'] > 0) {
                            $params['user_user_group_id'] = $post['filter_group'];
                        }
                        // if($post['filter_type'] !== "All") {
                        //     $params['user_type'] = $post['filter_type'];
                        // }
                    
                    $get_count = $this->User_model->get_all_users_count($params, $search);
                    if($get_count > 0){
                        $get_data = $this->User_model->get_all_users($params, $search, $limit, $start, $order, $dir);
                        $return->total_records   = $get_count;
                        $return->status          = 1; 
                        $return->result          = $get_data;
                    }else{
                        $return->total_records   = 0;
                        $return->result          = [];
                    }
                    $return->message             = 'Load '.$return->total_records.' data';
                    $return->recordsTotal        = $return->total_records;
                    $return->recordsFiltered     = $return->total_records;
                    break;
                case "create_update":
                    $this->form_validation->set_rules('user_user_group_id', 'Group', 'required');
                    $this->form_validation->set_rules('user_username', 'Username', 'required');
                    $this->form_validation->set_message('required', '{field} wajib diisi');
                    if ($this->form_validation->run() == FALSE){
                        $return->message = validation_errors();
                    }else{
                        $user_name = strtolower($post['user_username']);
                        if( (!empty($post['user_id'])) && (intval($post['user_id']) > 0) ){ /* Update if Exist */

                            /* Check Existing Data */
                            $where_not = [
                                'user_id' => intval($post['user_id']),
                            ];
                            $where_new = [
                                'user_username' => $post['user_username']
                            ];
                            $check_exists = $this->User_model->check_data_exist_two_condition($where_not,$where_new);

                            /* Continue Update if not exist */
                            if(!$check_exists){
                                $where = array(
                                    'user_id' => intval($post['user_id']),
                                );
                                $params = array(
                                    'user_branch_id' => 1,
                                    'user_user_group_id' => !empty($post['user_user_group_id']) ? intval($post['user_user_group_id']) : null,
                                    'user_username' => !empty($post['user_username']) ? strtolower($post['user_username']) : null,
                                    'user_fullname' => !empty($post['user_username']) ? strtolower($post['user_username']) : null,
                                    'user_phone_1' => !empty($post['user_phone_1']) ? intval($post['user_phone_1']) : null,
                                    'user_email_1' => !empty($post['user_email_1']) ? $post['user_email_1'] : null,
                                    'user_theme' => !empty($post['user_theme']) ? $post['user_theme'] : null,
                                    'user_date_updated' => date("YmdHis"),
                                    'user_flag' => !empty($post['user_flag']) ? intval($post['user_flag']) : null,
                                    'user_activation' => 1,
                                    'user_date_activation' => date("YmdHis"),
                                    'user_menu_style' => 0
                                );

                                if(!empty($post['user_password'])){
                                    $params['user_password'] = md5($post['user_password']);
                                }

                                $update = $this->User_model->update_user_custom($where,$params);
                                if($update){
                                    $get_user = $this->User_model->get_user_custom($where);
                                    $return->status  = 1;
                                    $return->message = 'Berhasil memperbarui '.$user_name;
                                    $return->result= array(
                                        'user_id' => $update,
                                        'user_name' => $get_user['user_username'],
                                        'user_session' => $get_user['user_session']
                                    );
                                }else{
                                    $return->message = 'Gagal memperbarui '.$user_name;
                                }
                            }else{
                                $return->message = 'Data sudah digunakan';
                            }
                        }else{ /* Save New Data */

                            /* Check Existing Data */
                            $params_check = [
                                'user_username' => $user_name
                            ];
                            $check_exists = $this->User_model->check_data_exist($params_check);

                            /* Continue Save if not exist */
                            if(!$check_exists){
                                $user_session = strtoupper(substr(hash('sha256', date_timestamp_get(date_create())),0,20));
                                $params = array(
                                    'user_branch_id' => 1,
                                    'user_user_group_id' => !empty($post['user_user_group_id']) ? intval($post['user_user_group_id']) : null,
                                    'user_username' => !empty($post['user_username']) ? strtolower($post['user_username']) : null,
                                    'user_fullname' => !empty($post['user_username']) ? strtolower($post['user_username']) : null,
                                    'user_phone_1' => !empty($post['user_phone_1']) ? intval($post['user_phone_1']) : null,
                                    'user_email_1' => !empty($post['user_email_1']) ? $post['user_email_1'] : null,
                                    'user_password' => !empty($post['user_password']) ? md5($post['user_password']) : md5(strtolower($post['user_username'])),
                                    'user_theme' => !empty($post['user_theme']) ? $post['user_theme'] : null,
                                    'user_date_created' => date("YmdHis"),
                                    'user_flag' => !empty($post['user_flag']) ? intval($post['user_flag']) : null,
                                    'user_activation' => 1,
                                    'user_date_activation' => date("YmdHis"),
                                    'user_session' => $user_session,
                                    'user_menu_style' => 0
                                );
                                $create = $this->User_model->add_user($params);
                                if($create){
                                    $get_user = $this->User_model->get_user($create);
                                    $return->status  = 1;
                                    $return->message = 'Berhasil menambahkan '.$user_name;
                                    $return->result= array(
                                        'user_id' => $create,
                                        'user_name' => $get_user['user_name'],
                                        'user_session' => $get_user['user_session']
                                    );
                                }else{
                                    $return->message = 'Gagal menambahkan '.$user_name;
                                }
                            }else{
                                $return->message = 'Data sudah ada';
                            }
                        }
                    }
                    break;
                case "read":
                    $this->form_validation->set_rules('user_id', 'user_id', 'required');
                    if ($this->form_validation->run() == FALSE){
                        $return->message = validation_errors();
                    }else{                
                        $user_id   = !empty($post['user_id']) ? $post['user_id'] : 0;
                        if(intval(strlen($user_id)) > 0){        
                            $datas = $this->User_model->get_user($user_id);
                            if($datas){
                                $return->status=1;
                                $return->message='Berhasil mendapatkan data';
                                $return->result=$datas;
                            }else{
                                $return->message = 'Data tidak ditemukan';
                            }
                        }else{
                            $return->message='Data tidak ada';
                        }
                    }
                    break;
                case "delete": die;
                    $this->form_validation->set_rules('user_id', 'user_id', 'required');
                    if ($this->form_validation->run() == FALSE){
                        $return->message = validation_errors();
                    }else{
                        $user_id   = !empty($post['user_id']) ? $post['user_id'] : 0;
                        $user_name = !empty($post['user_name']) ? $post['user_name'] : null;

                        if(strlen($user_id) > 0){
                            $get_data=$this->User_model->get_user($user_id);
                            // $set_data=$this->User_model->delete_user($user_id);
                            $set_data = $this->User_model->update_user_custom(array('user_id'=>$user_id),array('user_flag'=>4));                
                            if($set_data){
                                /*
                                $file = FCPATH.$this->folder_upload.$get_data['user_image'];
                                if (file_exists($file)) {
                                    unlink($file);
                                }
                                */
                                $return->status=1;
                                $return->message='Berhasil menghapus '.$user_name;
                            }else{
                                $return->message='Gagal menghapus '.$user_name;
                            } 
                        }else{
                            $return->message='Data tidak ditemukan';
                        }
                    }
                    break;
                case "update_flag":
                    $this->form_validation->set_rules('user_id', 'user_id', 'required');
                    $this->form_validation->set_rules('user_flag', 'user_flag', 'required');
                    $this->form_validation->set_message('required', '{field} wajib diisi');
                    if($this->form_validation->run() == FALSE){
                        $return->message = validation_errors();
                    }else{
                        $user_id = !empty($post['user_id']) ? $post['user_id'] : 0;
                        if(intval($user_id) > 0){
                            
                            $params = array(
                                'user_flag' => !empty($post['user_flag']) ? intval($post['user_flag']) : 0,
                            );
                            
                            $where = array(
                                'user_id' => !empty($post['user_id']) ? intval($post['user_id']) : 0,
                            );
                            
                            if($post['user_flag']== 0){
                                $set_msg = 'nonaktifkan';
                            }else if($post['user_flag']== 1){
                                $set_msg = 'mengaktifkan';
                            }else if($post['user_flag']== 4){
                                $set_msg = 'menghapus';
                            }else{
                                $set_msg = 'mendapatkan data';
                            }

                            if($post['user_flag'] == 4){
                                $params['user_url'] = null;
                            }

                            $get_data = $this->User_model->get_user_custom($where);
                            if($get_data){
                                $set_update=$this->User_model->update_user_custom($where,$params);
                                if($set_update){
                                    $return->status  = 1;
                                    $return->message = 'Berhasil '.$set_msg.' '.$get_data['user_username'];
                                }else{
                                    $return->message='Gagal '.$set_msg;
                                }
                            }else{
                                $return->message='Gagal mendapatkan data';
                            }   
                        }else{
                            $return->message = 'Tidak ada data';
                        } 
                    }
                    break;
                default:
                    $return->message='No Action';
                    break; 
            }
            echo json_encode($return);
        }else{
            // Default First Date & End Date of Current Month
            $firstdate = new DateTime('first day of this month');
            $firstdateofmonth = $firstdate->format('d-m-Y');

            $data['session'] = $this->session->userdata();  
            $session_user_id = !empty($data['session']['user_data']['user_id']) ? $data['session']['user_data']['user_id'] : null;

            $data['first_date'] = $firstdateofmonth;
            $data['end_date'] = date("d-m-Y");
            $data['hour'] = date("H:i");
            $data['theme'] = $this->User_model->get_user($data['session']['user_data']['user_id']);

            $data['image_width'] = intval($this->image_width);
            $data['image_height'] = intval($this->image_height);
            
            $data['title'] = 'User';
            $data['_view'] = 'layouts/admin/menu/webpage/user';
            $this->load->view('layouts/admin/index',$data);
            $this->load->view('layouts/admin/menu/webpage/user_js.php',$data);
        }
    }      
    function product(){
        $this->load->model("Product_model");
        $this->load->model("File_model");
        
        $product_route = 'produk';
        $folder_upload_product    = 'upload/product/'; // 6          
        $allowed_types = 'jpg|png|jpeg|mp4';
        $allowed_file_size = 1024; // 5 MB -> 5000 KB
        $image_width = 480;
        $image_height = 480;
    
        $session = $this->session->userdata();
        $session_branch_id = $session['user_data']['branch']['id'];
        $session_user_id = $session['user_data']['user_id'];

        if ($this->input->post()) {    
            $return = new \stdClass();
            $return->status = 0;
            $return->message = '';
            $return->result = '';

            $upload_directory = $this->folder_upload;     
            $upload_path_directory = $upload_directory;

            $post = $this->input->post();
            $get  = $this->input->get();
            $action = !empty($this->input->post('action')) ? $this->input->post('action') : false;
            
            switch($action){
                case "load":
                    $columns = array(
                        '0' => 'product_id',
                        '1' => 'product_name',
                        '2' => 'category_name'
                    );

                    $limit     = !empty($post['length']) ? $post['length'] : 10;
                    $start     = !empty($post['start']) ? $post['start'] : 0;
                    $order     = !empty($post['order']) ? $columns[$post['order'][0]['column']] : $columns[0];
                    $dir       = !empty($post['order'][0]['dir']) ? $post['order'][0]['dir'] : "asc";
                    
                    $search    = [];
                    if(!empty($post['search']['value'])) {
                        $s = $post['search']['value'];
                        foreach ($columns as $v) {
                            $search[$v] = $s;
                        }
                    }

                    $params = array();
                    
                    /* If Form Mode Transaction CRUD not Master CRUD
                    !empty($post['date_start']) ? $params['product_date >'] = date('Y-m-d H:i:s', strtotime($post['date_start'].' 23:59:59')) : $params;
                    !empty($post['date_end']) ? $params['product_date <'] = date('Y-m-d H:i:s', strtotime($post['date_end'].' 23:59:59')) : $params;
                    */

                    //Default Params for Master CRUD Form
                    // $params['product_id']   = !empty($post['product_id']) ? $post['product_id'] : $params;
                    // $params['product_name'] = !empty($post['product_name']) ? $post['product_name'] : $params;

                    /*
                        if($post['other_column'] && $post['other_column'] > 0) {
                            $params['other_column'] = $post['other_column'];
                        }
                        if($post['filter_type'] !== "All") {
                            $params['product_type'] = $post['filter_type'];
                        }
                    */
                    
                    $get_count = $this->Product_model->get_all_product_count($params, $search);
                    if($get_count > 0){
                        $get_data = $this->Product_model->get_all_product($params, $search, $limit, $start, $order, $dir);
                        $dd = [];
                        foreach($get_data as $v){
                            $dd[] = array(
                                'product_id' => intval($v['product_id']),
                                'product_branch_id' => intval($v['product_branch_id']),
                                'product_category_id' => intval($v['product_category_id']),
                                'product_ref_id' => intval($v['product_ref_id']),
                                'product_type' => intval($v['product_type']),
                                'product_barcode' => $v['product_barcode'],
                                'product_code' => intval($v['product_code']),
                                'product_name' => $v['product_name'],
                                'product_unit' => $v['product_unit'],
                                'product_note' => $v['product_note'],
                                'product_price_buy' => intval($v['product_price_buy']),
                                'product_price_sell' => intval($v['product_price_sell']),
                                'product_fee_1' => intval($v['product_fee_1']),
                                'product_fee_2' => intval($v['product_fee_2']),
                                'product_image' => $v['product_image'],
                                'product_url' => $v['product_url'],
                                'product_user_id' => intval($v['product_user_id']),
                                'product_date_created' => $v['product_date_created'],
                                'product_date_updated' => $v['product_date_updated'],
                                'product_flag' => intval($v['product_flag']),
                                'product_stock' => floatval($v['product_stock']),
                                'product_visitor' => intval($v['product_visitor']),
                                'product_contact_id' => $v['product_contact_id'],
                                'product_price_promo' => intval($v['product_price_promo']),
                                'product_dimension_size' => $v['product_dimension_size'],
                                'product_reminder' => $v['product_reminder'],
                                'product_reminder_date' => $v['product_reminder_date'],
                                'category_id' => $v['category_id'],
                                'category_name' => $v['category_name'],
                                'category_url' => $v['category_url']
                            );
                        }
                        $return->total_records   = $get_count;
                        $return->status          = 1; 
                        $return->result          = $dd;
                    }else{
                        $return->total_records   = 0;
                        $return->result          = [];
                    }
                    $return->message             = 'Load '.$return->total_records.' data';
                    $return->recordsTotal        = $return->total_records;
                    $return->recordsFiltered     = $return->total_records;
                    break;
                case "create_update":
                    $next = true;
                    // die;
                    // Cek if any Files
                    // if(!empty($_FILES['files'])){
                    //     $files_count = count($_FILES['files']['name']);
                    //     if($files_count > 0){
                    //         for($i=0; $i<$files_count; $i++){
                    //             if(intval($_FILES['files']['size'][$i] / 1024) > ($this->allowed_file_size)){
                    //                 $next = false;
                    //                 $set_msg = 'Gagal, ukuran melebihi '.($this->allowed_file_size / 1024).' Mb'; $next = false;
                    //                 $return->message = $set_msg;
                    //                 echo json_encode($return); die;
                    //             }else{
                    //                 $next = true;
                    //             }
                    //         }
                    //     }
                    // }

                    if($next){
                        $title = !empty($this->input->post('title')) ? $this->safe($this->input->post('title')) : '';
                        $url = $this->generate_seo_link($title);                
                        
                        if(strlen($title) > 0){
                            $params_save = array(
                                'product_branch_id' => $session_branch_id,
                                'product_category_id' => !empty($this->input->post('categories')) ? $this->input->post('categories') : null,
                                'product_type' => 1,
                                'product_name' => $title,
                                'product_unit' => !empty($post['satuan']) ? $post['satuan'] : null,
                                'product_note' => !empty($this->input->post('content')) ? $this->input->post('content') : null,
                                // 'product_price_buy' => !empty($post['product_price_buy']) ? intval($post['product_price_buy']) : null,
                                'product_price_sell' => !empty($post['harga']) ? intval($post['harga']) : null,
                                // 'product_image' => !empty($post['product_image']) ? $post['product_image'] : null,
                                'product_url' => $url,
                                'product_user_id' => $session_user_id,
                                'product_date_created' => date("YmdHis"),
                                // 'product_date_updated' => date("YmdHis"),
                                'product_flag' => !empty($this->input->post('status')) ? $this->input->post('status') : 0,
                                'product_stock' => !empty($post['stok']) ? floatval($post['stok']) : 0,
                                // 'product_visitor' => !empty($post['product_visitor']) ? intval($post['product_visitor']) : 0,
                                // 'product_reminder' => !empty($post['product_reminder']) ? $post['product_reminder'] : null,
                                // 'product_reminder_date' => !empty($post['product_reminder_date']) ? $post['product_reminder_date'] : null,
                            );
                            $params_update = array(
                                'product_category_id' => !empty($this->input->post('categories')) ? $this->input->post('categories') : null,
                                'product_type' => 1,
                                'product_name' => $title,
                                'product_unit' => !empty($post['satuan']) ? $post['satuan'] : null,
                                'product_note' => !empty($this->input->post('content')) ? $this->input->post('content') : null,
                                // 'product_price_buy' => !empty($post['product_price_buy']) ? intval($post['product_price_buy']) : null,
                                'product_price_sell' => !empty($post['harga']) ? intval($post['harga']) : null,
                                // 'product_image' => !empty($post['product_image']) ? $post['product_image'] : null,
                                'product_url' => $url,
                                // 'product_user_id' => $session_user_id,
                                // 'product_date_created' => date("YmdHis"),
                                'product_date_updated' => date("YmdHis"),
                                'product_flag' => !empty($this->input->post('status')) ? $this->input->post('status') : 0,
                                'product_stock' => !empty($post['stok']) ? floatval($post['stok']) : 0,
                                // 'product_visitor' => !empty($post['product_visitor']) ? intval($post['product_visitor']) : 0,
                                // 'product_reminder' => !empty($post['product_reminder']) ? $post['product_reminder'] : null,
                                // 'product_reminder_date' => !empty($post['product_reminder_date']) ? $post['product_reminder_date'] : null,
                            );                                        
                            //Check Data Exist UPDATE
                            if(!empty($this->input->post('id')) && ($this->input->post('id') > 0)){
                                $operator = 2; $mes = 'memperbarui';
                                $where_not = array('product_id'=>$this->input->post('id'));
                                $params_check = array(
                                    'product_name' => $title,
                                    'product_url' => $url,
                                    'product_type' => $post['tipe']
                                );
                                $check_exists = $this->Product_model->check_data_exist_two_condition($where_not,$params_check);                                
                            }else{ // SAVE
                                $operator = 1; $mes = 'menyimpan';
                                $params_check = array(
                                    'product_name' => $title,
                                    'product_url' => $url,
                                    'product_type' => $post['tipe']
                                );
                                $check_exists = $this->Product_model->check_data_exist($params_check);
                            }
                            
                            if($check_exists==false){
                                // if($next){

                                    if($operator == 1){
                                        $set_data=$this->Product_model->add_product($params_save);
                                        $data = $this->Product_model->get_product($set_data);
                                    }else{
                                        $update_data = $this->Product_model->update_product($this->input->post('id'),$params_update);
                                        $set_data = $this->input->post('id');
                                    }

                                    //Process for Upload
                                    $image_config=array(
                                        'compress' => 1,
                                        'width'=>$image_width,
                                        'height'=>$image_height
                                    );
                                    $watermark = [
                                        'text_1' => 'avista.id',
                                        'text_2' => 'property'                           
                                    ];                                    
                                    $folder = $folder_upload_product;

                                    //Croppie Upload Image
                                    $post_upload_1 = !empty($this->input->post('files_1')) ? $this->input->post('files_1') : "";
                                    if(strlen($post_upload_1) > 10){
                                        $upload_process = upload_file_base64_watermark($folder,$post_upload_1,$image_config,$watermark);
                                        if($upload_process['status'] == 1){
                                                $file_session = $this->random_session(20);
                                                $params = array(
                                                    'file_from_table' => 'products',
                                                    'file_from_id' => $set_data,
                                                    'file_session' => $file_session,
                                                    'file_date_created' => date("YmdHis"),
                                                    'file_user_id' => $session_user_id,
                                                    'file_type' => 1,
                                                    'file_name' => $upload_process['result']['file_name'],
                                                    'file_format' => $upload_process['result']['file_ext'],
                                                    'file_url' => $upload_process['result']['file_location'],
                                                    'file_size' => $upload_process['result']['file_size']
                                                );
                                                $save_data = $this->File_model->add_file($params);
                                        }else{
                                            $return->message = 'Fungsi Gambar gagal';
                                        }
                                    }

                                    $post_upload_2 = !empty($this->input->post('files_2')) ? $this->input->post('files_2') : "";
                                    if(strlen($post_upload_2) > 10){
                                        $upload_process = upload_file_base64_watermark($folder,$post_upload_2,$image_config,$watermark);
                                        if($upload_process['status'] == 1){
                                                $file_session = $this->random_session(20);
                                                $params = array(
                                                    'file_from_table' => 'products',
                                                    'file_from_id' => $set_data,
                                                    'file_session' => $file_session,
                                                    'file_date_created' => date("YmdHis"),
                                                    'file_user_id' => $session_user_id,
                                                    'file_type' => 1,
                                                    'file_name' => $upload_process['result']['file_name'],
                                                    'file_format' => $upload_process['result']['file_ext'],
                                                    'file_url' => $upload_process['result']['file_location'],
                                                    'file_size' => $upload_process['result']['file_size']
                                                );
                                                $save_data = $this->File_model->add_file($params);
                                        }else{
                                            $return->message = 'Fungsi Gambar gagal';
                                        }
                                    }
                                    
                                    $post_upload_3 = !empty($this->input->post('files_3')) ? $this->input->post('files_3') : "";
                                    if(strlen($post_upload_3) > 10){
                                        $upload_process = upload_file_base64_watermark($folder,$post_upload_3,$image_config,$watermark);
                                        if($upload_process['status'] == 1){
                                                $file_session = $this->random_session(20);
                                                $params = array(
                                                    'file_from_table' => 'products',
                                                    'file_from_id' => $set_data,
                                                    'file_session' => $file_session,
                                                    'file_date_created' => date("YmdHis"),
                                                    'file_user_id' => $session_user_id,
                                                    'file_type' => 1,
                                                    'file_name' => $upload_process['result']['file_name'],
                                                    'file_format' => $upload_process['result']['file_ext'],
                                                    'file_url' => $upload_process['result']['file_location'],
                                                    'file_size' => $upload_process['result']['file_size']
                                                );
                                                $save_data = $this->File_model->add_file($params);
                                        }else{
                                            $return->message = 'Fungsi Gambar gagal';
                                        }
                                    }
                                    
                                    $post_upload_4 = !empty($this->input->post('files_4')) ? $this->input->post('files_4') : "";
                                    if(strlen($post_upload_4) > 10){
                                        $upload_process = upload_file_base64_watermark($folder,$post_upload_4,$image_config,$watermark);
                                        if($upload_process['status'] == 1){
                                                $file_session = $this->random_session(20);
                                                $params = array(
                                                    'file_from_table' => 'products',
                                                    'file_from_id' => $set_data,
                                                    'file_session' => $file_session,
                                                    'file_date_created' => date("YmdHis"),
                                                    'file_user_id' => $session_user_id,
                                                    'file_type' => 1,
                                                    'file_name' => $upload_process['result']['file_name'],
                                                    'file_format' => $upload_process['result']['file_ext'],
                                                    'file_url' => $upload_process['result']['file_location'],
                                                    'file_size' => $upload_process['result']['file_size']
                                                );
                                                $save_data = $this->File_model->add_file($params);
                                        }else{
                                            $return->message = 'Fungsi Gambar gagal';
                                        }
                                    }                                        
                                    //End of Croppie

                                    
                                    $return->status=1;
                                    $return->message='Berhasil '.$mes;
                                    $return->result= array(
                                        'id' => $set_data,
                                        'title' => $title
                                    );  

                                    /* Start Activity */
                                    $params = array(
                                        'activity_user_id' => $session['user_data']['user_id'],
                                        'activity_action' => ($operator==1) ? 2 : 4,
                                        'activity_table' => 'product',
                                        'activity_table_id' => $set_data,                            
                                        'activity_text_1' => strtoupper($title),
                                        'activity_text_2' => ucwords(strtolower($title)),                        
                                        'activity_date_created' => date('YmdHis'),
                                        'activity_flag' => 1
                                    );
                                    $this->save_activity($params);
                                    /* End Activity */
                                // }
                            }else{
                                $return->message='Nama Produk sudah digunakan';                    
                            }
                        }else{
                            $return->message = 'Nama Produk harus diisi';
                        }
                    }                   
                    break;
                case "read":
                    $this->form_validation->set_rules('product_id', 'product_id', 'required');
                    if ($this->form_validation->run() == FALSE){
                        $return->message = validation_errors();
                    }else{                
                        $product_id   = !empty($post['product_id']) ? $post['product_id'] : 0;
                        if(intval(strlen($product_id)) > 0){        
                            $datas = $this->Product_model->get_product($product_id);
                            if($datas){
                                $return->status=1;
                                $return->message='Berhasil mendapatkan data';
                                $return->result=$datas;
                            }else{
                                $return->message = 'Data tidak ditemukan';
                            }
                        }else{
                            $return->message='Data tidak ada';
                        }
                    }
                    break;
                case "read_files":   
                    $data['id'] = $this->input->post('id');    
                    $get_files = $this->File_model->get_all_file(['file_from_table'=>'products','file_from_id'=>$data['id']],null,null,null,'file_id','id');
                    $return->status=1;
                    $return->message='Success';
                    $return->result=$get_files;   
                    break;  
                case "remove_with_files":
                    $data['id'] = $this->input->post('id');           
                    $datas = $this->File_model->get_all_file(['file_from_table'=>'products','file_from_id'=>$data['id']],null,null,null,'file_id','asc');
                    // if($datas==true){
                        //Delete old files
                        foreach($datas as $v){
                            if (!empty($v['file_url'])) {
                                if (file_exists(FCPATH . $v['file_url'])) {
                                    unlink(FCPATH . $v['file_url']);
                                }
                            }
                        }
                        $this->News_model->delete_news($data['id']);
                        /* Activity */
                        /*
                        $params = array(
                            'activity_user_id' => $session['user_data']['user_id'],
                            'activity_action' => 4,
                            'activity_table' => 'news',
                            'activity_table_id' => $datas['id'],
                            'activity_text_1' => strtoupper($datas['kode']),
                            'activity_text_2' => ucwords(strtolower($datas['username'])),
                            'activity_date_created' => date('YmdHis'),
                            'activity_flag' => 0
                        );
                        $this->save_activity($params);                    
                        /* End Activity */
                        $return->status=1;
                        $return->message='Berhasil menghapus';
                        $return->result=$datas;
                    // }                         
                    break; 
                case "remove_file":
                    $data['id'] = $this->input->post('id');           
                    $ge = $this->File_model->get_file($data['id']);
                    if (!empty($ge['file_url'])) {
                        if (file_exists(FCPATH . $ge['file_url'])) {
                            unlink(FCPATH . $ge['file_url']);
                        }
                    }
                        $this->File_model->delete_file($data['id']);
                        /* Activity */
                        /*
                        $params = array(
                            'activity_user_id' => $session['user_data']['user_id'],
                            'activity_action' => 4,
                            'activity_table' => 'news',
                            'activity_table_id' => $datas['id'],
                            'activity_text_1' => strtoupper($datas['kode']),
                            'activity_text_2' => ucwords(strtolower($datas['username'])),
                            'activity_date_created' => date('YmdHis'),
                            'activity_flag' => 0
                        );
                        $this->save_activity($params);                    
                        /* End Activity */
                        $return->status=1;
                        $return->message='Berhasil menghapus';
                        // $return->result=;
                    // }                         
                    break;                               
                case "delete":
                    $this->form_validation->set_rules('product_id', 'product_id', 'required');
                    if ($this->form_validation->run() == FALSE){
                        $return->message = validation_errors();
                    }else{
                        $product_id   = !empty($post['product_id']) ? $post['product_id'] : 0;
                        $product_name = !empty($post['product_name']) ? $post['product_name'] : null;

                        if(strlen($product_id) > 0){
                            $get_data=$this->Product_model->get_product($product_id);
                            // $set_data=$this->Product_model->delete_product($product_id);
                            $set_data = $this->Product_model->update_product_custom(array('product_id'=>$product_id),array('product_flag'=>4));                
                            if($set_data){
                                /*
                                $file = FCPATH.$this->folder_upload.$get_data['product_image'];
                                if (file_exists($file)) {
                                    unlink($file);
                                }
                                */
                                $return->status=1;
                                $return->message='Berhasil menghapus '.$product_name;
                            }else{
                                $return->message='Gagal menghapus '.$product_name;
                            } 
                        }else{
                            $return->message='Data tidak ditemukan';
                        }
                    }
                    break;
                case "update_flag":
                    $this->form_validation->set_rules('product_id', 'product_id', 'required');
                    $this->form_validation->set_rules('product_flag', 'product_flag', 'required');
                    $this->form_validation->set_message('required', '{field} wajib diisi');
                    if($this->form_validation->run() == FALSE){
                        $return->message = validation_errors();
                    }else{
                        $product_id = !empty($post['product_id']) ? $post['product_id'] : 0;
                        if(strlen(intval($product_id)) > 1){
                            
                            $params = array(
                                'product_flag' => !empty($post['product_flag']) ? intval($post['product_flag']) : 0,
                            );
                            
                            $where = array(
                                'product_id' => !empty($post['product_id']) ? intval($post['product_id']) : 0,
                            );
                            
                            if($post['product_flag']== 0){
                                $set_msg = 'nonaktifkan';
                            }else if($post['product_flag']== 1){
                                $set_msg = 'mengaktifkan';
                            }else if($post['product_flag']== 4){
                                $set_msg = 'menghapus';
                            }else{
                                $set_msg = 'mendapatkan data';
                            }

                            if($post['product_flag'] == 4){
                                $params['product_url'] = null;
                            }

                            $get_data = $this->Product_model->get_product_custom($where);
                            if($get_data){
                                $set_update=$this->Product_model->update_product_custom($where,$params);
                                if($set_update){
                                    if($post['product_flag'] == 4){
                                        /*
                                        $file = FCPATH.$this->folder_upload.$get_data['product_image'];
                                        if (file_exists($file)) {
                                            unlink($file);
                                        }
                                        */
                                    }
                                    $return->status  = 1;
                                    $return->message = 'Berhasil '.$set_msg.' '.$get_data['product_name'];
                                }else{
                                    $return->message='Gagal '.$set_msg;
                                }
                            }else{
                                $return->message='Gagal mendapatkan data';
                            }   
                        }else{
                            $return->message = 'Tidak ada data';
                        } 
                    }
                    break;
                case "update_stock_price":
                    $this->form_validation->set_rules('product_id', 'product_id', 'required');
                    $this->form_validation->set_rules('product_stock', 'product_stock', 'required');
                    $this->form_validation->set_rules('product_price_sell', 'product_price_sell', 'required');                    
                    $this->form_validation->set_message('required', '{field} wajib diisi');
                    if($this->form_validation->run() == FALSE){
                        $return->message = validation_errors();
                    }else{
                        $product_id = !empty($post['product_id']) ? $post['product_id'] : 0;
                        if(intval($product_id) > 0){
                            
                            $params = array(
                                'product_stock' => !empty($post['product_stock']) ? intval($post['product_stock']) : 0,
                                'product_price_sell' => !empty($post['product_price_sell']) ? intval($post['product_price_sell']) : 0                                
                            );
                            
                            $where = array(
                                'product_id' => !empty($post['product_id']) ? intval($post['product_id']) : 0,
                            );
                            
                            $set_update=$this->Product_model->update_product_custom($where,$params);
                            if($set_update){
                                $return->status  = 1;
                                $return->message = 'Stok -> '.intval($post['product_stock'].' Harga -> '. $post['product_price_sell']);
                            }else{
                                $return->message='Gagal ';
                            }
                        }else{
                            $return->message = 'Tidak ada data';
                        } 
                    }
                    break;
                default:
                    $return->message='No Action';
                    break; 
            }
            echo json_encode($return);
        }else{
            // Default First Date & End Date of Current Month
            $firstdate = new DateTime('first day of this month');
            $firstdateofmonth = $firstdate->format('d-m-Y');

            $data['session'] = $this->session->userdata();  
            $session_user_id = !empty($data['session']['user_data']['user_id']) ? $data['session']['user_data']['user_id'] : null;

            $data['first_date'] = $firstdateofmonth;
            $data['end_date'] = date("d-m-Y");
            $data['hour'] = date("H:i");
            $data['theme'] = $this->User_model->get_user($data['session']['user_data']['user_id']);

            $data['allowed_file_type'] = $allowed_types;
            $data['allowed_file_size'] = $allowed_file_size;

            $data['image_width'] = intval($image_width);
            $data['image_height'] = intval($image_height);
            /*
            // Reference Model
            $this->load->model('Reference_model');
            $data['reference'] = $this->Reference_model->get_all_reference();
            */

            $data['_route'] = $product_route;   

            $data['title'] = 'Product';
            $data['_view'] = 'layouts/admin/menu/webpage/product';
            $this->load->view('layouts/admin/index',$data);
            $this->load->view('layouts/admin/menu/webpage/product_js.php',$data);
        }
    }          
}
?>