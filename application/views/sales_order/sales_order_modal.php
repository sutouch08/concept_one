<!--  Add New Address Modal  --------->
<div class="modal fade" id="addressModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="max-width:500px;">
    <div class="modal-content">
      <div class="modal-header" style="border-bottom:solid 1px #e5e5e5;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title-site text-center margin-top-5 margin-bottom-5">เพิ่ม/แก้ไข ที่อยู่สำหรับจัดส่ง</h4>
      </div>
      <div class="modal-body" style="padding-top:5px;">
        <form id="addAddressForm"	>
          <input type="hidden" name="id_address" id="id_address" />
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 first last">
              <label class="input-label">ชื่อ</label>
              <input type="text" class="form-control input-sm" name="Fname" id="Fname" placeholder="ชื่อผู้รับ (จำเป็น)" />
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 first last">
              <label class="input-label">ที่อยู่</label>
              <input type="text" class="form-control input-sm" name="address" id="address1" placeholder="เลขที่, หมู่บ้าน, ถนน (จำเป็น)" />
            </div>

            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5 first">
              <label class="input-label">ตำบล/แขวง</label>
              <input type="text" class="form-control input-sm" name="sub_district" id="sub_district" placeholder="ตำบล" />
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5 last">
              <label class="input-label">อำเภอ/เขต</label>
              <input type="text" class="form-control input-sm" name="district" id="district" placeholder="อำเภอ (จำเป็น)" />
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5 first">
              <label class="input-label">จังหวัด</label>
              <input type="text" class="form-control input-sm" name="province" id="province" placeholder="จังหวัด (จำเป็น)" />
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5 last">
              <label class="input-label">รหัสไปรษณีย์</label>
              <input type="text" class="form-control input-sm" name="postcode" id="postcode" placeholder="รหัสไปรษณีย์" />
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5 first">
              <label class="input-label">ประเทศ</label>
              <input type="text" class="form-control input-sm" name="country" id="country" placeholder="Thailand" value="Thailand"/>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5 last">
              <label class="input-label">เบอร์โทรศัพท์</label>
              <input type="text" class="form-control input-sm" name="phone" id="s-phone" placeholder="000 000 0000" />
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5 first">
              <label class="input-label">อีเมล์</label>
              <input type="text" class="form-control input-sm" name="email" id="email" placeholder="someone@somesite.com" />
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5 last">
              <label class="input-label">ชื่อเรียก</label>
              <input type="text" class="form-control input-sm" name="alias" id="alias" placeholder="ใช้เรียกที่อยู่ เช่น บ้าน, ที่ทำงาน (จำเป็น)" />
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-success" onClick="saveAddress()" ><i class="fa fa-save"></i> บันทึก</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="max-width:95vw;">
    <div class="modal-content">
      <div class="modal-header" style="border-bottom:solid 1px #e5e5e5;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title-site text-center margin-top-5 margin-bottom-5">สร้างออเดอร์</h4>
      </div>
      <div class="modal-body" style="padding-top:5px;">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th class="fix-width-40 text-center"></th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-default btn-100" onClick="closeModal('detailsModal')" >ยกเลิก</button>
        <button type="button" class="btn btn-sm btn-primmary btn-100" onClick="createWo()" >สร้างออเดอร์</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="soGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:950px; max-width:95vw;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <center style="margin-bottom:10px;"><h4 class="modal-title">สร้างออเดอร์</h4></center>
      </div>
      <div class="modal-body" style="max-width:94vw; min-height:300px; max-height:70vh; overflow:auto;">
        <table class="table table-striped table-bordered" style="font-size:11px; table-layout: fixed; min-width:900px;">
          <thead>
            <th class="fix-width-40 text-center">#</th>
            <th class="fix-width-150 text-center">Item</th>
            <th class="min-width-200 text-center">Description</th>
						<th class="fix-width-80 text-center">Sell Price</th>
            <th class="fix-width-80 text-center">Qty</th>
            <th class="fix-width-80 text-center">Open</th>
            <th class="fix-width-80 text-center">Commited</th>
            <th class="fix-width-80 text-center">Available</th>
            <th class="fix-width-100 text-center">Order</th>
          </thead>
          <tbody id="so-table">

          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-default top-btn" data-dismiss="modal">ยกเลิก</button>
				<button type="button" class="btn btn-sm btn-yellow top-btn" onclick="takeAll()">เลือกทั้งหมด</button>
				<button type="button" class="btn btn-sm btn-purple top-btn" onclick="clearAll()">เคลียร์ทั้งหมด</button>
        <button type="button" class="btn btn-sm btn-primary top-btn" id="btn-create-wo" onclick="createWo()">สร้างออเดอร์</button>
        <button type="button" class="btn btn-sm btn-primary top-btn" id="btn-create-wq" onclick="createWq()">สร้างใบเบิกสินค้า</button>
       </div>
    </div>
  </div>
</div>

<script id="so-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    <tr id="row-{{id}}">
      <td class="middle text-center">{{no}}</td>
      <td class="middle">{{product_code}}</td>
      <td class="middle">{{product_name}}</td>
			<td class="middle text-right">{{sell_price_label}}</td>
      <td class="middle text-right">{{qty_label}}</td>
      <td class="middle text-right">{{open_label}}</td>
      <td class="middle text-right">{{commit_label}}</td>
      <td class="middle text-right">{{available_label}}</td>
      <td class="middle text-right">
        <input type="number" class="form-control input-sm text-right so-qty"
          id="so-qty-{{id}}"
          data-id="{{id}}"
          data-code="{{product_code}}"
          data-name="{{product_name}}"
          data-style="{{style_code}}"
          data-basecode="{{order_code}}"
          data-baseline="{{id}}"
          data-baseentry="{{id_order}}"
          data-openqty="{{OpenQty}}"
          data-qty="{{qty}}"
          data-commit="{{commit_qty}}"
          data-available="{{available}}"
          data-price="{{price}}"
					data-sellprice="{{sell_price}}"
					data-discprcnt="{{discount_label}}"
          data-avgbilldisc="{{avgBillDiscAmount}}"
          data-vatcode="{{vat_code}}"
          data-vatrate="{{vat_rate}}"
					data-unit="{{unit_code}}"
          data-iscount="{{is_count}}"
          value="{{available}}" />
      </td>
    </tr>
  {{/each}}
</script>

<script id="addressTemplate" type="text/x-handlebars-template">
<tr style="font-size:12px;" id="{{ id }}">
	<td align="center">{{ alias }}</td>
	<td>{{ name }}</td>
	<td>{{ address }}</td>
	<td>{{ email }}</td>
	<td>{{ phone }}</td>
	<td align="right">
	{{#if default}}
		<button type="button" class="btn btn-xs btn-success btn-address" id="btn-{{ id }}" onClick="setAddress({{ id }})"><i class="fa fa-check"></i></button>
	{{else}}
		<button type="button" class="btn btn-xs btn-address" id="btn-{{ id }}" onClick="setAddress({{ id }})"><i class="fa fa-check"></i></button>
	{{/if}}
		<button type="button" class="btn btn-xs btn-warning" onClick="editAddress({{ id }})"><i class="fa fa-pencil"></i></button>
		<button type="button" class="btn btn-xs btn-danger" onClick="removeAddress({{ id }})"><i class="fa fa-trash"></i></button>
	</td>
</tr>
</script>



<script id="addressTableTemplate" type="text/x-handlebars-template">
{{#each this}}
<tr style="font-size:12px;" id="{{ id }}">
	<td align="center">{{ alias }}</td>
	<td>{{ name }}</td>
	<td>{{ address }}</td>
	<td>{{ email }}</td>
	<td>{{ phone }}</td>
	<td align="right">
	{{#if default}}
		<button type="button" class="btn btn-xs btn-success btn-address" id="btn-{{ id }}" onClick="setAddress({{ id }})"><i class="fa fa-check"></i></button>
	{{else}}
		<button type="button" class="btn btn-xs btn-address" id="btn-{{ id }}" onClick="setAddress({{ id }})"><i class="fa fa-check"></i></button>
	{{/if}}
		<button type="button" class="btn btn-xs btn-warning" onClick="editAddress({{ id }})"><i class="fa fa-pencil"></i></button>
		<button type="button" class="btn btn-xs btn-danger" onClick="removeAddress({{ id }})"><i class="fa fa-trash"></i></button>
	</td>
</tr>
{{/each}}
</script>

<script src="<?php echo base_url(); ?>scripts/orders/country_list.js?v=<?php echo date('Ymd'); ?>"></script>
