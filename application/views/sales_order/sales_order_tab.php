<div class="tabbable" style="margin-bottom:15px;">
  <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#content" aria-expanded="true">Contents</a></li>
    <li class=""><a data-toggle="tab" href="#images" aria-expanded="true">Images</a></li>
  </ul>

  <div class="tab-content">
    <div class="tab-pane fade active in" id="content">
      <?php $this->load->view('sales_order/sales_order_tab_content'); ?>
    </div>
    <div class="tab-pane" id="images">
      <?php $this->load->view('sales_order/sales_order_tab_image'); ?>
    </div>
  </div>
</div>
