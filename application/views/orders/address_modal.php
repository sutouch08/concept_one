<div class="modal fade" id="billToModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog" style="width:95vw;">
		<div class="modal-content">
			<div class="modal-header" style="border-bottom:solid 1px #f4f4f4;">
				<h3 class="text-center" style="margin:0;">ที่อยู่เปิดบิล</h3>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5" style="max-height:450px; overflow:auto;">
						<table class="table table-striped">
							<thead>
								<tr>
									<th class="fix-width-100 text-center">รหัสสาขา</th>
									<th class="fix-width-150 text-center">ชื่อสาขา</th>
									<th class="fix-width-150">ชื่อ</th>
									<th class="min-width-150">ที่อยู่</th>
									<th class="fix-width-100"></th>
								</tr>
							</thead>
							<tbody id="bill-to-table">

							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
        <button class="btn btn-default btn-100" onclick="closeModal('billToModal')">ตกลง</button>
			</div>
		</div>
	</div>
</div>

<script id="bill-to-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    <tr id="bill-to-{{no}}">
      <td class="text-center">{{branch_code}}</td>
      <td class="text-center">{{branch_name}}</td>
      <td>{{name}}</td>
      <td>{{address}} {{sub_district}} {{district}} {{province}} {{postcode}}</td>
      <td>
        <button type="button"
          class="btn btn-sm btn-primary"
          id="btn-bill-to-{{no}}"
          data-branchcode="{{branch_code}}"
          data-branchname="{{branch_name}}"
          data-address="{{address}}"
          data-subdistrict="{{sub_district}}"
          data-district="{{district}}"
          data-province="{{province}}"
          data-postcode="{{postcode}}"
          onclick="setBillTo({{no}})">เลือก</button>
      </td>
    </tr>
  {{/each}}
</script>

<script>
  function setBillTo(no) {
    let ad = $('#btn-bill-to-'+no);
    console.log(ad);
    let branch_code = ad.data('branchcode');

    $('#branch-code').val(ad.data('branchcode'));
    $('#branch-name').val(ad.data('branchname'));
    $('#address').val(ad.data('address'));
    $('#sub-district').val(ad.data('subdistrict'));
    $('#district').val(ad.data('district'));
    $('#province').val(ad.data('province'));
    $('#postcode').val(ad.data('postcode'));

    if(branch_code != "") {
      $('#is-company').prop('checked', true);
    }
    else {
      $('#is-company').prop('checked', false);
    }

    $('#billToModal').modal('hide');
  }
</script>
