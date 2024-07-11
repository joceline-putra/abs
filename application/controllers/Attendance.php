<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Attendance extends MY_Controller{

    public $folder_upload = 'upload/attendance/';
    public $folder_upload_project = 'upload/project/';    
    public $allowed_types = 'jpg|png|jpeg|mp4';
    public $image_width   = 250;
    public $image_height  = 250;
    public $allowed_file_size; // 5 MB -> 5000 KB

    function __construct(){
        parent::__construct();
        if(!$this->is_logged_in()){

            // Will Return to Last URL Where session is empty
            $this->session->set_userdata('url_before',base_url(uri_string()));
            redirect(base_url("login/return_url"));
        }
        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->load->model('Attendance_model');
        $this->load->model('User_model');
        $this->load->model('Lokasi_model');
        $this->load->model('Branch_model');     
        
        $this->folder_upload = 'upload/attendance/';
        $this->allowed_types = 'jpg|png|jpeg|mp4';
        $this->image_width   = 250;
        $this->image_height  = 250;        
        $this->allowed_file_size     = 1024; // KiloByte    
    }
    function index(){
        if ($this->input->post()) {    
            // var_dump($this->input->post());die;
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
            // var_dump($action);die;
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
                    
                    $get_count = $this->Attendance_model->get_all_name_count($params, $search);
                    if($get_count > 0){
                        $get_data = $this->Attendance_model->get_all_name($params, $search, $limit, $start, $order, $dir);
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
                case "load_activity":
                    // $user = $this->input->post('user');  
                    $limit_start = !empty($this->input->post('limit_start')) ? $this->input->post('limit_start') : 1;
                    $limit_end = !empty($this->input->post('limit_end')) ? $this->input->post('limit_end') : 25;

                    $att_type = 0;
                    $limit  = ($limit_start * $limit_end) - $limit_end;
                        $get_data = $this->Attendance_model->get_all_attendance_activity($att_type,$limit, $limit_end);
                        if(count($get_data) > 0){
                            foreach($get_data as $v){
                                $datas[] = array(
                                    'att_id' => intval($v['att_id']),
                                    'att_type' => intval($v['att_type']),
                                    'att_location_id' => intval($v['att_location_id']),
                                    'att_user_id' => intval($v['att_user_id']),
                                    'att_image' => !empty($v['att_image']) ? site_url() . $v['att_image'] : site_url('upload/noimage.png'),
                                    'att_lat' => intval($v['att_lat']),
                                    'att_lng' => intval($v['att_lng']),
                                    'att_date_created' => $v['att_date_created'],
                                    'att_image_size' => $v['att_image_size'],
                                    'att_address' => $v['att_address'],
                                    'att_note' => $v['att_note'],
                                    'user_username' => $v['user_username'],
                                    'user_fullname' => $v['user_fullname'],   
                                    'user_image' => site_url('upload/default.png'),                             
                                    'time_ago' => $v['time_ago'],
                                    'location_name' => !empty($v['location_name']) ? $v['location_name'] : 'memposting sebuah gambar'
                                );
                            }
                        }else{
                            $datas = [];
                        }
                        $return->total_records   = count($datas);
                        $return->status          = 1; 
                        $return->result          = $datas;

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
                            $check_exists = $this->Attendance_model->check_data_exist_two_condition($where_not,$where_new);

                            /* Continue Update if not exist */
                            if(!$check_exists){
                                $where = array(
                                    'name_id' => intval($post['name_id']),
                                );
                                $params = array(
                                    'name_name' => $name_name,
                                    'name_flag' => !empty($post['name_flag']) ? $post['name_flag'] : 0
                                );
                                $update = $this->Attendance_model->update_name_custom($where,$params);
                                if($update){
                                    $get_name = $this->Attendance_model->get_name_custom($where);
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
                            $check_exists = $this->Attendance_model->check_data_exist($params_check);

                            /* Continue Save if not exist */
                            if(!$check_exists){
                                $name_session = strtoupper(substr(hash('sha256', date_timestamp_get(date_create())),0,20));
                                $params = array(
                                    'name_session' => $name_session,
                                    'name_name' => $name_name,
                                    'name_flag' => !empty($post['name_flag']) ? $post['name_flag'] : 0
                                );
                                $create = $this->Attendance_model->add_name($params);
                                if($create){
                                    $get_name = $this->Attendance_model->get_name($create);
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
                        $check_exists = $this->Attendance_model->check_data_exist($params_check);
                        if(!$check_exists){

                            $set_data=$this->Attendance_model->add_name($params);
                            if($set_data){

                                $name_id = $set_data;
                                $data = $this->Attendance_model->get_name($name_id);

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
                                                $stat = $this->Attendance_model->update_name_custom(array('name_id' => $set_data), $params_image);
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
                                            $stat = $this->Attendance_model->update_name_custom(array('name_id' => $set_data), $params_image);
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
                            $datas = $this->Attendance_model->get_name($name_id);
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
                           
                            $set_update=$this->Attendance_model->update_name($name_id,$params);
                            if($set_update){
                                
                                $get_data = $this->Attendance_model->get_name($name_id);
                                    
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
                                                $stat = $this->Attendance_model->update_name_custom(array('name_id' => $name_id), $params_image);
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
                            $get_data=$this->Attendance_model->get_name($name_id);
                            // $set_data=$this->Attendance_model->delete_name($name_id);
                            $set_data = $this->Attendance_model->update_name_custom(array('name_id'=>$name_id),array('name_flag'=>4));                
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

                            $get_data = $this->Attendance_model->get_name_custom($where);
                            if($get_data){
                                $set_update=$this->Attendance_model->update_name_custom($where,$params);
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
                    
                    $get_count = $this->Attendance_model->get_all_name_item_count($params, $search);
                    if($get_count > 0){
                        $get_data = $this->Attendance_model->get_all_name_item($params, $search, $limit, $start, $order, $dir);
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
                            $get_data = $this->Attendance_model->get_all_name_item($params, $search, $limit, $start, $order, $dir);
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
                case "get_location":
                    $return->status=1;
                    $return->result = $this->Lokasi_model->get_all_lokasis(['location_branch_id'=>1],null,null,null,'location_name','asc');
                    break;
                case "checkin":
                    $params = array(
                        'att_user_id' => $session_user_id,
                        'att_type' => 1,
                        'att_location_id' => $post['location_id'],
                        'att_lat' => $post['lat'],
                        'att_lng' => $post['lng'],
                        'att_date_created' => date("YmdHis"),
                        'att_address' => $post['address']
                    );
                    //Base64 or Croppie Upload Image
                    $post_upload = !empty($this->input->post('file')) ? $this->input->post('file') : "";
                    if(strlen($post_upload) > 10){
                        $image_config=array(
                            'compress' => 0,
                            'width'=>$this->image_width,
                            'height'=>$this->image_height
                        );
                        $watermark = [
                            'text_1' => $post['lat'].', '.$post['lng'],
                            'text_2' => $post['address']                            
                        ];
                        $upload_process = upload_file_base64_watermark($this->folder_upload,$post_upload, $image_config,$watermark);
                        // var_dump($upload_process['status']);die;
                        if($upload_process['status'] == 1){
                            // if ($get_att && $get_att['att_id']) {
                                $params['att_image'] = $upload_process['result']['file_location'];
                                $params['att_image_size'] = $upload_process['result']['file_size'];
                                // if (!empty($get_att['att_image'])) {
                                //     if (file_exists(FCPATH . $get_att['att_image'])) {
                                //         unlink(FCPATH . $get_att['att_image']);
                                //     }
                                // }
                            // }
                        }else{
                            $return->message = 'Fungsi Gambar gagal';
                            $next = false;
                        }
                    }
                    //End of Base64 or Croppie 
                    
                    // Call Helper for Upload ?? NOT USED
                    if(!empty($_FILES['filess'])){
                        //convert post (Byte to KiloByte) < 
                        if(intval($_FILES['file']['size'] / 1024) < ($this->allowed_file_size)){

                            //Process for Upload
                            $image_config=array(
                                'compress' => 1,
                                'width'=>$this->image_width,
                                'height'=>$this->image_height
                            );
                            $upload_helper = upload_file_files($this->folder_upload, $_FILES['file'], $image_config);
                            if ($upload_helper['status'] == 1) {

                                //Add Image for params before update
                                $params['att_image']        = $upload_helper['result']['file_location'];
                                $params['att_image_size'] = $upload_helper['result']['file_size'];
                                //Delete old files
                                /*
                                    if (!empty($datas['news_image'])) {
                                        if (file_exists(FCPATH . $datas['news_image'])) {
                                            unlink(FCPATH . $datas['news_image']);
                                        }
                                    }
                                */
                                $set_msg = 'Berhasil menyimpan dengan Gambar'; $next = true;
                            }else{
                                $set_msg = 'Error: '.$upload_helper['message']; $next = false;
                            }
                        }else{
                            $set_msg = 'Gagal, ukuran melebihi '.($this->allowed_file_size / 1024).' Mb'; $next = false;
                        }
                    } else{
                        $set_msg = 'Menyimpan tanpa gambar';
                    }   
                    // End Call Helper for Upload 
                                        
                    // var_dump($params);die;                  
                    $this->Attendance_model->add_attendance($params);
                    $return->message = $set_msg;
                    break;
                case "checkout":
                    $params = array(
                        'att_user_id' => $session_user_id,
                        'att_type' => 2,
                        'att_location_id' => $post['location_id'],
                        'att_lat' => $post['lat'],
                        'att_lng' => $post['lng'],
                        'att_date_created' => date("YmdHis"),
                        'att_address' => $post['address'],
                        'att_note' => $post['keterangan']
                    );
                    //Base64 or Croppie Upload Image
                    $post_upload = !empty($this->input->post('file')) ? $this->input->post('file') : "";
                    if(strlen($post_upload) > 10){
                        $image_config=array(
                            'compress' => 0,
                            'width'=>$this->image_width,
                            'height'=>$this->image_height
                        );
                        $watermark = [
                            'text_1' => $post['lat'].', '.$post['lng'],
                            'text_2' => $post['address']                            
                        ];
                        $upload_process = upload_file_base64_watermark($this->folder_upload,$post_upload, $image_config,$watermark);
                        // var_dump($upload_process['status']);die;
                        if($upload_process['status'] == 1){
                            // if ($get_att && $get_att['att_id']) {
                                $params['att_image'] = $upload_process['result']['file_location'];
                                $params['att_image_size'] = $upload_process['result']['file_size'];
                                // if (!empty($get_att['att_image'])) {
                                //     if (file_exists(FCPATH . $get_att['att_image'])) {
                                //         unlink(FCPATH . $get_att['att_image']);
                                //     }
                                // }
                            // }
                            $return->status = 1;
                            $set_msg = 'Berhasil checkout';
                        }else{
                            $set_msg = 'Fungsi Gambar gagal';
                            $next = false;
                        }
                    }
                    //End of Base64 or Croppie 
                                                
                    $this->Attendance_model->add_attendance($params);
                    $return->message = $set_msg;
                    break;        
                case "posting":
                    $params = array(
                        'att_user_id' => $session_user_id,
                        'att_type' => 3,
                        // 'att_location_id' => $post['location_id'],
                        'att_lat' => $post['lat'],
                        'att_lng' => $post['lng'],
                        'att_date_created' => date("YmdHis"),
                        'att_address' => $post['address'],
                        'att_note' => $post['keterangan']
                    );
                    //Base64 or Croppie Upload Image
                    $post_upload = !empty($this->input->post('file')) ? $this->input->post('file') : "";
                    if(strlen($post_upload) > 10){
                        $image_config=array(
                            'compress' => 1,
                            'width'=>$this->image_width,
                            'height'=>$this->image_height
                        );
                        $watermark = [
                            'text_1' => $post['lat'].', '.$post['lng'],
                            'text_2' => $post['address']                            
                        ];
                        $upload_process = upload_file_base64_watermark($this->folder_upload,$post_upload, $image_config,$watermark);
                        // var_dump($upload_process['status']);die;
                        if($upload_process['status'] == 1){
                            // if ($get_att && $get_att['att_id']) {
                                $params['att_image'] = $upload_process['result']['file_location'];
                                $params['att_image_size'] = $upload_process['result']['file_size'];
                                // if (!empty($get_att['att_image'])) {
                                //     if (file_exists(FCPATH . $get_att['att_image'])) {
                                //         unlink(FCPATH . $get_att['att_image']);
                                //     }
                                // }
                            // }
                            $return->status = 1;
                            $set_msg = 'Berhasil kirim gambar';
                        }else{
                            $set_msg = 'Fungsi Gambar gagal';
                            $next = false;
                        }
                    }
                    //End of Base64 or Croppie  
                    
                    $this->Attendance_model->add_attendance($params);
                    $return->message = $set_msg;
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

            $data['title'] = 'Attendance';
            $data['_view'] = 'layouts/admin/menu/webpage/attendance';
            // $this->load->view('layouts/admin/attendance',$data);
            $this->load->view('layouts/admin/index',$data);
            $this->load->view('layouts/admin/menu/webpage/attendance_js.php',$data);
            // $this->load->view($this->nav['admin']['layout'].'/firebase',$data);            
        }
    }
}

?>