<?php



/* 

 * To change this license header, choose License Headers in Project Properties.

 * To change this template file, choose Tools | Templates

 * and open the template in the editor.

 */



?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>





<div class="content-wrapper">



    <!-- Main content -->

    <section class="content">

    <?php echo get_flash(); ?>

      <!-- Contact Add -->

      <div class="box box-default">

        <div class="box-header with-border">

          <h3 class="box-title">Edit Leave</h3>



        </div>

        <!-- /.box-header -->

        <div class="box-body">

            <?php echo form_open_multipart($action);?>

			<div class="row">

				<div class="col-md-6">

					<div class="form-group">

						<label>Select Date* </label>

						<div class="input-group date">

						  <div class="input-group-addon">

							<i class="fa fa-calendar"></i>

						  </div>

						  <input type="hidden" name ="leave_id" value="<?php echo $leave_data[0]['leave_id']; ?>">
						<input name="start_date" class="form-control pull-right" id="reservation2" type="text" value="<?php echo date('d/m/Y',strtotime($leave_data[0]['from_date'])) .' - '. date('d/m/Y',strtotime($leave_data[0]['to_date'])); ?>">
						

						</div>

						<span class="control-label" for="inputError" style="color: red"><?php echo form_error('start_date'); ?></span>

					</div>

				</div>

	

			</div>

			<div class="row">

				<div class="col-md-6">

					<div class="form-group">

						<label>Remark</label>

						<textarea class="form-control" rows="3" name="remark" id="remark" placeholder="Reason for leave ..."><?php echo $leave_data[0]['remark']; ?></textarea>

					</div>

				</div>

			</div>

		</div>

		<div class="row">

            <div class="col-md-12">

                <!--<div class="form-group">-->

                <div class="box-footer">
                    <button type="submit" class="btn btn-success pull-right">Approve</button>
                    <?php  echo form_close();   ?>
                    <a href="<?php echo base_url()."leave/user_leave/cancel_leave/". urisafeencode($leave_data[0]['leave_id']);?>"><button class="btn btn-danger pull-left">Cancel Leave</button></a>
                </div>

            </div>

        </div>

          <!-- /.row -->

          

          <?php

          echo form_close(); 

          ?>

        </div>

        <!-- /.box-body -->

        

      </div>

      <!-- /.box -->



    </section>

    <!-- /.content -->

  </div>

<script src="<?php echo base_url();?>design/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

<script type='text/javascript'>

  $('#datepickerend').datepicker({

              autoclose: true

    }) ;

    $(function(){



    $('.select2').select2();

     

    });

   

</script>

