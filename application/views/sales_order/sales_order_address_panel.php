<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <div class="tabable">
    	<ul class="nav nav-tabs" role="tablist">
        <li class="active">
        	<a href="#state" aria-expanded="true" aria-controls="state" role="tab" data-toggle="tab">สเตท</a>
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
              <?php if( $rs->id == $doc->id_address ) : ?>
                      <button type="button" class="btn btn-minier btn-success btn-address" id="btn-<?php echo $rs->id; ?>" onclick="setAddress(<?php echo $rs->id; ?>)">
                        <i class="fa fa-check"></i>
                      </button>
              <?php else : ?>
                      <button type="button" class="btn btn-minier btn-address" id="btn-<?php echo $rs->id; ?>" onclick="setAddress(<?php echo $rs->id; ?>)">
                        <i class="fa fa-check"></i>
                      </button>
              <?php endif; ?>
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
				<?php $this->load->view("sales_order/sales_order_state"); ?>
      </div>
			<div role="tabpanel" class="tab-pane fade" id="sender">
        <div class="row" style="padding:15px;">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
            <div class="row">
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-3-harf padding-5 text-right">เลือกผู้จัดส่ง :</div>
              <div class="col-lg-4 col-md-5 col-sm-5 col-xs-5 padding-5">
                <select class="form-control input-sm" id="id_sender">
                  <option value="">เลือก</option>
                  <?php echo select_common_sender($doc->customer_code, $doc->id_sender); //--- sender helper?>
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
                <input type="text" class="form-control input-sm" id="tracking" value="<?php echo $doc->ship_code; ?>">
                <input type="hidden" id="trackingNo" value="<?php echo $doc->ship_code; ?>">
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
