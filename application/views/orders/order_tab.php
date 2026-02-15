<?php $disabled = $order->state == 1 && empty($order->so_code) && ($this->pm->can_add OR $this->pm->can_edit) ? '' : 'disabled'; ?>
<div role="tabpanel" class="tab-pane active" id="doc-pane">
  <div class="row">
  	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
      	<label>เลขที่เอกสาร</label>
        <input type="text" class="form-control input-sm text-center" value="<?php echo $order->code; ?>" disabled />
      </div>
  		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
        <label>เล่มเอกสาร</label>
        <select class="form-control input-sm h" id="is-term" <?php echo $disabled; ?>>
          <option value="">เลือก</option>
          <option value="0" <?php echo is_selected('0', $order->is_term); ?>>ขายสด</option>
          <option value="1" <?php echo is_selected('1', $order->is_term); ?>>ขายเชื่อ</option>
        </select>
      </div>
      <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
        <label>ชนิด VAT</label>
        <select class="form-control input-sm h" id="vat-type" onchange="toggleVatType()" <?php echo $disabled; ?>>
          <option value="">เลือก</option>
          <option value="E" <?php echo is_selected('E', $order->vat_type); ?>>แยกนอก</option>
          <option value="I" <?php echo is_selected('I', $order->vat_type); ?>>รวมใน</option>
          <option value="N" <?php echo is_selected('N', $order->vat_type); ?>>ไม่ VAT</option>
        </select>
        <input type="hidden" id="tax-status" value="<?php echo $order->TaxStatus; ?>">
      </div>
      <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      	<label>วันที่</label>
  			<input type="text" class="form-control input-sm text-center h" name="date" id="date" value="<?php echo thai_date($order->date_add); ?>" <?php echo $disabled; ?> readonly />
      </div>
  		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
  			<label>รหัสลูกค้า</label>
  			<input type="text" class="form-control input-sm text-center h" id="customer-code" value="<?php echo $order->customer_code; ?>" <?php echo $disabled; ?> />
  		</div>
      <div class="col-lg-4-harf col-md-4-harf col-sm-4 col-xs-8 padding-5">
      	<label>ชื่อลูกค้า</label>
  			<input type="text" class="form-control input-sm h" id="customer-name" value="<?php echo $order->customer_name; ?>"  <?php echo $disabled; ?> />
      </div>
  		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
  			<label class="display-block not-show">isCompany</label>
  			<label style="margin-top:0;">
  				<input type="checkbox" class="ace" id="is-company" value="1" onchange="toggleBranch()" <?php echo is_checked('1', $order->isCompany); ?> <?php echo $disabled; ?>/>
  				<span class="lbl margin-top-5">&nbsp;&nbsp;นิติบุคคล</span>
  			</label>
  		</div>
  		<div class="col-lg-5-harf col-md-4 col-sm-4 col-xs-6 padding-5">
  	    <label>ผู้ติดต่อ</label>
        <input type="text" class="form-control input-sm h" id="customer_ref" name="customer_ref" value="<?php echo str_replace('"', '&quot;',$order->customer_ref); ?>" <?php echo $disabled; ?> />
      </div>
  		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
  	    <label>เบอร์โทร</label>
  			<input type="text" class="form-control input-sm h" name="phone" id="phone" value="<?php echo $order->phone; ?>" <?php echo $disabled; ?>/>
  	  </div>
  		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
  			<label>เลขที่ผู้เสียภาษี</label>
  			<input type="text" class="form-control input-sm text-center h" id="tax-id" value="<?php echo $order->tax_id; ?>" <?php echo $disabled; ?>/>
  		</div>
  		<div class="col-lg-1 col-md-1 col-sm-1 col-xs-6 padding-5">
  			<label>สาขา</label>
  			<input type="text" class="form-control input-sm h" id="branch-code" value="<?php echo $order->branch_code; ?>" <?php echo $disabled; ?>/>
  		</div>
  		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
  			<label>ชื่อสาขา</label>
  			<input type="text" class="form-control input-sm h" id="branch-name" value="<?php echo $order->branch_name; ?>" <?php echo $disabled; ?>/>
  		</div>
  		<div class="col-lg-5 col-md-6 col-sm-4-harf col-xs-12 padding-5">
  			<label>ที่อยู่เปิดบิล</label>
  			<input type="text" class="form-control input-sm h" id="address" value="<?php echo $order->address; ?>" <?php echo $disabled; ?>/>
  		</div>
  		<div class="col-lg-2 col-md-1-harf col-sm-2 col-xs-6 padding-5">
  			<label>ตำบล</label>
  			<input type="text" class="form-control input-sm h" id="sub-district" value="<?php echo $order->sub_district; ?>" <?php echo $disabled; ?>/>
  		</div>
  		<div class="col-lg-2 col-md-1-harf col-sm-2 col-xs-6 padding-5">
  			<label>อำเภอ</label>
  			<input type="text" class="form-control input-sm h" id="district" value="<?php echo $order->district; ?>" <?php echo $disabled; ?>/>
  		</div>
  		<div class="col-lg-2 col-md-1-harf col-sm-2 col-xs-6 padding-5">
  			<label>จังหวัด</label>
  			<input type="text" class="form-control input-sm h" id="province" value="<?php echo $order->province; ?>" <?php echo $disabled; ?>/>
  		</div>
  		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
  			<label>ไปรษณีย์</label>
  			<input type="text" class="form-control input-sm h" id="postcode" value="<?php echo $order->postcode; ?>" <?php echo $disabled; ?>/>
  		</div>

  		<div class="divider"></div>

  		<div class="col-lg-2 col-md-2-harf col-sm-2 col-xs-6 padding-5">
  			<label>อ้างอิง</label>
  			<input type="text" class="form-control input-sm text-center h" name="reference" id="reference" value="<?php echo $order->reference; ?>" <?php echo $disabled; ?> />
  		</div>

      <div class="col-lg-1-harf col-md-2-harf col-sm-2 col-xs-6 padding-5">
      	<label>ช่องทางขาย</label>
  			<select class="form-control input-sm h" name="channels" id="channels" <?php echo $disabled; ?>>
  				<option value="">เลือกรายการ</option>
  				<?php echo select_channels($order->channels_code); ?>
  			</select>
      </div>

  		<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
  			<label>คลัง</label>
  	    <select class="form-control input-sm h" name="warehouse" id="warehouse">
  				<option value="">เลือกคลัง</option>
  				<?php echo select_sell_warehouse($order->warehouse_code); ?>
  			</select>
  	  </div>
  		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
  		 	<label>ใบกำกับ</label>
  		  <input type="text" class="form-control input-sm" value="<?php echo $order->invoice_code; ?>" disabled />
  		</div>

  		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
  			<label>ใบสั่งขาย</label>
  	    <input type="text" class="form-control input-sm text-center" id="so-code" placeholder="ใบสั่งขาย" value="<?php echo $order->so_code; ?>" <?php echo $disabled; ?> <?php echo (empty($order->so_code) ? '' : 'disabled'); ?> />
  	  </div>
  	  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
  	    <?php $load = empty($order->so_code) ? '' : 'hide'; ?>
  	    <?php $clear = empty($order->so_code) ? 'hide' : ''; ?>
  			<label class="display-block not-show">load</label>

  			<?php $active = "disabled"; ?>
  			<?php if($order->state == 1 && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
  				<?php $active = ""; ?>
  			<?php endif; ?>
  	  	<button type="button" class="btn btn-xs btn-primary btn-block <?php echo $load; ?>" id="btn-add-so" onclick="loadSo()" <?php echo $active; ?>>Add</button>
  	    <button type="button" class="btn btn-xs btn-warning btn-block <?php echo $clear; ?>" id="btn-clear-so" onclick="clearSO()"  <?php echo $active; ?>>Clear</button>
  	  </div>

      <input type="hidden" name="customerCode" id="customerCode" value="<?php echo $order->customer_code; ?>" />
  		<input type="hidden" name="order_code" id="order_code" value="<?php echo $order->code; ?>" />
  		<input type="hidden" name="address_id" id="address_id" value="<?php echo $order->id_address; //--- id_address ใช้แล้วใน online modal?>" />
  		<input type="hidden" id="current-date" value="<?php echo thai_date($order->date_add); ?>" />
  		<input type="hidden" id="current-customer" value="<?php echo $order->customer_code; ?>" />
  		<input type="hidden" id="current-channels" value="<?php echo $order->channels_code; ?>" />
  </div>
</div>
