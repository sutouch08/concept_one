<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0 padding-bottom-15">
		<div class="tabable">
      <ul class="nav nav-tabs" role="tablist">
        <li class="active">
          <a href="#doc-pane" id="doc-tab" aria-expanded="true" aria-controls="doc-pane" role="tab" data-toggle="tab">เอกสาร</a>
        </li>
        <li>
          <a href="#image-pane" id="image-tab" aria-expanded="false" aria-controls="image-pane" role="tab" data-toggle="tab">รูปภาพ</a>
        </li>
      </ul>

			<div class="tab-content">
				<?php $this->load->view('orders/order_tab'); ?>
				<?php $this->load->view('orders/image_tab'); ?>
			</div>
		</div>

	</div>
</div>

<hr class="margin-bottom-15 padding-5"/>

<script>
	$('#sale-id').select2();
</script>
