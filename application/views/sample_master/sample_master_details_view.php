<?php



/* 

 * Niraj Kumar

 * date : 18-10-2017

 * show list of sample data

 */

  $sample_list = json_decode($sample_data);

//  pr($sample_list); die;



?>

<link href="<?= base_url()?>design/css/div_table/one.css" rel="stylesheet" type="text/css"/>


<link href="<?= base_url()?>design/css/div_table/custom_table.css" rel="stylesheet" type="text/css"/>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>


<link rel="stylesheet" href="<?= base_url()?>design/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">





<style>
@media	only screen and (max-width: 760px),	(min-device-width: 768px) and (max-device-width: 1024px)  {
		/*
		Label the data
		*/
		td:nth-of-type(1):before { content: "Sample Name"; }
		td:nth-of-type(2):before { content: "Added By"; }
		td:nth-of-type(3):before { content: "Action"; }
	}
</style>



<div class="content-wrapper">

   

    <!-- Main content -->

    <section class="content">

      <div class="row">

        <div class="col-xs-12">

   <?php echo get_flash(); ?>

          <div class="box">

            <div class="box-header">

                <a href="<?= base_url();?>admin_control/sample_master/add_sample"> <h3 class="box-title"><button type="button" class="btn btn-block btn-success">Add New</button></h3></a>

            </div>

            <!-- /.box-header -->

            <div class="box-body">

                <table id="example2" class="table table-bordered table-striped">


                <thead>


                <tr>


					<th>Sample Name</th>
					<th>Added By</th>
					<th> Action</th>
					  </tr>
			</thead>


                <tbody>
                    <?php if(!empty($sample_list)){foreach($sample_list as $k_sm=>$val_sm){?>
					<tr>
						<td> <?=$val_sm->sample_name;?></td>
						<td> <?=$val_sm->sampleaddedby?></td>
						<td>
                             <a href="<?php echo base_url()."admin_control/sample_master/add_sample/". urisafeencode($val_sm->id);?>"><button type="button" class="btn btn-info"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button></a>
							| <a href="<?php echo base_url()."admin_control/sample_master/del_sample/".urisafeencode($val_sm->id);?>" onclick="return confirm('Are you sure want to delete this record.')" class=""><button type="button" class="btn btn-info"><i class="fa fa-trash-o" aria-hidden="true"></i></button></a>
						</td>

                     </tr>


                    <?php }  } ?>

 
                </tbody>


              </table>

            </div>

            <!-- /.box-body -->

            </div></div>

          <!-- /.box -->

        </div>

        <!-- /.col -->

      <!-- /.row -->

    </section>

    <!-- /.content -->

  </div>
  


<script src="<?= base_url()?>design/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>

<script src="<?= base_url()?>design/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>


  <script>
  $(function () {
    $('#example2').DataTable({
      'responsive' : true,
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : true,
    });
  });

</script>