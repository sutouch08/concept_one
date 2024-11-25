<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6 hidden-xs padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-xs-12 visible-xs padding-5">
    <h3 class="title-xs"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <p class="pull-right top-p">
      <?php if(empty($approve_view)) : ?>
      <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
      <?php endif; ?>

      <?php if($order->role == 'N' && ($order->is_valid == '0' OR $order->is_received === NULL OR $order->is_received === 'N') ) : ?>
      <button type="button" class="btn btn-sm btn-primary" onclick="confirm_receipted()"><i class="fa fa-check"></i> ยืนยันการรับสินค้า</button>
    <?php elseif($order->role == 'N' && ($order->is_valid == '1' OR $order->is_received === 'Y')) : ?>
      <button type="button" class="btn btn-sm btn-default" disabled><i class="fa fa-check"></i> รับสินค้าแล้ว</button>
      <?php endif; ?>

      <?php if($order->role == 'S' && empty($order->invoice_code)) : ?>
        <?php if($order->TaxStatus == 'Y') : ?>
          <button type="button" class="btn btn-sm btn-primary" onclick="createTaxInvoice('<?php echo $order->code; ?>')">Create Tax Invoice</button>
        <?php else : ?>
          <button type="button" class="btn btn-sm btn-info" onclick="createInvoice('<?php echo $order->code; ?>')">Create Invoice</button>
        <?php endif; ?>
      <?php endif; ?>
      <?php if($order->role != 'S' && empty($approve_view)) : ?>
      <button type="button" class="btn btn-sm btn-success" onclick="doExport()">ส่งข้อมูลไป SAP</button>
      <?php endif; ?>
    </p>
  </div>
</div>
<hr/>


<?php if( $order->state == 8) : ?>
  <input type="hidden" id="order_code" value="<?php echo $order->code; ?>" />
  <input type="hidden" id="customer_code" value="<?php echo $order->customer_code; ?>" />
  <input type="hidden" id="customer_ref" value="<?php echo $order->customer_ref; ?>" />
<?php $reference = empty($order->reference) ? $order->code : $order->code . " [{$order->reference}]"; ?>
<?php $cust_name = empty($order->customer_ref) ? $order->customer_name : $order->customer_name.' ['.$order->customer_ref.']'; ?>
  <div class="row">
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>เลขที่เอกสาร</label>
      <input type="text" class="form-control input-sm text-center" value="<?php echo $order->code; ?>" disabled />
    </div>

    <?php if($order->role == 'C' OR $order->role == 'N') : ?>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>รหัสลูกค้า</label>
      <input type="text" class="form-control input-sm text-center" value="<?php echo $order->customer_code; ?>" disabled />
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5">
      <label>ลูกค้า</label>
      <input type="text" class="form-control input-sm" value="<?php echo $cust_name; ?>" disabled />
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5">
      <label>โซน</label>
      <input type="text" class="form-control input-sm" value="<?php echo $order->zone_name; ?>" disabled />
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
      <label>พนักงาน</label>
      <input type="text" class="form-control input-sm" value="<?php echo $order->user; ?>" disabled />
    </div>
    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-4 padding-5">
      <label>หมายเหตุ</label>
      <input type="text" class="form-control input-sm" value="<?php echo $order->remark; ?>" disabled />
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
      <label class="font-size-2 blod">SAP No</label>
      <input type="text" class="form-control input-sm text-center" value="<?php echo $order->inv_code; ?>" disabled />
    </div>
    <?php else : ?>
      <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
        <label>อ้างอิง</label>
        <input type="text" class="form-control input-sm text-center" value="<?php echo $order->reference; ?>" disabled />
      </div>
      <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
        <label>รหัสลูกค้า</label>
        <input type="text" class="form-control input-sm text-center" value="<?php echo $order->customer_code; ?>" disabled />
      </div>
      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
        <label>ลูกค้า</label>
        <input type="text" class="form-control input-sm" value="<?php echo $cust_name; ?>" disabled />
      </div>
      <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
        <label>พนักงาน</label>
        <input type="text" class="form-control input-sm" value="<?php echo $order->user; ?>" disabled />
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8 col-xs-4 padding-5">
        <label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm" value="<?php echo $order->remark; ?>" disabled />
      </div>
      <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
        <label class="font-size-2 blod">SAP No</label>
        <input type="text" class="form-control input-sm text-center" value="<?php echo $order->inv_code; ?>" disabled />
      </div>
    <?php endif; ?>
  </div>
  <hr/>

  <div class="row hidden-xs">
    <div class="col-sm-12 text-right">
    <?php if( ! empty($order->id_address)) : ?>
      <button type="button" class="btn btn-sm btn-info top-btn" onclick="printOnlineAddress('<?php echo $order->id_address; ?>', '<?php echo $order->code; ?>')"><i class="fa fa-print"></i> ใบนำส่ง</button>
    <?php endif; ?>
      <button type="button" class="btn btn-sm btn-primary top-btn" onclick="printDelivery()"><i class="fa fa-print"></i> ใบส่งของ </button>
      <button type="button" class="btn btn-sm btn-success top-btn" onclick="printOrderBarcode()"><i class="fa fa-print"></i> Packing List (barcode)</button>
      <button type="button" class="btn btn-sm btn-warning top-btn" onclick="showBoxList()"><i class="fa fa-print"></i> Packing List (ปะหน้ากล่อง)</button>
    </div>
  </div>
  <hr class="padding-5 hidden-xs"/>

  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
      <table class="table table-bordered" style="min-width:960px;">
        <thead>
          <tr class="font-size-12">
            <th class="fix-width-40 text-center">ลำดับ</th>
            <th class="min-width-300 text-center">สินค้า</th>
            <th class="fix-width-100 text-center">ราคา</th>
            <th class="fix-width-80 text-center">ออเดอร์</th>
            <th class="fix-width-80 text-center">จัด</th>
            <th class="fix-width-80 text-center">ตรวจ</th>
            <th class="fix-width-80 text-center">เปิดบิล</th>
            <th class="fix-width-100 text-center">ส่วนลด</th>
            <th class="fix-width-100 text-center">มูลค่า</th>
          </tr>
        </thead>
        <tbody>
  <?php if(!empty($details)) : ?>
  <?php   $no = 1;
          $totalQty = 0;
          $totalPrepared = 0;
          $totalQc = 0;
          $totalSold = 0;
          $totalAmount = 0;
          $totalDiscount = 0;
          $totalPrice = 0;
  ?>
  <?php   foreach($details as $rs) :  ?>
    <?php     $color = ($rs->order_qty == $rs->qc OR $rs->is_count == 0) ? '' : 'red'; ?>
            <tr class="font-size-12 <?php echo $color; ?>">
              <td class="middle text-center">
                <?php echo $no; ?>
              </td>

              <!--- รายการสินค้า ที่มีการสั่งสินค้า --->
              <td class="moddle">
                <?php echo limitText($rs->product_code.' : '. $rs->product_name, 100); ?>
              </td>

              <!--- ราคาสินค้า  --->
              <td class="middle text-center">
                <?php echo number($rs->price, 2); ?>
              </td>

              <!---   จำนวนที่สั่ง  --->
              <td class="middle text-center">
                <?php echo number($rs->order_qty); ?>
              </td>

              <!--- จำนวนที่จัดได้  --->
              <td class="middle text-center">
                <?php echo $rs->is_count == 0 ? number($rs->order_qty) : number($rs->prepared); ?>
              </td>

              <!--- จำนวนที่ตรวจได้ --->
              <td class="middle text-center">
                <?php echo $rs->is_count == 0 ? number($rs->order_qty) : number($rs->qc); ?>
              </td>

              <!--- จำนวนที่บันทึกขาย --->
              <td class="middle text-center">
                <?php echo number($rs->sold); ?>
              </td>

              <!--- ส่วนลด  --->
              <td class="middle text-center">
                <?php echo discountLabel($rs->discount1, $rs->discount2, $rs->discount3); ?>
              </td>

              <td class="middle text-right">
                <?php echo $rs->is_count == 0 ? number($rs->final_price * $rs->order_qty) : number( $rs->final_price * $rs->sold , 2); ?>
              </td>

            </tr>
    <?php
          $totalQty += $rs->order_qty;
          $totalPrepared += ($rs->is_count == 0 ? $rs->order_qty : $rs->prepared);
          $totalQc += ($rs->is_count == 0 ? $rs->order_qty : $rs->qc);
          $totalSold += $rs->sold;
          $totalDiscount += $rs->discount_amount * $rs->sold;
          $totalAmount += $rs->final_price * $rs->sold;
          $totalPrice += $rs->price * $rs->sold;
          $no++;
    ?>
  <?php   endforeach; ?>
          <tr class="font-size-12">
            <td colspan="3" class="text-right font-size-14">
              รวม
            </td>

            <td class="text-center">
              <?php echo number($totalQty); ?>
            </td>

            <td class="text-center">
              <?php echo number($totalPrepared); ?>
            </td>

            <td class="text-center">
              <?php echo number($totalQc); ?>
            </td>

            <td class="text-center">
              <?php echo number($totalSold); ?>
            </td>

            <td class="text-center">
              ส่วนลดท้ายบิล
            </td>

            <td class="text-right">
              <?php echo number($order->bDiscAmount, 2); ?>
            </td>
          </tr>


          <tr>
            <td colspan="4" rowspan="3" style="white-space:normal;">
              <?php if(!empty($order->remark)) : ?>
              หมายเหตุ : <?php echo $order->remark; ?>
              <?php endif; ?>
            </td>
            <td colspan="3" class="blod">
              ราคารวม
            </td>
            <td colspan="2" class="text-right">
              <?php echo number($totalPrice, 2); ?>
            </td>
          </tr>

          <tr>
            <td colspan="3">
              ส่วนลดรวม
            </td>
            <td colspan="2" class="text-right">
              <?php echo number($totalDiscount + $order->bDiscAmount, 2); ?>
            </td>
          </tr>

          <tr>
            <td colspan="3" class="blod">
              ยอดเงินสุทธิ
            </td>
            <td colspan="2" class="text-right">
              <?php echo number($totalPrice - ($totalDiscount + $order->bDiscAmount), 2); ?>
            </td>
          </tr>

  <?php else : ?>
        <tr><td colspan="8" class="text-center"><h4>ไม่พบรายการ</h4></td></tr>
  <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>


  <!--************** Address Form Modal ************-->
  <div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="addressModal" aria-hidden="true">
    <div class="modal-dialog" style="width:500px;">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        </div>
        <div class="modal-body" id="info_body">

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-primary" onclick="printSelectAddress()"><i class="fa fa-print"></i> พิมพ์</button>
        </div>
      </div>
    </div>
  </div>

  <?php $this->load->view('inventory/order_closed/box_list');  ?>

  <script src="<?php echo base_url(); ?>scripts/print/print_address.js?v=<?php echo date('Ymd'); ?>"></script>
  <script src="<?php echo base_url(); ?>scripts/print/print_order.js?v=<?php echo date('Ymd'); ?>"></script>

<?php else : ?>
  <?php $this->load->view('inventory/delivery_order/invalid_state'); ?>
<?php endif; ?>


<script>

  function confirm_receipted(){
    var code = $('#order_code').val();
    swal({
      title: "ยืนยันการรับสินค้า",
      text: "คุณได้รับสินค้าครบเอกสารเลขที่ "+code+" แล้วใช่หรือไม่ ?",
      type:"warning",
      showCancelButton:true,
      confirmButtonColor:"#428bca",
      confirmButtonText:"ยืนยัน ได้รับครบแล้ว",
      cancelButtonText:"ยกเลิก",
      closeOnConfirm: false
    }, function(){
      $.ajax({
        url:BASE_URL + 'inventory/transfer/confirm_receipted',
        type:'POST',
        cache:false,
        data:{
          'code' : code
        },
        success:function(rs){
          var rs = $.trim(rs);
          if(rs === 'success'){
            swal({
              title:'Confirmed',
              type:'success',
              timer:1000
            });
            setTimeout(function(){
              window.location.reload();
            }, 1200);
          }else{
            swal({
              title:'Error!!',
              text:rs,
              type:'error'
            });
          }
        }
      })
    })
  }
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/order_closed/closed.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
