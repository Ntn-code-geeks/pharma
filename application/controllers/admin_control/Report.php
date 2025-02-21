<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/* 
 * Niraj Kumar
 * Dated: 30/10/2017
 * 
 * This Controller is for Report
 */

class Report extends Parent_admin_controller {
   function __construct() 
   {
      parent::__construct();
      $loggedData=logged_user_data();
      if(empty($loggedData)){
        redirect('user'); 
      }
      $this->load->library('excel');
      $this->load->model('report/report_model','report');
      $this->load->model('user_model','user');
      $this->load->model('dealer/Dealer_model','dealer');
      $this->load->model('permission/Permission_model','permission');
    }

  public function index($userid='')
  { 
    $data['title'] = "Report List";
    $data['page_name'] = "Reports";
    $data['action2'] ="admin_control/report/cust_relationship";
    $data['action'] ="admin_control/report/sale_travel";
    $data['users']=array();
    $data['user_id']='';
    if($userid=='')
    {
      if(is_admin()){
        //$data['sale'] =$this->report->travel_report_doctor();
        //$data['sale'] =$this->report->relationship_report_dealer();
        $data['users'] = $this->user->users_report();
        $data['dealer_list']= $this->report->dealer_list();
        $data['pharma_list']= $this->report->pharmacy_list();
        //pr($data['sample_data']); die;
      }
      else{
        redirect('user'); 
      }
    }
    else
    {
      $data['user_id']=urisafedecode($userid);
      $data['dealer_list']= $this->dealer->dealer_list();
      $cities_are = logged_user_cities();
      $data['pharma_list']= json_encode($this->permission->pharmacy_list_user());
    }
    $this->load->get_view('report/report_view',$data);
  }

  public function customer_relation()
  { 
    $data['title'] = "Customer Relation Reports";
    $data['page_name'] = "Customer Relation Reports";
    $data['action'] ="admin_control/report/customer_relationship_report";
    $data['dealer_list']= $this->dealer->dealer_list();
    $cities_are = logged_user_cities();
    $data['pharma_list']= json_encode($this->permission->pharmacy_list($cities_are));
    $this->load->get_view('report/customer_relation_view',$data);
  }

  public function customer_relationship_report()
  { 
    $request = $this->input->post();
    $report_date = explode('-',$request['report_date'] );
    $followstart_date =  trim($report_date[0]);
    $newstartdate = str_replace('/', '-', $followstart_date);
    $followend_date =  trim($report_date[1]);
    $newenddate = str_replace('/', '-', $followend_date);
    $start = date('Y-m-d', strtotime($newstartdate))." 00:00:00";
    $end = date('Y-m-d', strtotime($newenddate))." 23:59:59";
    if(isset($request['dealer_id']))
    {
      $this->relationship_report_dealer_user($request['dealer_id'],$start,$end); 
    }
    if(isset($request['pharma_id']))
    {
      $this->relationship_report_pharma_user($request['pharma_id'],$start,$end);  
    }
  }

  public function get_tada_report($userid=''){ 
      
        $data['title'] = "TA DA Report List";
        $data['page_name'] = "TA DA Report List";
        $data['action'] ="admin_control/report/generate_tada_report";
        $data['users']=array();
        $data['user_id']='';
         if(is_admin()){
            $data['users'] =$this->user->users_report();
          }
          else{
            redirect('user'); 
          }
        $this->load->get_view('report/tada_report_view',$data);

  }

  public function generate_tada_report(){

        if(is_admin()){
          $request = $this->input->post();

          $report_date = explode('-',$request['report_date'] );
//          $followstart_date =  trim($report_date[0]);
//          $newstartdate = str_replace('/', '-', $followstart_date);
//          $followend_date =  trim($report_date[1]);
//          $newenddate = str_replace('/', '-', $followend_date);
//          $start = date('Y-m-d', strtotime($newstartdate))." 00:00:00";
//          $end = date('Y-m-d', strtotime($newenddate))." 23:59:59";
          
          $follow_month =  trim($report_date[0]); $follow_year =  trim($report_date[1]);
          $month_year = $request['report_date'];
          $start =    $follow_month.'/01/'.$follow_year;         
          
          $end =  $follow_month.'/20/'.$follow_year; 
          
          $this->load->library('form_validation');
          $this->form_validation->set_rules('user_id', 'User', 'required');
          $this->form_validation->set_rules('report_date', 'Report Date range', 'required'); 
          if($this->form_validation->run() == TRUE){

            $this->show_tada_report($request['user_id'],$start,$end,$month_year);
            //$data['attendance_report'] =$this->user_report->get_attendance_report($request['user_id'],$start,$end);
          }else{
            // for false validation
            $this->get_tada_report();  
          }

        }
        else{
          redirect('user');
        }
  }

 public function show_tada_report($userid,$start,$end,$month_year)
  {
      $totalrow=0;
      $totalstprow=0;
      $gtrow=0;
      $gtkms=0;
      $gtta=0;
      $gtda=0;
      $gtpostage=0;
      $lastdaydestination=0;
      $gtstprow=0;
      $gtstpkms=0;
      $gtstpta=0;
      $this->excel->setActiveSheetIndex(0);
          //name the worksheet
      $this->excel->getActiveSheet()->setTitle('TA DA Report');
      $styleArray = array(
          'font'  => array(
              'bold'  => true,
              //'type' => PHPExcel_Style_Fill::FILL_SOLID,
              //'color' => array('rgb' => 'FFFF00'),
              'size'  => 12,
              //'name'  => 'Verdana'
          ));
       $styleArray2 = array(
          'font'  => array(
              'bold'  => true,
              //'type' => PHPExcel_Style_Fill::FILL_SOLID,
              //'color' => array('rgb' => 'FFFF00'),
              'size'  => 18,
              //'name'  => 'Verdana'
          ));
       $styleArray1 = array(
          'font'  => array(
              'bold'  => true,
              //'type' => PHPExcel_Style_Fill::FILL_SOLID,
              //'color' => array('rgb' => 'FFFF00'),
              'size'  => 22,
              'name'  => 'Algerian'
          ));
       
      $this->excel->getActiveSheet()->getStyle('A5:O5')->applyFromArray($styleArray);
      $this->excel->getActiveSheet()->getStyle('A5:O5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
      $this->excel->getActiveSheet()->mergeCells('A1:O2');
      $this->excel->getActiveSheet()->getStyle('A1:O2')->applyFromArray($styleArray1);
      $this->excel->getActiveSheet()->getStyle('A1:O2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
      // $this->excel->getActiveSheet()->getStyle("A1:J2")->getFont()->setSize(22);
      $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      $this->excel->getActiveSheet()->setCellValue('A1', 'B.JAIN PHARMACEUTICALS PVT. LTD.');
      //set cell A1 content with some text
      $this->excel->getActiveSheet()->mergeCells('A3:C3');
      $this->excel->getActiveSheet()->mergeCells('D3:N4');
      $this->excel->getActiveSheet()->getStyle('D3:N4')->applyFromArray($styleArray2);
      $this->excel->getActiveSheet()->getStyle('D3:N4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('32CD32');
      $this->excel->getActiveSheet()->getStyle('D3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      $this->excel->getActiveSheet()->setCellValue('D3', 'TA/DA EXPENSE STATEMENT');
      $this->excel->getActiveSheet()->getStyle("A3:C3")->applyFromArray($styleArray);
      $this->excel->getActiveSheet()->setCellValue('A3', 'NAME : '.get_user_name($userid));
      $this->excel->getActiveSheet()->mergeCells('A4:C4');
      $this->excel->getActiveSheet()->getStyle("A4:C4")->applyFromArray($styleArray);
      $this->excel->getActiveSheet()->setCellValue('A4', 'HQ : '.get_city_name(get_user_deatils($userid)->headquarters_city));
//      $this->excel->getActiveSheet()->mergeCells('K3:O4');
//      $this->excel->getActiveSheet()->getStyle('K3:O4')->applyFromArray($styleArray2);
//      $this->excel->getActiveSheet()->getStyle('K3:O4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('32CD32');
//      $this->excel->getActiveSheet()->getStyle('K3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//      $this->excel->getActiveSheet()->setCellValue('K3', 'EXPENSE STATEMENT By Google');
      $this->excel->getActiveSheet()->mergeCells('O3:O3');
      $this->excel->getActiveSheet()->getStyle("O3:O3")->applyFromArray($styleArray);
      $this->excel->getActiveSheet()->setCellValue('O3', 'FROM : '.date('d/m/Y',strtotime($start)));
//      $this->excel->getActiveSheet()->mergeCells('P4:P4');
//      $this->excel->getActiveSheet()->getStyle("P4:P4")->applyFromArray($styleArray);
      $this->excel->getActiveSheet()->setCellValue('O4', 'TO : '.date('d/m/Y',strtotime($end)));
      $this->excel->getActiveSheet()->setCellValue('A5', 'Filled DATES');
      $this->excel->getActiveSheet()->setCellValue('B5', 'Interaction DATES');
      $this->excel->getActiveSheet()->setCellValue('C5', 'FROM');
      $this->excel->getActiveSheet()->setCellValue('D5', 'CITY WORKED');
      $this->excel->getActiveSheet()->setCellValue('E5', 'TO');
      $this->excel->getActiveSheet()->setCellValue('F5', 'KMS');
      $this->excel->getActiveSheet()->setCellValue('G5', 'T.A.');
      $this->excel->getActiveSheet()->setCellValue('H5', 'D.A.');
      $this->excel->getActiveSheet()->setCellValue('I5', 'POSTAGE');
      $this->excel->getActiveSheet()->setCellValue('J5', 'TOTAL');
      $this->excel->getActiveSheet()->setCellValue('K5', 'Any Other Charge Name');
      $this->excel->getActiveSheet()->setCellValue('L5', 'Any Other Charge Amount');
      $this->excel->getActiveSheet()->setCellValue('M5', 'Manager Remark');
      $this->excel->getActiveSheet()->setCellValue('N5', 'Admin Remark');
//      $this->excel->getActiveSheet()->setCellValue('O5', 'TOTAL');
      $this->excel->getActiveSheet()->setCellValue('O5', 'Remark');
//      $this->excel->getActiveSheet()->setCellValue('Q5', 'Manager Remark');
//      $this->excel->getActiveSheet()->setCellValue('R5', 'Admin Remark');
      
//      $data['tada_report'] =$this->report->get_tada_report($userid,$start,$end);
       $data['tada_report'] =$this->report->verified_admin_tada_report($userid,$month_year);
      
      $k_num = 6;
//     pr($data['tada_report']);die;  
      /* For Attendance */
      if(!empty($data['tada_report'])){
        $frmCity='x';
        $toCity='y';
        $workCity='z';
        $prevdate='';
        $crdate='';
        $crtddate =''; 
        $ft=0;
        $gtstprow =0; $gtda=0; $gtpostage=0; $gtstpta=0; $gtta=0; $aoc_amt=0;
        $total_aoc_amount=0; $total_payble_amount=0;
          
//      pr($data['tada_report']);
//        die;
        foreach ($data['tada_report']['tada_detail'] as $key=>$row){
        //if(){ 
//        if($row['destination_city']!=$row['source_city'] || $row['is_stay'] || $ft==0 || $crdate!=$row['doi']){
//        if($key==0 || ($data['tada_report'][$key-1]['destination_city']!=$row['destination_city'] && $data['tada_report'][$key-1]['source_city']!=$row['source_city'])){ 
//          $crdate = $row['doi'];
//          $da=0;
//          $hqdistance=0;
//          $nxtdestination=0;
//          
//          $ft=1;
//          if($key==0)
//          {
//            $lastdaydestination=get_destination_before($userid,$start);
//          }
//          else
//          {
//            $lastdaydestination=$row['destination_city'];
//          }
//          $hqdistance=get_distance_hq($userid,$row['meet_id']);
//          $hq= get_user_deatils($userid)->headquarters_city;
//          $prevdate=$row['doi'];
//          $tpinfo=get_tp_interaction($userid,$row['source_city'],$row['destination_city'],$row['doi']);
          $this->excel->getActiveSheet()->getStyle("A".$k_num)->getFont()->getColor()->setRGB('808080');
          $this->excel->getActiveSheet()->setCellValue('A'.$k_num, date('d.m.Y',$row['filled_date']));
          $this->excel->getActiveSheet()->setCellValue('B'.$k_num, date('d.m.Y',$row['ineraction_date']));
          $this->excel->getActiveSheet()->setCellValue('C'.$k_num, $row['from_city']);
//          $frmCity=get_city_name($row['source_city']);
//          if($row['up_down'])
//          {
//            $this->excel->getActiveSheet()->setCellValue('E'.$k_num, get_city_name(get_user_deatils($userid)->headquarters_city));
//            $toCity=get_city_name(get_user_deatils($userid)->headquarters_city);
////          }
//          else
//          {
            $this->excel->getActiveSheet()->setCellValue('E'.$k_num, $row['city_worked']);
//            $toCity=get_city_name($row['destination_city']);
//          }
          $this->excel->getActiveSheet()->setCellValue('D'.$k_num, $row['city_worked']);
//          $workCity=get_city_name($row['destination_city']);

          $this->excel->getActiveSheet()->setCellValue('F'.$k_num, $row['distance']);
//           $this->excel->getActiveSheet()->setCellValue('F'.$k_num, $row['stp_distance']);
//          $is_metro=is_city_metro($row['destination_city']);
//          $designation_id=get_user_deatils($userid)->user_designation_id;
//          if($row['source_city']==$row['destination_city'])
//          {
//            if($row['distance']==1)
//            {
//              $this->excel->getActiveSheet()->setCellValue('K'.$k_num, 0);
//              $this->excel->getActiveSheet()->setCellValue('L'.$k_num, 0);
//              $this->excel->getActiveSheet()->setCellValue('F'.$k_num, 0);
//              $this->excel->getActiveSheet()->setCellValue('G'.$k_num, 0);
//              $row['ta']=0;
//              $row['distance']=0;
//              $row['stp_ta']=0;
//              $row['stp_distance']=0;
//            }
//            else
//            {
//              $this->excel->getActiveSheet()->setCellValue('K'.$k_num, $row['distance']);
              $this->excel->getActiveSheet()->setCellValue('G'.$k_num, $row['ta']);
              $this->excel->getActiveSheet()->setCellValue('H'.$k_num, $row['da']);
              $this->excel->getActiveSheet()->setCellValue('I'.$k_num, $row['postage']);
              
              $this->excel->getActiveSheet()->setCellValue('J'.$k_num, $row['total_amount']);
              
              $this->excel->getActiveSheet()->setCellValue('K'.$k_num, $row['aoc_name']);
              $this->excel->getActiveSheet()->setCellValue('L'.$k_num, $row['aoc_amount']);
              $this->excel->getActiveSheet()->setCellValue('M'.$k_num, $row['manager_remark']);
              $this->excel->getActiveSheet()->setCellValue('N'.$k_num, $row['admin_remark']);
              
               $this->excel->getActiveSheet()->setCellValue('O'.$k_num, $row['back_ho']);
//            }
//          }
//          else
//          {
//            if($row['ta']==0)
//            {
//              $this->excel->getActiveSheet()->setCellValue('G'.$k_num,"Actual Cost");
//              $this->excel->getActiveSheet()->setCellValue('L'.$k_num,"Actual Cost");
//            }
//            else
//            {
//              $this->excel->getActiveSheet()->setCellValue('L'.$k_num, $row['ta']); 
//              $this->excel->getActiveSheet()->setCellValue('G'.$k_num, $row['stp_ta']); 
//            }
//          }
//          $lenght= count($data['tada_report'])-1;
//          $day= date('D',strtotime($row['doi']));
//          if($key!=0 && $data['tada_report'][$key-1]['doi']==$row['doi'])
//          {
//            $da=0;
//          }
//          else
//          {
//            if($row['is_stay']==1 && $row['destination_city']==$lastdaydestination && $hqdistance>75)
//            {
//                 $da=get_user_da(5,$designation_id,$is_metro);        
//            }
//            elseif($row['is_stay']==1 && $row['destination_city']!=$hq && $hqdistance>200)
//            {
//                $da=get_user_da(3,$designation_id,$is_metro); 
//            }
//            elseif($hqdistance>450 && $tpinfo)
//            {
//                $da=get_user_da(2,$designation_id,$is_metro); 
//            }
//            elseif($row['is_stay']==1 && $day=='Sat')
//            {
//              if($key==$lenght)
//              {
//                if($row['destination_city']!=$hq)
//                {
//                  $da=get_user_da(5,$designation_id,$is_metro)+get_user_da(2,$designation_id,$is_metro); 
//                }
//                else
//                {
//                  $da=get_user_da(1,$designation_id,$is_metro); 
//                }
//              }
//              else
//              {
//                if(date('D',strtotime($data['tada_report'][$key+1]['doi']))=='Mon' && $data['tada_report'][$key+1]['destination_city']==$row['destination_city'])
//                {
//                  $da=get_user_da(5,$designation_id,$is_metro)+get_user_da(2,$designation_id,$is_metro);
//                }
//                else
//                {
//                  $da=get_user_da(5,$designation_id,$is_metro); 
//                }
//              }
//            }
//            else
//            {
//              $da=get_user_da(1,$designation_id,$is_metro); 
//            }
//          }
//          if(date('Y-m-d',strtotime($crtddate))==date('Y-m-d',strtotime($row['created_date'])))
//          {
//            $row['internet_charge']=0;
//          }

//          $totalrow=$row['ta']+$da+$row['internet_charge'];
//          $totalstprow=$row['stp_ta']+$da+$row['internet_charge'];
//          $this->excel->getActiveSheet()->setCellValue('H'.$k_num, $da);
//          $this->excel->getActiveSheet()->setCellValue('I'.$k_num, $row['internet_charge']);
//          $this->excel->getActiveSheet()->setCellValue('O'.$k_num, $totalrow);
//          if($row['up_down'])
//          {
//            $this->excel->getActiveSheet()->setCellValue('P'.$k_num, 'Back HO');
//          }
//          $this->excel->getActiveSheet()->setCellValue('M'.$k_num, $da);
//          $this->excel->getActiveSheet()->setCellValue('N'.$k_num, $row['internet_charge']);
//          $this->excel->getActiveSheet()->setCellValue('J'.$k_num, $totalstprow);
//          $gtrow=$gtrow+$totalrow;
          $gtta=$gtta+$row['ta'];
          $gtda=$gtda+ $row['da'];
          $gtpostage=$gtpostage+$row['postage'];
          $gtstprow=$gtstprow+$row['total_amount'];
          $total_aoc_amount = $aoc_amt+$row['aoc_amount'];
          $gtstpta=$gtstpta+$row['ta'];
          $k_num++;
//          $crtddate=$row['created_date'];
//        }
       
//         $crdate = $row['doi'];
       // }
      }
    
    
    $total_payble_amount = $gtstprow+$total_aoc_amount;
      $k_num=$k_num+1;
      $this->excel->getActiveSheet()->getStyle('A'.$k_num.':P'.$k_num)->applyFromArray($styleArray);
      $this->excel->getActiveSheet()->getStyle('A'.$k_num.':P'.$k_num)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
      $this->excel->getActiveSheet()->mergeCells('A'.$k_num.':D'.$k_num);
      $this->excel->getActiveSheet()->getStyle('A'.$k_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      $this->excel->getActiveSheet()->setCellValue('A'.$k_num, 'Total');
      $this->excel->getActiveSheet()->setCellValue('G'.$k_num, $gtta);
      $this->excel->getActiveSheet()->setCellValue('H'.$k_num, $gtda);
      $this->excel->getActiveSheet()->setCellValue('I'.$k_num, $gtpostage);
//      $this->excel->getActiveSheet()->setCellValue('J'.$k_num, $gtrow);
//      $this->excel->getActiveSheet()->setCellValue('G'.$k_num, $gtstpta);
//      $this->excel->getActiveSheet()->setCellValue('M'.$k_num, $gtda);
//      $this->excel->getActiveSheet()->setCellValue('N'.$k_num, $gtpostage);
      $this->excel->getActiveSheet()->setCellValue('J'.$k_num, $gtstprow);
      $this->excel->getActiveSheet()->setCellValue('L'.$k_num, $total_aoc_amount);
       
      $this->excel->getActiveSheet()->setCellValue('M'.$k_num, $data['tada_report'][0]['manager_total_amount']);
      $this->excel->getActiveSheet()->setCellValue('N'.$k_num, $data['tada_report'][0]['admin_total_amount']);
      
      $total_payble_amount_manager = $gtstprow+$data['tada_report'][0]['manager_total_amount'];
       $total_payble_amount_admin = $gtstprow+$data['tada_report'][0]['admin_total_amount'];
      
      $total_payable = 'Total Payable Amount of system:- Rs. '.$total_payble_amount;
//                       . ' Total Payable Amount of Manager:- Rs. '.$total_payble_amount_manager.'<\n>'
//              . 'Total Payable Amount of Admin:- Rs. '.$total_payble_amount_admin;
      
      $k_num++;
      $extra=$k_num+1;
      $this->excel->getActiveSheet()->mergeCells('A'.$k_num.':E'.$extra);
      $this->excel->getActiveSheet()->getStyle('A'.$k_num.':A'.$extra)->applyFromArray($styleArray);
      $this->excel->getActiveSheet()->getStyle('A'.$k_num)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
      $this->excel->getActiveSheet()->setCellValue('A'.$k_num, $total_payable);

      $k_num++;
      $extra=$k_num+1;
      $less=$k_num-1;
      $this->excel->getActiveSheet()->mergeCells('F'.$less.':M'.$extra);
      $this->excel->getActiveSheet()->mergeCells('N'.$less.':P'.$less);
      $this->excel->getActiveSheet()->setCellValue('N'.$less, 'Received On');
      $this->excel->getActiveSheet()->mergeCells('N'.$k_num.':P'.$k_num);
      $this->excel->getActiveSheet()->setCellValue('N'.$k_num, 'Checked By');
      $k=$k_num+1;
      $this->excel->getActiveSheet()->mergeCells('A'.$k.':E'.$extra);
      $this->excel->getActiveSheet()->mergeCells('N'.$k.':P'.$k);
      $this->excel->getActiveSheet()->setCellValue('N'.$k, 'Passed By');
      $this->excel->getActiveSheet()->setCellValue('A'.$extra, 'Total No. of Vouchers.');
      $this->excel->getActiveSheet()->getStyle('F'.$less)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
      $this->excel->getActiveSheet()->getStyle('F'.$less)->applyFromArray($styleArray);
      $this->excel->getActiveSheet()->setCellValue('F'.$less, 'For the HO use only.');
      $styleArray1 = array(
        'borders' => array(
            'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
          )
        )
      );
      $this->excel->getActiveSheet()->getStyle("A1:O".$extra)->applyFromArray($styleArray1);
      $name=preg_replace('/\s+/', '', ucfirst(get_user_name($userid)));
      $filename=$name.'_TADA_Report.xls'; //save our workbook as this file name
      header('Content-Type: application/vnd.ms-excel'); //mime type
      header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
      header('Cache-Control: max-age=0'); //no cache
      $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
      //ob_end_clean();
      //ob_start();
      $objWriter->save('php://output');
      }else{
          redirect($_SERVER['HTTP_REFERER']);
      }
  }


  public function cust_relationship(){
    $request = $this->input->post();
    if(!empty($request)){
      //pr($request); die;
        $report_date = explode('-',$request['report_date'] );
        $followstart_date =  trim($report_date[0]);
        $newstartdate = str_replace('/', '-', $followstart_date);
        //  echo $newstartdate;
        $followend_date =  trim($report_date[1]);
        $newenddate = str_replace('/', '-', $followend_date);
        $start = date('Y-m-d', strtotime($newstartdate))." 00:00:00";
        $end = date('Y-m-d', strtotime($newenddate))." 23:59:59";
        $this->load->library('form_validation');
        /*if(!($request['dealer_id']=='' && $request['pharma_id']=='')&&($request['dealer_id']=='' || $request['pharma_id']==''))
        {*/
        if(isset($request['dealer_id'])){
          $this->relationship_report_dealer($request['dealer_id'],$start,$end); 
        }
        if(isset($request['pharma_id'])){
          $this->relationship_report_pharma($request['pharma_id'],$start,$end);  
        }
    }
    else{
      redirect($_SERVER['HTTP_REFERER']);
    }
  }

  public function sale_travel(){
    $request = $this->input->post();
    if(!empty($request)){
      $report_date = explode('-',$request['report_date'] );
      $followstart_date =  trim($report_date[0]);
      $newstartdate = str_replace('/', '-', $followstart_date);
      //echo $newstartdate;
      $followend_date =  trim($report_date[1]);
      $newenddate = str_replace('/', '-', $followend_date);
      //pr($report_date); 
      $start = date('Y-m-d', strtotime($newstartdate))." 00:00:00";
      $end = date('Y-m-d', strtotime($newenddate))." 23:59:59";
      //echo $end."<br>";
      //echo $start; die;
      $this->load->library('form_validation');
      $this->form_validation->set_rules('user_id', 'User', 'required');
      $this->form_validation->set_rules('report_date', 'Report Date range', 'required'); 
      $this->form_validation->set_rules('r1', 'Report Type', 'required'); 
      if($this->form_validation->run() == TRUE){
        if($request['r1']=="sales"){
          $this->sale_report($request['user_id'],$start,$end);
        }
        if($request['r1']=="travel"){

          $this->travel_report($request['user_id'],$start,$end);  
        }
      }
      else{
        $this->index();  
      }
    }
    else{
      redirect('admin_control/report'); 
   }
  }

  // Customer Relationship Report  
  public function relationship_report_dealer_user($dealer_id,$start,$end)
  {
    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Relationship Report');
    $this->excel->getActiveSheet()->setCellValue('A1', 'Date');
    $this->excel->getActiveSheet()->setCellValue('B1', 'Customer');
    $this->excel->getActiveSheet()->setCellValue('C1', 'City');
    $this->excel->getActiveSheet()->setCellValue('D1', 'Type');
    $this->excel->getActiveSheet()->setCellValue('E1', 'User');            
    $this->excel->getActiveSheet()->setCellValue('F1', 'Primary Sale');
    $this->excel->getActiveSheet()->setCellValue('G1', 'Payment');
    $this->excel->getActiveSheet()->setCellValue('H1', 'Stock Date');
    $this->excel->getActiveSheet()->setCellValue('I1', 'Stock');          
    $this->excel->getActiveSheet()->setCellValue('J1', 'Secondary Sale');
    $data['rr_dealer'] =$this->report->relationship_report_dealer($dealer_id,$start,$end); // for Dealer
    $k_num=2;
    $total_secondary_sale=0;
    if(!empty($data['rr_dealer']['dealer_info'])){
      foreach ( $data['rr_dealer']['dealer_info'] as $k=>$row){   // for Dealer information 
        if($row['user_id']==logged_user_data())
        {
          $this->excel->getActiveSheet()->setCellValue('A'.$k_num, date('d.m.Y', strtotime($row['date'])) );
          $this->excel->getActiveSheet()->setCellValue('B'.$k_num, $row['customer']);
          $this->excel->getActiveSheet()->setCellValue('C'.$k_num, $row['city']);
          if(empty($row['is_cf'])){
            $this->excel->getActiveSheet()->setCellValue('D'.$k_num, 'Dealer');
          }
          else{
            $this->excel->getActiveSheet()->setCellValue('D'.$k_num, 'C & F'); 
          }
          $this->excel->getActiveSheet()->setCellValue('E'.$k_num, $row['user']);
          $this->excel->getActiveSheet()->setCellValue('F'.$k_num,$row['sale']);
          $this->excel->getActiveSheet()->setCellValue('G'.$k_num, $row['payment']);
          $this->excel->getActiveSheet()->setCellValue('H'.$k_num, date('d.m.Y', strtotime($row['date'])));
          $this->excel->getActiveSheet()->setCellValue('I'.$k_num, $row['stock']);
          $this->excel->getActiveSheet()->setCellValue('J'.$k_num, '');
          $total_secondary_sale += $row['sale'];
          $k_num++;
        }
      }
      $total_positon = count($data['rr_dealer']['dealer_info'])+2;
      $this->excel->getActiveSheet()->setCellValue('B'.$total_positon, 'TOTAL');
      $this->excel->getActiveSheet()->setCellValue('F'.$total_positon, 'Rs.'.$total_secondary_sale);
      $this->excel->getActiveSheet()->getStyle('B'.$total_positon)->getFont()->setBold(true);
      $this->excel->getActiveSheet()->getStyle('F'.$total_positon)->getFont()->setBold(true);
    }
    $k_num_int = count($data['rr_dealer']['dealer_info'])+4;
    $total_secondary_sale_doc=0;
    if(!empty($data['rr_dealer']['dealer_doc_relation'])){
      foreach ( $data['rr_dealer']['dealer_doc_relation'] as $k_doc=>$row_doc){   // for Doctor information 
        if($row_doc['user_id']==logged_user_data())
        {
          $this->excel->getActiveSheet()->setCellValue('A'.$k_num_int, date('d.m.Y', strtotime($row_doc['date'])) );
          $this->excel->getActiveSheet()->setCellValue('B'.$k_num_int, $row_doc['customer']);
          $this->excel->getActiveSheet()->setCellValue('C'.$k_num_int, $row_doc['city']);
          $this->excel->getActiveSheet()->setCellValue('D'.$k_num_int, 'Doctor');
          $this->excel->getActiveSheet()->setCellValue('E'.$k_num_int, $row_doc['user']);
          $this->excel->getActiveSheet()->setCellValue('F'.$k_num_int,'');
          $this->excel->getActiveSheet()->setCellValue('G'.$k_num_int, '');
          $this->excel->getActiveSheet()->setCellValue('H'.$k_num_int, '');
          $this->excel->getActiveSheet()->setCellValue('I'.$k_num_int, '');
          $this->excel->getActiveSheet()->setCellValue('J'.$k_num_int, $row_doc['secondary_sale']);
          $total_secondary_sale_doc += $row_doc['secondary_sale'];
          $k_num_int++;
        }
      }    
    }
    if(!empty($data['rr_dealer']['dealer_pharma_relation'])){
      foreach ( $data['rr_dealer']['dealer_pharma_relation'] as $k_doc=>$row_doc){   // for dealer pharmacy relation
        if($row_doc['user_id']==logged_user_data())
        {
          $this->excel->getActiveSheet()->setCellValue('A'.$k_num_int, date('d.m.Y', strtotime($row_doc['date'])) );
          $this->excel->getActiveSheet()->setCellValue('B'.$k_num_int, $row_doc['customer']);
          $this->excel->getActiveSheet()->setCellValue('C'.$k_num_int, $row_doc['city']);
          $this->excel->getActiveSheet()->setCellValue('D'.$k_num_int, 'Sub Dealer');
          $this->excel->getActiveSheet()->setCellValue('E'.$k_num_int, $row_doc['user']);
          $this->excel->getActiveSheet()->setCellValue('F'.$k_num_int,'');
          $this->excel->getActiveSheet()->setCellValue('G'.$k_num_int, '');
          $this->excel->getActiveSheet()->setCellValue('H'.$k_num_int, '');
          $this->excel->getActiveSheet()->setCellValue('I'.$k_num_int, '');
          $this->excel->getActiveSheet()->setCellValue('J'.$k_num_int, $row_doc['secondary_sale']);
          $total_secondary_sale_doc += $row_doc['secondary_sale'];
          $k_num_int++;
        }
      }
    }
    $total_positon_doc = count($data['rr_dealer']['dealer_pharma_relation'])+count($data['rr_dealer']['dealer_info'])+count($data['rr_dealer']['dealer_doc_relation'])+6;
    $this->excel->getActiveSheet()->setCellValue('B'.$total_positon_doc, 'TOTAL');
    $this->excel->getActiveSheet()->setCellValue('J'.$total_positon_doc, 'Rs.'.$total_secondary_sale_doc);
    $this->excel->getActiveSheet()->getStyle('B'.$total_positon_doc)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->getStyle('J'.$total_positon_doc)->getFont()->setBold(true);
    $relationship_percentage =  $total_positon_doc+2;
    if($total_secondary_sale!=0){
      $result_pecentage = ($total_secondary_sale_doc/$total_secondary_sale)*100 ;
    }
    else{
      $result_pecentage=0;  
    }
    $this->excel->getActiveSheet()->setCellValue('A'.$relationship_percentage,'B. Jain Contribution' );
    $this->excel->getActiveSheet()->setCellValue('B'.$relationship_percentage,$result_pecentage.'%' );
    $this->excel->getActiveSheet()->getStyle('A'.$relationship_percentage)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->getStyle('B'.$relationship_percentage)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $this->excel->getActiveSheet()->getStyle('C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $filename='DealerReportCard.xls'; //save our workbook as this file name
    header('Content-Type: application/vnd.ms-excel'); //mime type
    header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
    header('Cache-Control: max-age=0'); //no cache
    $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
    $objWriter->save('php://output');
  }

  public function relationship_report_pharma_user($pharma_id,$start,$end)
  {
    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Relationship Report');
    $this->excel->getActiveSheet()->setCellValue('A1', 'Date');
    $this->excel->getActiveSheet()->setCellValue('B1', 'Customer');
    $this->excel->getActiveSheet()->setCellValue('C1', 'City');
    $this->excel->getActiveSheet()->setCellValue('D1', 'Type');
    $this->excel->getActiveSheet()->setCellValue('E1', 'User');            
    $this->excel->getActiveSheet()->setCellValue('F1', 'Primary Sale');
    $this->excel->getActiveSheet()->setCellValue('G1', 'Payment');
    $this->excel->getActiveSheet()->setCellValue('H1', 'Stock Date');
    $this->excel->getActiveSheet()->setCellValue('I1', 'Stock');          
    $this->excel->getActiveSheet()->setCellValue('J1', 'Secondary Sale');
    $data['rr_pharmacy'] =$this->report->relationship_report_pharmacy($pharma_id,$start,$end); // for Sub Dealer
    $k_num=2;
    $total_secondary_sale=0;
    if(!empty($data['rr_pharmacy']['pharmacy_info'])){
      foreach ( $data['rr_pharmacy']['pharmacy_info'] as $k=>$row){   // for Sub Dealer information 
        if($row['user_id']==logged_user_data())
        {
          $this->excel->getActiveSheet()->setCellValue('A'.$k_num, date('d.m.Y', strtotime($row['date'])) );
          $this->excel->getActiveSheet()->setCellValue('B'.$k_num, $row['customer']);
          $this->excel->getActiveSheet()->setCellValue('C'.$k_num, $row['city']);
          $this->excel->getActiveSheet()->setCellValue('D'.$k_num, 'Sub Dealer');
          $this->excel->getActiveSheet()->setCellValue('E'.$k_num, $row['user']);
          $this->excel->getActiveSheet()->setCellValue('F'.$k_num,'');
          $this->excel->getActiveSheet()->setCellValue('G'.$k_num, '');
          $this->excel->getActiveSheet()->setCellValue('H'.$k_num, '');
          $this->excel->getActiveSheet()->setCellValue('I'.$k_num, '');
          $this->excel->getActiveSheet()->setCellValue('J'.$k_num, $row['secondary_sale']);
          $total_secondary_sale += $row['secondary_sale'];
          $k_num++;
        }
      }    
      $total_positon = count($data['rr_pharmacy']['pharmacy_info'])+2;
      $this->excel->getActiveSheet()->setCellValue('B'.$total_positon, 'TOTAL');
      $this->excel->getActiveSheet()->setCellValue('J'.$total_positon, 'Rs.'.$total_secondary_sale);
      $this->excel->getActiveSheet()->getStyle('B'.$total_positon)->getFont()->setBold(true);
      $this->excel->getActiveSheet()->getStyle('J'.$total_positon)->getFont()->setBold(true);
    }
    $k_num_int = count($data['rr_pharmacy']['pharmacy_info'])+4;
    $total_secondary_sale_doc=0;
    if(!empty($data['rr_pharmacy']['pharma_doc_relation'])){
      foreach ( $data['rr_pharmacy']['pharma_doc_relation'] as $k_doc=>$row_doc){   // for Doctor information
        if($row_doc['user_id']==logged_user_data())
        { 
          $this->excel->getActiveSheet()->setCellValue('A'.$k_num_int, date('d.m.Y', strtotime($row_doc['date'])) );
          $this->excel->getActiveSheet()->setCellValue('B'.$k_num_int, $row_doc['customer']);
          $this->excel->getActiveSheet()->setCellValue('C'.$k_num_int, $row_doc['city']);
          $this->excel->getActiveSheet()->setCellValue('D'.$k_num_int, 'Doctor');
          $this->excel->getActiveSheet()->setCellValue('E'.$k_num_int, $row_doc['user']);
          $this->excel->getActiveSheet()->setCellValue('F'.$k_num_int,'');
          $this->excel->getActiveSheet()->setCellValue('G'.$k_num_int, '');
          $this->excel->getActiveSheet()->setCellValue('H'.$k_num_int, '');
          $this->excel->getActiveSheet()->setCellValue('I'.$k_num_int, '');
          $this->excel->getActiveSheet()->setCellValue('J'.$k_num_int, $row_doc['secondary_sale']);
          $total_secondary_sale_doc += $row_doc['secondary_sale'];
          $k_num_int++;
        }
      }
    }
    $total_positon_doc = count($data['rr_pharmacy']['pharmacy_info'])+count($data['rr_pharmacy']['pharma_doc_relation'])+6;
    $this->excel->getActiveSheet()->setCellValue('B'.$total_positon_doc, 'TOTAL');
    $this->excel->getActiveSheet()->setCellValue('J'.$total_positon_doc, 'Rs.'.$total_secondary_sale_doc);
    $this->excel->getActiveSheet()->getStyle('B'.$total_positon_doc)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->getStyle('J'.$total_positon_doc)->getFont()->setBold(true);
    $relationship_percentage =  $total_positon_doc+2;
    if($total_secondary_sale !=0){
      $result_pecentage = ($total_secondary_sale_doc/$total_secondary_sale)*100 ;
    }
    else{
      $result_pecentage=0;
    }
    $this->excel->getActiveSheet()->setCellValue('A'.$relationship_percentage,'B. Jain Contribution' );
    $this->excel->getActiveSheet()->setCellValue('B'.$relationship_percentage,$result_pecentage.'%' );
    $this->excel->getActiveSheet()->getStyle('A'.$relationship_percentage)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->getStyle('B'.$relationship_percentage)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $this->excel->getActiveSheet()->getStyle('C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $filename='PharmacyReportCard.xls'; //save our workbook as this file name
    header('Content-Type: application/vnd.ms-excel'); //mime type
    header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
    header('Cache-Control: max-age=0'); //no cache
    $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
    $objWriter->save('php://output');
  }

  // Customer Relationship Report  
  public function relationship_report_dealer($dealer_id,$start,$end)
  {
    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Relationship Report');
    $this->excel->getActiveSheet()->setCellValue('A1', 'Date');
    $this->excel->getActiveSheet()->setCellValue('B1', 'Customer');
    $this->excel->getActiveSheet()->setCellValue('C1', 'City');
    $this->excel->getActiveSheet()->setCellValue('D1', 'Type');
    $this->excel->getActiveSheet()->setCellValue('E1', 'User');            
    $this->excel->getActiveSheet()->setCellValue('F1', 'Primary Sale');
    $this->excel->getActiveSheet()->setCellValue('G1', 'Payment');
    $this->excel->getActiveSheet()->setCellValue('H1', 'Stock Date');
    $this->excel->getActiveSheet()->setCellValue('I1', 'Stock');          
    $this->excel->getActiveSheet()->setCellValue('J1', 'Secondary Sale');
    $data['rr_dealer'] =$this->report->relationship_report_dealer($dealer_id,$start,$end); // for Dealer
    $k_num=2;
    $total_secondary_sale=0;
    if(!empty($data['rr_dealer']['dealer_info'])){
      foreach ( $data['rr_dealer']['dealer_info'] as $k=>$row){   // for Dealer information 
        $this->excel->getActiveSheet()->setCellValue('A'.$k_num, date('d.m.Y', strtotime($row['date'])) );
        $this->excel->getActiveSheet()->setCellValue('B'.$k_num, $row['customer']);
        $this->excel->getActiveSheet()->setCellValue('C'.$k_num, $row['city']);
        if(empty($row['is_cf'])){
          $this->excel->getActiveSheet()->setCellValue('D'.$k_num, 'Dealer');
        }
        else{
          $this->excel->getActiveSheet()->setCellValue('D'.$k_num, 'C & F'); 
        }
        $this->excel->getActiveSheet()->setCellValue('E'.$k_num, $row['user']);
        $this->excel->getActiveSheet()->setCellValue('F'.$k_num,$row['sale']);
        $this->excel->getActiveSheet()->setCellValue('G'.$k_num, $row['payment']);
        $this->excel->getActiveSheet()->setCellValue('H'.$k_num, date('d.m.Y', strtotime($row['date'])));
        $this->excel->getActiveSheet()->setCellValue('I'.$k_num, $row['stock']);
        $this->excel->getActiveSheet()->setCellValue('J'.$k_num, '');
        $total_secondary_sale += $row['sale'];
        $k_num++;
      }
      $total_positon = count($data['rr_dealer']['dealer_info'])+2;
      $this->excel->getActiveSheet()->setCellValue('B'.$total_positon, 'TOTAL');
      $this->excel->getActiveSheet()->setCellValue('F'.$total_positon, 'Rs.'.$total_secondary_sale);
      $this->excel->getActiveSheet()->getStyle('B'.$total_positon)->getFont()->setBold(true);
      $this->excel->getActiveSheet()->getStyle('F'.$total_positon)->getFont()->setBold(true);
    }
    $k_num_int = count($data['rr_dealer']['dealer_info'])+4;
    $total_secondary_sale_doc=0;
    if(!empty($data['rr_dealer']['dealer_doc_relation'])){
      foreach ( $data['rr_dealer']['dealer_doc_relation'] as $k_doc=>$row_doc){   // for Doctor information 
        $this->excel->getActiveSheet()->setCellValue('A'.$k_num_int, date('d.m.Y', strtotime($row_doc['date'])) );
        $this->excel->getActiveSheet()->setCellValue('B'.$k_num_int, $row_doc['customer']);
        $this->excel->getActiveSheet()->setCellValue('C'.$k_num_int, $row_doc['city']);
        $this->excel->getActiveSheet()->setCellValue('D'.$k_num_int, 'Doctor');
        $this->excel->getActiveSheet()->setCellValue('E'.$k_num_int, $row_doc['user']);
        $this->excel->getActiveSheet()->setCellValue('F'.$k_num_int,'');
        $this->excel->getActiveSheet()->setCellValue('G'.$k_num_int, '');
        $this->excel->getActiveSheet()->setCellValue('H'.$k_num_int, '');
        $this->excel->getActiveSheet()->setCellValue('I'.$k_num_int, '');
        $this->excel->getActiveSheet()->setCellValue('J'.$k_num_int, $row_doc['secondary_sale']);
        $total_secondary_sale_doc += $row_doc['secondary_sale'];
        $k_num_int++;
      }    
    }
    if(!empty($data['rr_dealer']['dealer_pharma_relation'])){
      foreach ( $data['rr_dealer']['dealer_pharma_relation'] as $k_doc=>$row_doc){   // for dealer pharmacy relation
        $this->excel->getActiveSheet()->setCellValue('A'.$k_num_int, date('d.m.Y', strtotime($row_doc['date'])) );
        $this->excel->getActiveSheet()->setCellValue('B'.$k_num_int, $row_doc['customer']);
        $this->excel->getActiveSheet()->setCellValue('C'.$k_num_int, $row_doc['city']);
        $this->excel->getActiveSheet()->setCellValue('D'.$k_num_int, 'Sub Dealer');
        $this->excel->getActiveSheet()->setCellValue('E'.$k_num_int, $row_doc['user']);
        $this->excel->getActiveSheet()->setCellValue('F'.$k_num_int,'');
        $this->excel->getActiveSheet()->setCellValue('G'.$k_num_int, '');
        $this->excel->getActiveSheet()->setCellValue('H'.$k_num_int, '');
        $this->excel->getActiveSheet()->setCellValue('I'.$k_num_int, '');
        $this->excel->getActiveSheet()->setCellValue('J'.$k_num_int, $row_doc['secondary_sale']);
        $total_secondary_sale_doc += $row_doc['secondary_sale'];
        $k_num_int++;
      }
    }
    $total_positon_doc = count($data['rr_dealer']['dealer_pharma_relation'])+count($data['rr_dealer']['dealer_info'])+count($data['rr_dealer']['dealer_doc_relation'])+6;
    $this->excel->getActiveSheet()->setCellValue('B'.$total_positon_doc, 'TOTAL');
    $this->excel->getActiveSheet()->setCellValue('J'.$total_positon_doc, 'Rs.'.$total_secondary_sale_doc);
    $this->excel->getActiveSheet()->getStyle('B'.$total_positon_doc)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->getStyle('J'.$total_positon_doc)->getFont()->setBold(true);
    $relationship_percentage =  $total_positon_doc+2;
    if($total_secondary_sale!=0){
      $result_pecentage = ($total_secondary_sale_doc/$total_secondary_sale)*100 ;
    }
    else{
      $result_pecentage=0;  
    }
    $this->excel->getActiveSheet()->setCellValue('A'.$relationship_percentage,'B. Jain Contribution' );
    $this->excel->getActiveSheet()->setCellValue('B'.$relationship_percentage,$result_pecentage.'%' );
    $this->excel->getActiveSheet()->getStyle('A'.$relationship_percentage)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->getStyle('B'.$relationship_percentage)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $this->excel->getActiveSheet()->getStyle('C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $filename='DealerReportCard.xls'; //save our workbook as this file name
    header('Content-Type: application/vnd.ms-excel'); //mime type
    header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
    header('Cache-Control: max-age=0'); //no cache
    $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
    $objWriter->save('php://output');
  }

  public function relationship_report_pharma($pharma_id,$start,$end)
  {
    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Relationship Report');
    $this->excel->getActiveSheet()->setCellValue('A1', 'Date');
    $this->excel->getActiveSheet()->setCellValue('B1', 'Customer');
    $this->excel->getActiveSheet()->setCellValue('C1', 'City');
    $this->excel->getActiveSheet()->setCellValue('D1', 'Type');
    $this->excel->getActiveSheet()->setCellValue('E1', 'User');            
    $this->excel->getActiveSheet()->setCellValue('F1', 'Primary Sale');
    $this->excel->getActiveSheet()->setCellValue('G1', 'Payment');
    $this->excel->getActiveSheet()->setCellValue('H1', 'Stock Date');
    $this->excel->getActiveSheet()->setCellValue('I1', 'Stock');          
    $this->excel->getActiveSheet()->setCellValue('J1', 'Secondary Sale');
    $data['rr_pharmacy'] =$this->report->relationship_report_pharmacy($pharma_id,$start,$end); // for Sub Dealer
    $k_num=2;
    $total_secondary_sale=0;
    if(!empty($data['rr_pharmacy']['pharmacy_info'])){
      foreach ( $data['rr_pharmacy']['pharmacy_info'] as $k=>$row){   // for Sub Dealer information 
        $this->excel->getActiveSheet()->setCellValue('A'.$k_num, date('d.m.Y', strtotime($row['date'])) );
        $this->excel->getActiveSheet()->setCellValue('B'.$k_num, $row['customer']);
        $this->excel->getActiveSheet()->setCellValue('C'.$k_num, $row['city']);
        $this->excel->getActiveSheet()->setCellValue('D'.$k_num, 'Sub Dealer');
        $this->excel->getActiveSheet()->setCellValue('E'.$k_num, $row['user']);
        $this->excel->getActiveSheet()->setCellValue('F'.$k_num,'');
        $this->excel->getActiveSheet()->setCellValue('G'.$k_num, '');
        $this->excel->getActiveSheet()->setCellValue('H'.$k_num, '');
        $this->excel->getActiveSheet()->setCellValue('I'.$k_num, '');
        $this->excel->getActiveSheet()->setCellValue('J'.$k_num, $row['secondary_sale']);
        $total_secondary_sale += $row['secondary_sale'];
        $k_num++;
      }    
      $total_positon = count($data['rr_pharmacy']['pharmacy_info'])+2;
      $this->excel->getActiveSheet()->setCellValue('B'.$total_positon, 'TOTAL');
      $this->excel->getActiveSheet()->setCellValue('J'.$total_positon, 'Rs.'.$total_secondary_sale);
      $this->excel->getActiveSheet()->getStyle('B'.$total_positon)->getFont()->setBold(true);
      $this->excel->getActiveSheet()->getStyle('J'.$total_positon)->getFont()->setBold(true);
    }
    $k_num_int = count($data['rr_pharmacy']['pharmacy_info'])+4;
    $total_secondary_sale_doc=0;
    if(!empty($data['rr_pharmacy']['pharma_doc_relation'])){
      foreach ( $data['rr_pharmacy']['pharma_doc_relation'] as $k_doc=>$row_doc){   // for Doctor information 
        $this->excel->getActiveSheet()->setCellValue('A'.$k_num_int, date('d.m.Y', strtotime($row_doc['date'])) );
        $this->excel->getActiveSheet()->setCellValue('B'.$k_num_int, $row_doc['customer']);
        $this->excel->getActiveSheet()->setCellValue('C'.$k_num_int, $row_doc['city']);
        $this->excel->getActiveSheet()->setCellValue('D'.$k_num_int, 'Doctor');
        $this->excel->getActiveSheet()->setCellValue('E'.$k_num_int, $row_doc['user']);
        $this->excel->getActiveSheet()->setCellValue('F'.$k_num_int,'');
        $this->excel->getActiveSheet()->setCellValue('G'.$k_num_int, '');
        $this->excel->getActiveSheet()->setCellValue('H'.$k_num_int, '');
        $this->excel->getActiveSheet()->setCellValue('I'.$k_num_int, '');
        $this->excel->getActiveSheet()->setCellValue('J'.$k_num_int, $row_doc['secondary_sale']);
        $total_secondary_sale_doc += $row_doc['secondary_sale'];
        $k_num_int++;
      }
    }
    $total_positon_doc = count($data['rr_pharmacy']['pharmacy_info'])+count($data['rr_pharmacy']['pharma_doc_relation'])+6;
    $this->excel->getActiveSheet()->setCellValue('B'.$total_positon_doc, 'TOTAL');
    $this->excel->getActiveSheet()->setCellValue('J'.$total_positon_doc, 'Rs.'.$total_secondary_sale_doc);
    $this->excel->getActiveSheet()->getStyle('B'.$total_positon_doc)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->getStyle('J'.$total_positon_doc)->getFont()->setBold(true);
    $relationship_percentage =  $total_positon_doc+2;
    if($total_secondary_sale !=0){
      $result_pecentage = ($total_secondary_sale_doc/$total_secondary_sale)*100 ;
    }
    else{
      $result_pecentage=0;
    }
    $this->excel->getActiveSheet()->setCellValue('A'.$relationship_percentage,'B. Jain Contribution' );
    $this->excel->getActiveSheet()->setCellValue('B'.$relationship_percentage,$result_pecentage.'%' );
    $this->excel->getActiveSheet()->getStyle('A'.$relationship_percentage)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->getStyle('B'.$relationship_percentage)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $this->excel->getActiveSheet()->getStyle('C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $filename='PharmacyReportCard.xls'; //save our workbook as this file name
    header('Content-Type: application/vnd.ms-excel'); //mime type
    header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
    header('Cache-Control: max-age=0'); //no cache
    $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
    $objWriter->save('php://output');
  }

  // Travel Report 
  public function travel_report($userid,$start,$end)
  {

    $this->excel->setActiveSheetIndex(0);
    //name the worksheet
    $this->excel->getActiveSheet()->setTitle('Travel Report');
    //set cell A1 content with some text
    $this->excel->getActiveSheet()->setCellValue('A1', 'Date');
    $this->excel->getActiveSheet()->setCellValue('B1', 'Customer');
    $this->excel->getActiveSheet()->setCellValue('C1', 'City');
    $this->excel->getActiveSheet()->setCellValue('D1', 'Type');
    $this->excel->getActiveSheet()->setCellValue('E1', 'User');
    //$this->excel->getActiveSheet()->setCellValue('E1', 'Total Visits');
    $this->excel->getActiveSheet()->setCellValue('F1', 'Primary Sale');
    $this->excel->getActiveSheet()->setCellValue('G1', 'Payment');
    // $this->excel->getActiveSheet()->setCellValue('H1', 'Stock Date');
    $this->excel->getActiveSheet()->setCellValue('H1', 'Stock');
    $this->excel->getActiveSheet()->setCellValue('I1', 'Sample');
    $this->excel->getActiveSheet()->setCellValue('J1', 'Discussion');
    $this->excel->getActiveSheet()->setCellValue('K1', 'Not Met');
    $this->excel->getActiveSheet()->setCellValue('L1', 'Telephonic Order');
    $this->excel->getActiveSheet()->setCellValue('M1', 'Physical Order');
    $this->excel->getActiveSheet()->setCellValue('N1', 'Secondary Sale');
    $this->excel->getActiveSheet()->setCellValue('O1', 'Orignal Supply Value');
    $this->excel->getActiveSheet()->setCellValue('P1', 'Date of Supply');
    $this->excel->getActiveSheet()->setCellValue('Q1', 'Remarks');
    $this->excel->getActiveSheet()->setCellValue('R1', 'Joint Working');
    $this->excel->getActiveSheet()->setCellValue('S1', 'Dealer/Sub Dealer');
    $this->excel->getActiveSheet()->setCellValue('T1', 'Payment Term');
    $data['travel'] =$this->report->travel_report_doctor($userid,$start,$end); // for doctor
    $k_num=2;
    if(!empty($data['travel']['doc_info'])){
      foreach ( $data['travel']['doc_info'] as $k=>$row){   // for doctor information with sample
               
        $this->excel->getActiveSheet()->setCellValue('A'.$k_num, date('d.m.Y',strtotime($row['date'])));
        $this->excel->getActiveSheet()->setCellValue('B'.$k_num, $row['customer']);
        $this->excel->getActiveSheet()->setCellValue('C'.$k_num, $row['city']);
        $this->excel->getActiveSheet()->setCellValue('D'.$k_num, 'Doctor');
        $this->excel->getActiveSheet()->setCellValue('E'.$k_num, $row['user']);
        $this->excel->getActiveSheet()->setCellValue('R'.$k_num, get_dealer_pharma_name($row['dealer_id']));
        $this->excel->getActiveSheet()->setCellValue('I'.$k_num, $row['sample']['sample']);

        if($row['metnotmet']!=NULL  && $row['metnotmet']==1){
          $this->excel->getActiveSheet()->setCellValue('J'.$k_num, 'Yes');
        }
        else
        {
          $this->excel->getActiveSheet()->setCellValue('J'.$k_num, 'No');
        }
        if($row['metnotmet']!=NULL && $row['metnotmet']==0)
        {
          $this->excel->getActiveSheet()->setCellValue('K'.$k_num,'Yes');
        }
        else
        {
          $this->excel->getActiveSheet()->setCellValue('K'.$k_num,'No');
        }
        if($row['oncall']!=NULL && $row['oncall']==1)
        {
          $this->excel->getActiveSheet()->setCellValue('L'.$k_num,'Yes');
        }
        else
        {
          $this->excel->getActiveSheet()->setCellValue('L'.$k_num,'No');
        }
        if($row['oncall']==NULL )
        {
          if($row['secondary_sale']!=NULL )
          {
            $this->excel->getActiveSheet()->setCellValue('M'.$k_num,'Yes');
          }
          else
          {
            $this->excel->getActiveSheet()->setCellValue('M'.$k_num,'No');
          }
        }
        else
        {
          $this->excel->getActiveSheet()->setCellValue('M'.$k_num,'No');
        }
        $this->excel->getActiveSheet()->setCellValue('N'.$k_num, $row['secondary_sale']);
        $paymentterm=get_payment_term($row['id'],$row['doc_id']);
        $this->excel->getActiveSheet()->setCellValue('T'.$k_num, $paymentterm);
        $this->excel->getActiveSheet()->setCellValue('O'.$k_num, $row['order_supply']);
        if($row['date_of_supply'] != NULL){
          $this->excel->getActiveSheet()->setCellValue('P'.$k_num, date('d.m.Y', strtotime($row['date_of_supply'])));
        }
        $this->excel->getActiveSheet()->setCellValue('Q'.$k_num, $row['remark']);
       // $this->excel->getActiveSheet()->setCellValue('Q'.$k_num, $row['oncall']);
        $k_num++;
      }
    }
      $k_num_team=2;
       if(!empty($data['travel']['team_info'])){
      foreach($data['travel']['team_info'] as $k_team=>$row_team){
        $this->excel->getActiveSheet()->setCellValue('S'.$k_num_team, $row_team['team_user']);
        $k_num_team++;
      }
       }        
      $data['travel_dealer'] =$this->report->travel_report_dealer($userid,$start,$end);   // for dealer
      $k_num= count($data['travel']['doc_info'])+2;
      if(!empty($data['travel_dealer']['dealer_info'])){
        foreach ( $data['travel_dealer']['dealer_info'] as $k=>$row){   // for dealer information with sample
          $this->excel->getActiveSheet()->setCellValue('A'.$k_num, date('d.m.Y',strtotime($row['date'])));
          $this->excel->getActiveSheet()->setCellValue('B'.$k_num, $row['customer']);
          $this->excel->getActiveSheet()->setCellValue('C'.$k_num, $row['city']);
          if(empty($row['is_cf'])){
            $this->excel->getActiveSheet()->setCellValue('D'.$k_num, 'Dealer');
          }
          else{
            $this->excel->getActiveSheet()->setCellValue('D'.$k_num, 'C & F'); 
          }
          $this->excel->getActiveSheet()->setCellValue('E'.$k_num, $row['user']); 
          $this->excel->getActiveSheet()->setCellValue('F'.$k_num, $row['sale']);
          $paymentterm=get_payment_term($row['id'],$row['d_id']);
          $this->excel->getActiveSheet()->setCellValue('T'.$k_num, $paymentterm);

          $this->excel->getActiveSheet()->setCellValue('G'.$k_num, $row['payment']);
          $this->excel->getActiveSheet()->setCellValue('H'.$k_num, $row['stock']);
          $this->excel->getActiveSheet()->setCellValue('I'.$k_num, $row['sample']['sample']);
          if($row['metnotmet']!=NULL  && $row['metnotmet']==1){
          $this->excel->getActiveSheet()->setCellValue('J'.$k_num, 'Yes');
        }
        else
        {
          $this->excel->getActiveSheet()->setCellValue('J'.$k_num, 'No');
        }
        if($row['metnotmet']!=NULL && $row['metnotmet']==0)
        {
          $this->excel->getActiveSheet()->setCellValue('K'.$k_num,'Yes');
        }
        else
        {
          $this->excel->getActiveSheet()->setCellValue('K'.$k_num,'No');
        }
        if($row['oncall']!=NULL && $row['oncall']==1)
        {
          $this->excel->getActiveSheet()->setCellValue('L'.$k_num,'Yes');
        }
        else
        {
          $this->excel->getActiveSheet()->setCellValue('L'.$k_num,'No');
        }
        if($row['oncall']==NULL )
        {
          if($row['sale']!=NULL )
          {
            $this->excel->getActiveSheet()->setCellValue('M'.$k_num,'Yes');
          }
          else
          {
            $this->excel->getActiveSheet()->setCellValue('M'.$k_num,'No');
          }
          
        }
        else
        {
          $this->excel->getActiveSheet()->setCellValue('M'.$k_num,'No');
        }
          $this->excel->getActiveSheet()->setCellValue('Q'.$k_num, $row['remark']);
        //  $this->excel->getActiveSheet()->setCellValue('Q'.$k_num, $row['oncall']);
          $k_num++;         
        }
        $k_num_team=count($data['travel']['doc_info'])+2;
        foreach($data['travel_dealer']['team_info'] as $k_team=>$row_team){
          $this->excel->getActiveSheet()->setCellValue('S'.$k_num_team, $row_team['team_user']);
          $k_num_team++;
        }
      }  
      $data['travel_pharmacy'] =$this->report->travel_report_pharmacy($userid,$start,$end);   // for pharmacy
      $k_num= count($data['travel_dealer']['dealer_info'])+ count($data['travel']['doc_info'])+2;
      if(!empty($data['travel_pharmacy']['pharmacy_info'])){
        foreach ( $data['travel_pharmacy']['pharmacy_info'] as $k=>$row){   // for pharmacy information with sample
          $this->excel->getActiveSheet()->setCellValue('A'.$k_num, date('d.m.Y',strtotime($row['date'])));
          $this->excel->getActiveSheet()->setCellValue('B'.$k_num, $row['customer']);
          $this->excel->getActiveSheet()->setCellValue('C'.$k_num, $row['city']);
          $this->excel->getActiveSheet()->setCellValue('D'.$k_num, 'Sub Dealer');
          $this->excel->getActiveSheet()->setCellValue('R'.$k_num, get_dealer_pharma_name($row['dealer_id']));
          $this->excel->getActiveSheet()->setCellValue('E'.$k_num, $row['user']);
          $this->excel->getActiveSheet()->setCellValue('I'.$k_num, $row['sample']['sample']);
          if($row['metnotmet']!=NULL  && $row['metnotmet']==1){
          $this->excel->getActiveSheet()->setCellValue('J'.$k_num, 'Yes');
        }
        else
        {
          $this->excel->getActiveSheet()->setCellValue('J'.$k_num, 'No');
        }
        if($row['metnotmet']!=NULL && $row['metnotmet']==0)
        {
          $this->excel->getActiveSheet()->setCellValue('K'.$k_num,'Yes');
        }
        else
        {
          $this->excel->getActiveSheet()->setCellValue('K'.$k_num,'No');
        }
        if($row['oncall']!=NULL && $row['oncall']==1)
        {
          $this->excel->getActiveSheet()->setCellValue('L'.$k_num,'Yes');
        }
        else
        {
          $this->excel->getActiveSheet()->setCellValue('L'.$k_num,'No');
        }
        if($row['oncall']==NULL )
        {
          if($row['secondary_sale']!=NULL )
          {
            $this->excel->getActiveSheet()->setCellValue('M'.$k_num,'Yes');
          }
          else
          {
            $this->excel->getActiveSheet()->setCellValue('M'.$k_num,'No');
          }
        }
        else
        {
          $this->excel->getActiveSheet()->setCellValue('M'.$k_num,'No');
        }
          $this->excel->getActiveSheet()->setCellValue('N'.$k_num, $row['secondary_sale']);
          $paymentterm=get_payment_term($row['id'],$row['pharma_id']);
          $this->excel->getActiveSheet()->setCellValue('T'.$k_num, $paymentterm);
          $this->excel->getActiveSheet()->setCellValue('O'.$k_num, $row['order_supply']);
          if($row['date_of_supply'] != NULL){
          $this->excel->getActiveSheet()->setCellValue('P'.$k_num, date('d.m.Y', strtotime($row['date_of_supply'])));
          }
          $this->excel->getActiveSheet()->setCellValue('Q'.$k_num, $row['remark']);
         // $this->excel->getActiveSheet()->setCellValue('Q'.$k_num, $row['oncall']);
          $k_num++;
        }   
        $k_num_team=count($data['travel_dealer']['dealer_info'])+ count($data['travel']['doc_info'])+2;
        foreach($data['travel_pharmacy']['team_info'] as $k_team=>$row_team){
          $this->excel->getActiveSheet()->setCellValue('S'.$k_num_team, $row_team['team_user']);
          $k_num_team++;
        }
      }
      $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      $this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      $this->excel->getActiveSheet()->getStyle('C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      $filename='TravelReportUser.xls'; //save our workbook as this file name
      header('Content-Type: application/vnd.ms-excel'); //mime type
      header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
      header('Cache-Control: max-age=0'); //no cache
      $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
      $objWriter->save('php://output');
   
  }

  // sale report
  public function sale_report($userid,$start,$end)
  {
    $primarySale=0;
    $secondarySale=0;
    $duplicateSale=0;
    $payment=0;
    $totVisit=0;
    $docMeet=0;
    $k_num_int=0;
    $pharmaMeet=0;
    $docNo=0;
    $pharmaNo=0;

    $docnotmeet=0;
    $subdealernotmeet=0;


    $dealernotmeet=0;
    $dealermeet=0;
    $totday=0;

    $date1=date_create($start);
    $date2=date_create($end);
    $diff=date_diff($date1,$date2);
    $totday = $diff->format("%a")+1;

    if($this->permission->pharmacy_list_user($userid)!=FALSE)
    {
      $pharmaNo = count($this->permission->pharmacy_list_user($userid));
    }
    if($this->permission->doctor_list_user($userid)!=FALSE)
    {
      $docNo = count($this->permission->doctor_list_user($userid));
    }

    $this->excel->setActiveSheetIndex(0);
    //name the worksheet
    $this->excel->getActiveSheet()->setTitle('Sale Report');
    //set cell A1 content with some text
    $this->excel->getActiveSheet()->setCellValue('A1', 'Customer');
    $this->excel->getActiveSheet()->setCellValue('B1', 'City');
    $this->excel->getActiveSheet()->setCellValue('C1', 'Type');
    $this->excel->getActiveSheet()->setCellValue('D1', 'User');
    $this->excel->getActiveSheet()->setCellValue('E1', 'Total Visits');
    $this->excel->getActiveSheet()->setCellValue('F1', 'Primary Sale');
    $this->excel->getActiveSheet()->setCellValue('G1', 'Payment');
    $this->excel->getActiveSheet()->setCellValue('H1', 'Stock Date');
    $this->excel->getActiveSheet()->setCellValue('I1', 'Stock');
    $this->excel->getActiveSheet()->setCellValue('J1', 'Sample');
    /* $this->excel->getActiveSheet()->setCellValue('K1', 'Met');
    $this->excel->getActiveSheet()->setCellValue('L1', 'Not Met');*/
    $this->excel->getActiveSheet()->setCellValue('K1', 'Secondary Sale');
    $this->excel->getActiveSheet()->setCellValue('L1', 'Duplicate Secondary');
    $this->excel->getActiveSheet()->setCellValue('M1', 'Duplicate Product');
    $this->excel->getActiveSheet()->setCellValue('N1', 'Duplicate Secondary Customer');
    $this->excel->getActiveSheet()->setCellValue('O1', 'Payment Terms');
    //$this->excel->getActiveSheet()->setCellValue('P1', 'Duplicate Secondary Person Type');
    $data['sale'] =$this->report->sale_report_doctor($userid,$start,$end); // for doctor
    //pr($data['sale']);
    //die;
    $k_num=2;
    if(!empty($data['sale']['doc_info'])){
      foreach ( $data['sale']['doc_info'] as $k=>$row){   // for doctor information with sample
        $this->excel->getActiveSheet()->setCellValue('A'.$k_num, $row['customer']);
        $docMeet++;
        $this->excel->getActiveSheet()->setCellValue('B'.$k_num, $row['city']);
        $this->excel->getActiveSheet()->setCellValue('C'.$k_num, 'Doctor');
        $this->excel->getActiveSheet()->setCellValue('D'.$k_num, $row['user']);
        //$this->excel->getActiveSheet()->setCellValue('E'.$k_num, $row['total_visits']);
        $this->excel->getActiveSheet()->setCellValue('J'.$k_num, $row['sample']);
        $k_num++;
      }    
      $k_num_int = 2;
      foreach ( $data['sale']['doc_interaction'] as $k_doc=>$row_doc){  // for doctor total visit,secondry sale,met,not met
        $this->excel->getActiveSheet()->setCellValue('E'.$k_num_int, $row_doc['total_visits']);
        $totVisit=$totVisit+$row_doc['total_visits'];
        $met=$row_doc['met']==0?'No':'Yes';
        $notmet=$row_doc['notmet']==0?'No':'Yes';
        if($row_doc['notmet']==1)
        {
          $docnotmeet++;
        }
       // $this->excel->getActiveSheet()->setCellValue('K'.$k_num_int,  $met);
       // $this->excel->getActiveSheet()->setCellValue('L'.$k_num_int, $notmet);
        /*$this->excel->getActiveSheet()->setCellValue('K'.$k_num_int, $row_doc['met']);
        $this->excel->getActiveSheet()->setCellValue('L'.$k_num_int, $row_doc['notmet']);*/
        $this->excel->getActiveSheet()->setCellValue('K'.$k_num_int, $row_doc['secondary_sale']);
        $paymentterm=get_payment_term($row_doc['id'],$row_doc['doc_id']);
        $this->excel->getActiveSheet()->setCellValue('O'.$k_num_int, $paymentterm);
        $secondarySale=$secondarySale+$row_doc['secondary_sale'];
        $k_num_int++;
      }
    }
    $data['sale_dealer'] =$this->report->sale_report_dealer($userid,$start,$end);   // for dealer
    $k_num= count($data['sale']['doc_info'])+2;
    if(!empty($data['sale_dealer']['dealer_info'])){   

      foreach ( $data['sale_dealer']['dealer_info'] as $k=>$row){   // for dealer information with sample
        $dealermeet++;
        $this->excel->getActiveSheet()->setCellValue('A'.$k_num, $row['customer']);
        $this->excel->getActiveSheet()->setCellValue('B'.$k_num, $row['city']);
        if(empty($row['is_cf'])){
          $this->excel->getActiveSheet()->setCellValue('C'.$k_num, 'Dealer');
        }
        else{
          $this->excel->getActiveSheet()->setCellValue('C'.$k_num, 'C & F'); 
        }
        $this->excel->getActiveSheet()->setCellValue('D'.$k_num, $row['user']);
        $this->excel->getActiveSheet()->setCellValue('J'.$k_num, $row['sample']);
        //$this->excel->getActiveSheet()->setCellValue('E'.$k_num, $row['total_visits']);
        $this->excel->getActiveSheet()->setCellValue('F'.$k_num, $row['sale']);
        $paymentterm=get_payment_term($row['id'],$row['d_id']);
        $this->excel->getActiveSheet()->setCellValue('O'.$k_num_int, $paymentterm);
        $primarySale=$primarySale+$row['sale'];
        $this->excel->getActiveSheet()->setCellValue('G'.$k_num, $row['Payment']);
        $payment=$payment+$row['Payment'];
        $k_num++;
      }    
      $k_num_int = count($data['sale']['doc_info'])+2;
      foreach ( $data['sale_dealer']['dealer_interaction'] as $k_doc=>$row_doc){  // for dealer total visit,secondry sale,met,not met
        if(!empty($row_doc['stock_date'])){  
          $this->excel->getActiveSheet()->setCellValue('H'.$k_num_int,date('d.m.Y',strtotime($row_doc['stock_date'])) );
        }
        $this->excel->getActiveSheet()->setCellValue('I'.$k_num_int, $row_doc['stock']);
        $this->excel->getActiveSheet()->setCellValue('E'.$k_num_int, $row_doc['total_visits']);
        $totVisit=$totVisit+$row_doc['total_visits'];
        $met=$row_doc['met']==0?'No':'Yes';
        $notmet=$row_doc['notmet']==0?'No':'Yes';
        //$this->excel->getActiveSheet()->setCellValue('K'.$k_num_int,  $met);
        //$this->excel->getActiveSheet()->setCellValue('L'.$k_num_int, $notmet);
        //$this->excel->getActiveSheet()->setCellValue('N'.$k_num_int, $row_doc['duplicate_secondary']);
        $k_num_int++;
      }
    }
    $data['sale_pharmacy'] =$this->report->sale_report_pharmacy($userid,$start,$end);   // for pharmacy
    $k_num= count($data['sale_dealer']['dealer_info'])+ count($data['sale']['doc_info'])+2;
    if(!empty($data['sale_pharmacy']['pharmacy_info'])){
      foreach ( $data['sale_pharmacy']['pharmacy_info'] as $k=>$row){   // for pharmacy information with sample
        $this->excel->getActiveSheet()->setCellValue('A'.$k_num, $row['customer']);
        $pharmaMeet++;
        $this->excel->getActiveSheet()->setCellValue('B'.$k_num, $row['city']);
        $this->excel->getActiveSheet()->setCellValue('C'.$k_num, 'Sub Dealer');
        $this->excel->getActiveSheet()->setCellValue('D'.$k_num, $row['user']);
        $this->excel->getActiveSheet()->setCellValue('J'.$k_num, $row['sample']);
        $k_num++;
      }    
      $k_num_int = count($data['sale_dealer']['dealer_info'])+ count($data['sale']['doc_info'])+2;
      foreach ( $data['sale_pharmacy']['pharmacy_interaction'] as $k_doc=>$row_doc){  // for pharmacy total visit,secondry sale,met,not met
        $productDuplicate='';
        $docDuplicate='';
        $this->excel->getActiveSheet()->setCellValue('E'.$k_num_int, $row_doc['total_visits']);
        $totVisit=$totVisit+$row_doc['total_visits'];
        $met=$row_doc['met']==0?'No':'Yes';
        $notmet=$row_doc['notmet']==0?'No':'Yes';
        if($row_doc['notmet']==1)
        {
          $subdealernotmeet++;
        }
        //echo $row_doc['duplicate_product'];
        $product=explode(',',$row_doc['duplicate_product']);
        $proDeatils=array_unique(array_filter($product));
        foreach($proDeatils as $proid)
        {
          $prodetl=get_product_name($proid).'('.get_packsize_name($proid).')';
          if($productDuplicate=='')
          {
            $productDuplicate=$prodetl;
          }
          else
          {
            $productDuplicate=$productDuplicate.','.$prodetl;
          }             
        }
        $docDetails=explode(',',$row_doc['dup_doctor_id']);
        $dupdocDetails=array_unique(array_filter($docDetails));
        foreach($dupdocDetails as $doc)
        {
          $docdetl=get_doctor_name($doc);
          if($docDuplicate=='')
          {
            $docDuplicate=$docdetl;
          }
          else
          {
            $docDuplicate=$docDuplicate.','.$docdetl;
          }
        }
       //$this->excel->getActiveSheet()->setCellValue('K'.$k_num_int,  $met);
       //$this->excel->getActiveSheet()->setCellValue('L'.$k_num_int, $notmet);
        /* $this->excel->getActiveSheet()->setCellValue('K'.$k_num_int, $row_doc['met']);
        $this->excel->getActiveSheet()->setCellValue('L'.$k_num_int, $row_doc['notmet']);*/
        $this->excel->getActiveSheet()->setCellValue('K'.$k_num_int, $row_doc['secondary_sale']);
        $paymentterm=get_payment_term($row_doc['id'],$row_doc['pharma_id']);
        $this->excel->getActiveSheet()->setCellValue('O'.$k_num_int, $paymentterm);
        $secondarySale=$secondarySale+$row_doc['secondary_sale'];
        $this->excel->getActiveSheet()->setCellValue('L'.$k_num_int, $row_doc['duplicate_secondary']);
        $this->excel->getActiveSheet()->setCellValue('M'.$k_num_int, $productDuplicate);
        $this->excel->getActiveSheet()->setCellValue('N'.$k_num_int, $docDuplicate);
        $duplicateSale=$duplicateSale+$row_doc['duplicate_secondary']; 
        $k_num_int++;
      }
    }
    $this->excel->getActiveSheet()->setCellValue('F'.$k_num_int, $primarySale);
    $this->excel->getActiveSheet()->setCellValue('K'.$k_num_int, $secondarySale);
    $this->excel->getActiveSheet()->setCellValue('L'.$k_num_int, $duplicateSale);
    $this->excel->getActiveSheet()->setCellValue('G'.$k_num_int, $payment);
    $this->excel->getActiveSheet()->setCellValue('E'.$k_num_int, $totVisit);
    $k_num_int=$k_num_int+3;
    //$docNo = $this->report->all_user_doctor($userid);
   // $pharmaNo = $this->report->all_user_pharma($userid);
    $this->excel->getActiveSheet()->setCellValue('A'.$k_num_int, 'Total Doctor');
    $this->excel->getActiveSheet()->setCellValue('B'.$k_num_int, $docNo);
    $this->excel->getActiveSheet()->setCellValue('H'.$k_num_int, 'Total Sub Dealer');
    $this->excel->getActiveSheet()->setCellValue('I'.$k_num_int, $pharmaNo);
    $k_num_int++;
    $this->excel->getActiveSheet()->setCellValue('A'.$k_num_int, 'Met Doctor');
    $this->excel->getActiveSheet()->setCellValue('B'.$k_num_int, $docMeet);
    $this->excel->getActiveSheet()->setCellValue('H'.$k_num_int, 'Met Sub Dealer');
    $this->excel->getActiveSheet()->setCellValue('I'.$k_num_int, $pharmaMeet);
    $k_num_int++;
    $docMissed=$docNo-$docMeet;
    $pharmaMissed=$pharmaNo-$pharmaMeet;
    $this->excel->getActiveSheet()->setCellValue('A'.$k_num_int, 'Missed Doctor');
    $this->excel->getActiveSheet()->setCellValue('B'.$k_num_int, $docMissed);
    $this->excel->getActiveSheet()->setCellValue('H'.$k_num_int, 'Missed Sub Dealer');
    $this->excel->getActiveSheet()->setCellValue('I'.$k_num_int, $pharmaMissed);

    $k_num_int=$k_num_int+2;
    $this->excel->getActiveSheet()->setCellValue('A'.$k_num_int, 'Average Doctor Call');
    $docavg=number_format((float)($docMeet-$docnotmeet)/$totday, 2, '.', '').'%';
    //$docavg=$docnotmeet;
    $this->excel->getActiveSheet()->setCellValue('B'.$k_num_int, $docavg);
    $this->excel->getActiveSheet()->setCellValue('H'.$k_num_int, 'Average Sub Dealer Call');
    $subdavg=number_format((float)($pharmaMeet-$subdealernotmeet)/$totday, 2, '.', '').'%';
    $this->excel->getActiveSheet()->setCellValue('I'.$k_num_int, $subdavg);


    $this->excel->getActiveSheet()->setCellValue('L'.$k_num_int, 'Average Dealer Call');
    $dlravg=number_format((float)($dealermeet)/$totday, 2, '.', '').'%';
    $this->excel->getActiveSheet()->setCellValue('M'.$k_num_int, $dlravg);

    $k_num_int=$k_num_int+1;
    $this->excel->getActiveSheet()->setCellValue('A'.$k_num_int, 'Negative Average Doctor Call');
    $ndocavg=number_format((float)($docnotmeet)/$totday, 2, '.', '').'%';
    //$docavg=$docnotmeet;
    $this->excel->getActiveSheet()->setCellValue('B'.$k_num_int, $ndocavg);
    
    $this->excel->getActiveSheet()->setCellValue('H'.$k_num_int, 'Negative Average Sub Dealer Call');
    $nsubdavg=number_format((float)($subdealernotmeet)/$totday, 2, '.', '').'%';
    $this->excel->getActiveSheet()->setCellValue('I'.$k_num_int, $nsubdavg);
    // Fill data 
    // $this->excel->getActiveSheet()->fromArray($customerdata,'A2');
    $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $this->excel->getActiveSheet()->getStyle('C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $filename='SaleReportUser.xls'; //save our workbook as this file name
    header('Content-Type: application/vnd.ms-excel'); //mime type
    header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
    header('Cache-Control: max-age=0'); //no cache
    //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
    //if you want to save it as .XLSX Excel 2007 format
    $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
    //force user to download the Excel file without writing it to server's HD
    //ob_end_clean();
    //ob_start();
    $objWriter->save('php://output');
  }
  
  
  public function expense_chart(){ 
    $data['title'] = "Expense Chart";
    $data['page_name'] = "Expense Chart";
    $data['action'] ="admin_control/report/send_expense_report";
    $data['users']=array();
    $data['user_id']='';
    $data['users'] =$this->user->users_report();
    $this->load->get_view('report/expense_report_view',$data);
  }

  public function send_expense_report(){
    $request = $this->input->post();
    $range=array();
    $city=array();
    $sale=0;
    $expense=0;
    $expense=0;
    $sale_all=array();
    $ta_da_bill=array();
    $expense_chart=array();
    $sale_chart=array();
    $report_date = explode('-',$request['report_date'] );
    $followstart_date =  trim($report_date[0]);
    $newstartdate = str_replace('/', '-', $followstart_date);
    $followend_date =  trim($report_date[1]);
    $newenddate = str_replace('/', '-', $followend_date);
    $start = date('Y-m-d', strtotime($newstartdate))." 00:00:00";
    $end = date('Y-m-d', strtotime($newenddate))." 23:59:59";
    $this->load->library('form_validation');
    $this->form_validation->set_rules('user_id', 'User', 'required');
    $this->form_validation->set_rules('report_date', 'Report Date range', 'required'); 
    if($this->form_validation->run() == TRUE){
     // $ta_da_expense=$this->get_tada_expense($request['user_id'],$start,$end);
      $secondary_sale=$this->get_secondary_sale_expense_bar($request['user_id'],$start,$end);
      $ta_da_expense=$this->get_ta_expense_bar($request['user_id'],$start,$end);
      /*$startDate= substr($start, 0, 10);
      $endXt= date('Y-m-d H:i:s', strtotime($end . ' +1 day'));
      $endDate= substr($endXt, 0, 10);
      $begin = new DateTime($startDate);
      $end1 = new DateTime($endDate);
      $interval = new DateInterval('P1D'); // 1 Day
      $dateRange = new DatePeriod($begin, $interval, $end1);
      foreach ($dateRange as $date) {
        $range[] = $date->format('d.m.Y');
      }
      foreach ($range as $date)
      {
        foreach ($secondary_sale as $key => $value) 
        {
          if($date==$value['doi'])
          {
            $sale=$value['sale'];
          }
        }
        $sale_all[]=array('doi'=>date('d.m.Y',strtotime($date)),
                        'sale'=>$sale);
        $sale=0;
        foreach ($ta_da_expense as $key => $value) 
        {
          if($date==$value['doi'])
          {
            $expense=$value['expense'];
          }
        }
        $ta_da_bill[]=array('doi'=>date('d.m.Y',strtotime($date)),
                        'expense'=>$expense);
        $expense=0;
      }*/
     
     foreach ($ta_da_expense as $key => $value) {
        $expense_chart[]=$value['expense'];
     }
     foreach ($secondary_sale as $key => $value) {
        $sale_chart[]=$value['sale'];
     }
     foreach ($secondary_sale as $key => $value) {
        $city[]=$value['city'];
     }
     $data['title'] = "Expense Chart";
     $data['page_name'] = "Expense Chart";
     //$data['range']=$range;
     $data['city']=$city;
     $data['user']=get_user_name($request['user_id']);
     $data['filename']=$request['report_date'];
     $data['expense_chart']=$expense_chart;
     $data['sale_chart']=$sale_chart;
     if($request['send']==1)
     {
        //$this->load->get_view('report/expense_chart',$data);
        $this->load->get_view('report/expense_chart_bar',$data);
        //$this->load->get_view('report/category_item',$data);
     }
     else
     {
        //$this->load->view('report/expense_chart_view',$data);//for full screen view
        $this->load->view('report/expense_chart_view_bar',$data);//for full screen view
     }
     //$this->load->view('report/expense_chart_view',$data);//for full screen view
    // $this->load->get_view('report/expense_chart',$data);
    }else{
      // for false validation
      $this->expense_chart();  
    }
  }

  public function get_secondary_sale_expense_bar($userid,$start,$end)
  {
    $doc_secondary=array();
    $doc_secondary_fn=array();
    $doc_sum=0;
    $pharma_secondary=array();
    $pharma_secondary_fn=array();
    $sale_fn=array();
    $data['travel'] =$this->report->travel_report_doctor($userid,$start,$end); 
    $data['travel_pharmacy'] =$this->report->travel_report_pharmacy($userid,$start,$end);
    //pr($data['travel']);
   // pr($data['travel_pharmacy']);
    if(!empty($data['travel']['doc_info'])){
      foreach ( $data['travel']['doc_info'] as $k=>$row){ 
        $doc_secondary[]=array('city'=>$row['city'],
                          'sale'=>$row['secondary_sale']);
      }
    }
    if(!empty($data['travel_pharmacy']['pharmacy_info'])){
      foreach ( $data['travel_pharmacy']['pharmacy_info'] as $k=>$row){ 
        $pharma_secondary[]=array('city'=>$row['city'],
                          'sale'=>$row['secondary_sale']);
      }
    }
   // pr($doc_secondary);
   // pr($pharma_secondary);
     $tempDoc = array_unique(array_column($doc_secondary, 'city'));
    $tempPharma = array_unique(array_column($pharma_secondary, 'city'));
    sort($tempDoc);
    sort($tempPharma);
    foreach($tempDoc as $doc_first)
    {
      foreach($doc_secondary as $doc_sec)
      {
        if($doc_first==$doc_sec['city'])
        {
          $doc_sum=$doc_sum+$doc_sec['sale'];
        }
      }
      $doc_secondary_fn[]=array('city'=>$doc_first,
                          'sale'=>$doc_sum);
      $doc_sum=0;
    }
    foreach($tempPharma as $doc_first)
    {
      foreach($pharma_secondary as $doc_sec)
      {
        if($doc_first==$doc_sec['city'])
        {
          $doc_sum=$doc_sum+$doc_sec['sale'];
        }
      }
      $pharma_secondary_fn[]=array('city'=>$doc_first,
                          'sale'=>$doc_sum);
      $doc_sum=0;
    }
    $total_sale=array_merge($doc_secondary_fn,$pharma_secondary_fn);
    $tempSale = array_unique(array_column($total_sale, 'city'));
    foreach($tempSale as $doc_first)
    {
      foreach($total_sale as $doc_sec)
      {
        if($doc_first==$doc_sec['city'])
        {
          $doc_sum=$doc_sum+$doc_sec['sale'];
        }
      }
      $sale_fn[]=array('city'=>$doc_first,
                          'sale'=>$doc_sum);
      $doc_sum=0;
    }
    return $sale_fn;
  }

  public function get_ta_expense_bar($userid,$start,$end)
  {
    $totalstprow=0;
    $lastdaydestination=0;
    $doc_sum=0;
    $ta_da=array();
    $ta_da_fn=array();
    $data['tada_report'] =$this->report->get_tada_report($userid,$start,$end);
    //pr($data['tada_report']);
     if(!empty($data['tada_report'])){
      foreach ($data['tada_report']as $key=>$row){ 
        $da=0;
        $hqdistance=0;
        $nxtdestination=0;
        if($key==0)
        {
          $lastdaydestination=get_destination_before($userid,$start);
        }
        else
        {
          $lastdaydestination=$row['destination_city'];
        }
        $hqdistance=get_distance_hq($userid,$row['meet_id']);
        $hq= get_user_deatils($userid)->headquarters_city;
        $tpinfo=get_tp_interaction($userid,$row['source_city'],$row['destination_city'],$row['doi']);
        $is_metro=is_city_metro($row['destination_city']);
        $designation_id=get_user_deatils($userid)->user_designation_id;
        if($row['source_city']==$row['destination_city'])
        {
          
          if($row['distance']==1)
          {
            $row['ta']=0;
            $row['distance']=0;
            $row['stp_ta']=0;
            $row['stp_distance']=0;
          }
        
        }

        $lenght= count($data['tada_report'])-1;
        $day= date('D',strtotime($row['doi']));
        if($key!=0 && $data['tada_report'][$key-1]['doi']==$row['doi'])
        {
          $da=0;
        }
        else
        {
          if($row['is_stay']==1 && $row['destination_city']==$lastdaydestination && $hqdistance>75)
          {
               $da=get_user_da(5,$designation_id,$is_metro);        
          }
          elseif($row['is_stay']==1 && $row['destination_city']!=$hq && $hqdistance>200)
          {
              $da=get_user_da(3,$designation_id,$is_metro); 
          }
          elseif($hqdistance>450 && $tpinfo)
          {
              $da=get_user_da(2,$designation_id,$is_metro); 
          }
          elseif($row['is_stay']==1 && $day=='Sat')
          {
              if($key==$lenght)
              {
                if($row['destination_city']!=$hq)
                {
                   $da=get_user_da(5,$designation_id,$is_metro)+get_user_da(2,$designation_id,$is_metro); 
                }
                else
                {
                   $da=get_user_da(1,$designation_id,$is_metro); 
                }
               
              }
              else
              {
                if(date('D',strtotime($data['tada_report'][$key+1]['doi']))=='Mon' && $data['tada_report'][$key+1]['destination_city']==$row['destination_city'])
                {
                  $da=get_user_da(5,$designation_id,$is_metro)+get_user_da(2,$designation_id,$is_metro);
                }
                else
                {
                  $da=get_user_da(5,$designation_id,$is_metro); 
                }
              }
          }
          else
          {
            $da=get_user_da(1,$designation_id,$is_metro); 
          }
        }
        if($key!=0 && $data['tada_report'][$key-1]['created_date']!=$row['created_date'])
        {
          $row['internet_charge']=0;
        }
        $totalstprow=$row['stp_ta']+$da+$row['internet_charge'];
        $ta_da[]=array('city'=>get_city_name($row['destination_city']),
                          'expense'=>$totalstprow);
      }
    }
    $tempDa = array_unique(array_column($ta_da, 'city'));
    sort($tempDa);
    foreach($tempDa as $doc_first)
    {
      //echo $doc_first;
      foreach($ta_da as $doc_sec)
      {
        if($doc_first==$doc_sec['city'])
        {
          $doc_sum=$doc_sum+$doc_sec['expense'];
        }
      }
      $ta_da_fn[]=array('city'=>$doc_first,
                          'expense'=>$doc_sum);
      $doc_sum=0;
    }
    return $ta_da_fn;
  }

  public function get_secondary_sale_expense($userid,$start,$end)
  {

    $doc_secondary=array();
    $doc_secondary_fn=array();
    $doc_sum=0;
    $pharma_secondary=array();
    $pharma_secondary_fn=array();
    $sale_fn=array();
    $data['travel'] =$this->report->travel_report_doctor($userid,$start,$end); 
    $data['travel_pharmacy'] =$this->report->travel_report_pharmacy($userid,$start,$end);
    /*pr($data['travel']);
    pr($data['travel_pharmacy']);*/
    if(!empty($data['travel']['doc_info'])){
      foreach ( $data['travel']['doc_info'] as $k=>$row){ 
        $doc_secondary[]=array('doi'=>date('d.m.Y',strtotime($row['date'])),
                          'sale'=>$row['secondary_sale']);
      }
    }
    if(!empty($data['travel_pharmacy']['pharmacy_info'])){
      foreach ( $data['travel_pharmacy']['pharmacy_info'] as $k=>$row){ 
        $pharma_secondary[]=array('doi'=>date('d.m.Y',strtotime($row['date'])),
                          'sale'=>$row['secondary_sale']);
      }
    }
    $tempDoc = array_unique(array_column($doc_secondary, 'doi'));
    $tempPharma = array_unique(array_column($pharma_secondary, 'doi'));
    sort($tempDoc);
    sort($tempPharma);
    foreach($tempDoc as $doc_first)
    {
      foreach($doc_secondary as $doc_sec)
      {
        if($doc_first==$doc_sec['doi'])
        {
          $doc_sum=$doc_sum+$doc_sec['sale'];
        }
      }
      $doc_secondary_fn[]=array('doi'=>date('d.m.Y',strtotime($doc_first)),
                          'sale'=>$doc_sum);
      $doc_sum=0;
    }
    foreach($tempPharma as $doc_first)
    {
      foreach($pharma_secondary as $doc_sec)
      {
        if($doc_first==$doc_sec['doi'])
        {
          $doc_sum=$doc_sum+$doc_sec['sale'];
        }
      }
      $pharma_secondary_fn[]=array('doi'=>date('d.m.Y',strtotime($doc_first)),
                          'sale'=>$doc_sum);
      $doc_sum=0;
    }
    $total_sale=array_merge($doc_secondary_fn,$pharma_secondary_fn);
    $tempSale = array_unique(array_column($total_sale, 'doi'));
    foreach($tempSale as $doc_first)
    {
      foreach($total_sale as $doc_sec)
      {
        if($doc_first==$doc_sec['doi'])
        {
          $doc_sum=$doc_sum+$doc_sec['sale'];
        }
      }
      $sale_fn[]=array('doi'=>date('d.m.Y',strtotime($doc_first)),
                          'sale'=>$doc_sum);
      $doc_sum=0;
    }
    return $sale_fn;
  }

  public function get_ta_expense($userid,$start,$end)
  {
    $totalstprow=0;
    $lastdaydestination=0;
    $doc_sum=0;
    $ta_da=array();
    $ta_da_fn=array();
    $data['tada_report'] =$this->report->get_tada_report($userid,$start,$end);
    // pr($data['tada_report']);  
    if(!empty($data['tada_report'])){
      foreach ($data['tada_report']as $key=>$row){ 
        $da=0;
        $hqdistance=0;
        $nxtdestination=0;
        if($key==0)
        {
          $lastdaydestination=get_destination_before($userid,$start);
        }
        else
        {
          $lastdaydestination=$row['destination_city'];
        }
        $hqdistance=get_distance_hq($userid,$row['meet_id']);
        $hq= get_user_deatils($userid)->headquarters_city;
        $tpinfo=get_tp_interaction($userid,$row['source_city'],$row['destination_city'],$row['doi']);
        $is_metro=is_city_metro($row['destination_city']);
        $designation_id=get_user_deatils($userid)->user_designation_id;
        if($row['source_city']==$row['destination_city'])
        {
          
          if($row['distance']==1)
          {
            $row['ta']=0;
            $row['distance']=0;
            $row['stp_ta']=0;
            $row['stp_distance']=0;
          }
        
        }

        $lenght= count($data['tada_report'])-1;
        $day= date('D',strtotime($row['doi']));
        if($key!=0 && $data['tada_report'][$key-1]['doi']==$row['doi'])
        {
          $da=0;
        }
        else
        {
          if($row['is_stay']==1 && $row['destination_city']==$lastdaydestination && $hqdistance>75)
          {
               $da=get_user_da(5,$designation_id,$is_metro);        
          }
          elseif($row['is_stay']==1 && $row['destination_city']!=$hq && $hqdistance>200)
          {
              $da=get_user_da(3,$designation_id,$is_metro); 
          }
          elseif($hqdistance>450 && $tpinfo)
          {
              $da=get_user_da(2,$designation_id,$is_metro); 
          }
          elseif($row['is_stay']==1 && $day=='Sat')
          {
              if($key==$lenght)
              {
                if($row['destination_city']!=$hq)
                {
                   $da=get_user_da(5,$designation_id,$is_metro)+get_user_da(2,$designation_id,$is_metro); 
                }
                else
                {
                   $da=get_user_da(1,$designation_id,$is_metro); 
                }
               
              }
              else
              {
                if(date('D',strtotime($data['tada_report'][$key+1]['doi']))=='Mon' && $data['tada_report'][$key+1]['destination_city']==$row['destination_city'])
                {
                  $da=get_user_da(5,$designation_id,$is_metro)+get_user_da(2,$designation_id,$is_metro);
                }
                else
                {
                  $da=get_user_da(5,$designation_id,$is_metro); 
                }
              }
          }
          else
          {
            $da=get_user_da(1,$designation_id,$is_metro); 
          }
        }
        if($key!=0 && $data['tada_report'][$key-1]['created_date']!=$row['created_date'])
        {
          $row['internet_charge']=0;
        }
        $totalstprow=$row['stp_ta']+$da+$row['internet_charge'];
        $ta_da[]=array('doi'=>date('d.m.Y',strtotime($row['doi'])),
                          'expense'=>$totalstprow);
      }
    }
    $tempDa = array_unique(array_column($ta_da, 'doi'));
    sort($tempDa);
    foreach($tempDa as $doc_first)
    {
      foreach($ta_da as $doc_sec)
      {
        if($doc_first==$doc_sec['doi'])
        {
          $doc_sum=$doc_sum+$doc_sec['expense'];
        }
      }
      $ta_da_fn[]=array('doi'=>date('d.m.Y',strtotime($doc_first)),
                          'expense'=>$doc_sum);
      $doc_sum=0;
    }
    return $ta_da_fn;
  }
  
  
  
}
?>