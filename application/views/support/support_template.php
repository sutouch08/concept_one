<script id="text-template" type="text/x-handlebarsTemplate">
  <tr id="text-row-{{id}}" data-id="{{id}}" class="rows">
    <td class="text-center"></td>
    <td class="text-center"></td>
    <td class="text-center">
      <a class="pointer" href="javascript:removeTextRow({{id}})" title="Remove this row"><i class="fa fa-trash fa-lg red"></i></a>
    </td>
    <td class="" colspan="2">
      <textarea id="text-{{id}}" class="autosize autosize-transition form-control" style="border:0px;" onblur="updateLineText({{id}})"></textarea>
    </td>
    <td colspan="4"></td>
  </tr>
</script>

<script id="row-template" type="text/x-handlebarsTemplate">
  <tr class="font-size-12" id="row-{{id}}">
    <input type="hidden" id="currentQty-{{id}}" value="{{qty}}">
    <input type="hidden" id="currentPrice-{{id}}" value="{{price}}">
    <input type="hidden" id="currentDisc-{{id}}" value="{{discLabel}}">
    <input type="hidden" id="sellPrice-{{id}}" value="{{price}}">
    <input type="hidden" id="discAmount-{{id}}" value="{{discount_amount}}">
    <td class="middle text-center no"></td>
    <td class="middle text-center">
      <a class="pointer" href="JavaScript:removeDetail('{{id}}', '{{product_code}}')">
        <i class="fa fa-trash fa-lg red"></i>
      </a>
    </td>
    <td class="middle text-center add-text">
      <a class="pointer {{hide}}" id="add-text-{{id}}" href="javascript:insertTextRow({{id}})" title="Insert text row">
        <i class="fa fa-plus-square-o fa-lg"></i>
      </a>
    </td>
    <td class="middle">{{product_code}}</td>

    <td class="middle">
    <input type="text" class="form-control input-sm" style="border:0px;" id="pd-name-{{id}}" data-code="{{product_code}}"
      data-id="{{id}}" value="{{product_name}}" onchange="updateItem({{id}})" />
    </td>

    <td class="middle text-center">
    <input type="number" class="form-control input-sm text-center line-price" style="border:0px;" id="price-{{id}}"
      name="price[{{id}}]"  data-code="{{product_code}}" data-id="{{id}}" value="{{price}}"  onchange="updateItem({{id}})"  />
    </td>

    <td class="middle text-center">
      <input type="number" class="form-control input-sm text-center line-qty" style="border:0px;" id="qty-{{id}}"
        data-code="{{product_code}}"
        data-vatcode="{{vat_code}}"
        data-vatrate="{{vat_rate}}"
        data-id="{{id}}"
        value="{{qty}}"
        onchange="updateItem({{id}})"  />
    </td>

    <td class="middle text-center">
      <input type="text" class="form-control input-sm text-center line-disc" style="border:0px;" id="disc-{{id}}" name="disc[{{id}}]"
        data-code="{{product_code}}" data-id="{{id}}" value="{{discLabel}}" onchange="updateItem({{id}})" />
    </td>

    <td class="middle text-right">
      <input type="text" class="form-control input-sm text-right" style="border:0px;" id="total-{{id}}" value="{{total_amount}}" readonly/>
    </td>
  </tr>
</script>
