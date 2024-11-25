<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    	<h3 class="title" >
        <?php echo $this->title; ?>
      </h3>
	</div>
    <div class="col-sm-6 col-xs-6 padding-5">
      	<p class="pull-right top-p">
			    <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
          <?php if(($this->pm->can_add OR $this->pm->can_edit) && $doc->status == 0) : ?>
            <button type="button" class="btn btn-sm btn-success" onclick="saveAdjust()"><i class="fa fa-save"></i> บันทึก</button>
          <?php endif; ?>
        </p>
    </div>
</div>
<hr class="padding-5" />

<div class="row">
    <div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
    </div>
		<div class="col-sm-1 col-xs-6 padding-5">
    	<label>วันที่</label>
      <input type="text" class="form-control input-sm text-center edit" id="date_add" value="<?php echo thai_date($doc->date_add) ?>" readonly disabled/>
    </div>
		<div class="col-sm-2 col-xs-4 padding-5">
			<label>รหัสโซน</label>
			<input type="text" class="form-control input-sm text-center" id="zone" value="<?php echo $doc->from_zone; ?>" disabled />
		</div>

		<div class="col-sm-3 col-xs-8 padding-5">
	    <label>โซน</label>
	    <input type="text" class="form-control input-sm" id="zoneName" value="<?php echo $doc->zone_name; ?>" disabled />
	  </div>

		<div class="col-sm-3 col-3-harf col-xs-12 padding-5">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" value="<?php echo $doc->remark; ?>" disabled/>
    </div>
    <?php if($doc->status == 0) : ?>
		<div class="col-sm-1 col-xs-12 padding-5">
			<label class="display-block not-show">add</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()"><i class="fa fa-pencil"></i> แก้ไข</button>
      <button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="updateHeader()"><i class="fa fa-save"></i> บันทึก</button>
		</div>
    <?php endif; ?>
</div>

<?php if($doc->status == 0) : ?>
<hr class="margin-top-15 margin-bottom-15 padding-5"/>
<div class="row">
	<div class="col-sm-2 col-xs-8 padding-5">
		<label>เอกสารแปรสภาพ</label>
		<input type="text" class="form-control input-sm edit" id="reference" value="<?php echo $doc->reference; ?>" />
	</div>
  <div class="col-sm-1 col-1-harf col-xs-4 padding-5">
    <label class="display-block not-show">change</label>
    <button type="button" class="btn btn-xs btn-yellow btn-block hide" id="btn-change-zone" onclick="changeReference()">เปลี่ยนโซน</button>
    <button type="button" class="btn btn-xs btn-info btn-block" id="btn-set-zone" onclick="load_reference()">โหลดเอกสาร</button>
  </div>

</div>
<?php endif; ?>
<hr class="margin-top-15 margin-bottom-15 padding-5"/>
<form id="detail-from" method="post" action="<?php echo $this->home; ?>/save">
<div class="row">
  <div class="col-sm-12 col-xs-12 padding-5">
    <table class="table table-striped border-1">
      <thead>
        <tr>
          <th class="width-5 text-center">ลำดับ</th>
          <th class="width-20">รหัสสินค้า</th>
          <th class="">สินค้า</th>
					<th class="width-10 text-center">เปิดบิล</th>
					<th class="width-10 text-center">ตัดแล้ว</th>
					<th class="width-10 text-center">คงเหลือ</th>
					<th class="width-10 text-center">ในโซน</th>
          <th class="width-10 text-center">ยอดตัด</th>
					<th class="width-5"></th>
        </tr>
      </thead>
      <tbody id="detail-table">

      </tbody>
    </table>
  </div>
</div>
<input type="hidden" id="code" name="code" value="<?php echo $doc->code; ?>" />
<input type="hidden" id="zone_code" name="zone_code" value="<?php echo $doc->from_zone; ?>" />
<input type="hidden" id="transform_code" name="transform_code" value="<?php echo $doc->reference; ?>" />
</from>

<script id="detail-template" type="text/x-handlebars-template">
	{{#each this}}
		{{#if @last}}
			<tr>
				<td colspan="3" class="middle text-right">รวม</td>
				<td class="middle text-center">{{total_bill_qty}}</td>
				<td class="middle text-center">{{total_issue_qty}}</td>
				<td class="middle text-center">{{total_qty}}</td>
				<td class="middle text-center" id="total-in-zone">{{total_in_zone}}</td>
				<td class="middle text-center" id="total-qty">{{total_qty}}</td>
				<td></td>
			</tr>
		{{else}}
		<tr class="font-size-12 rox {{hilight}}" id="row-{{no}}">
		  <td class="middle text-center no">{{no}}</td>
		  <td class="middle">{{ pdCode }}</td>
		  <td class="middle">{{ pdName }}</td>
			<td class="middle text-center">{{ bill_qty }}</td>
			<td class="middle text-center">{{ issued_qty }}</td>
			<td class="middle text-center">{{qty}}</td>
			<td class="middle text-center in-zone">{{ in_zone_qty }}</td>
		  <td class="middle text-center">
				<input type="hidden" id="limit-{{no}}" value="{{qty}}"/>
				<input type="number" class="form-control input-sm text-center input-qty" data-no="{{no}}" data-product="{{ pdCode }}" value="{{ qty }}">
			</td>
			<td class="middle text-right">
				<button type="button" class="btn btn-xs btn-danger" onclick="removeRow('row-{{no}}', '{{pdCode}}')">
					<i class="fa fa-trash"></i>
				</button>
			</td>
		</tr>
	{{/if}}
{{/each}}
</script>


<script src="<?php echo base_url(); ?>scripts/inventory/adjust_transform/adjust_transform.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/adjust_transform/adjust_transform_add.js?v=<?php echo date('YmdH'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
