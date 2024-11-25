<div class="modal fade" id="itemGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" id="modal" style="min-width:300px; min-height:400px; max-width:95vw; max-height:95vh;">
		<div class="modal-content">
  			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modalTitle">title</h4>
			 </div>
			 <div class="modal-body">
         <div class="row">
           <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5"
           id="modalBody"
           style="position:relative; min-width:250px; min-height:400px; max-width:100%; max-height:60vh; overflow:auto;">

           </div>
         </div>
       </div>
			 <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
				<button type="button" class="btn btn-primary" onClick="addItems()" >เพิ่มในรายการ</button>
			 </div>
		</div>
	</div>
</div>

<script id="row-template" type="text/x-handlebarsTemplate">
  <tr id="row-{{no}}">
    <td class="middle text-center">
      <label><input type="checkbox" class="ace chk" value="{{no}}" /><span class="lbl"></span></label>
    </td>
    <td class="middle text-center no">{{no}}</td>
    <td class="middle"><input type="text" class="form-control input-sm item-code" value="{{product_code}}" readonly /></td>
    <td class="middle">
			<input type="text" class="form-control input-sm item-name"
				id="pd-name-{{no}}"
				data-no="{{no}}"
				data-code="{{product_code}}"
				value="{{product_name}}" />
		</td>
		<td class="middle text-right">
      <input type="text" class="form-control input-sm text-right row-open-qty"
        id="open-qty-{{no}}"
				data-no="{{no}}"
        value="{{qtyLabel}}" readonly/>
    </td>
		<td class="middle text-right">
      <input type="text" class="form-control input-sm text-right row-qty"
        id="qty-label-{{no}}" onchange="recalQty({{no}})"
        data-no="{{no}}"
				data-id="0"
        data-code="{{product_code}}"
        data-name="{{product_name}}"
        data-style="{{style_code}}"
        data-uom="{{unit_code}}"
        data-cost="{{cost}}"
        data-vatcode="{{vatCode}}"
        data-vatrate="{{vatRate}}"
				data-count="{{count_stock}}"
				data-linked="N"
				data-status="O"
				data-openqty="{{qty}}"
				data-qty="{{qty}}"
        value="{{qtyLabel}}" />				
    </td>
    <td class="middle text-right">
      <input type="text" class="form-control input-sm text-right row-price"
        id="price-label-{{no}}" onchange="recalAmount({{no}})"
        data-no="{{no}}" value="{{priceLabel}}"/>
    </td>

    <td class="middle text-right">
      <input type="text" class="form-control input-sm text-right row-disc"
        id="disc-label-{{no}}" onfocusout="recalAmount({{no}})"
        data-no="{{no}}" value="" />
    </td>
    <td class="middle text-right">
      <input type="text" class="form-control input-sm text-right row-total" id="total-label-{{no}}" value="{{amountLabel}}" readonly/>
    </td>

    <input type="hidden" id="disc-amount-{{no}}" value="0" />
  </tr>
</script>

<script id="details-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    <tr id="row-{{no}}">
      <td class="middle text-center">
        <label><input type="checkbox" class="ace chk" value="{{no}}" /><span class="lbl"></span></label>
      </td>
      <td class="middle text-center no">{{no}}</td>
      <td class="middle">{{product_code}}</td>
      <td class="middle">
				<input type="text" class="form-control input-sm item-name"
					id="pd-name-{{no}}"
					data-no="{{no}}"
					data-code="{{product_code}}"
					value="{{product_name}}" />
			</td>
      <td class="middle text-right">
        <input type="text" class="form-control input-sm text-right row-price"
          id="price-label-{{no}}" onchange="recalAmount({{no}})"
          data-no="{{no}}" value="{{priceLabel}}"/>
      </td>
      <td class="middle text-right">
        <input type="text" class="form-control input-sm text-right row-qty"
          id="qty-label-{{no}}" onchange="recalAmount({{no}})"
          data-no="{{no}}"
          data-code="{{product_code}}"
          data-name="{{product_name}}"
          data-style="{{style_code}}"
          data-uom="{{unit_code}}"
          data-cost="{{cost}}"
          data-vatcode="{{vatCode}}"
          data-vatrate="{{vatRate}}"
					data-count="{{count_stock}}"
          value="{{qtyLabel}}" />
      </td>
      <td class="middle text-right">
        <input type="text" class="form-control input-sm text-right row-disc"
          id="disc-label-{{no}}" onfocusout="recalAmount({{no}})"
          data-no="{{no}}" value="" />
      </td>
      <td class="middle text-right">
        <input type="text" class="form-control input-sm text-right row-total" id="total-label-{{no}}" value="{{amountLabel}}" readonly/>
      </td>

      <input type="hidden" id="disc-amount-{{no}}" value="0" />
    </tr>
  {{/each}}
</script>
