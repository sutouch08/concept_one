<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <div class="tabbable">
      <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a data-toggle="tab" href="#orders-tab" aria-expanded="false">Orders</a></li>
        <?php if($doc->status == 'P') : ?>
          <li class=""><a data-toggle="tab" href="#details-tab" onclick="reloadDetails()" aria-expanded="true">Items</a></li>
        <?php else : ?>
          <li class=""><a data-toggle="tab" href="#details-tab" aria-expanded="true">Items</a></li>
          <li class=""><a data-toggle="tab" href="#rows-tab" aria-expanded="false">Summary</a></li>
        <?php endif; ?>
      </ul>
      <div class="tab-content" style="padding:0px;">
        <?php $this->load->view('inventory/pick_list/pick_tab_orders'); ?>
        <?php $this->load->view('inventory/pick_list/pick_tab_details'); ?>
        <?php if($doc->status != 'P') : ?>
          <?php $this->load->view('inventory/pick_list/pick_tab_rows'); ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
