<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';




class Interaction extends REST_Controller {
    function __construct() {
    // Construct the parent class
        parent::__construct();
        $this->load->model('api/interaction_api_model','interact');
        $this->load->model('doctor/Doctor_model','doctor');
        $this->load->model('dealer/Dealer_model','dealer');
        $this->load->model('pharmacy/Pharmacy_model','pharmacy');
        $this->load->model('users/User_model','user');
    
    }
     
	

     /* Nitin kumar
     	!!.Code Starts Here.!!
     */

/*After clicking over Add Secondary/Add Product*/
	function log_interaction_details_post(){
		 $msg='';
		 # initialize variables
		 $interaction_data=json_decode($this->input->raw_input_stream);

		 if (!isset($interaction_data->user_id) || empty($interaction_data->user_id)){
			 $msg = 'Please enter User ID';
		 }
		 if (!isset($interaction_data->city) || empty($interaction_data->city)){
			 $msg = 'Please enter City of Interaction';
		 }
		 if (!isset($interaction_data->doc_name) || empty($interaction_data->doc_name)){
			 $msg = 'Please enter Name of doctor';
		 }
		 if (!isset($interaction_data->dealer_view_id) || empty($interaction_data->dealer_view_id)){
			 $msg = 'Please enter Doctor / Dealer ID';
		 }
		 if (!isset($interaction_data->doc_id) || empty($interaction_data->doc_id)){
			 $msg = 'Please enter Doctor / Dealer ID';
		 }
		 if (!isset($interaction_data->doi_doc) || empty($interaction_data->doi_doc)){
			 $msg = 'Please enter Date of Interaction';
		 }
//		  if (!isset($interaction_data->team_member) || empty($interaction_data->team_member)){
//			  $msg = 'Please enter Joint Interaction with. ID Only';
//		  }
//		  if (!isset($interaction_data->m_sample) || empty($interaction_data->m_sample)){
//			  $msg = 'Please enter Samples. ID Only';
//		  }
//		 if (!isset($interaction_data->telephonic) || empty($interaction_data->telephonic)){
//			 $msg = 'Please enter telephonic / Not';
//		 }
//		 if (!isset($interaction_data->remark) || empty($interaction_data->remark)){
//			 $msg = 'Please enter Remarks of Interaction';
//		 }
//		 if (!isset($interaction_data->fup_a) || empty($interaction_data->fup_a)){
//			 $msg = 'Please enter Date of Interaction';
//		 }


		 if($msg == ''){
//			 pr($interaction_data); die;
			 $data = $this->interact->interaction_log_details($interaction_data);
			 if($data!=FALSE){
				 $result = array(
					 'Data' => $data,
					 // 'Status' => true,
					 'Message' => 'Logs saved successfully',
					 'Code' => 200
				 );
			 }
			 else{
				 $result = array(
					 'Data' => new stdClass(),
					 'Status' => false,
					 'Message' => 'Error in Save Logs.',
					 'Code' => 404
				 );
			 }
		 }
		 else{
			 $result = array(
				 'Data' => new stdClass(),
				 'Status' => false,
				 'Message' => $msg,
				 'Code' => 404
			 );
		 }
		 $this->response($result);


	 }

/*Single Interaction   Doctor / Sub Dealer / Dealer*/
	 function single_interaction_post(){

		 $orderData='';
		 $emailordt='';
		 $emailorderdata='';
		 $subject="Interaction Email.";
		 $msg = '';
		 $dealerEmail='';
		 $interaction_data=json_decode($this->input->raw_input_stream);
/*
 * doc_id
 * pharma_id
 * dealer_id ==> supplier_dealer_id  (Third party Dealer Supplier)
 * */
//pr($interaction_data); die;

			 if((isset($interaction_data->stay)) && (isset($interaction_data->up)) ) {
				 if ( ($interaction_data->stay == 0) && ($interaction_data->up == 0) ||
					 ($interaction_data->stay == 1) && ($interaction_data->up == 1) ||
					 ($interaction_data->stay == 1) && ($interaction_data->up == 0) ||
					 ($interaction_data->stay == 0) && ($interaction_data->up == 1)
				 )
				 {
					 $dealerNumber = '';
					 $dealerEmail = '';
					 $docNumber = '';
					 $docEmail = '';
					 $pharmacyNumber = '';
					 $pharmacyEmail = '';
					 $sms = '';
					 $emailbody = '';
					 $orderData = '';
					 $total_cost = 0;
					 $emailorderdata = '';
					 $emailordt = '';
					 $message = '';
					 $subject = "Interaction Email.";
					 $emailMessage = 'Dear ,
						Greetings,
			
						Many Thanks!
					   ---------------------------- 
						BJAIN Pharmaceutical Pvt Ltd
						A 98 Sector 63, Noida
						201301, Uttar Pradesh 
						Tel: +91-120-49 33 333';

//					 pr($interaction_data); die;
					 $senderemail = get_user_email($interaction_data->user_id);

					 if (isset($interaction_data->m_sale))//only product
					 {
						 $sms = 'Thank you Dear Doctor for your support to B. Jain Pharma. I am happy to receive your order which is mentioned below.';
						 $orderDetails = $this->dealer->get_orderdeatils_user($interaction_data);
//pr($orderDetails); die;
						 if(!empty($orderDetails)){
							 foreach ($orderDetails as $details) {
								 $orderData = $orderData .' ' .get_product_name($details['product_id']) . '(' .
									 get_packsize_name($details['product_id']).',quantity=' . $details['quantity'].'.';

								 $total_cost = $total_cost + $details['net_amount'];

								 $emailordt = $emailordt . '<tr><td>' . get_product_name($details['product_id']) . '(' . get_packsize_name($details['product_id']) . ')</td><td>' . $details['quantity'] . '</td></tr>';

							 }
							 $emailorderdata = ' <h2>Your Order Details</h2> <table cellspacing="0" cellpadding="5" border="1" style="width:100%; border-color:#222;" ><thead><tr><th>Product</th><th>Qty.</th> </tr></thead> 
        <tbody>' . $emailordt . '</tbody></table> ';
						 }
						 // else{
							//  $result = array(
							//  'Data' => new stdClass(),
							//   'Message' => 'Alert.! No Order Found on this Date for this Doctor.',
							//  // 'Message' => 'Alert.! You Already Interacted with same person on same date.',
							//  'Code' => 500
							//  );
							//  $this->response($result);
						 // }

					 }

					 if ((isset($interaction_data->telephonic)) || (isset($interaction_data->meet_or_not)) ) {
						 $interactionDate = $interaction_data->doi_doc;
						 $user_id=$interaction_data->user_id;

						 $result_leave = $this->dealer->checkleave($interactionDate,$user_id);
						 if(!$result_leave) {
							 $result = array(
							 'Data' => new stdClass(),
							 'Message' => 'Alert.! You have taken leave  or holiday on that day please change date!!',
							 'Code' => 500
							 );
							 $this->response($result);
//							 return "Alert.! You have taken leave  or holiday on that day please change date!!";
						 }
					 }

					 if(($interaction_data->telephonic=='0') || ($interaction_data->telephonic=='1') ) {
					 if((empty($interaction_data->m_sale)) ){
//						 return "Alert! Add Secondary / Add Product is Mandatory after  - Order Received.";
						 $result = array(
							 'Data' => new stdClass(),
							 'Message' => 'Alert! Add Secondary / Add Product is Mandatory after  - Order Received.',
							 'Code' => 500
						 );
						 $this->response($result);
							 }
						 }

					 if (!empty($interaction_data->m_sale) || !empty($interaction_data->m_payment) || !empty
						 ($interaction_data->m_stock) || !empty($interaction_data->m_sample) || (isset
							 ($interaction_data->meet_or_not) || !empty($interaction_data->meet_or_not)) || (!empty
						 ($interaction_data->telephonic)) ) {

						 $id = urisafeencode($interaction_data->dealer_view_id);

						 $success = $this->dealer->save_interaction($interaction_data);
						 if ($success == 1) {
							 $this->dealer->insert_ta_da($interaction_data);
							 if (isset($interaction_data->doc_name)) {
								 //for doctor side
								 if (isset($interaction_data->dealer_id)) {
									 if (is_numeric($interaction_data->dealer_id)) {
										 //for dealer;
										 $data = $this->dealer->get_dealer_data($interaction_data->dealer_id);
										 if ($data != FALSE) {
											 $dealerNumber = $data->d_phone;
											 $dealerEmail = $data->d_email;
										 }
									 } else {
										 //for pharmacy;
										 $data = $this->pharmacy->get_pharmacy_data($interaction_data->dealer_id);
										 if ($data != FALSE) {
											 $dealerNumber = $data->company_phone;
											 $dealerEmail = $data->company_email;
										 }
									 }
								 }
								 $docdata = $this->doctor->get_doctor_data($interaction_data->doc_id);
								 if ($docdata != FALSE) {
									 $docNumber = $docdata->doc_phone;
									 $docEmail = $docdata->doc_email;
								 }
								 //send_msg('1','8604111305','8604111305');
								 //	send_msg($message,$docNumber,$dealerNumber);
								 //send message to pharmacy/dealer and doctor
								 try {
									 if (isset($interaction_data->meet_or_not)) {
										 if ($interaction_data->meet_or_not == 1) {
											 $sms = 'Thank you Dear Doctor for your valuable time. We look forward to your kind support for B. Jain’s Product.';//but no sale or sample
											 $emailbody = '<html><head><title>BJain Pharmaceuticals</title><style type="text/css">body{padding:0;margin:0;font-family: calibri;}.content{ width:40%; margin:0 auto;}.regards_box{float:left;margin-top:20px;}p.user_name_brand{ margin:0px;}h3.user_name_regards{ margin:0px;padding-bottom:10px;}img.email_logo{ margin:15px 0px;}</style></head><body><div class="content"><center><img src="' . base_url() . '/design/bjain_pharma/bjain_logo.png" class="email_logo" style="width:250px;" /></center>  
		  <h3>Dear,</h3> <p>' . $sms . '</p><p><i>This is an auto generated email.</i></p>
		  <div class="regards_box"><h3 class="user_name_regards">Regards,</h3><p class="user_name_brand">' . get_user_name($interaction_data->user_id) . '<br>BJain Pharmaceuticals Pvt. Ltd.</p></div></div></body></html>';
//                    echo $sms; die;
											 send_msg($sms, $docNumber);
										 }
										 else {
											 $sms = 'Doctor I visited your clinic today but was unable to meet you. May I request you for a suitable time for a meeting when I can see you.';
											 $emailbody = '<html><head><title>BJain Pharmaceuticals</title><style type="text/css">body{padding:0;margin:0;font-family: calibri;} .content{ width:40%; margin:0 auto;}.regards_box{float:left;margin-top:20px;}p.user_name_brand{ margin:0px;}h3.user_name_regards{
		  margin:0px;padding-bottom:10px;}img.email_logo{ margin:15px 0px;}</style></head><body><div class="content"><center><img src="' . base_url() . '/design/bjain_pharma/bjain_logo.png" class="email_logo" style="width:250px;" /></center>  
		  <h3>Dear,</h3> <p>' . $sms . '</p><p><i>This is an auto generated email.</i></p>
		  <div class="regards_box"><h3 class="user_name_regards">Regards,</h3><p class="user_name_brand">' . get_user_name($interaction_data->user_id) . '<br>BJain Pharmaceuticals Pvt. Ltd.</p></div></div></body></html>';
											 send_msg($sms, $docNumber);
										 }
									 } else // for sale
									 {
										 if (isset($interaction_data->m_sale) && isset($interaction_data->m_sample)) {
											 $sms = 'Thank you Dear Doctor for your support to B. Jain Pharma. Please give your valuable feedback for provided samples. I am happy to receive your order which is mentioned below.';
											 $sms1 = $sms;
											 $sms = $sms . ' ' . $orderData;
											 $emailbody = '<html><head><title>BJain Pharmaceuticals</title><style type="text/css">body{padding:0;margin:0;font-family: calibri;}.content{ width:40%; margin:0 auto;}.regards_box{float:left;margin-top:20px;}p.user_name_brand{ margin:0px;}h3.user_name_regards{
		 margin:0px;padding-bottom:10px;}img.email_logo{ margin:15px 0px;}</style></head><body><div class="content"><center><img src="' . base_url() . '/design/bjain_pharma/bjain_logo.png" class="email_logo" style="width:250px;" /></center>  
	  <h3>Dear,</h3> <p>' . $sms1 . '</p>' . $emailorderdata . '<p><i>This is an auto generated email.</i></p>
	  <div class="regards_box"><h3 class="user_name_regards">Regards,</h3><p class="user_name_brand">' . get_user_name($interaction_data->user_id) . '<br>BJain Pharmaceuticals Pvt. Ltd.</p></div></div></body></html>';


											 $dealerSms = 'Dear Dealer/Sub Dealer, we have received an order from Dr.' . $interaction_data->doc_name . '  Kindly deliver at mentioned time and discount.The order details are mentioned below. ' . $orderData;

											 $dealerSms1 = 'Dear Dealer/Sub Dealer, we have received an order from Dr.' . $interaction_data->doc_name . '  Kindly deliver at mentioned time and discount.The order details are mentioned below. ';

											 $dealeremailbody = '<html><head><title>BJain Pharmaceuticals</title><style type="text/css">body{padding:0;margin:0;font-family: calibri;}.content{ width:40%; margin:0 auto;}.regards_box{float:left;margin-top:20px;}p.user_name_brand{ margin:0px;}h3.user_name_regards{
		 margin:0px;padding-bottom:10px;}img.email_logo{ margin:15px 0px;}</style></head><body><div class="content"><center><img src="' . base_url() . '/design/bjain_pharma/bjain_logo.png" class="email_logo" style="width:250px;" /></center>  
	  <h3>Dear,</h3> <p>' . $dealerSms1 . '</p>' . $emailorderdata . '<p><i>This is an auto generated email.</i></p>
	  <div class="regards_box"><h3 class="user_name_regards">Regards,</h3><p class="user_name_brand">' . get_user_name($interaction_data->user_id) . '<br>BJain Pharmaceuticals Pvt. Ltd.</p></div></div></body></html>';
											 /*Dealer message or email*/
											 if ($interaction_data->dealer_mail == 1) {
												 send_msg($dealerSms, $dealerNumber);
												 //send_msg($dealerSms,'7838359383');
												 if ($dealerEmail != '') {
													 $success = send_email($dealerEmail, $senderemail, $subject, $dealeremailbody);//send message to pharmacy/dealer
												 }
											 }
										 }
										 else if (isset($interaction_data->m_sample))// only sample
										 {
											 $sms = 'Thank you Dear Doctor for your valuable time. Kindly give your feedback for samples.';
											 $emailbody = '<html><head><title>BJain Pharmaceuticals</title><style type="text/css">body{padding:0;margin:0;font-family: calibri;}.content{ width:40%; margin:0 auto;}.regards_box{float:left;margin-top:20px;}p.user_name_brand{ margin:0px;}h3.user_name_regards{
  margin:0px;padding-bottom:10px;}img.email_logo{ margin:15px 0px;}</style></head><body><div class="content"><center><img src="' . base_url() . '/design/bjain_pharma/bjain_logo.png" class="email_logo" style="width:250px;" /></center>  
  <h3>Dear,</h3> <p>' . $sms . '</p><p><i>This is an auto generated email.</i></p>
  <div class="regards_box"><h3 class="user_name_regards">Regards,</h3><p class="user_name_brand">' . get_user_name($interaction_data->user_id) . '<br>BJain Pharmaceuticals Pvt. Ltd.</p></div></div></body></html>';
										 } else if (isset($interaction_data->m_sale))//only product
										 {
											 $sms = 'Thank you Dear Doctor for your support to B. Jain Pharma. I am happy to receive your order which is mentioned below.';
											 $sms1 = $sms;
											 $sms = $sms . ' ' . $orderData;
											 $emailbody = '<html><head><title>BJain Pharmaceuticals</title><style type="text/css">body{padding:0;margin:0;font-family: calibri;}.content{ width:40%; margin:0 auto;}.regards_box{float:left;margin-top:20px;}p.user_name_brand{ margin:0px;}h3.user_name_regards{
		 margin:0px;padding-bottom:10px;}img.email_logo{ margin:15px 0px;}</style></head><body><div class="content"><center><img src="' . base_url() . '/design/bjain_pharma/bjain_logo.png" class="email_logo" style="width:250px;" /></center>  
	  <h3>Dear,</h3> <p>' . $sms1 . '</p>' . $emailorderdata . '<p><i>This is an auto generated email.</i></p>
	  <div class="regards_box"><h3 class="user_name_regards">Regards,</h3><p class="user_name_brand">' . get_user_name($interaction_data->user_id) . '<br>BJain Pharmaceuticals Pvt. Ltd.</p></div></div></body></html>';


											 $dealerSms = 'Dear Dealer/Sub Dealer, we have received an order from Dr.' . $interaction_data->doc_name . '  Kindly deliver at mentioned time and discount.The order details are mentioned below. ' . $orderData;

											 $dealerSms1 = 'Dear Dealer/Sub Dealer, we have received an order from Dr.' . $interaction_data->doc_name . '  Kindly deliver at mentioned time and discount.The order details are mentioned below. ';

											 $dealeremailbody = '<html><head><title>BJain Pharmaceuticals</title><style type="text/css">body{padding:0;margin:0;font-family: calibri;}.content{ width:40%; margin:0 auto;}.regards_box{float:left;margin-top:20px;}p.user_name_brand{ margin:0px;}h3.user_name_regards{
		 margin:0px;padding-bottom:10px;}img.email_logo{ margin:15px 0px;}</style></head><body><div class="content"><center><img src="' . base_url() . '/design/bjain_pharma/bjain_logo.png" class="email_logo" style="width:250px;" /></center>  
	  <h3>Dear,</h3> <p>' . $dealerSms1 . '</p>' . $emailorderdata . '<p><i>This is an auto generated email.</i></p>
	  <div class="regards_box"><h3 class="user_name_regards">Regards,</h3><p class="user_name_brand">' . get_user_name($interaction_data->user_id) . '<br>BJain Pharmaceuticals Pvt. Ltd.</p></div></div></body></html>';
											 // send_msg($dealerSms,'7838359383');
											 /*Dealer */
											 if ($interaction_data->dealer_mail == 1) {
												 send_msg($dealerSms, $dealerNumber);
												 if ($dealerEmail != '') {
													 $success = send_email($dealerEmail, $senderemail, $subject, $dealeremailbody);//send message to pharmacy/dealer
												 }
											 }
										 }


									 }
									 //send_msg($sms,'8604111305');
									 send_msg($sms, $docNumber);
									 if ($docEmail != '') {
										 $success = send_email($docEmail, $senderemail, $subject, $emailbody);//send message to doctor	//send message to doctor
										 //send_email('niraj@bjain.com', $senderemail, $subject, $emailbody);
									 }
								 }
								 catch (Exception $e) {
									 $result = array(
										 'Data' => $e,
										 'Message' => 'Alert! Something went wrong.',
										 'Code' => 500
										 );
									 $this->response($result);
//									 return "Alert! Something went wrong.";
								 }

							 }
							 elseif (isset($interaction_data->pharma_id)) {

								 //for Pharma side
								 if (isset($interaction_data->dealer_id)) {
									 /* $data=$this->dealer->get_dealer_data($interaction_data['dealer_id']);
												$dealerNumber=$data->d_phone;
												$dealerEmail=$data->d_email;
											}*/
									 if (is_numeric($interaction_data->dealer_id)) {
										 //for dealer;
										 $data = $this->dealer->get_dealer_data($interaction_data->dealer_id);
										 if ($data != FALSE) {
											 $dealerNumber = $data->d_phone;
											 $dealerEmail = $data->d_email;
										 }
									 } else {
										 //for pharmacy;
										 $data = $this->pharmacy->get_pharmacy_data($interaction_data->dealer_id);
										 if ($data != FALSE) {
											 $dealerNumber = $data->company_phone;
											 $dealerEmail = $data->company_email;
										 }
									 }

								 }

								 $dataPharmacy = $this->pharmacy->get_pharmacy_data($interaction_data->pharma_id);
								 if ($dataPharmacy != FALSE) {
									 $pharmacyNumber = $dataPharmacy->company_phone;
									 $pharmacyEmail = $dataPharmacy->company_email;
								 }


								 //send_msg('5','8604111305','8604111305');
								 if ($interaction_data->dealer_mail == 1) {
									 send_msg($message, $dealerNumber, $pharmacyNumber); //send message to dealer & dealer
								 }
								 try {

									 //send_email('niraj@bjain.com', $subject, $message);
									 if (isset($interaction_data->meet_or_not)) {
										 if ($interaction_data->meet_or_not == 1) {
											 # code...
											 $sms = 'Thank you Dear Sub Dealer for your valuable time. We look forward to your kind support for B. Jain’s Product.';//but no sale or sample
											 $emailbody = '<html><head><title>BJain Pharmaceuticals</title><style type="text/css">body{padding:0;margin:0;font-family: calibri;}.content{ width:40%; margin:0 auto;}.regards_box{float:left;margin-top:20px;}p.user_name_brand{ margin:0px;}h3.user_name_regards{ margin:0px;padding-bottom:10px;}img.email_logo{ margin:15px 0px;}</style></head><body><div class="content"><center><img src="' . base_url() . '/design/bjain_pharma/bjain_logo.png" class="email_logo" style="width:250px;" /></center>  
	<h3>Dear,</h3> <p>' . $sms . '</p><p><i>This is an auto generated email.</i></p>
	<div class="regards_box"><h3 class="user_name_regards">Regards,</h3><p class="user_name_brand">' . get_user_name($interaction_data->user_id) . '<br>BJain Pharmaceuticals Pvt. Ltd.</p></div></div></body></html>';
										 } else {
											 $sms = 'Sub Dealer I visited today but was unable to meet you. May I request you for a suitable time for a meeting when I can see you.';
											 $emailbody = '<html><head><title>BJain Pharmaceuticals</title><style type="text/css">body{padding:0;margin:0;font-family: calibri;}.content{ width:40%; margin:0 auto;}.regards_box{float:left;margin-top:20px;}p.user_name_brand{ margin:0px;}h3.user_name_regards{ margin:0px;padding-bottom:10px;}img.email_logo{ margin:15px 0px;}</style></head><body><div class="content"><center><img src="' . base_url() . '/design/bjain_pharma/bjain_logo.png" class="email_logo" style="width:250px;" /></center>  
	<h3>Dear,</h3> <p>' . $sms . '</p><p><i>This is an auto generated email.</i></p>
	<div class="regards_box"><h3 class="user_name_regards">Regards,</h3><p class="user_name_brand">' . get_user_name($interaction_data->user_id) . '<br>BJain Pharmaceuticals Pvt. Ltd.</p></div></div></body></html>';
										 }
									 } else // for sale
									 {
										 if (isset($interaction_data->m_sale) && isset($interaction_data->m_sample)) {

											 $sms = 'Thank you Dear Sub Dealer for your support to B. Jain Pharma. Please give your valuable feedback for provided samples. I am happy to receive your order which is mentioned below.';
											 $sms1 = $sms;
											 $sms = $sms . ' ' . $orderData;

											 $emailbody = '<html><head><title>BJain Pharmaceuticals</title><style type="text/css">body{padding:0;margin:0;font-family: calibri;}.content{ width:40%; margin:0 auto;}.regards_box{float:left;margin-top:20px;}p.user_name_brand{ margin:0px;}h3.user_name_regards{
		 margin:0px;padding-bottom:10px;}img.email_logo{ margin:15px 0px;}</style></head><body><div class="content"><center><img src="' . base_url() . '/design/bjain_pharma/bjain_logo.png" class="email_logo" style="width:250px;" /></center>  
	  <h3>Dear,</h3> <p>' . $sms1 . '</p>' . $emailorderdata . '<p><i>This is an auto generated email.</i></p>
	  <div class="regards_box"><h3 class="user_name_regards">Regards,</h3><p class="user_name_brand">' . get_user_name($interaction_data->user_id) . '<br>BJain Pharmaceuticals Pvt. Ltd.</p></div></div></body></html>';

											 $dealerSms = 'Dear Dealer/Sub Dealer, we have received an order .  Kindly deliver at mentioned time and discount.The order details are mentioned below. ' . $orderData;

											 $dealerSms1 = 'Dear Dealer/Sub Dealer, we have received an order.  Kindly deliver at mentioned time and discount.The order details are mentioned below. ';

											 $dealeremailbody = '<html><head><title>BJain Pharmaceuticals</title><style type="text/css">body{padding:0;margin:0;font-family: calibri;}.content{ width:40%; margin:0 auto;}.regards_box{float:left;margin-top:20px;}p.user_name_brand{ margin:0px;}h3.user_name_regards{
		 margin:0px;padding-bottom:10px;}img.email_logo{ margin:15px 0px;}</style></head><body><div class="content"><center><img src="' . base_url() . '/design/bjain_pharma/bjain_logo.png" class="email_logo" style="width:250px;" /></center>  
	  <h3>Dear,</h3> <p>' . $dealerSms1 . '</p>' . $emailorderdata . '<p><i>This is an auto generated email.</i></p>
	  <div class="regards_box"><h3 class="user_name_regards">Regards,</h3><p class="user_name_brand">' . get_user_name($interaction_data->user_id) . '<br>BJain Pharmaceuticals Pvt. Ltd.</p></div></div></body></html>';
											 //send_msg($dealerSms,'7838359383');
											 if ($interaction_data->dealer_mail == 1) {
												 send_msg($dealerSms, $dealerNumber);
												 if ($dealerEmail != '') {
													 $success = send_email($dealerEmail, $senderemail, $subject, $dealeremailbody);
													 //$success =send_email('android@bjain.com', $senderemail, $subject, $dealeremailbody);//send message to pharmacy/dealer
												 }
											 }
										 }
										 else if (isset($interaction_data->m_sample))// only sample
										 {
											 $sms = 'Thank you Dear Sub Dealer for your valuable time. Kindly give your feedback for samples.';
											 $emailbody = '<html><head><title>BJain Pharmaceuticals</title><style type="text/css">body{padding:0;margin:0;font-family: calibri;}.content{ width:40%; margin:0 auto;}.regards_box{float:left;margin-top:20px;}p.user_name_brand{ margin:0px;}h3.user_name_regards{ margin:0px;padding-bottom:10px;}img.email_logo{ margin:15px 0px;}</style></head><body><div class="content"><center><img src="' . base_url() . '/design/bjain_pharma/bjain_logo.png" class="email_logo" style="width:250px;" /></center>  
	<h3>Dear,</h3> <p>' . $sms . '</p><p><i>This is an auto generated email.</i></p>
	<div class="regards_box"><h3 class="user_name_regards">Regards,</h3><p class="user_name_brand">' . get_user_name($interaction_data->user_id) . '<br>BJain Pharmaceuticals Pvt. Ltd.</p></div></div></body></html>';
										 }
										 else if (isset($interaction_data->m_sale))//only product
										 {
											 $sms = 'Thank you Dear Sub Dealer for your support to B. Jain Pharma. I am happy to receive your order which is mentioned below.';
											 $sms1 = $sms;
											 $sms = $sms . ' ' . $orderData;

											 $dealerSms = 'Dear Dealer/Sub Dealer, we have received an order.  Kindly deliver at mentioned time and discount.The order details are mentioned below. ' . $orderData;

											 $dealerSms1 = 'Dear Dealer/Sub Dealer, we have received an order.  Kindly deliver at mentioned time and discount.The order details are mentioned below. ';

											 $dealeremailbody = '<html><head><title>BJain Pharmaceuticals</title><style type="text/css">body{padding:0;margin:0;font-family: calibri;}.content{ width:40%; margin:0 auto;}.regards_box{float:left;margin-top:20px;}p.user_name_brand{ margin:0px;}h3.user_name_regards{
		 margin:0px;padding-bottom:10px;}img.email_logo{ margin:15px 0px;}</style></head><body><div class="content"><center><img src="' . base_url() . '/design/bjain_pharma/bjain_logo.png" class="email_logo" style="width:250px;" /></center>  
	  <h3>Dear,</h3> <p>' . $dealerSms1 . '</p>' . $emailorderdata . '<p><i>This is an auto generated email.</i></p>
	  <div class="regards_box"><h3 class="user_name_regards">Regards,</h3><p class="user_name_brand">' . get_user_name($interaction_data->user_id) . '<br>BJain Pharmaceuticals Pvt. Ltd.</p></div></div></body></html>';

											 //send_msg($dealerSms,'7838359383');
											 if ($interaction_data->dealer_mail == 1) {
												 send_msg($dealerSms, $dealerNumber);
												 if ($dealerEmail != '') {
													 $success = send_email($dealerEmail, $senderemail, $subject, $dealeremailbody);//send message to pharmacy/dealer
												 }
											 }
										 }
									 }
									 if ($interaction_data->dealer_mail == 1) {
										 // send_msg($sms,'8604111305');
										 send_msg($sms, $pharmacyNumber);
										 if ($pharmacyEmail != '') {
											 // send_email('niraj@bjain.com', $senderemail, $subject, $emailbody);
											 $success = send_email($pharmacyEmail, $senderemail, $subject, $emailbody);//send message to doctor  //send message to doctor
										 }
									 }
								 } catch (Exception $e) {
									 $result = array(
										 'Data' => $e,
										 'Message' => 'Alert! Something went wrong.',
										 'Code' => 500
									 );
									 $this->response($result);
//									 return "Alert! Something went wrong.";
								 }
							 }
							 elseif (isset($interaction_data->d_id)) {            //for Dealer side
								 $data = $this->dealer->get_dealer_data($interaction_data->d_id);
								 if ($data != FALSE) {
									 $dealerNumber = $data->d_phone;
									 $dealerEmail = $data->d_email;
								 }
								 if (isset($interaction_data->meet_or_not)) {
									 if ($interaction_data->meet_or_not == 1) {
										 # code...
										 $sms = 'Thank you Dear Dealer for your valuable time. We look forward to your kind support for B. Jain’s Product.';//but no sale or sample
										 $emailbody = '<html><head><title>BJain Pharmaceuticals</title><style type="text/css">body{padding:0;margin:0;font-family: calibri;}.content{ width:40%; margin:0 auto;}.regards_box{float:left;margin-top:20px;}p.user_name_brand{ margin:0px;}h3.user_name_regards{ margin:0px;padding-bottom:10px;}img.email_logo{ margin:15px 0px;}</style></head><body><div class="content"><center><img src="' . base_url() . '/design/bjain_pharma/bjain_logo.png" class="email_logo" style="width:250px;" /></center>  
	<h3>Dear,</h3> <p>' . $sms . '</p><p><i>This is an auto generated email.</i></p>
	<div class="regards_box"><h3 class="user_name_regards">Regards,</h3><p class="user_name_brand">' . get_user_name($interaction_data->user_id) . '<br>BJain Pharmaceuticals Pvt. Ltd.</p></div></div></body></html>';
									 }
									 else {
										 $sms = 'Dealer I visited today but was unable to meet you. May I request you for a suitable time for a meeting when I can see you.';
										 $emailbody = '<html><head><title>BJain Pharmaceuticals</title><style type="text/css">body{padding:0;margin:0;font-family: calibri;}.content{ width:40%; margin:0 auto;}.regards_box{float:left;margin-top:20px;}p.user_name_brand{ margin:0px;}h3.user_name_regards{ margin:0px;padding-bottom:10px;}img.email_logo{ margin:15px 0px;}</style></head><body><div class="content"><center><img src="' . base_url() . '/design/bjain_pharma/bjain_logo.png" class="email_logo" style="width:250px;" /></center>  
	<h3>Dear,</h3> <p>' . $sms . '</p><p><i>This is an auto generated email.</i></p>
	<div class="regards_box"><h3 class="user_name_regards">Regards,</h3><p class="user_name_brand">' . get_user_name($interaction_data->user_id) . '<br>BJain Pharmaceuticals Pvt. Ltd.</p></div></div></body></html>';
									 }
								 } else // for sale
								 {
									 if (isset($interaction_data->m_sale) && isset($interaction_data->m_sample)) {
										 $sms = 'Thank you Dear Dealer for your support to B. Jain Pharma. Please give your valuable feedback for provided samples. I am happy to receive your order which is mentioned below.';
										 $sms1 = $sms;
										 $sms = $sms . ' ' . $orderData;
										 $emailbody = '<html><head><title>BJain Pharmaceuticals</title><style type="text/css">body{padding:0;margin:0;font-family: calibri;}.content{ width:40%; margin:0 auto;}.regards_box{float:left;margin-top:20px;}p.user_name_brand{ margin:0px;}h3.user_name_regards{
		 margin:0px;padding-bottom:10px;}img.email_logo{ margin:15px 0px;}</style></head><body><div class="content"><center><img src="' . base_url() . '/design/bjain_pharma/bjain_logo.png" class="email_logo" style="width:250px;" /></center>  
	  <h3>Dear,</h3> <p>' . $sms . '</p>' . $emailorderdata . '<p><i>This is an auto generated email.</i></p>
	  <div class="regards_box"><h3 class="user_name_regards">Regards,</h3><p class="user_name_brand">' . get_user_name($interaction_data->user_id) . '<br>BJain Pharmaceuticals Pvt. Ltd.</p></div></div></body></html>';
									 }
									 else if (isset($interaction_data->m_sample))// only sample
									 {
										 $sms = 'Thank you Dear Dealer for your valuable time. Kindly give your feedback for samples.';
										 $emailbody = '<html><head><title>BJain Pharmaceuticals</title><style type="text/css">body{padding:0;margin:0;font-family: calibri;}.content{ width:40%; margin:0 auto;}.regards_box{float:left;margin-top:20px;}p.user_name_brand{ margin:0px;}h3.user_name_regards{ margin:0px;padding-bottom:10px;}img.email_logo{ margin:15px 0px;}</style></head><body><div class="content"><center><img src="' . base_url() . '/design/bjain_pharma/bjain_logo.png" class="email_logo" style="width:250px;" /></center>  
	<h3>Dear,</h3> <p>' . $sms . '</p><p><i>This is an auto generated email.</i></p>
	<div class="regards_box"><h3 class="user_name_regards">Regards,</h3><p class="user_name_brand">' . get_user_name($interaction_data->user_id) . '<br>BJain Pharmaceuticals Pvt. Ltd.</p></div></div></body></html>';
									 } else if (isset($interaction_data->m_sale))//only product
									 {
										 $sms = 'Thank you Dear Dealer for your support to B. Jain Pharma. I am happy to receive your order which is mentioned below.';
										 $sms1 = $sms;
										 $sms = $sms . ' ' . $orderData;
										 $emailbody = '<html><head><title>BJain Pharmaceuticals</title><style type="text/css">body{padding:0;margin:0;font-family: calibri;}.content{ width:40%; margin:0 auto;}.regards_box{float:left;margin-top:20px;}p.user_name_brand{ margin:0px;}h3.user_name_regards{
		 margin:0px;padding-bottom:10px;}img.email_logo{ margin:15px 0px;}</style></head><body><div class="content"><center><img src="' . base_url() . '/design/bjain_pharma/bjain_logo.png" class="email_logo" style="width:250px;" /></center>  
	  <h3>Dear,</h3> <p>' . $sms1 . '</p>' . $emailorderdata . '<p><i>This is an auto generated email.</i></p>
	  <div class="regards_box"><h3 class="user_name_regards">Regards,</h3><p class="user_name_brand">' . get_user_name($interaction_data->user_id) . '<br>BJain Pharmaceuticals Pvt. Ltd.</p></div></div></body></html>';
									 }
								 }

								 //send_msg($sms,'8604111305');

								 try {
									 if ($interaction_data->dealer_mail == 1) {
										 send_msg($sms, $dealerNumber); //send message to dealer
										 if ($dealerEmail != '') {
											 send_email($dealerEmail, $senderemail, $subject, $emailbody);//send message to dealer
											 //send_email('niraj@bjain.com', $senderemail, $subject, $emailbody);
										 }
									 }
								 } catch (Exception $e) {
									 $result = array(
										 'Data' => $e,
										 'Message' => 'Alert! Something went wrong.',
										 'Code' => 500
									 );
									 $this->response($result);
//									 return "Alert! Something went wrong.";
								 }
							 }

							 if (isset($interaction_data->m_sale)) {
								 $userBoss = $this->user->getUserBoss($interaction_data->user_id);
								 $username = get_user_name($interaction_data->user_id);
								 $msname = '';
								 if (isset($interaction_data->dealer_id)) {
									 if (is_numeric($interaction_data->dealer_id)) {
										 //for dealer;
										 $data = $this->dealer->get_dealer_data($interaction_data->dealer_id);
										 if ($data != FALSE) {
											 $msname = $data->dealer_name;

										 }
									 } else {
										 //for pharmacy;
										 $data = $this->pharmacy->get_pharmacy_data($interaction_data->dealer_id);
										 if ($data != FALSE) {
											 $msname = $data->company_name;
										 }
									 }
								 }

								 $userbossms = '';
								 $userbosemail = '';
								 if (isset($interaction_data->doc_name)) {
									 $userbossms = 'Mr. ' . $username . ' Has placed an order from Dr.' .
										 $interaction_data->doc_name . '. To M/S ' . $msname . ' the order details are as. ' . $orderData;
									 $userbossms1 = 'Mr. ' . $username . ' Has placed an order from Dr.' .
										 $interaction_data->doc_name . '. To M/S ' . $msname . ' the order details are as. ';
									 $userbosemail = '<html><head><title>BJain Pharmaceuticals</title><style type="text/css">body{padding:0;margin:0;font-family: calibri;}.content{ width:40%; margin:0 auto;}.regards_box{float:left;margin-top:20px;}p.user_name_brand{ margin:0px;}h3.user_name_regards{
		 margin:0px;padding-bottom:10px;}img.email_logo{ margin:15px 0px;}</style></head><body><div class="content"><center><img src="' . base_url() . '/design/bjain_pharma/bjain_logo.png" class="email_logo" style="width:250px;" /></center>  
	  <h3>Dear,</h3> <p>' . $userbossms1 . '</p>' . $emailorderdata . '<p><i>This is an auto generated email.</i></p>
	  <div class="regards_box"><h3 class="user_name_regards">Regards,</h3><p class="user_name_brand">' . get_user_name($interaction_data->user_id) . '<br>BJain Pharmaceuticals Pvt. Ltd.</p></div></div></body></html>';
								 }
								 else if (isset($interaction_data->com_name)) {
									 $userbossms = 'Mr.' . $username . ' Has placed an order from Sub Dealer  ' .
										 $interaction_data->com_name . '. To M/S ' . $msname . ' the order details are as. ' . $orderData;
									 $userbossms1 = 'Mr.' . $username . ' Has placed an order from Sub Dealer  ' .
										 $interaction_data->com_name . '. To M/S ' . $msname . ' the order details are as. ';
									 $userbosemail = '<html><head><title>BJain Pharmaceuticals</title><style type="text/css">body{padding:0;margin:0;font-family: calibri;}.content{ width:40%; margin:0 auto;}.regards_box{float:left;margin-top:20px;}p.user_name_brand{ margin:0px;}h3.user_name_regards{
		 margin:0px;padding-bottom:10px;}img.email_logo{ margin:15px 0px;}</style></head><body><div class="content"><center><img src="' . base_url() . '/design/bjain_pharma/bjain_logo.png" class="email_logo" style="width:250px;" /></center>  
	  <h3>Dear,</h3> <p>' . $userbossms1 . '</p>' . $emailorderdata . '<p><i>This is an auto generated email.</i></p>
	  <div class="regards_box"><h3 class="user_name_regards">Regards,</h3><p class="user_name_brand">' . get_user_name($interaction_data->user_id) . '<br>BJain Pharmaceuticals Pvt. Ltd.</p></div></div></body></html>';
								 }
								 else if (isset($interaction_data->d_name)) {

									 $userbossms = 'Mr.' . $username . ' Has placed an order to Dealer ' .
										 $interaction_data->d_name . '. The order details are as. ' . $orderData;
									 $userbossms1 = 'Mr.' . $username . ' Has placed an order to Dealer ' .
										 $interaction_data->d_name . '. The order details are as. ';
									 $userbosemail = '<html><head><title>BJain Pharmaceuticals</title><style type="text/css">body{padding:0;margin:0;font-family: calibri;}.content{ width:40%; margin:0 auto;}.regards_box{float:left;margin-top:20px;}p.user_name_brand{ margin:0px;}h3.user_name_regards{
		 margin:0px;padding-bottom:10px;}img.email_logo{ margin:15px 0px;}</style></head><body><div class="content"><center><img src="' . base_url() . '/design/bjain_pharma/bjain_logo.png" class="email_logo" style="width:250px;" /></center>  
	  <h3>Dear,</h3> <p>' . $userbossms1 . '</p>' . $emailorderdata . '<p><i>This is an auto generated email.</i></p>
	  <div class="regards_box"><h3 class="user_name_regards">Regards,</h3><p class="user_name_brand">' . get_user_name($interaction_data->user_id) . '<br>BJain Pharmaceuticals Pvt. Ltd.</p></div></div></body></html>';

								 }
								 $userbosssms = '';
								 if ($userBoss != False) {
									 foreach ($userBoss as $boss) {
										 if (!empty($boss['user_phone'])) {
											 //send_msg('6','8604111305');//send message to all boss
											 send_msg($userbossms, $boss['user_phone']);//send message to all boss
											 // send_msg($userbossms,'9891747698');
											 try {
												 if ($boss['email_id'] != '') {
													 //send_msg($message,$boss['user_phone'])
													 //send_email('ios@bjain.com', $senderemail, $subject, $userbosemail);//ashis
													 send_email($boss['email_id'], $senderemail, $subject, $userbosemail);//send message to dealer
												 }
											 } catch (Exception $e) {
												 $result = array(
													 'Data' => $e,
													 'Message' => 'Alert! Something went wrong.',
													 'Code' => 500
												 );
												 $this->response($result);
//												 return "Alert! Something went wrong.";
											 }
										 }
									 }
								 }

								 send_email('pharma.reports@bjain.com', $senderemail, $subject, $userbosemail);//send only email to H.O.
							 }
							 if ($interaction_data->path_info == '' || $interaction_data->path_info == 0) {

								 if (!is_numeric($interaction_data->dealer_view_id)) {
									 if (substr($interaction_data->dealer_view_id, 0, 3) == 'doc') {
										 $result = array(
											 'Data' => $interaction_data->dealer_view_id,
											 'Message' => 'Success! Interaction is being saved for this.',
											 'Code' => 200
										 );
//										 return "Success! Interaction is being saved for this.";
									 }
									 else {
										 $result = array(
											 'Data' => $interaction_data->dealer_view_id,
											 'Message' => 'Success! Interaction is being saved for this.',
											 'Code' => 200
										 );
//										 return "Success! Interaction is being saved for this.";
									 }
								 } else {
							 $gd_id = json_decode($this->dealer->edit_dealer($interaction_data->dealer_view_id));
									 if ($gd_id->gd_id == '') {
										 $result = array(
											 'Data' => $gd_id,
											 'Message' => 'Success! Interaction is being saved for this.',
											 'Code' => 200
										 );
//									 	return "Success! Interaction is being saved for this.";
									 }
									 else {
										 $result = array(
											 'Data' => $gd_id,
											 'Message' => 'Success! Interaction is being saved for this.',
											 'Code' => 200
										 );
//										 return "Success.! Interaction is being saved for this.";
									 }
								 }

							 }
							 else {
								 $result = array(
									 'Data' => $interaction_data->path_info,
									 'Message' => 'Success! Interaction is being saved for this.',
									 'Code' => 200
								 );
//								 return "Success! Interaction is being saved for this.";
							 }
						 }
						 else {
							 $result = array(
								 'Data' => $success,
								 'Message' => 'Success! Interaction is being saved for this.',
								 'Code' => 200
							 );
//							 return "Alert! Interaction not saved please try Later. ";

						 }

					 }
					 else {
						 $result = array(
							 'Data' => new stdClass(),
							 'Message' => 'Alert! Please Select Any One Type of Meeting..',
							 'Code' => 500
						 );
//							 return "Alert! Please Select Any One Type of Meeting..";
		  				 }



				 }
			 }
			 else{
				 $result = array(
					 'Data' => 'Alert! Stay / Not Stay - Features Not Used..',
					 'Message' => 'Alert! Stay / Not Stay - Features Not Used..',
					 'Code' => 500
				 );
//				 return "Alert! Stay / Not Stay - Features Not Used..";
			 }

		 $this->response($result);
	 }

/*Joint Interaction  For Managers*/
	 function joint_interaction_post()
	 {

		 $msg = '';
		 # initialize variables
		 $interaction_data = json_decode($this->input->raw_input_stream);

		 if ($msg == '') {
			 $summaryArr = array(
				 'summry1' => 'THE PRIMARY / SECONDARY ORDER RATIO : ' . $interaction_data->remark[0],
				 'summry2' => 'THE CURRENT RATION OF TARGET TO ACHIEVEMENT : ' . $interaction_data->remark[1],
				 'summry3' => 'THE DAILY CALL AVERAGE : ' . $interaction_data->remark[2],
				 'summry4' => 'DOCTORS VISITED MORE THAN 3 TIMES BUT NO ORDER : ' . $interaction_data->remark[3],
				 'summry5' => 'SECONDARY PAYMENT OVERDUE IN MARKET : ' . $interaction_data->remark[4]
			 );

			 if ($interaction_data->stay || $interaction_data->up) {
				 $success = $this->dealer->save_asm_dsr($interaction_data);

				 if ($success = 1) {
					 /*Mail to Boss(Manager), Ajay Mahajan & Nishant */
					 $joint_with = $interaction_data->joint_workwith;
					 $joint_with_name = get_user_name($joint_with);
					 $boss_userID = logged_user_boss();
					 $boss_mail = get_boss_email_user($boss_userID);
					 $date_Time = date("d-M-Y/D");
					 $user_ID = $interaction_data->user_id;
					 $user_name = get_user_name($user_ID);
					 $user_email = get_boss_email_user($user_ID);

					 $mailArr = array(
						 'boss' => $boss_mail,
						 //'nishant' => 'nishant@bjain.com',
						 // 'ajay' => 'pharmamarketing@bjain.com',
						 'user' => $user_email,
						 'nitin' => 'php@bjaintech.com'
					 );

					 foreach ($mailArr as $mailID) {
						 $dealeremailbody = '<html><head><title>BJain Pharmaceuticals</title><style type="text/css">body{padding:0;margin:0;font-family: calibri;}.content{ width:40%; margin:0 auto;}.regards_box{float:left;margin-top:20px;}p.user_name_brand{ margin:0px;}h3.user_name_regards{margin:0px;padding-bottom:10px;}img.email_logo{ margin:15px 0px;}</style></head><body><div class="content"><center><img src="' . base_url() . '/design/bjain_pharma/bjain_logo.png" class="email_logo" style="width:250px;" /></center><h3>Dear,</h3> <p>Here\'s the summary of Joint Interaction ' . $user_name . ' with ' . $joint_with_name . ' on ' . $date_Time . '. 	</p> <ul>';

						 $body2 = array();
						 foreach ($summaryArr as $anwsers) {
							 $body2[] = "<li>" . $anwsers . "</li>";
						 }
						 $dealeremailbody2 = implode(' ', $body2);

						 $dealeremailbody3 = '</ul> <p style="font-size: 11px; text-align: center;"><i>(This is an auto-generated email.)</i></p><div class="regards_box"><h3 class="user_name_regards">Regards,</h3><p class="user_name_brand">BJain Pharmaceuticals Pvt. Ltd.</p></div></div></body></html>';

						 $message = $dealeremailbody . $dealeremailbody2 . $dealeremailbody3;
						 $subject = "Joint Interaction of " . $user_name . "with " . $joint_with_name . " Summary Report on "
							 . $date_Time;

						 /*Open on Server for mailing */
//						 $mail_Send = send_email($mailID, 'pharma.reports@bjain.com', $subject, $message);
						 $mail_Send = send_email($mailID, 'php@bjaintech.com', $subject, $message);

					 }

					 $result = array(
						 //'Data' => new stdClass(),
						 'Status' => true,
						 'Message' => ' Success! Interaction are being saved for this.',
						 'Code' => 200
					 );

				 } else {
					 $result = array(
						// 'Data' => new stdClass(),
						 'Status' => false,
						 'Message' => ' Alert! Interaction was not saved please try later..',
						 'Code' => 404
					 );

				 }
			 } else {
				 $result = array(
					 //'Data' => new stdClass(),
					 'Status' => false,
					 'Message' => 'Alert! Stay / Not Stay - Features Not Used..',
					 'Code' => 404
				 );

			 }

		 } else {
			 $result = array(
				 'Data' => new stdClass(),
				 'Status' => false,
				 'Message' => $msg,
				 'Code' => 404
			 );
		 }
		 $this->response($result);

	 }




/* Check Order status  Incomplete or completed in interaction */
function checkOrder_post(){
		# initialize variables
		$msg = '';
		$post = array_map('trim', $this->input->post());

		if(!(isset($post['user_id'])&& !empty($post['user_id'])))
		{
			$msg='User Id is required.';
		}
		if(!(isset($post['doc_id'])&& !empty($post['doc_id'])))
		{
			$msg='Doctor Id is required.';
		}
		if(!(isset($post['int_date'])&& !empty($post['int_date'])))
		{
			$msg='Date of Interaction is required.';
		}
		if ($msg == '')
		{
			$data['user_id']  = $post['user_id'];
			$data['doc_id']  = $post['doc_id'];
			$data['int_date']  = $post['int_date'];
			$res = $this->interact->get_log__doctor_data($data);
			if ($res!=FALSE)
			{
				$res2 = $this->interact->get_orderamount($data);
				if($res2 != FALSE){
					$dataArr=array(
						'person_id' => $res->person_id,
						'interaction_date' => $res->interaction_date,
						'order_amount' => $res2->order_amount,
						'provider' => $res2->provider,
					);
					$result = array(
						'Data' => $dataArr,
						'Status' => true,
						'Message' => 'Successfully',
						'Code' => 200
					);
				}else{
					$dataArr=array(
						'person_id' => 0,
						'interaction_date' => 0,
						'order_amount' => 0,
						'provider' => 0,
					);

					$result = array(
						'Data' => $dataArr,
						'Status' => false,
						'Message' => 'No Found Previous Data',
						'Code' => 404
					);
				}

			}
			else
			{
				$dataArr=array(
					'person_id' => "0",
					'interaction_date' => "0",
					'order_amount' => "0",
					'provider' => "0",
				);
				$result = array(
					'Data' => $dataArr,
					'Status' => false,
					'Message' => 'No Found Previous Data',
					'Code' => 404
				);
			}
		}
		else
		{
			$result = array(
				'Data' => new stdClass(),
				'Status' => false,
				'Message' => $msg,
				'Code' => 404
			);
		}
		$this->response($result);

	}


	function dealer_sub_dealer_list_post() {
		$msg = '';
		$post = array_map('trim', $this->input->post());
		$msg = '';

		if(!(isset( $post['sp_code'])&& !empty( $post['sp_code']))){
			$msg='Sp code is required.';
		}
		if ($msg == '')
		{
			$sp_code  = $post['sp_code'];
			$data = $this->interact->get_dealer_sub_list($sp_code);
			if ($data!=FALSE)
			{
				$result = array(
					'Data' => $data,
					// 'Status' => true,
					'Message' => 'successfully',
					'Code' => 200
				);
			}
			else
			{
				$result = array(
					'Data' => new stdClass(),
					'Status' => false,
					'Message' => 'No Dealer',
					'Code' => 404
				);
			}
		}
		else
		{
			$result = array(
				'Data' => new stdClass(),
				'Status' => false,
				'Message' => $msg,
				'Code' => 404
			);
		}
		$this->response($result);
	}
	

}

