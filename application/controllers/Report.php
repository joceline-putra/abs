<?php 
/*
    @AUTHOR: Joe Witaya
*/ 
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends MY_Controller{
    function __construct(){
        parent::__construct();
        
        if(!$this->is_logged_in()){

            //Will Return to Last URL Where session is empty
            $this->session->set_userdata('url_before',base_url(uri_string()));
            redirect(base_url("login/return_url"));
        }                
        $this->load->model('User_model');    
        $this->load->model('Account_model');
        $this->load->model('Transaksi_model'); 
        $this->load->model('Order_model'); 
        $this->load->model('Journal_model'); 
        $this->load->model('Branch_model');
        $this->load->model('Produk_model');
        $this->load->model('Lokasi_model');              
        $this->load->model('Kontak_model');     
        $this->load->model('Kategori_model');
        $this->load->model('Type_model');
        $this->load->model('Attendance_model');

        $this->journal_url = site_url('keuangan/print/');
        $this->trans_url = site_url('transaksi/print_history/');
        $this->order_url = site_url('order/print/');       

        $this->customer_alias   = 'Customer';
        $this->supplier_alias   = 'Supplier';
        $this->employee_alias   = 'Karyawan';

        $this->product_alias    = 'Produk';
        $this->ref_alias        = 'Room';             

        $this->so_alias         = 'Sales Order';
        $this->po_alias         = 'Purchase Order';
        $this->pos_alias        = 'POS';                                    

        $this->buy_alias        = 'Pembelian';
        $this->sell_alias       = 'Penjualan';                                                    
    }
    function index(){
        $data['session'] = $this->session->userdata();  
        $session = $this->session->userdata(); 
        $session_branch_id = $session['user_data']['branch']['id'];
        $session_user_id = $session['user_data']['user_id'];

        $data['theme'] = $this->User_model->get_user($data['session']['user_data']['user_id']);
        $data['identity'] = 0;
        
        if($this->input->post('action')){
            $action         = $this->input->post('action');
            $post_data      = $this->input->post('data');
            $data           = json_decode($post_data, TRUE);
            $identity       = $this->input->post('tipe');
            $trans_type     = $this->input->post('trans_type');

            $return = new \stdClass();
            $return->status = 0;
            $return->message = '';
            $return->result = '';

            switch($action){
                case "load":
                        $return->message="Not Ready";
                    break;
                case "load-report-purchase-buy-recap":
                        $return->message="Not Ready";                    
                    break;                    
                case "load-report-purchase-buy-detail":
                        $return->message="Not Ready";
                    break;                        
                case "load-report-purchase-buy-account-payable":
                    $columns = array(
                        '0' => 'trans_date',
                        '1' => 'trans_number',
                        '2' => 'trans_total',
                        '3' => 'contact_name'
                    );

                    $limit  = $this->input->post('length');
                    $start  = $this->input->post('start');
                    $order  = !empty($this->input->post('order')) ? $columns[$this->input->post('order')[0]['column']] : $columns[0];
                    $dir    = $this->input->post('order')[0]['dir'];

                    $search = [];
                    if ($this->input->post('search')['value']) {
                        $s = $this->input->post('search')['value'];
                        foreach ($columns as $v) {
                            $search[$v] = $s;
                        }
                    }
                    $date_start = !empty($this->input->post('date_start')) ? date('Y-m-d', strtotime($this->input->post('date_start').' 00:00:00')) : date('Y-m-01');
                    $date_end   = !empty($this->input->post('date_end')) ? date('Y-m-d', strtotime($this->input->post('date_end').' 23:59:59')) : date('Y-m-d');

                    $contact_id = !empty($this->input->post('contact')) ? $this->input->post('contact') : 0;
                    
                    $total_data = 0;
                    $mdatas = array();
                    $get_datas = $this->report_finance(4,$date_start,$date_end,$session_branch_id,$contact_id,$search);
                    foreach($get_datas as $k => $v):
                        if(intval($v['total_data']) > 0){
                            $mdatas[] = array(                                 
                                'temp_id' => $v['temp_id'],
                                'type_name' => $v['type_name'],
                                'trans_date' => $v['trans_date'],
                                'trans_date_due' => $v['trans_date_due'],
                                'trans_date_due_over' => $v['trans_date_due_over'],
                                'trans_id' => $v['trans_id'],
                                'trans_note' => $v['trans_note'],
                                'trans_number' => $v['trans_number'],
                                'trans_total' => $v['trans_total'],
                                'trans_total_paid' => $v['trans_total_paid'],
                                'contact_id' => $v['contact_id'],
                                'contact_code' => $v['contact_code'],
                                'contact_name' => $v['contact_name'],
                                'balance' => $v['balance'],
                                'trans_date_format' => date("d/m/Y", strtotime($v['trans_date'])),
                                'trans_date_due_format' => date("d/m/Y", strtotime($v['trans_date_due'])),                                
                                'status' => $v['status'],
                                'message' => $v['message'],
                                'total_data' => $v['total_data'],
                                'trans_session' => $v['trans_session']
                            );
                        }
                        $total_data = $v['total_data'];
                    endforeach;
                    if(intval($total_data) > 0){
                        $total=$total_data;
                        $return->status=1; 
                        $return->message='Loaded'; 
                        $return->total_records=$total;
                        $return->result=$mdatas;        
                    }else{ 
                        $data_source=0; $total=0; 
                        $return->status=0; $return->message='No data'; $return->total_records=$total;
                        $return->result=0;    
                    }
                    $return->recordsTotal = $total;
                    $return->recordsFiltered = $total;
                    echo json_encode($return);
                    break;  
                case "load-report-sales-sell-recap":
                    break;                    
                case "load-report-sales-sell-detail":
                    break;   
                case "load-report-sales-sell-account-receivable":
                    $columns = array(
                        '0' => 'trans_date',
                        '1' => 'trans_number',
                        '2' => 'trans_total',
                        '3' => 'contact_name'
                    );

                    $limit  = $this->input->post('length');
                    $start  = $this->input->post('start');
                    $order  = !empty($this->input->post('order')) ? $columns[$this->input->post('order')[0]['column']] : $columns[0];
                    $dir    = $this->input->post('order')[0]['dir'];

                    $search = [];
                    if ($this->input->post('search')['value']) {
                        $s = $this->input->post('search')['value'];
                        foreach ($columns as $v) {
                            $search[$v] = $s;
                        }
                    }
                    $date_start = !empty($this->input->post('date_start')) ? date('Y-m-d', strtotime($this->input->post('date_start').' 00:00:00')) : date('Y-m-01');
                    $date_end   = !empty($this->input->post('date_end')) ? date('Y-m-d', strtotime($this->input->post('date_end').' 23:59:59')) : date('Y-m-d');
                    $contact_id = !empty($this->input->post('contact')) ? $this->input->post('contact') : 0;
                    
                    $total_data = 0;
                    $mdatas = array();
                    $get_datas = $this->report_finance(5,$date_start,$date_end,$session_branch_id,$contact_id,$search);
                    foreach($get_datas as $k => $v):
                        if(intval($v['total_data']) > 0){
                            $mdatas[] = array(                                 
                                'temp_id' => $v['temp_id'],
                                'type_name' => $v['type_name'],
                                'trans_date' => $v['trans_date'],
                                'trans_date_due' => $v['trans_date_due'],
                                'trans_date_due_over' => $v['trans_date_due_over'],                                
                                'trans_id' => $v['trans_id'],
                                'trans_note' => $v['trans_note'],
                                'trans_number' => $v['trans_number'],
                                'trans_total' => $v['trans_total'],
                                'trans_total_paid' => $v['trans_total_paid'],
                                'contact_id' => $v['contact_id'],
                                'contact_code' => $v['contact_code'],
                                'contact_name' => $v['contact_name'],
                                'balance' => $v['balance'],
                                'trans_date_format' => date("d/m/Y", strtotime($v['trans_date'])),
                                'trans_date_due_format' => date("d/m/Y", strtotime($v['trans_date_due'])),                                
                                'status' => $v['status'],
                                'message' => $v['message'],
                                'total_data' => $v['total_data'],
                                'trans_session' => $v['trans_session']
                            );
                        }
                        $total_data = $v['total_data'];
                    endforeach;
                    if(intval($total_data) > 0){
                        $total=$total_data;
                        $return->status=1; 
                        $return->message='Loaded'; 
                        $return->total_records=$total;
                        $return->result=$mdatas;        
                    }else{ 
                        $data_source=0; $total=0; 
                        $return->status=0; $return->message='No data'; $return->total_records=$total;
                        $return->result=0;    
                    }
                    $return->recordsTotal = $total;
                    $return->recordsFiltered = $total;
                    echo json_encode($return);
                    break;  

                case "load-report-stock-warehouse":
                    break;                      
                case "load-report-stock-moving":
                    break;                                          
                case "load-report-journal":
                    $columns = array(
                        '0' => 'journal_item_group_session',
                        '1' => 'journal_item_account_id',
                        '2' => 'journal_item_debit',
                        '3' => 'journal_item_credit'
                    );

                    $limit = $this->input->post('length');
                    $start = $this->input->post('start');
                    $order = $columns[$this->input->post('order')[0]['column']];
                    $dir = $this->input->post('order')[0]['dir'];

                    $search = [];
                    if ($this->input->post('search')['value']) {
                        $s = $this->input->post('search')['value'];
                        foreach ($columns as $v) {
                            $search[$v] = $s;
                        }
                    }
                    $date_start = date('Y-m-d', strtotime($this->input->post('date_start').' 00:00:00'));
                    $date_end = date('Y-m-d', strtotime($this->input->post('date_end').' 23:59:59'));

                    $account_id = !empty($this->input->post('account')) ? $this->input->post('account') : 0;
                    
                    $total_data = 0;
                    $mdatas = array();
                    $get_datas = $this->report_finance(1,$date_start,$date_end,$session_branch_id,$account_id,$search);
                    // log_message('debug',$get_datas);die;
                    foreach($get_datas as $k => $v):
                        if(intval($v['total_data']) > 0){
                            if(strlen($v['journal_number']) > 1){
                                $url = $this->journal_url.$v['journal_session'];
                            }else if(strlen($v['trans_number']) > 1){
                                $url = $this->trans_url.$v['trans_session'];
                            }else{
                                $url = '#';
                            }                            
                            // $mdatas['"'.$v['journal_item_group_session'].'"'] = array(
                            $mdatas[] = array(                            
                                'journal_group_session' => $v['journal_item_group_session'],
                                'journal_text' => $v['journal_text'],
                                'type_name' => $v['type_name'],                                    
                                'temp_id' => $v['temp_id'],
                                'journal_item_id' => $v['journal_item_id'],
                                'journal_item_note' => $v['journal_item_note'],
                                'journal_number' => $v['journal_number'],                       
                                'trans_id' => $v['trans_id'],
                                'trans_number' => $v['trans_number'],
                                'trans_session' => $v['trans_session'],
                                'account_id' => $v['account_id'],
                                'account_code' => $v['account_code'],
                                'account_name' => $v['account_name'],
                                'debit' => $v['debit'],
                                'credit' => $v['credit'],
                                'balance' => $v['balance'],
                                'journal_item_date_format' => $v['journal_item_date_format'],
                                'status' => $v['status'],
                                'message' => $v['message'],
                                'total_data' => $v['total_data'],
                                'journal_session' => $v['journal_session'],
                                'trans_session' => $v['trans_session'],
                                'journal_id' => $v['journal_id'],
                                'trans_id' => $v['trans_id'],
                                'order_id' => $v['order_id'],
                                'journal_text' => $v['journal_text'],
                                'contact_name' => $v['contact_name'],
                                'url' => $url                                
                            );
                        }
                        $total_data = $v['total_data'];
                    endforeach;

                    if(intval($total_data) > 0){
                        $total=$total_data;
                        $return->status=1; 
                        $return->message='Loaded'; 
                        $return->total_records=$total;
                        $return->result=$mdatas;        
                    }else{ 
                        $data_source=0; $total=0; 
                        $return->status=0; $return->message='No data'; $return->total_records=$total;
                        $return->result=0;    
                    }
                    $return->recordsTotal = $total;
                    $return->recordsFiltered = $total;
                    echo json_encode($return);
                    break;                
                case "load-report-ledger":
                    $columns = array(
                        '0' => 'journal_item_date',
                        '1' => 'journal_item_account_id',
                        '2' => 'journal_item_debit',
                        '3' => 'journal_item_credit'
                    );

                    $limit = $this->input->post('length');
                    $start = $this->input->post('start');
                    $order = $columns[$this->input->post('order')[0]['column']];
                    $dir = $this->input->post('order')[0]['dir'];

                    $search = [];
                    if ($this->input->post('search')['value']) {
                        $s = $this->input->post('search')['value'];
                        foreach ($columns as $v) {
                            $search[$v] = $s;
                        }
                    }
                    $date_start = date('Y-m-d', strtotime($this->input->post('date_start').' 00:00:00'));
                    $date_end = date('Y-m-d', strtotime($this->input->post('date_end').' 23:59:59'));

                    $account_id = !empty($this->input->post('account')) ? $this->input->post('account') : 0;
                    
                    $total_data = 0;
                    $mdatas = array();
                    if($account_id > 0){
                        $get_datas = $this->report_finance(2,$date_start,$date_end,$session_branch_id,$account_id,$search);
                        foreach($get_datas as $k => $v):
                            if(intval($v['total_data']) > 0){
                                if(strlen($v['journal_number']) > 1){
                                    $url = $this->journal_url.$v['journal_session'];
                                }else if(strlen($v['trans_number']) > 1){
                                    $url = $this->trans_url.$v['trans_session'];
                                }else{
                                    $url = '#';
                                }                                   
                                $mdatas[] = array(
                                    'type_name' => $v['type_name'],                                    
                                    'temp_id' => $v['temp_id'],
                                    'journal_item_id' => $v['journal_item_id'],
                                    'journal_item_note' => $v['journal_item_note'],
                                    'journal_number' => $v['journal_number'],                       
                                    'trans_number' => $v['trans_number'],
                                    'account_id' => $v['account_id'],
                                    'account_code' => $v['account_code'],
                                    'account_name' => $v['account_name'],
                                    'debit' => $v['debit'],
                                    'credit' => $v['credit'],
                                    'balance' => $v['balance'],
                                    'journal_item_date_format' => $v['journal_item_date_format'],
                                    'status' => $v['status'],
                                    'message' => $v['message'],
                                    'total_data' => $v['total_data'],
                                    'journal_session' => $v['journal_session'],
                                    'trans_session' => $v['trans_session'],
                                    'journal_id' => $v['journal_id'],
                                    'trans_id' => $v['trans_id'],
                                    'order_id' => $v['order_id'],
                                    'contact_name' => $v['contact_name'],
                                    'url' => $url      
                                );
                            }
                            $total_data = $v['total_data'];
                        endforeach;
                    }
                    if(intval($total_data) > 0){
                        $total=$total_data;
                        $return->status=1; 
                        $return->message='Loaded'; 
                        $return->total_records=$total;
                        $return->result=$mdatas;        
                    }else{ 
                        $data_source=0; $total=0; 
                        $return->status=0; $return->message='No data'; $return->total_records=$total;
                        $return->result=0;    
                    }
                    $return->recordsTotal = $total;
                    $return->recordsFiltered = $total;
                    echo json_encode($return);
                    break;                
                case "load-report-trial-balance":
                    $columns = array(
                        '0' => 'journal_item_group_session',
                        '1' => 'journal_item_account_id',
                        '2' => 'journal_item_debit',
                        '3' => 'journal_item_credit'
                    );

                    $limit = $this->input->post('length');
                    $start = $this->input->post('start');
                    $order = $columns[$this->input->post('order')[0]['column']];
                    $dir = $this->input->post('order')[0]['dir'];

                    $search = [];
                    if ($this->input->post('search')['value']) {
                        $s = $this->input->post('search')['value'];
                        foreach ($columns as $v) {
                            $search[$v] = $s;
                        }
                    }
                    $date_start = date('Y-m-d', strtotime($this->input->post('date_start').' 00:00:00'));
                    $date_end = date('Y-m-d', strtotime($this->input->post('date_end').' 23:59:59'));

                    $account_id = !empty($this->input->post('account')) ? $this->input->post('account') : 0;
                    
                    $total_data = 0;
                    $mdatas = array();
                    $get_datas = $this->report_finance(3,$date_start,$date_end,$session_branch_id,$account_id,$search);
                    // log_message('debug',$get_datas);die;
                    // var_dump($date_start,$date_end,$session_branch_id,$account_id,$search);
                    foreach($get_datas as $k => $v):
                        if(intval($v['total_data']) > 0){
                            // $mdatas['"'.$v['journal_item_group_session'].'"'] = array(
                            $mdatas[] = array(
                                'parent_id' => $v['parent_id'],
                                'account_group' => $v['account_group'],
                                'group_sub' => $v['group_sub'],                                
                                'account_id' => $v['account_id'],
                                'account_code' => $v['account_code'],
                                'account_name' => $v['account_name'],
                                'start_debit' => $v['start_debit'],
                                'start_credit' => $v['start_credit'],
                                'movement_debit' => $v['movement_debit'],
                                'movement_credit' => $v['movement_credit'],
                                'end_debit' => $v['end_debit'],
                                'end_credit' => $v['end_credit'],
                                'profit_loss_debit' => $v['profit_loss_debit'],
                                'profit_loss_credit' => $v['profit_loss_credit'],
                                'balance_debit' => $v['balance_debit'],
                                'balance_credit' => $v['balance_credit'],                                      
                                // 'journal_item_date_format' => $v['journal_item_date_format'],
                                'status' => $v['status'],
                                'message' => $v['message'],
                                'total_data' => $v['total_data']
                            );
                        }
                        $total_data = $v['total_data'];
                    endforeach;

                    if(intval($total_data) > 0){
                        $total=$total_data;
                        $return->status=1; 
                        $return->message='Loaded'; 
                        $return->total_records=$total;
                        $return->result=$mdatas;        
                    }else{ 
                        $data_source=0; $total=0; 
                        $return->status=0; $return->message='No data'; $return->total_records=$total;
                        $return->result=0;    
                    }
                    $return->recordsTotal = $total;
                    $return->recordsFiltered = $total;
                    echo json_encode($return);                
                    break;
                case "load-report-profit-loss":
                    $columns = array(
                        '0' => 'journal_item_group_session',
                        '1' => 'journal_item_account_id',
                        '2' => 'journal_item_debit',
                        '3' => 'journal_item_credit'
                    );

                    $limit = $this->input->post('length');
                    $start = $this->input->post('start');
                    $order = $columns[$this->input->post('order')[0]['column']];
                    $dir = $this->input->post('order')[0]['dir'];

                    $search = [];
                    if ($this->input->post('search')['value']) {
                        $s = $this->input->post('search')['value'];
                        foreach ($columns as $v) {
                            $search[$v] = $s;
                        }
                    }
                    $date_start = date('Y-m-d', strtotime($this->input->post('date_start').' 00:00:00'));
                    $date_end = date('Y-m-d', strtotime($this->input->post('date_end').' 23:59:59'));

                    $account_id = !empty($this->input->post('account')) ? $this->input->post('account') : 0;
                    
                    $total_data = 0;
                    $mdatas = array();
                    $get_datas = $this->report_finance(6,$date_start,$date_end,$session_branch_id,$account_id,$search);
                    // log_message('debug',$get_datas);die;
                    // var_dump($date_start,$date_end,$session_branch_id,$account_id,$search);
                    foreach($get_datas as $k => $v):
                        if(intval($v['total_data']) > 0){
                            // $mdatas['"'.$v['journal_item_group_session'].'"'] = array(
                            if(intval($v['parent_id']) > 3){
                                $mdatas[] = array(
                                    'parent_id' => $v['parent_id'],
                                    'account_group' => $v['account_group'],
                                    'group_sub' => $v['group_sub'],                                
                                    'account_id' => $v['account_id'],
                                    'account_code' => $v['account_code'],
                                    'account_name' => $v['account_name'],
                                    'start_debit' => $v['start_debit'],
                                    'start_credit' => $v['start_credit'],
                                    'movement_debit' => $v['movement_debit'],
                                    'movement_credit' => $v['movement_credit'],
                                    'end_debit' => $v['end_debit'],
                                    'end_credit' => $v['end_credit'],
                                    'profit_loss_debit' => $v['profit_loss_debit'],
                                    'profit_loss_credit' => $v['profit_loss_credit'],
                                    'profit_loss_end' => $v['profit_loss_end'],                                    
                                    'balance_debit' => $v['balance_debit'],
                                    'balance_credit' => $v['balance_credit'],                                      
                                    'balance_credit' => $v['balance_end'],                                                                          
                                    // 'journal_item_date_format' => $v['journal_item_date_format'],
                                    'status' => $v['status'],
                                    'message' => $v['message'],
                                    'total_data' => $v['total_data']
                                );
                            }
                        }
                        $total_data = $v['total_data'];
                    endforeach;

                    if(intval($total_data) > 0){
                        $total=$total_data;
                        $return->status=1; 
                        $return->message='Loaded'; 
                        $return->total_records=$total;
                        $return->result=$mdatas;        
                    }else{ 
                        $data_source=0; $total=0; 
                        $return->status=0; $return->message='No data'; $return->total_records=$total;
                        $return->result=0;    
                    }
                    $return->recordsTotal = $total;
                    $return->recordsFiltered = $total;
                    echo json_encode($return);                    
                    break;                        
                case "load-report-balance":
                    $columns = array(
                        '0' => 'journal_item_group_session',
                        '1' => 'journal_item_account_id',
                        '2' => 'journal_item_debit',
                        '3' => 'journal_item_credit'
                    );

                    $limit = $this->input->post('length');
                    $start = $this->input->post('start');
                    $order = $columns[$this->input->post('order')[0]['column']];
                    $dir = $this->input->post('order')[0]['dir'];

                    $search = [];
                    if ($this->input->post('search')['value']) {
                        $s = $this->input->post('search')['value'];
                        foreach ($columns as $v) {
                            $search[$v] = $s;
                        }
                    }
                    $date_start = date('Y-m-d', strtotime($this->input->post('date_start').' 00:00:00'));
                    $date_end = date('Y-m-d', strtotime($this->input->post('date_end').' 23:59:59'));

                    $account_id = !empty($this->input->post('account')) ? $this->input->post('account') : 0;
                    
                    $total_data = 0;
                    $mdatas = array();
                    $get_datas = $this->report_finance(3,$date_start,$date_end,$session_branch_id,$account_id,$search);
                    // log_message('debug',$get_datas);die;
                    // var_dump($date_start,$date_end,$session_branch_id,$account_id,$search);
                    foreach($get_datas as $k => $v):
                        if(intval($v['total_data']) > 0){
                            // $mdatas['"'.$v['journal_item_group_session'].'"'] = array(
                            if( ((intval($v['parent_id']) > 0) and (intval($v['parent_id']) < 4)) or (intval($v['parent_id']) == 6)){
                                $mdatas[] = array(
                                    'parent_id' => $v['parent_id'],
                                    'account_group' => $v['account_group'],
                                    'group_sub' => $v['group_sub'],                                
                                    'account_id' => $v['account_id'],
                                    'account_code' => $v['account_code'],
                                    'account_name' => $v['account_name'],
                                    'start_debit' => $v['start_debit'],
                                    'start_credit' => $v['start_credit'],
                                    'movement_debit' => $v['movement_debit'],
                                    'movement_credit' => $v['movement_credit'],
                                    'end_debit' => $v['end_debit'],
                                    'end_credit' => $v['end_credit'],
                                    'profit_loss_debit' => $v['profit_loss_debit'],
                                    'profit_loss_credit' => $v['profit_loss_credit'],
                                    'profit_loss_end' => $v['profit_loss_end'],                                    
                                    'balance_debit' => $v['balance_debit'],
                                    'balance_credit' => $v['balance_credit'],                             
                                    'balance_end' => $v['balance_end'],                             
                                    // 'journal_item_date_format' => $v['journal_item_date_format'],
                                    'status' => $v['status'],
                                    'message' => $v['message'],
                                    'total_data' => $v['total_data']
                                );
                            }
                        }
                        $total_data = $v['total_data'];
                    endforeach;

                    if(intval($total_data) > 0){
                        $total=$total_data;
                        $return->status=1; 
                        $return->message='Loaded'; 
                        $return->total_records=$total;
                        $return->result=$mdatas;        
                    }else{ 
                        $data_source=0; $total=0; 
                        $return->status=0; $return->message='No data'; $return->total_records=$total;
                        $return->result=0;    
                    }
                    $return->recordsTotal = $total;
                    $return->recordsFiltered = $total;
                    echo json_encode($return);                    
                    break;                        
                case "load-report-cash-in":
                    $columns = array(
                        '0' => 'journal_item_date',
                        '1' => 'journal_item_account_id',
                        '2' => 'journal_item_debit',
                        '3' => 'journal_item_credit'
                    );

                    $limit = $this->input->post('length');
                    $start = $this->input->post('start');
                    $order = $columns[$this->input->post('order')[0]['column']];
                    $dir = $this->input->post('order')[0]['dir'];

                    /*
                    $search = [];
                    if ($this->input->post('search')['value']) {
                        $s = $this->input->post('search')['value'];
                        foreach ($columns as $v) {
                            $search[$v] = $s;
                        }
                    }
                    */
                    $date_start = date('Y-m-d', strtotime($this->input->post('date_start').' 00:00:00'));
                    $date_end = date('Y-m-d', strtotime($this->input->post('date_end').' 23:59:59'));

                    $account_id = !empty($this->input->post('account')) ? $this->input->post('account') : 0;
                    $search = !empty($this->input->post('filter_type')) ? $this->input->post('filter_type') : 0;

                    $total_data = 0;
                    $mdatas = array();
                    $get_datas = $this->report_cashflow(6,$date_start,$date_end,$session_branch_id,$account_id,$search,$start,$limit);
                    foreach($get_datas as $k => $v):
                        if(intval($v['total_data']) > 0){
                            if(strlen($v['journal_number']) > 1){
                                $url = $this->journal_url.$v['journal_session'];
                            }else if(strlen($v['trans_number']) > 1){
                                $url = $this->trans_url.$v['trans_session'];
                            }else{
                                $url = '#';
                            }                                   
                            $mdatas[] = array(
                                'type_name' => $v['type_name'],                                    
                                'temp_id' => $v['temp_id'],
                                'journal_item_id' => $v['journal_item_id'],
                                'journal_item_note' => $v['journal_item_note'],
                                'journal_number' => $v['journal_number'],                       
                                'trans_number' => $v['trans_number'],
                                'account_id' => $v['account_id'],
                                'account_code' => $v['account_code'],
                                'account_name' => $v['account_name'],
                                'debit' => $v['debit'],
                                'credit' => $v['credit'],
                                'balance' => $v['balance'],
                                'journal_item_date_format' => $v['journal_item_date_format'],
                                'status' => $v['status'],
                                'message' => $v['message'],
                                'total_data' => $v['total_data'],
                                'journal_session' => $v['journal_session'],
                                'trans_session' => $v['trans_session'],
                                'journal_id' => $v['journal_id'],
                                'trans_id' => $v['trans_id'],
                                'order_id' => $v['order_id'],
                                'contact_name' => $v['contact_name'],
                                'url' => $url      
                            );
                        }
                        $total_data = $v['total_data'];
                    endforeach;
                    if(intval($total_data) > 0){
                        $total=$total_data;
                        $return->status=1; 
                        $return->message='Loaded'; 
                        $return->total_records=intval($total);
                        $return->result=$mdatas;        
                    }else{ 
                        $data_source=0; $total=0; 
                        $return->status=0; $return->message='No data'; $return->total_records=intval($total);
                        $return->result=0;    
                    }
                    $return->recordsTotal = intval($total);
                    $return->recordsFiltered = intval($total);
                    echo json_encode($return);                        
                    break;
                case "load-report-cash-out":
                    $columns = array(
                        '0' => 'journal_item_date',
                        '1' => 'journal_item_account_id',
                        '2' => 'journal_item_debit',
                        '3' => 'journal_item_credit'
                    );

                    $limit = $this->input->post('length');
                    $start = $this->input->post('start');
                    $order = $columns[$this->input->post('order')[0]['column']];
                    $dir = $this->input->post('order')[0]['dir'];

                    $search = [];
                    if ($this->input->post('search')['value']) {
                        $s = $this->input->post('search')['value'];
                        foreach ($columns as $v) {
                            $search[$v] = $s;
                        }
                    }
                    $date_start = date('Y-m-d', strtotime($this->input->post('date_start').' 00:00:00'));
                    $date_end = date('Y-m-d', strtotime($this->input->post('date_end').' 23:59:59'));

                    $account_id = !empty($this->input->post('account')) ? $this->input->post('account') : 0;
                    $search = !empty($this->input->post('filter_type')) ? $this->input->post('filter_type') : 0;

                    $total_data = 0;
                    $mdatas = array();
                    $get_datas = $this->report_cashflow(7,$date_start,$date_end,$session_branch_id,$account_id,$search,$start,$limit);
                    foreach($get_datas as $k => $v):
                        if(intval($v['total_data']) > 0){
                            if(strlen($v['journal_number']) > 1){
                                $url = $this->journal_url.$v['journal_session'];
                            }else if(strlen($v['trans_number']) > 1){
                                $url = $this->trans_url.$v['trans_session'];
                            }else{
                                $url = '#';
                            }                                   
                            $mdatas[] = array(
                                'type_name' => $v['type_name'],                                    
                                'temp_id' => $v['temp_id'],
                                'journal_item_id' => $v['journal_item_id'],
                                'journal_item_note' => $v['journal_item_note'],
                                'journal_number' => $v['journal_number'],                       
                                'trans_number' => $v['trans_number'],
                                'account_id' => $v['account_id'],
                                'account_code' => $v['account_code'],
                                'account_name' => $v['account_name'],
                                'debit' => $v['debit'],
                                'credit' => $v['credit'],
                                'balance' => $v['balance'],
                                'journal_item_date_format' => $v['journal_item_date_format'],
                                'status' => $v['status'],
                                'message' => $v['message'],
                                'total_data' => $v['total_data'],
                                'journal_session' => $v['journal_session'],
                                'trans_session' => $v['trans_session'],
                                'journal_id' => $v['journal_id'],
                                'trans_id' => $v['trans_id'],
                                'order_id' => $v['order_id'],
                                'contact_name' => $v['contact_name'],
                                'url' => $url      
                            );
                        }
                        $total_data = $v['total_data'];
                    endforeach;
                    if(intval($total_data) > 0){
                        $total=$total_data;
                        $return->status=1; 
                        $return->message='Loaded'; 
                        $return->total_records=intval($total);
                        $return->result=$mdatas;        
                    }else{ 
                        $data_source=0; $total=0; 
                        $return->status=0; $return->message='No data'; $return->total_records=intval($total);
                        $return->result=0;    
                    }
                    $return->recordsTotal = intval($total);
                    $return->recordsFiltered = intval($total);
                    echo json_encode($return);                        
                break;                 
            }
        }else{

            // Date Now
            $firstdate = new DateTime('first day of this month');
            $firstdateofmonth = $firstdate->format('Y-m-d');        
            $datenow =date("d-m-Y");         
            $data['first_date'] = $firstdateofmonth;
            $data['end_date'] = $datenow;

            /*
            // Reference Model
                $this->load->model('Reference_model');
                $data['reference'] = $this->Reference_model->get_all_reference();
            */
                
            $data['title'] = 'Report';
            $data['_view'] = 'layouts/admin/menu/report/index';
            $this->load->view('layouts/admin/index',$data);
            $this->load->view('layouts/admin/menu/report/index_js',$data);
        }        
    }
    function attendance(){
        $session = $this->session->userdata(); 
        $data['session'] = $this->session->userdata();     
        $data['theme'] = $this->User_model->get_user($data['session']['user_data']['user_id']);
       
        //Date First of the month
        $firstdate = new DateTime('first day of this month');
        $firstdateofmonth = $firstdate->format('d-m-Y');

        $data['title']  = 'Laporan Absensi';
        $data['_view']  = 'layouts/admin/menu/report/absen';
        $file_js        = 'layouts/admin/menu/report/absen_js.php';

        //Date Now
        $datenow =date("d-m-Y"); 
        $data['first_date'] = $firstdateofmonth;
        $data['end_date'] = $datenow;

        $this->load->view('layouts/admin/index',$data);
        $this->load->view($file_js,$data);
    }
    function report_absen($date_start,$date_end,$userr){
        $data['periode'] = date("d-M-Y, H:i", strtotime($date_start.' 00:00:00')).' sd '.date("d-M-Y, H:i", strtotime($date_end.' 23:59:59')); 
        $data['periode_ringkasan'] = date("d-M-Y", strtotime($date_start.' 00:00:00')).' sd '.date("d-M-Y", strtotime($date_end.' 23:59:59')); 
        $data['branch'] = $this->Branch_model->get_branch(1);
        
        $order = $this->input->get('order');
        $dir = $this->input->get('dir');
        $type = $this->input->get('type');        

        $limit  = null;
        $start  = null;
        $order  = $order;
        $dir    = $dir;
        $search = null;

        $params = array(
            'att_date_created >' => date("Y-m-d", strtotime($date_start)).' 00:00:00',
            'att_date_created <' => date("Y-m-d", strtotime($date_end)).' 23:59:59'
        );
        
        if(($type > 0) and ($type < 99)){
            $params['att_type'] = $type;
        }else if($type > 99){
            $params['att_type !='] = 3;
        }

        if(intval($userr) > 0){
            $params['att_user_id'] = intval($userr);
            $get_user = $this->User_model->get_user(intval($userr));
            $data['userr'] = $get_user;
        }

        // var_dump($params);die;
        $datas = $this->Attendance_model->get_all_attendance($params, $search, $limit, $start, $order, $dir);
        // var_dump($datas);die;
        $data['content'] = $datas;
        $data['title']  = 'Laporan Absensi';
        // $data['_view']  = 'layouts/admin/menu/prints/absen';
        // $file_js        = 'layouts/admin/menu/prints/recap_js.php';        
        $this->load->view('layouts/admin/menu/prints/reports/report_absen',$data);
        // $this->load->view($file_js,$data);
    }
    function report_absen_gambar($date_start,$date_end,$userr){
        $data['periode'] = date("d-M-Y, H:i", strtotime($date_start.' 00:00:00')).' sd '.date("d-M-Y, H:i", strtotime($date_end.' 23:59:59')); 
        $data['periode_ringkasan'] = date("d-M-Y", strtotime($date_start.' 00:00:00')).' sd '.date("d-M-Y", strtotime($date_end.' 23:59:59')); 
        $data['branch'] = $this->Branch_model->get_branch(1);
        
        $order = $this->input->get('order');
        $dir = $this->input->get('dir');
        $type = $this->input->get('type');        

        $limit  = null;
        $start  = null;
        $order  = $order;
        $dir    = $dir;
        $search = null;

        $params = array(
            'att_date_created >' => date("Y-m-d", strtotime($date_start)).' 00:00:00',
            'att_date_created <' => date("Y-m-d", strtotime($date_end)).' 23:59:59'
        );
        
        if(($type > 0) and ($type < 99)){
            $params['att_type'] = $type;
        }else if($type > 99){
            $params['att_type !='] = 3;
        }

        if(intval($userr) > 0){
            $params['att_user_id'] = intval($userr);
            $get_user = $this->User_model->get_user(intval($userr));
            $data['userr'] = $get_user;
        }

        // var_dump($params);die;
        $datas = $this->Attendance_model->get_all_attendance($params, $search, $limit, $start, $order, $dir);
        // var_dump($datas);die;
        $data['content'] = $datas;
        $data['title']  = 'Laporan Absensi Bergambar';
        // $data['_view']  = 'layouts/admin/menu/prints/absen';
        // $file_js        = 'layouts/admin/menu/prints/recap_js.php';        
        $this->load->view('layouts/admin/menu/prints/reports/report_absen_gambar',$data);
        // $this->load->view($file_js,$data);
    }
}

?>