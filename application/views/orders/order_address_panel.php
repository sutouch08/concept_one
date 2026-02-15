<?php
$cn = get_permission("SOCNDO", get_cookie("uid"), get_cookie("id_profile")); //--- ยกเลิกออเดอร์ที่จัดส่งแล้ว บนระบบ WMS
$canCancleShipped = ($cn->can_add + $cn->can_edit + $cn->can_delete) > 0 ? TRUE : FALSE;
 ?>
<?php if($order->role == "S") : ?>
	<?php 	$paymentLabel = paymentLabel($payments); //paymentLabel($order->code, paymentExists($order->code), $order->is_paid);	?>
	<?php if(!empty($paymentLabel)) : ?>
		<div class="row">
		  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		  	<?php echo $paymentLabel; ?>
		  </div>
		</div>
		<hr class="padding-5"/>
	<?php endif; ?>
<?php endif; ?>

<style>
	@media(min-width:768px) {
		#rc-div {
			margin-bottom:-30px;
		}
	}
</style>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <div class="tabable">
    	<ul class="nav nav-tabs" role="tablist">
        <li class="active">
        	<a href="#state" aria-expanded="true" aria-controls="state" role="tab" data-toggle="tab">สถานะ</a>
        </li>
      	<li>
          <a href="#ship-to" aria-expanded="false" aria-controls="ship-to" role="tab" data-toggle="tab">ที่อยู่จัดส่ง</a>
        </li>
				<li>
          <a href="#sender" aria-expanded="false" aria-controls="sender" role="tab" data-toggle="tab">ผู้จัดส่ง</a>
        </li>
      </ul>

      <!-- Tab panes -->
      <div class="tab-content" style="margin:0px; padding:0px;">

				<div role="tabpanel" class="tab-pane fade" id="ship-to">
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="table-responsive" style="max-height:250px; overflow:auto;">
              <table class="table table-bordered" style="min-width:900px; margin-bottom:0px; border-collapse:collapse; border:0;">
                <thead>
                  <tr style="background-color:white;">
                    <th colspan="6" align="center">ที่อยู่สำหรับจัดส่ง
                      <p class="pull-right top-p">
                        <button type="button" class="btn btn-info btn-xs" onClick="addNewAddress()"> เพิ่มที่อยู่ใหม่</button>
                      </p>
                    </th>
                  </tr>
                  <tr style="font-size:12px; background-color:white;">
                    <th class="fix-width-120">ชื่อเรียก</th>
                    <th class="fix-width-150">ผู้รับ</th>
                    <th class="min-width-250">ที่อยู่</th>
                    <th class="fix-width-150">โทรศัพท์</th>
                    <th class="fix-width-120"></td>
                  </tr>
                </thead>
                <tbody id="adrs">
          <?php if(!empty($addr)) : ?>
          <?php 	foreach($addr as $rs) : ?>
                  <tr style="font-size:12px;" id="<?php echo $rs->id; ?>">
                    <td align="center"><?php echo $rs->alias; ?></td>
                    <td><?php echo $rs->name; ?></td>
                    <td><?php echo $rs->address." ". $rs->sub_district." ".$rs->district." ".$rs->province." ". $rs->postcode; ?></td>
                    <td><?php echo $rs->phone; ?></td>
                    <td align="right">
              <?php if( $rs->id == $order->id_address ) : ?>
                      <button type="button" class="btn btn-minier btn-success btn-address" id="btn-<?php echo $rs->id; ?>" onclick="setAddress(<?php echo $rs->id; ?>)">
                        <i class="fa fa-check"></i>
                      </button>
              <?php else : ?>
                      <button type="button" class="btn btn-minier btn-address" id="btn-<?php echo $rs->id; ?>" onclick="setAddress(<?php echo $rs->id; ?>)">
                        <i class="fa fa-check"></i>
                      </button>
              <?php endif; ?>
											<button type="button" class="btn btn-minier btn-primary" onclick="printOnlineAddress(<?php echo $rs->id; ?>, '<?php echo $order->code; ?>')">
												<i class="fa fa-print"></i>
											</button>
                      <button type="button" class="btn btn-minier btn-warning" onClick="editAddress(<?php echo $rs->id; ?>)"><i class="fa fa-pencil"></i></button>
                      <button type="button" class="btn btn-minier btn-danger" onClick="removeAddress(<?php echo $rs->id; ?>)"><i class="fa fa-trash"></i></button>
                    </td>
                  </tr>

          <?php 	endforeach; ?>
          <?php else : ?>
                  <tr><td colspan="6" align="center">ไม่พบที่อยู่</td></tr>
          <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div><!-- /row-->
      </div>

      <div role="tabpanel" class="tab-pane active" id="state">
				<?php $this->load->view("orders/order_state"); ?>
      </div>
			<div role="tabpanel" class="tab-pane fade" id="sender">
        <div class="row" style="padding:15px;">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
            <div class="row">
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-3-harf padding-5 text-right">เลือกผู้จัดส่ง :</div>
              <div class="col-lg-4 col-md-5 col-sm-5 col-xs-5 padding-5">
                <select class="form-control input-sm" id="id_sender">
                  <option value="">เลือก</option>
                  <?php echo select_common_sender($order->customer_code, $order->id_sender); //--- sender helper?>
                </select>
              </div>
              <div class="col-lg-2 col-md-3 col-sm-3 col-xs-3 padding-5">
                <button type="button" class="btn btn-xs btn-success btn-block" onclick="setSender()">บันทึก</button>
              </div>
            </div>
          </div>
          <div class="divider-hidden visible-xs"></div>
          <div class="divider-hidden visible-xs"></div>

          <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 padding-5">
            <div class="row">
              <div class="col-lg-3 col-md-4 col-sm-4 col-xs-3-harf padding-5 text-right">Tracking No :</div>
              <div class="col-lg-4 col-md-5 col-sm-5 col-xs-5 padding-5">
                <input type="text" class="form-control input-sm" id="tracking" value="<?php echo $order->shipping_code; ?>">
                <input type="hidden" id="trackingNo" value="<?php echo $order->shipping_code; ?>">
              </div>
              <div class="col-lg-2 col-md-3 col-sm-3 col-xs-3 padding-5">
                <button type="button" class="btn btn-xs btn-success btn-block" onclick="update_tracking()">บันทึก</button>
              </div>
            </div>
          </div>
				</div>
			</div>

    </div>
  </div>
	</div>
</div>
<hr class="padding-5"/>

<div class="modal fade" id="cancle-shipped-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
 <div class="modal-dialog" style="width:500px;">
   <div class="modal-content">
       <div class="modal-header">
       <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
       <h4 class="modal-title">เหตุผลในการยกเลิก</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-9">
            <input type="text" class="form-control input-sm" id="cancle-shipped-reason" value=""/>
          </div>
          <div class="col-sm-3">
            <button type="button" class="btn btn-sm btn-info" onclick="cancle_order_shipped()">ตกลง</button>
          </div>
        </div>

       </div>
      <div class="modal-footer">

      </div>
   </div>
 </div>
</div>

<script>
function update_wms_status() {
	const order_code = $("#order_code").val();
	if(order_code !== "" && order_code !== undefined) {
		load_in();
		$.ajax({
			url:BASE_URL + "rest/V1/wms_order_status/update_wms_status",
			type:"GET",
			cache:false,
			data:{
				"order_code" : order_code
			},
			success:function(rs) {
				load_out();
				if(rs === "success") {
					swal({
						title:"Success",
						type:"success",
						timer:1000
					});

					setTimeout(function(){
						window.location.reload();
					}, 1200);
				}
				else {
					swal(rs);
				}
			}
		})
	}
}



function cancle_shipped_order() {
	swal({
		title: "ยกเลิกออเดอร์ ?",
		text: "ออเดอร์นี้ถูกจัดส่งแล้ว หากคุณต้องการยกเลิกคุณต้องประสานงานกับคลัง Pioneer เพื่อรับสินค้ากลับเข้าคลังด้วย <br/> ต้องการยกเลิกหรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: "ยืนยัน",
		cancelButtonText: "ยกเลิก",
		html:true,
		closeOnConfirm: true,
		}, function(){
			$("#cancle-shipped-modal").modal("show");
	});
}


//--
function cancle_order_shipped() {
	$("#cancle-shipped-modal").modal("hide");
	const order_code = $("#order_code").val();
	const reason = $.trim($("#cancle-shipped-reason").val());

	if(reason == "") {
		$("#cancle-shipped-modal").modal("show");
		return false;
	}


	if(order_code !== "" && order_code !== undefined) {
		load_in();
		$.ajax({
			url:BASE_URL + "orders/orders/cancle_wms_shipped_order",
			type:"POST",
			cache:false,
			data:{
				"order_code" : order_code,
				"cancle_reason" : reason
			},
			success:function(rs) {
				load_out();
				if(rs === "success") {
					swal({
						title:"Success",
						type:"success",
						timer:1000
					});

					setTimeout(function(){
						window.location.reload();
					}, 1200);
				}
				else {
					swal(rs);
				}
			}
		})
	}
}

$("#cancle-shipped-modal").on("shown.bs.modal", function() {
	$("#cancle-shipped-reason").focus();
});


function send_return_request() {
	const order_code = $("#order_code").val();
	if(order_code !== "" && order_code !== undefined) {
		load_in();
		$.ajax({
			url:BASE_URL + "orders/orders/send_return_request",
			type:"POST",
			cache:false,
			data:{
				"order_code" : order_code
			},
			success:function(rs) {
				load_out();
				if(rs === "success") {
					swal({
						title:"Success",
						type:"success",
						timer:1000
					});

					setTimeout(function(){
						window.location.reload();
					}, 1200);
				}
				else {
					swal(rs);
				}
			}
		})
	}

}


function print_wms_return_request() {
	const order_code = $("#order_code").val();
	if(order_code !== "" && order_code !== undefined) {
		const center = ($(document).width() - 800) /2;
	  const target = BASE_URL + "orders/orders/print_wms_return_request/"+order_code;
	  window.open(target, "_blank", "width=800, height=900, left="+center+", scrollbars=yes");
	}
}


</script>
