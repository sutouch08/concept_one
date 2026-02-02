<div class="row" style="padding:15px;">
	<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 padding-5">
    	<table class="table" style="margin-bottom:0px;">
        <?php if( $this->pm->can_add OR $this->pm->can_edit OR $this->pm->can_delete ) : ?>
        	<tr>
            	<td class="width-25 middle text-right" style="border:0px; padding:5px;">สเตท : </td>
                <td class="width-50" style="border:0px; padding:5px;">
                	<select class="form-control input-sm" style="padding-top:0px; padding-bottom:0px;" id="stateList">
                    	<option value="0">เลือกสเตท</option>
											<?php echo select_so_state(); ?>
											<!-- <option value="1">รอดำเนินการ</option>
											<option value="2">รอชำระเงิน</option>
											<option value="3">รอแบบ</option>
											<option value="31">ออกแบบเสร็จ</option>
											<option value="32">รอปริ้นเฟล็ก</option>
											<option value="33">ปริ้นเฟล็กเสร็จ</option>
                      <option value="4">รอผลิต</option>
                      <option value="5">กำลังผลิต</option>
                      <option value="6">ผลิตเสร็จ</option>
                      <option value="7">รอจัดส่ง</option>
											<option value="8">จัดส่งแล้ว</option> -->
                    </select>
                </td>
                <td class="width-25" style="border:0px; padding:5px;">
									<button class="btn btn-xs btn-primary btn-block" onclick="changeState()">เปลี่ยนสเตท</button>
                </td>
            </tr>
       <?php else : ?>
       			<tr>
            	<td class="width-30 text-center" style="border:0px;">สเตท</td>
              <td class="width-40 text-center" style="border:0px;">พนักงาน</td>
              <td class="width-30 text-center" style="border:0px;">เวลา</td>
            </tr>
       <?php endif; ?>
      </table>
	</div>

  <?php $link = $doc->state == 9 ? 'onclick="showReason()"' : ''; ?>
  <?php $pointer = $doc->state == 9 ? 'pointer' : ''; ?>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5 font-size-14 <?php echo $pointer; ?>"
    <?php echo $link; ?>	style="height: 49px; border:solid 2px white; <?php echo state_color($doc->state); ?>"	>
		<center>สถานปัจจุบัน</center>
		<center><?php echo get_state_name($doc->state); ?></center>
	</div>


<?php if( ! empty($state) ) : ?>
  <?php foreach($state as $rs) : ?>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5 font-size-10" style="height: 49px; border:solid 2px white; white-space: nowrap; overflow: hidden; <?php echo state_color($rs->state); ?>" >
    <center><?php echo get_state_name($rs->state); ?></center>
    <center><?php echo $this->user_model->get_name($rs->user); ?></center>
    <center><?php echo thai_date($rs->date_upd,TRUE, '/'); ?></center>
  </div>
<?php	endforeach; ?>
<?php endif; ?>
</div>


<div class="modal fade" id="cancle-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
 <div class="modal-dialog" style="max-width:95%; margin-left:auto; margin-right:auto;">
   <div class="modal-content">
       <div class="modal-header">
       <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
       <h4 class="modal-title">เหตุผลในการยกเลิก</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <input type="text" class="form-control input-sm" id="cancle-reason" maxlength="100" placeholder="กรุณาระบุเหตุผลในการยกเลิกอย่างน้อย 10 ตัวอักษร"/>
            <input type="hidden" id="cancle-code" value="" />
          </div>
        </div>
       </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-info" onclick="doCancle()">Submit</button>
      </div>
   </div>
 </div>
</div>

<div class="modal fade" id="cancle-reason-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
 <div class="modal-dialog" style="max-width:800px;">
   <div class="modal-content">
       <div class="modal-header">
       <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
       <h4 class="modal-title">เหตุผลในการยกเลิก</h4>
      </div>
      <div class="modal-body" style="border-top:solid 1px #CCC; padding:13px; padding-top:0px;">
        <div class="row">
          <div class="col-sm-12 no-padding">
            <table class="table table-bordered">
							<thead>
	            	<tr>
	            		<th class="width-60">เหตุผล</th>
									<th class="width-20">User</th>
									<th class="width-20">วันที่ยกเลิก</th>
	            	</tr>
							</thead>
							<tbody>
							<?php if(!empty($cancle_reason)) : ?>
								<?php foreach($cancle_reason as $reason) : ?>
									<tr>
										<td><?php echo $reason->reason; ?></td>
										<td><?php echo $this->user_model->get_name($reason->user); ?></td>
										<td><?php echo thai_date($reason->cancle_date, TRUE); ?></td>
									</tr>
								<?php endforeach; ?>
							<?php else : ?>
								<tr><td colspan="3" class="text-center">--ไม่ระบุเหตุผล--</td></tr>
							<?php endif; ?>
							</tbody>
            </table>
          </div>
        </div>

       </div>

   </div>
 </div>
</div>
