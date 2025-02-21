<?php





/* 


 * Developed By: Niraj Kumar


 * Dated: 04-nov-2017


 * Email: sss.shailesh@gmail.com


 * 


 * model for secondary supply


 */





class Secondary_model extends CI_Model {





  


    public function doctor_interaction_view($limit='',$start=''){
//      echo $limit; die;  
     $arr = " dl.doc_name as doctorname,pid.orignal_sale as actualsale,pid.id,`pid`.`meeting_sale` secondarysale, `pid`.`create_date` as `date_of_interaction`,d.dealer_name ,pl.company_name as pharmaname,pid.close_status";

        $this->db->select($arr);


        $this->db->from("pharma_interaction_doctor pid");


        $this->db->join("doctor_list dl","dl.doctor_id=pid.doc_id");


        $this->db->join("dealer d","d.dealer_id=pid.dealer_id","left");


        $this->db->join("pharmacy_list pl","pl.pharma_id=pid.dealer_id","left");


        $this->db->join("doctor_interaction_with_team team","team.pidoc_id=pid.id","left");


      


      $this->db->where("pid.meeting_sale !=","");
      $this->db->where("pid.status =",1);


//      $this->db->where("pid.dealer_id IS NOT NULL",NULL, FALSE);


       if(!is_admin()){
        $this->db->where("(pid.crm_user_id=".logged_user_data()." or team.team_id=".logged_user_data().")");
       }


       /*  if($limit!=''){ 


        $this->db->limit($limit, decode($start));


        } */

        $query = $this->db->get();


//    echo $this->db->last_query(); die;
        if($this->db->affected_rows()){
          return json_encode($query->result_array());
        }

        else{
            return FALSE;
        }
    }


    


    // save doctor orignal sale 


    public function save_doctor_orignal_sale($data='',$interaction_id=''){


        


        if($data!=''){


            


            $orignal_sale = array(


                            


                'orignal_sale'=>$data['os_sale'],


                'date_of_supply'=>$data['dos_doc']!='' ? date('Y-m-d', strtotime($data['dos_doc'])):NULL


                    


            );


            


            $this->db->where('id',$interaction_id);


           $this->db->update('pharma_interaction_doctor',$orignal_sale);


           


//             $query = $this->db->get();


//             echo $this->db->last_query();  die;


            if ($this->db->affected_rows()== TRUE)


                           {


                              return true;





                           }


                           else{





                               return false;





                           }  


            


        }


        


    }


    public function pharmacy_interaction_view($limit='',$start=''){


      


        $arr = " pl.company_name as pharmaname,pip.orignal_sale as actualsale,pip.id,`pip`.`meeting_sale` as secondarysale, `pip`.`create_date` as `date_of_interaction`,d.dealer_name,pip.close_status";


        $this->db->select($arr);


        $this->db->from("pharma_interaction_pharmacy pip");


        $this->db->join("pharmacy_list pl","pl.pharma_id=pip.pharma_id");


        $this->db->join("dealer d","d.dealer_id=pip.dealer_id","left");


        $this->db->join("pharmacy_interaction_with_team team","team.pipharma_id=pip.id","left");

  
       $this->db->where("pip.meeting_sale !=","");

        $this->db->where("pip.status =",1);
       if(!is_admin()){
          $this->db->where("(pip.crm_user_id=".logged_user_data()." or team.team_id=".logged_user_data().")");
        }


       if($limit!=''){ 


        $this->db->limit($limit, decode($start));


        }


      


        $query = $this->db->get();


    // echo $this->db->last_query(); die;


        if($this->db->affected_rows()){


            


            return json_encode($query->result_array());


        }


        else{
          return FALSE;
        }


        


        


    }


    





    


     // save pharmacy orignal sale 


    public function save_pharmacy_orignal_sale($data='',$interaction_id=''){


        


        if($data!=''){


            


            $orignal_sale = array(


                            


                'orignal_sale'=>$data['os_sale'],


                'date_of_supply'=>$data['dos_doc']!='' ? date('Y-m-d', strtotime($data['dos_doc'])):NULL


                    


            );


            


            $this->db->where('id',$interaction_id);


           $this->db->update('pharma_interaction_pharmacy',$orignal_sale);


           


//             $query = $this->db->get();


//             echo $this->db->last_query();  die;


            if ($this->db->affected_rows()== TRUE)


                           {


                              return true;





                           }


                           else{





                               return false;





                           }  


            


        }


        


    }


    


    


    // close interaction of doctor


    public function doctor_interaction_close($id=''){


        


       


         $orignal_sale = array(


                            


                'close_status'=>1,


                    


            );


            


            $this->db->where('id',$id);


           $this->db->update('pharma_interaction_doctor',$orignal_sale);


           


//             $query = $this->db->get();


//             echo $this->db->last_query();  die;


            if ($this->db->affected_rows()== TRUE)


                           {


                              return true;





                           }


                           else{





                               return false;





                           }


        


    }


    


    


 


   // close pharmacy interaction of the sale


    


     public function pharmacy_interaction_close($id=''){


        


       


         $orignal_sale = array(


                            


                'close_status'=>1,


                    


            );


            


            $this->db->where('id',$id);


           $this->db->update('pharma_interaction_pharmacy',$orignal_sale);


           


            if ($this->db->affected_rows()== TRUE)


                           {


                              return true;





                           }


                           else{





                               return false;





                           }


        


    }


    


    


}