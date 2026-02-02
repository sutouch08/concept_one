<?php $this->load->view('include/header'); ?>
<?php $this->load->view('sales_order/style'); ?>
<script src="<?php echo base_url(); ?>assets/js/jquery.colorbox.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/colorbox.css" />
<div class="row">
  <div class="col-lg-2 col-md-2 col-sm-2 padding-5 hidden-xs">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
  <div class="col-xs-12 padding-5 visible-xs">
    <h4 class="title-xs"><?php echo $this->title; ?></h4>
  </div>
  <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12 padding-5">
    <p class="pull-right top-p">
      <button type="button" class="btn btn-xs btn-default" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
      <button type="button" class="btn btn-xs btn-yellow" onclick="showLogs()"><i class="fa fa-history"></i> Logs</button>
      <?php if($doc->status == 'O' OR $doc->status == 'C') : ?>
        <button type="button" class="btn btn-xs btn-info" onclick="printOrder('<?php echo $doc->code; ?>')"><i class="fa fa-print"></i> พิมพ์</button>
      <?php endif; ?>
      <?php if($doc->status == 'P' OR $doc->status == 'O') : ?>
        <?php if($this->pm->can_edit) : ?>
          <button type="button" class="btn btn-xs btn-warning" onclick="edit('<?php echo $doc->code; ?>')">แก้ไข</button>
          <?php if($doc->status == 'O') : ?>
            <button type="button" class="btn btn-xs btn-purple" onclick="closeOrder('<?php echo $doc->code; ?>')">Force Close</button>
          <?php endif; ?>
        <?php endif; ?>
        <?php if($this->pm->can_delete) : ?>
          <button type="button" class="btn btn-xs btn-danger" onclick="goDelete('<?php echo $doc->code; ?>')"><i class="fa fa-trash"></i> ยกเลิก</button>
        <?php endif; ?>
        <?php if($doc->status == 'O' && $this->pm->can_edit) : ?>
          <button type="button" class="btn btn-xs btn-primary btn-100" onclick="getSaleOrderDetail()">สร้างใบออเดอร์/ใบเบิกสินค้า</button>
        <?php endif; ?>
      <?php endif; ?>
    </p>
  </div>
</div>
<hr class="padding-5"/>
<div class="row">
  <input type="hidden" id="prev-image" value="<?php echo $image; ?>" />
  <input type="hidden" id="no-img-path" value="<?php echo $no_image_path; ?>">
  <?php $ad = empty($doc->image_path) ? '' : 'hide'; ?>
  <?php $del = empty($doc->image_path) ? 'hide' : ''; ?>
  <div class="col-lg-12 col-md-12 col-sm-12 padding-0 margin-bottom-15">
    <div class="tabable">
      <ul class="nav nav-tabs" role="tablist">
        <li class="active">
          <a href="#doc-pane" id="doc-tab" aria-expanded="true" aria-controls="doc-pane" role="tab" data-toggle="tab">เอกสาร</a>
        </li>
        <li>
          <a href="#image-pane" id="image-tab" aria-expanded="false" aria-controls="image-pane" role="tab" data-toggle="tab">รูปภาพ</a>
        </li>
      </ul>

      <!-- Tab panes -->
      <div class="tab-content" style="">
        <div role="tabpanel" class="tab-pane active" id="doc-pane">
          <div class="row">
            <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
              <label>เลขที่</label>
              <input type="text" class="form-control input-sm text-center" id="code" value="<?php echo $doc->code; ?>" disabled/>
            </div>
            <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
              <label>เล่ม</label>
              <select class="form-control input-sm h" id="is-term" disabled>
                <option value="">เลือก</option>
                <option value="0" <?php echo is_selected('0', $doc->is_term); ?>>ขายสด</option>
                <option value="1" <?php echo is_selected('1', $doc->is_term); ?>>ขายเชื่อ</option>
              </select>
            </div>
            <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
              <label>ชนิด VAT</label>
              <select class="form-control input-sm h" id="vat-type" onchange="toggleVatType()" disabled>
                <option value="">เลือก</option>
                <option value="E" <?php echo is_selected('E', $doc->vat_type); ?>>แยกนอก</option>
                <option value="I" <?php echo is_selected('I', $doc->vat_type); ?>>รวมใน</option>
                <option value="N" <?php echo is_selected('N', $doc->vat_type); ?>>ไม่ VAT</option>
              </select>
              <input type="hidden" id="tax-status" value="">
            </div>
            <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
              <label>วันที่</label>
              <input type="text" class="form-control input-sm text-center h" id="date_add" value="<?php echo thai_date($doc->date_add); ?>" readonly disabled/>
            </div>
            <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
              <label>รหัสลูกค้า</label>
              <input type="text" class="form-control input-sm text-center h" id="customer-code" value="<?php echo $doc->customer_code; ?>" disabled/>
            </div>
            <div class="col-lg-5 col-md-5-harf col-sm-4 col-xs-6 padding-5">
              <label class="display-block not-show">ชื่อลูกค้า</label>
              <input type="text" class="form-control input-sm h" id="customer-name" value="<?php echo $doc->customer_name; ?>" disabled/>
            </div>
            <div class="col-lg-1 col-md-1-harf col-sm-1-harf padding-5">
          		<label class="display-block not-show">isCompany</label>
          		<label style="margin-top:0;">
          			<input type="checkbox" class="ace" id="is-company" value="1" <?php echo is_checked('1', $doc->isCompany); ?> disabled />
          			<span class="lbl margin-top-5">&nbsp;&nbsp;นิติบุคคล</span>
          		</label>
          	</div>
            <div class="col-lg-6-harf col-md-5 col-sm-4 col-xs-6 padding-5">
              <label>ผู้ติดต่อ</label>
              <input type="text" class="form-control input-sm h" maxlength="100" id="customer-ref" value="<?php echo $doc->customer_ref; ?>" disabled />
            </div>

            <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
              <label>เบอร์โทร</label>
              <input type="text" class="form-control input-sm h" id="phone" value="<?php echo $doc->phone; ?>" disabled/>
            </div>
            <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
              <label>เลขผู้เสียภาษี</label>
              <input type="text" class="form-control input-sm text-center h" id="tax-id" value="<?php echo $doc->tax_id; ?>" disabled/>
            </div>
            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-6 padding-5">
              <label>สาขา</label>
              <input type="text" class="form-control input-sm text-center h" id="branch-code" value="<?php echo $doc->branch_code; ?>" disabled/>
            </div>
            <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
              <label>ชื่อสาขา</label>
              <input type="text" class="form-control input-sm h" id="branch-name" value="<?php echo $doc->branch_name; ?>" disabled/>
            </div>
            <div class="col-lg-6-harf col-md-5 col-sm-5 col-xs-12 padding-5">
              <label>ที่อยู่เปิดบิล</label>
              <input type="text" class="form-control input-sm h" id="address" value="<?php echo $doc->address; ?>" disabled/>
            </div>
            <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
              <label>ตำบล</label>
              <input type="text" class="form-control input-sm h" id="sub-district" value="<?php echo $doc->sub_district; ?>" disabled/>
            </div>
            <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
              <label>อำเภอ</label>
              <input type="text" class="form-control input-sm h" id="district" value="<?php echo $doc->district; ?>" disabled/>
            </div>
            <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
              <label>จังหวัด</label>
              <input type="text" class="form-control input-sm h" id="province" value="<?php echo $doc->province; ?>" disabled/>
            </div>
            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-6 padding-5">
              <label>ไปรษณีย์</label>
              <input type="text" class="form-control input-sm h" id="postcode" value="<?php echo $doc->postcode; ?>" disabled/>
            </div>


            <div class="divider"></div>

            <div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-6 padding-5">
              <label>ประเภทงาน</label>
              <select class="form-control input-sm h" id="job-type" disabled>
                <option value="">เลือก</option>
                <?php echo select_job_type($doc->job_type); ?>
              </select>
            </div>

            <div class="col-lg-7 col-md-6-harf col-sm-8 col-xs-12 padding-5">
              <label>ชื่องาน</label>
              <input type="text" class="form-control input-sm h" id="job-title" value="<?php echo $doc->job_title; ?>" disabled/>
            </div>

            <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
              <label>กำหนดส่ง</label>
              <input type="text" class="form-control input-sm text-center h" id="due_date" value="<?php echo thai_date($doc->due_date); ?>" readonly disabled/>
            </div>

            <div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
              <label>ช่องทางขาย</label>
              <select class="form-control input-sm h" id="channels" disabled>
                <option value="">เลือก</option>
                <?php echo select_channels($doc->channels_code); ?>
              </select>
            </div>

            <div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
              <label>การออกแบบ</label>
              <select class="form-control input-sm h" id="design" disabled>
                <option value="">เลือก</option>
                <option value="no" <?php echo is_selected('no', $doc->design); ?>>ไม่มีแบบ</option>
                <option value="old" <?php echo is_selected('old', $doc->design); ?>>แบบเดิม</option>
                <option value="new" <?php echo is_selected('new', $doc->design); ?>>แบบใหม่</option>
              </select>
            </div>

            <div class="col-lg-2-harf col-md-2-harf col-sm-3 col-xs-6 padding-5">
              <label>คลัง</label>
              <select class="form-control input-sm h" id="warehouse" disabled>
                <?php echo select_sell_warehouse($doc->whsCode); ?>
              </select>
            </div>

            <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
              <label>มัดจำ</label>
              <input type="text" class="form-control input-sm text-right h" id="dep-amount" value="<?php echo number($doc->DepAmount, 2); ?>" disabled/>
            </div>

            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
              <label>ใบเบิก</label>
              <?php if( ! empty($wq)) : ?>
                <div class="input-group">
                  <select class="form-control input-sm" id="wq">
                    <?php foreach($wq as $q) : ?>
                      <option value="<?php echo $q->code; ?>"><?php echo $q->code; ?></option>
                    <?php endforeach; ?>
                  </select>
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-xs btn-info" onclick="openWq()"><i class="fa fa-eye"></i></button>
                  </span>
                </div>
              <?php else : ?>
                <input type="text" class="form-control input-sm text-center" value="ไม่มี" disabled/>
              <?php endif; ?>
            </div>

            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
              <label>ออเดอร์</label>
              <?php if( ! empty($wo)) : ?>
                <div class="input-group">
                  <select class="form-control input-sm" id="wo">
                    <?php foreach($wo as $q) : ?>
                      <option value="<?php echo $q->code; ?>"><?php echo $q->code; ?></option>
                    <?php endforeach; ?>
                  </select>
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-xs btn-info" onclick="openWo()"><i class="fa fa-eye"></i></button>
                  </span>
                </div>
              <?php else : ?>
                <input type="text" class="form-control input-sm text-center" value="ไม่มี" disabled/>
              <?php endif; ?>
            </div>

            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
              <label>บิลขาย</label>
              <?php if( ! empty($bi)) : ?>
                <div class="input-group">
                  <select class="form-control input-sm" id="bi">
                    <?php foreach($bi as $q) : ?>
                      <option value="<?php echo $q->code; ?>"><?php echo $q->code; ?></option>
                    <?php endforeach; ?>
                  </select>
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-xs btn-info" onclick="openBill()"><i class="fa fa-eye"></i></button>
                  </span>
                </div>
              <?php else : ?>
                <input type="text" class="form-control input-sm text-center" value="ไม่มี" disabled/>
              <?php endif; ?>
            </div>

          </div>
        </div>

        <div role="tabpanel" class="tab-pane" id="image-pane">
          <div class="row">
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 padding-5">
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-center">
                <span class="profile-picture" id="so-img-preview">
                  <a data-rel="colorbox" href="<?php echo $image; ?>">
                    <img class="editable img-responsive" id="so-image"
                    src="<?php echo $image; ?>"
                    style="width:100%; height:100%; max-width:160px; max-height:160px;">
                  </a>
                </span>
                <input type="hidden" id="img-blob" />
              </div>
              <div class="col-sm-12 col-xs-12 text-center margin-top-5">
                <?php if($doc->status != 'D') : ?>
                  <button type="button" class="btn btn-minier btn-success <?php echo $ad; ?>" id="btn-add-img" onclick="addImage()"><i class="fa fa-plus"></i> เพิ่ม</button>
                  <button type="button" class="btn btn-minier btn-danger <?php echo $del; ?>" id="btn-del-img" onclick="deleteImage()"><i class="fa fa-trash"></i> ลบ</button>
                  <button type="button" class="btn btn-minier btn-primary hide" id="btn-save-img" onclick="saveImage()"><i class="fa fa-save"></i> Save</button>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
<hr class="margin-top-10 margin-bottom-10"/>

<?php if($doc->status == 'D') : ?>
  <?php $this->load->view('cancle_watermark'); ?>
<?php endif; ?>
<?php $this->load->view('sales_order/sales_order_address_panel'); ?>

<div class="row padding-5">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0 border-1 table-responsive margin-bottom-15"
  style="min-height:300px; max-height:600px; overflow:auto; border-top:solid 1px #ddd;">
		<table class="table table-bordered tableFixHead" style="min-width:1000px; margin-bottom:20px;">
			<thead>
				<tr>
					<th class="fix-width-40 text-center">#</th>
					<th class="fix-width-200">รหัสสินค้า</th>
					<th class="min-width-200">ชื่อสินค้า</th>
          <th class="fix-width-100 text-right">จำนวน</th>
          <th class="fix-width-100 text-right">OpenQty</th>
          <th class="fix-width-100 text-right">ราคา/หน่วย</th>
          <th class="fix-width-100 text-right">ส่วนลด</th>
          <th class="fix-width-120 text-right">มูลค่า</th>
				</tr>
			</thead>
			<tbody id="detail-list">
<?php if( ! empty($details)) : ?>
  <?php $no = 1; ?>
  <?php $totalAmount = 0; ?>
  <?php foreach($details as $rs) : ?>
    <tr>
      <td class="middle text-center no"><?php echo $no; ?></td>
      <td class="middle"><?php echo $rs->product_code; ?></td>
      <td class="middle"><?php echo $rs->product_name; ?></td>
      <td class="middle text-right"><?php echo number($rs->qty, 2); ?></td>
      <td class="middle text-right"><?php echo number($rs->OpenQty, 2); ?></td>
      <td class="middle text-right"><?php echo number($rs->price, 2); ?></td>
      <td class="middle text-right"><?php echo $rs->discount_label; ?></td>
      <td class="middle text-right"><?php echo number($rs->total_amount, 2); ?></td>
    </tr>
    <?php $no++; ?>
    <?php $totalAmount += $rs->total_amount; ?>
  <?php endforeach; ?>
<?php endif; ?>
			</tbody>
		</table>
  </div>
</div>

<div class="row">
  <!--- left column -->
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <div class="form-horizontal">

			<div class="form-group">
        <label class="col-lg-3 col-md-4 col-sm-4 control-label no-padding-right">พนักงานขาย</label>
        <div class="col-lg-5 col-md-6 col-sm-6 col-xs-12">
          <select class="form-control input-sm" id="sale_id" disabled>
            <?php echo select_saleman($doc->sale_id); ?>
					</select>
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-3 col-md-4 col-sm-4 control-label no-padding-right">เจ้าของ</label>
        <div class="col-lg-5 col-md-6 col-sm-6 col-xs-12">
          <input type="text" class="form-control input-sm" value="<?php echo $this->user_model->get_name($doc->user); ?>" disabled />
  				<input type="hidden" id="owner" value="<?php echo $doc->user; ?>" />
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label no-padding-right">หมายเหตุ</label>
        <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
          <textarea id="remark" maxlength="254" rows="3" class="form-control" disabled><?php echo $doc->remark; ?></textarea>
        </div>
      </div>

    </div>
  </div>

  <div class="col-lg-6 col-md-6 col-sm-6 padding-5">
    <div class="form-horizontal">
      <div class="form-group">
        <label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">มูลค่าก่อนส่วนลด</label>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
					<input type="hidden" id="total-amount" value="0.00">
          <input type="text" class="form-control input-sm text-right" id="total-amount-label" value="<?php echo number($doc->TotalBfDisc, 2); ?>" disabled>
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-6 col-md-6 col-sm-4-harf control-label no-padding-right">ส่วนลด</label>
        <div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-3 padding-5">
          <span class="input-icon input-icon-right">
          <input type="number" id="discPrcnt" class="form-control input-sm" value="<?php echo number($doc->DiscPrcnt, 2); ?>" disabled/>
          <i class="ace-icon fa fa-percent"></i>
          </span>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
          <input type="hidden" id="disc-amount" value="0.00" />
          <input type="text" id="disc-amount-label" class="form-control input-sm text-right" value="<?php echo number($doc->DiscAmount, 2); ?>" disabled>
        </div>
      </div>

      <div class="form-group <?php echo ($doc->TaxStatus == 'Y' ? '' : 'hide'); ?>" id="bill-wht">
        <label class="col-lg-6 col-md-6 col-sm-4-harf col-xs-3 control-label no-padding-right">หัก ณ ที่จ่าย</label>
        <div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-3 padding-5">
          <span class="input-icon input-icon-right">
          <input type="number" id="whtPrcnt" class="form-control input-sm" onchange="recalTotal()" value="<?php echo number($doc->WhtPrcnt, 2); ?>" disabled />
          <i class="ace-icon fa fa-percent"></i>
          </span>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
          <input type="hidden" id="wht-amount" value="<?php echo $doc->WhtAmount; ?>" />
          <input type="text" id="wht-amount-label" class="form-control input-sm text-right" value="<?php echo number($doc->WhtAmount, 2); ?>" disabled />
        </div>
      </div>

      <div class="form-group <?php echo $doc->TaxStatus == 'Y' ? '' : 'hide'; ?>">
        <label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">ภาษีมูลค่าเพิ่ม</label>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
          <input type="hidden" id="vat-total" value="0.00"/>
          <input type="text" id="vat-total-label" class="form-control input-sm text-right" value="<?php echo number($doc->VatSum, 2); ?>" readonly disabled/>
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">รวมทั้งสิ้น</label>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
          <input type="hidden" id="doc-total" value="0.00"/>
          <input type="text" id="doc-total-label" class="form-control input-sm text-right" value="<?php echo number($doc->DocTotal - $doc->WhtAmount, 2); ?>" disabled/>
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">ชำระแล้ว</label>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
          <input type="text" id="dep-label" class="form-control input-sm text-right" value="<?php echo number($doc->paidAmount, 2); ?>" disabled/>
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">คงเหลือ</label>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
          <input type="text" id="balance-label" class="form-control input-sm text-right" value="<?php echo number($doc->TotalBalance - $doc->WhtAmount, 2); ?>" disabled/>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="logModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title-site text-center margin-top-5 margin-bottom-5">Transection logs</h4>
			</div>
      <div class="modal-body">
        <div class="row">
          <div class="col-lg-12 col-mg-12 col-sm-12 col-xs-12">
            <?php if( ! empty($logs)) : ?>
              <?php foreach($logs as $log) : ?>
                <span class="display-block"><?php echo sale_order_log_label($log->action); ?> : <?php echo $log->name; ?> @ <?php echo thai_date($log->date_upd, TRUE); ?></span>
              <?php endforeach; ?>
            <?php else : ?>
              <span class="display-block text-center">--- No transection logs ---</span>
            <?php endif; ?>
            <?php if($doc->status == 'D') : ?>
              <span class="red display-block">ยกเลิกโดย : <?php echo $doc->cancle_user; ?> @ <?php echo thai_date($doc->cancle_date, TRUE); ?></span>
              <span class="red display-block">หมายเหตุ : <?php echo $doc->cancle_reason; ?></span>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm" data-dismiss="modal"><i class="ace-icon fa fa-times"></i> Close</button>
      </div>
		</div>
	</div>
</div>

<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">×</button>
				<h4 class="blue">Add Image</h4>
			</div>
			<form class="no-margin" id="imageForm">
				<div class="modal-body">
					<div style="width:75%;margin-left:12%;">
						<label id="btn-select-file" class="ace-file-input ace-file-multiple">
							<input type="file" name="image" id="image" accept="image/*" style="display:none;" />
							<span class="ace-file-container" data-title="Click to choose new Image">
								<span class="ace-file-name" data-title="No File ...">
									<i class=" ace-icon ace-icon fa fa-picture-o"></i>
								</span>
							</span>
						</label>
						<div id="block-image" style="opacity:0;">
							<div id="previewImg" class="width-100 center"></div>
							<span onClick="removeFile()" style="position:absolute; left:385px; top:1px; cursor:pointer; color:red;">
								<i class="fa fa-times fa-2x"></i>
							</span>
						</div>
					</div>
				</div>
				<div class="modal-footer center">
					<button type="button" class="btn btn-sm btn-success" onclick="getImage()"><i class="ace-icon fa fa-check"></i> Submit</button>
					<button type="button" class="btn btn-sm" data-dismiss="modal"><i class="ace-icon fa fa-times"></i> Cancel</button>
				</div>
			</form>
		</div>
	</div>
</div>

<?php $this->load->view('sales_order/sales_order_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/sales_order/sales_order.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/sales_order/sales_order_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/sales_order/sales_order_control.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/sales_order/sales_order_address.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/sales_order/sales_order_payment.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
