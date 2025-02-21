<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
  $state_data=json_decode($statename);
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<div class="content-wrapper">
  <!-- Main content -->
  <section class="content">
    <!-- Contact Add -->
    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Add</h3>
          <?= get_flash();?>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <?php echo form_open_multipart($action);?>
        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label>State * </label>
              <input class="form-control" name="city_id" type="hidden" value="<?=$city_data->city_id?>">
                <select name="state" id="state"  class="form-control select3" style="width: 100%;">
                  <option value="">--Select State--</option>
                    <?php foreach($state_data as $k_st => $val_st){ ?>   
                       <option <?php echo $city_data->state_id==$val_st->state_id?'selected':'';?> value="<?=$val_st->state_id?>"><?=$val_st->state_name; ?></option>
                   <?php } ?>
                </select>
                <span class="control-label" for="inputError" style="color: red"><?php echo form_error('state'); ?></span>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label>City Name *</label>
              <input class="form-control" name="city_name" placeholder="Enter Name ..." type="text" value="<?=$city_data->city_name?>">               
              <span class="control-label" for="inputError" style="color: red"><?php echo form_error('city_name'); ?></span>
            </div>
          </div>
            
          <div class="col-md-3">
            <div class="form-group">
              <label>City Pin Code *</label>
              <input class="form-control" name="pincode" placeholder="Enter Pincode of the City ..." type="text" value="<?=$city_data->pincode?>">               
              <span class="control-label" for="inputError" style="color: red"><?php echo form_error('pincode'); ?></span>
            </div>
          </div>  
            
          <div class="col-md-3">
            <div class="form-group">
              <label>Metro City *</label><br>
              <input name="metro" <?php echo $city_data->is_metro==0?'checked':'';?> type="radio" value="0">&nbsp;&nbsp;No &nbsp;&nbsp;&nbsp;&nbsp;            
              <input name="metro" <?php echo $city_data->is_metro==1?'checked':'';?>  type="radio" value="1">&nbsp;&nbsp;Yes         
            </div>
          </div>
        </div>
        <div class="col-md-12">
          <div class="box-footer">
            <button type="submit" class="btn btn-info pull-right">Submit</button>
          </div>
        </div>
      </div>
    </div>
  </section>
</div> 
<script type="text/javascript">
  $(function(){
    $('.select3').select2(); 
  });    
</script>         