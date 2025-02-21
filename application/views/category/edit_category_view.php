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
          <h3 class="box-title">Add Category </h3>

        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?php echo form_open_multipart($action);?>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label>Select Parent Category : </label>
						<input type="hidden" name ="category_id" value="<?php echo $category_data[0]['category_id']; ?>">
						<select name="parent_category" id="parent_category"  class="form-control select2" style="width: 100%;">
						  <option value="">--Select Category --</option>
							<?php foreach($category_list as $category){ ?>   
								<option <?php echo ($category['category_id']==$category_data[0]['parent_category_id'])?'selected':'';?> value="<?=$category['category_id']?>" ><?=$category['category_name']?></option>
							<?php }  ?>
						</select>
						<span class="control-label" for="inputError" style="color: red"><?php echo form_error('parent_category'); ?></span>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label>Category Name* :</label>
						  <input class="form-control pull-right" name="cat_name" value="<?= $category_data[0]['category_name']?>" id="cat_name" type="text">
							<span class="control-label" for="inputError" style="color: red"><?php echo form_error('cat_name'); ?></span>
					</div>
				</div>

			</div>

			<div class="row">
				<div class="col-md-6">
				   <div class="form-group">
					<label>Select Status&nbsp; : &nbsp;&nbsp;</label>
					<input <?php echo ($category_data[0]['status']==1)?'checked':'';?>  type="radio" if class="form-check-input"  name="status"  id="status" value="1">
					&nbsp; Enable &nbsp;
					<input type="radio" <?php echo ($category_data[0]['status']==0)?'checked':'';?> class="form-check-input ts" name="status" id="status" value="0">
					&nbsp; Disable &nbsp;
					
				  </div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<textarea class="form-control" rows="3" name="remark" id="remark" placeholder="Remark ..."><?= $category_data[0]['remark'] ?></textarea>
						<label>Remark</label>
					</div>
				</div>
			</div>

		</div>
		<div class="row">
            <div class="col-md-12">
                <!--<div class="form-group">-->
                <div class="box-footer">
					<button type="submit" class="btn btn-info pull-right">Edit</button>
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


