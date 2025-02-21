<?php

defined('BASEPATH') OR exit('No direct script access allowed');



/* 

 * Niraj Kumar

 * Dated: 07/10/2017

 * 

 * This Controller is for Appointment List

 */



class Interaction_order extends Parent_admin_controller {


   function __construct() 

    {

        parent::__construct();
		$loggedData=logged_user_data();
		if(empty($loggedData)){
			redirect('user'); 
		}
        $this->load->model('order/order_model','order');    	
        $this->load->model('category/Category_model','category');
        $this->load->model('product/product_model','product');

        $this->load->model('doctor/Doctor_model','doctor');
		$this->load->model('dealer/Dealer_model','dealer');
		$this->load->model('permission/permission_model','permission');
		$this->load->model('pharmacy/pharmacy_model','pharmacy');
        
    }

    

    public function index(){    



		$data['title'] = "Interaction Order List";

        $data['page_name'] = "Interaction Order List";

		$data['order_list']=array();

		$orderList=$this->order->get_order_list();

		if($orderList!=FALSE)

		{

			$data['order_list'] =$orderList; 

		}

        $this->load->get_view('order/order_list_view',$data);	

    }



	public function complete_order_list(){    

		$data['title'] = "Complete Order List";

        $data['page_name'] = "Complete Order List";

		$data['order_list']=array();

		$orderList=$this->order->get_order_list();

		if($orderList!=FALSE)

		{

			$data['order_list'] =$orderList; 

		}

        $this->load->get_view('order/complete_order_list_view',$data);	

    }

	

	public function cancel_order_list(){    

		$data['title'] = "Cancel Order List";

        $data['page_name'] = "Cancel Order List";

		$data['order_list']=array();

		$orderList=$this->order->get_order_list();

		if($orderList!=FALSE)

		{

			$data['order_list'] =$orderList; 

		}

        $this->load->get_view('order/cancel_order_list_view',$data);	

    }

  public function products_list(){
        $data= $this->input->post();
        $productList= json_decode($this->order->products_list($data));
 
            $options='<option value="">---Select Product---</option>';
            foreach ($productList as $key => $value) {
                $options=$options.'<option value="'.$value->product_id.'">'.$value->product_name.'</option>';
            }
            echo $options;
    }
 
    public function products_list_get(){
       $data=$this->input->post();
       $productList=$this->order->products_list_get($data);
       echo $productList;   die;
    }
	

public function add_order($orderid='',$personId=''){
		$oId= urisafedecode($orderid);
		$pId= urisafedecode($personId);
        $data['order_id']=$oId;
		$data['person_id']=$pId;
		$data['category_list']=array();
		$data['title'] = "Product Details";
        $data['page_name'] = "Product Details";
		$productList= $this->product->get_product_active();
		if($productList!=FALSE)
		{
			$data['product_list'] = $productList; 
		}
		$categoryList= $this->category->get_active_category();
	//pr($categoryList); die;
		if($categoryList!=FALSE)
		{
			$data['category_list'] = $categoryList; 
		}
		$data['dealer_list'] = $this->dealer->dealer_list();
		$data['pharma_list']= $this->permission->pharmacy_list(logged_user_cities());
		$doctor_list= $this->doctor->edit_doctor($pId);
		$dealer_list = $this->dealer->edit_dealer($pId);
		$sub_dealer_list=$this->pharmacy->edit_pharmacy($pId);

		$userDealersOnly=$this->dealer->dealermaster_info();
		$UserDealers=json_decode($userDealersOnly);
		$all_sp_codes=explode(',',all_user_sp_code());
		$dealer_user=array();
		foreach($UserDealers as $deal_u){
			// if(in_array($deal_u->sp_code,$all_sp_codes)){
			// 	$dealer_user[]=$deal_u;
			// }
			$spcnt=explode(',',$deal_u->sp_code);
			if(count($spcnt) > 1){
				if(in_array($deal_u->sp_code[0],$all_sp_codes)){
					$dealer_user[]=$deal_u;
				}else{
					$dealer_user[]=$deal_u;
				}
			}else{
				if(in_array($deal_u->sp_code,$all_sp_codes)){
					$dealer_user[]=$deal_u;
				}
			}
		}
		$userPharmaOnly=$this->pharmacy->pharmacymaster_info();
		$UserPharmacy=json_decode($userPharmaOnly);
		$pharma_user=array();
		foreach ($UserPharmacy as $pharma_u){
			if(in_array($pharma_u->sp_code,$all_sp_codes)){
				$pharma_user[]=$pharma_u;
			}
		}
		$data['action'] = "order/interaction_order/add_product_interaction";
		if(!empty($doctor_list)){
			$data['sp_dealers']=$dealer_user;
			$data['sp_subDealers']=$pharma_user;
			$data['edit_list']=$doctor_list;
			$edit_list_data=json_decode($data['edit_list']);
			$pin=$edit_list_data->id;
			$D_pin=explode('_',$pin);
			$pincount=count($D_pin);
			if($pincount <= 1){
				$this->load->get_view('order/select_product_view',$data);
			}
		}else if(!empty($sub_dealer_list)){
			$data['sp_dealers']=$dealer_user;
			$data['sp_subDealers']=$pharma_user;
			$data['edit_list']= $sub_dealer_list;
			$data['doc_rel_pharma']=$this->pharmacy->get_pharmacy_doc($pId);
			$edit_list_data=json_decode($data['edit_list']);
			$pin=$edit_list_data->id;
			$D_pin=explode('_',$pin);
			$pincount=count($D_pin);
			if($pincount > 1){
				$this->load->get_view('order/select_product_view',$data);
			}
		}else{
			$data['sp_dealers']=$dealer_user;
			$data['sp_subDealers']=$pharma_user;
			$data['edit_list']=$dealer_list;
			$edit_list_data=json_decode($data['edit_list']);
			$pin=$edit_list_data->d_id;
			$D_pin=explode('_',$pin);
			$pincount=count($D_pin);
			if($pincount >= 1){
				$this->load->get_view('order/pharma_product_view',$data);
			}
		}

    }


  //   public function add_order($orderid='',$personId=''){
        
		// $oId= urisafedecode($orderid);
		// $pId= urisafedecode($personId);
  //       $data['order_id']=$oId;
		// $data['person_id']=$pId;
		// $data['category_list']=array();
		// $data['title'] = "Product Details";
  //       $data['page_name'] = "Product Details";
		// $productList= $this->product->get_product_active();
		// if($productList!=FALSE)
		// {
		// 	$data['product_list'] = $productList; 
		// }
		// $categoryList= $this->category->get_active_category();
		// if($categoryList!=FALSE)
		// {
		// 	$data['category_list'] = $categoryList; 
		// }
		
		// $data['action'] = "order/interaction_order/add_product_interaction"; 
		// $this->load->get_view('order/select_product_view',$data);
		
  //   }



  //   public function add_order($orderid='',$personId=''){
        
		// $oId= urisafedecode($orderid);
		// $pId= urisafedecode($personId);
  //       $data['order_id']=$oId;
		// $data['person_id']=$pId;
		// $data['category_list']=array();
		// $data['title'] = "Product Details";
  //       $data['page_name'] = "Product Details";
		// $productList= $this->product->get_product_active();
		// if($productList!=FALSE)
		// {
		// 	$data['product_list'] = $productList; 
		// }
		// $categoryList= $this->category->get_active_category();
		// if($categoryList!=FALSE)
		// {
		// 	$data['category_list'] = $categoryList; 
		// }
		// $data['dealer_list'] = $this->dealer->dealer_list();
		// $data['pharma_list']= $this->permission->pharmacy_list(logged_user_cities());
		// $doctor_list= $this->doctor->edit_doctor($pId);

		// if(!empty($doctor_list)){
		// 	$data['edit_list']=$doctor_list;
		// }else{
		// 	$data['edit_list']= $this->pharmacy->edit_pharmacy($pId);
		// 	$data['doc_rel_pharma']=$this->pharmacy->get_pharmacy_doc($pId);
		// }
		// $data['action'] = "order/interaction_order/add_product_interaction";

		// $edit_list_data=json_decode($data['edit_list']);
		// $pin=$edit_list_data->id;
		// $D_pin=explode('_',$pin);
		// $pincount=count($D_pin);

		// if($pincount <= 1){
		// 	$this->load->get_view('order/select_product_view',$data);
		// }
		// if($pincount >= 2){
		// 	$this->load->get_view('order/pharma_product_view',$data);
		// }

		
  //   }

    public function test($orderid='',$personId=''){
        $data['order_id']=$oId;
		$data['person_id']=$pId;
		$data['category_list']=array();
		$data['title'] = "Product Details";
        $data['page_name'] = "Product Details";
		$productList= $this->product->get_product_active();
		if($productList!=FALSE)
		{
			$data['product_list'] = $productList; 
		}
		$categoryList= $this->category->get_active_category();
		if($categoryList!=FALSE)
		{
			$data['category_list'] = $categoryList; 
		}
		$checkOrder=$this->order->check_order($oId,$pId);
	
		$data['action'] = "order/interaction_order/add_product_interaction"; 
		$this->load->get_view('order/test',$data);
		
    }

	
	public function get_product_packsize_list(){
	    
		$catId= $this->input->post('id');
		
		$options='<option value="">---Select Packsize---</option>';
		$packsize= json_decode($this->order->get_cat_packsize($catId));
		foreach ($packsize as $key => $value) {
			$options=$options.'<option value="'.$value->product_packsize.'">'.$value->packsize_value.'</option>';
		}
		
		echo $options;
	}

	public function get_product_potency_list(){
		$catId= $this->input->post('id');
		$options='<option value="">---Select Potency---</option>';
		$potency= json_decode($this->order->get_cat_potency($catId));
		foreach ($potency as $key => $value) {
			# code...
				$options=$options.'<option value="'.$value->product_potency.'">'.$value->potency_value.'</option>';
		}
		echo $options;
	}

	public function product_select(){
		$post_data = $this->input->post();
		$data['interaction_order_id']=0;
		$this->form_validation->set_rules('category_list[]', 'Atleast One Category', "required");
		if($this->form_validation->run() == TRUE){
			$productIds=array();
			$data['category_list']=array();
			$data['order_id']=$post_data['order_id'];

			$data['person_id']=$post_data ['person_id'];

			foreach($post_data ['category_list']as $category)

			{

				if(isset($post_data ['product_list_'.$category]))

				{

					foreach($post_data ['product_list_'.$category]as $product)

					{

						$productIds[]=$product;

					}

				}

			}

			

			if(count($productIds)==0)

			{ // no product

			   set_flash('<div class="alert alert-danger alert-dismissible">

				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>

				<h4><i class="icon fa fa-ban"></i> Alert!</h4>

				Please select atleast one product.</div>');

				redirect('order/interaction_order/add_order/'.urisafeencode($post_data['order_id']).'/'.urisafeencode($post_data['person_id']));

			}

			$data['title'] = "Product List";

			$data['page_name'] = "Product List";

			$data['product_list'] = $productIds; 

			

			if(isset($post_data['interaction_order_id']))

			{

				$data['action'] = "order/interaction_order/product_discount_edit"; 

				$data['interaction_order_id']=$post_data['interaction_order_id'];

				$this->load->get_view('order/product_discount_view_edit',$data);

			}

			else{

				$data['action'] = "order/interaction_order/product_discount"; 

				$this->load->get_view('order/product_discount_view',$data);

			}

			

		}

		else{

			$this->add_order(urisafeencode($post_data['order_id']),urisafeencode($post_data['person_id']));

		}



    }

	public function add_product_interaction(){
		$post_data = $this->input->post();
                // pr($post_data); die;
		foreach($post_data['pro_mrp_val'] as $k=>$val)
		{
			if($val!='')
			{
				if($post_data['pro_qnty'][$k]==''|| $post_data['pro_dis'][$k]=='')
				{
					set_flash('<div class="alert alert-danger alert-dismissible">
	                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
	                <h4><i class="icon fa fa-ban"></i> Alert!</h4>Please Select Product quantity and Discount both!!
	              	</div>');
	              	redirect($_SERVER['HTTP_REFERER']);
				}
			}
		}
		$this->form_validation->set_rules('category_list[]', 'Atleast One Category', "required");
		$this->form_validation->set_rules('payment_mode', 'Payment Mode', "required");
                $this->form_validation->set_rules('payment_term', 'Payment Term', "required");
                if($post_data['payment_term']==1){
                    $this->form_validation->set_rules('payment', 'Payment Terms with No of days', "required");
                }
                

//                if(!in_array(11,$post_data['category_list']) &&  !in_array(7,$post_data['category_list'])){
                    
                      if(!isset($post_data['product_name']))
                      {
                              $this->form_validation->set_rules('product_name[]', 'Atleast One Product', "required");
                      }

                      if(!array_filter($post_data['product_name']))
                      {
                              $this->form_validation->set_rules('product_name[]', 'Atleast One Product', "required");
                      }   
                      
//                }


		if($this->form_validation->run() == TRUE){

			$success=$this->order->save_product_interaction($post_data);
			if($success==1){  // on sucess
				if(!is_numeric($post_data['person_id']))
				{
					if(substr($post_data['person_id'],0,3)=='doc'){
						redirect('doctors/doctor/doctor_interaction_sales/'.urisafeencode($post_data['person_id']));
					}
					else{
						redirect('pharmacy/pharmacy/pharma_interaction_sales/'.urisafeencode($post_data['person_id']));
					}

				}
				else{
					redirect('dealer/dealer/dealer_interaction_sales/'.urisafeencode($post_data['person_id']));
				}
		   }

		   else{ // on fail

	

				if(!is_numeric($post_data['person_id']))

				{

					if(substr($post_data['person_id'],0,3)=='doc'){

					redirect('doctors/doctor/doctor_interaction_sales/'.urisafeencode($post_data['person_id']));

					}

					else{
						redirect('pharmacy/pharmacy/pharma_interaction_sales/'.urisafeencode($post_data['person_id']));

					}

				}
				else{
					redirect('dealer/dealer/dealer_interaction_sales/'.urisafeencode($post_data['person_id']));
				}

		   }

		}
		else
		{
			set_flash('<div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>Please Select atleast Product or Category !!
              </div>');
              redirect($_SERVER['HTTP_REFERER']);
		}
		/*pr($post_data);
		die;*/
	}

	public function product_discount(){

		$post_data = $this->input->post();
		$success=$this->order->save_order($post_data);

		if($success==1){  // on sucess
				if(!is_numeric($post_data['person_id']))
				{

					if(substr($post_data['person_id'],0,3)=='doc'){

					redirect('doctors/doctor/doctor_interaction_sales/'.urisafeencode($post_data['person_id']));

					}
					else{
						redirect('pharmacy/pharmacy/pharma_interaction_sales/'.urisafeencode($post_data['person_id']));
					}
				}
				else{
					redirect('dealer/dealer/dealer_interaction_sales/'.urisafeencode($post_data['person_id']));
				}
		   }

		   else{ // on fail

	
				if(!is_numeric($post_data['person_id']))

				{

					if(substr($post_data['person_id'],0,3)=='doc'){

					redirect('doctors/doctor/doctor_interaction_sales/'.urisafeencode($post_data['person_id']));

					}

					else{
						redirect('pharmacy/pharmacy/pharma_interaction_sales/'.urisafeencode($post_data['person_id']));
					}

				}
				else{
					redirect('dealer/dealer/dealer_interaction_sales/'.urisafeencode($post_data['person_id']));
				}

		   }

    }

	

	public function product_discount_edit(){

		$post_data = $this->input->post();
		$success=$this->order->edit_save_order($post_data);

		if($success==1){  // on sucess
				if(!is_numeric($post_data['person_id']))
				{
					if(substr($post_data['person_id'],0,3)=='doc'){
						redirect('interaction/edit_doc_interaction/'.urisafeencode($post_data['order_id']));
					}

					else{
						redirect('interaction/edit_pharma_interaction/'.urisafeencode($post_data['order_id']));
					}

				}
				else{
					redirect('interaction/edit_dealer_interaction/'.urisafeencode($post_data['order_id']));
				}

			   

		   }

		   else{ // on fail

			   if(!is_numeric($post_data['person_id']))

				{

					if(substr($post_data['person_id'],0,3)=='doc'){
						redirect('doctors/doctor/doctor_interaction_sales/'.urisafeencode($post_data['person_id']));
					}

					else{
						redirect('pharmacy/pharmacy/pharma_interaction_sales/'.urisafeencode($post_data['person_id']));

					}
				}
				else{
					redirect('dealer/dealer/dealer_interaction_sales/'.urisafeencode($post_data['person_id']));
				}
		   }

    }

	

	public function get_product_list(){

		$data= $this->input->post();
		
		$productList= json_decode($this->order->get_cat_product($data));
//                pr($productList); die;
                if(($data['catid']==11 || $data['catid']==7) && count($productList)<2 ){
                     echo $productList[0]->product_id; die;

                }else{
                     $options='<option value="">---Select Product---</option>';
		foreach ($productList as $key => $value) {
			$options=$options.'<option value="'.$value->product_id.'">'.$value->product_name.'</option>';
		}
		 echo $options;
//                  
                }
		//echo json_encode($productList);

    }

    public function get_product_details($productid=''){
		
//            if(empty($productid)){
                 $productId= $this->input->post('productid');
//            }else{
//                $productId = $productid;
//            }
                               
		$productList= $this->order->get_single_product_details($productId);
		echo json_encode($productList);
		die;
    }
	

	

	public function complete_order($orderid='',$personId=''){

		$oId= urisafedecode($orderid);

		$pId= urisafedecode($personId);

		$success=$this->order->complete_order($oId,$pId);

		if($success==1){  // on sucess
				set_flash('<div class="alert alert-success alert-dismissible">

				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>

				<h4><i class="icon fa fa-check"></i> Success!</h4>

				Order Successfully Completed. </div>'); 

				redirect('order/interaction_order/index');
		}
		else{ // on fail
		   set_flash('<div class="alert alert-danger alert-dismissible">
 
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>

			<h4><i class="icon fa fa-ban"></i> Alert!</h4>

			Order can not completed.</div>');

			redirect('order/interaction_order/index');

		}

    }

	

	

	public function cancel_order($orderid='',$personId='',$price=''){

		$oId= urisafedecode($orderid);

		$pId= urisafedecode($personId);

		$amount= urisafedecode($price);

		$success=$this->order->cancel_order($oId,$pId,$amount);

		if($success==1){  // on sucess

			

				set_flash('<div class="alert alert-success alert-dismissible">

				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>

				<h4><i class="icon fa fa-check"></i> Success!</h4>

				Order Successfully Canceled. </div>'); 

				redirect('order/interaction_order/index');

			   

		}

		else{ // on fail

		   set_flash('<div class="alert alert-danger alert-dismissible">

			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>

			<h4><i class="icon fa fa-ban"></i> Alert!</h4>

			Order can not canceled.</div>');

			redirect('order/interaction_order/index');

		}

    }

	

	public function view_order($orderid='',$personId=''){  

		$oId= urisafedecode($orderid);

		$pId= urisafedecode($personId);

		$data['title'] = "Product Details View";

        $data['page_name'] = "Product Details View";

		$data['order_details']=array();

		$data['order_interaction']=array();

		$orderInteraction=$this->order->get_order($oId,$pId);

		if($orderInteraction!=FALSE)

		{

			$data['order_interaction'] =$orderInteraction; 

		}
		
		$orderDetails=$this->order->get_interaction_order_details($orderInteraction[0]['id']);

		if($orderDetails!=FALSE)

		{

			$data['order_details'] =$orderDetails; 

		}

		

        $this->load->get_view('order/order_details_view',$data);	

    }



}



?>