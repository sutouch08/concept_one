<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Current_stock extends PS_Controller
{
  public $menu_code = 'RICSTC';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'REINVT';
	public $title = 'รายงานสินค้าคงเหลือปัจจุบัน';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/inventory/current_stock';
    $this->load->model('report/inventory/current_stock_report_model');
    $this->load->model('masters/products_model');
    $this->load->model('masters/product_style_model');
    $this->load->model('masters/product_group_model');
    $this->load->helper('product_images');
  }

  public function index()
  {

		//--- จำนวนคงเหลือ มูลค่าคงเหลือ
    $stock = $this->current_stock_report_model->get_stock_summary();

    $ds['allQty'] = !empty($stock) ? $stock->qty : 0;

    $ds['allAmount'] = !empty($stock) ? $stock->amount : 0;

    //--- จำนวนรุ่นสินค้า
    $ds['allModel'] = $this->current_stock_report_model->get_count_style();

    //--- จำนวนรายการสินค้า
    $ds['allSku'] = $this->current_stock_report_model->get_count_item();

		$ds['product_group'] = $this->product_group_model->get_data();

    $this->load->view('report/inventory/current_stock_report_view', $ds);

  }


  public function get_report()
  {
    ini_set('memory_limit','512M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
    ini_set('sqlsrv.ClientBufferMaxKBSize','524288'); // Setting to 512M
    ini_set('pdo_sqlsrv.client_buffer_max_kb_size','524288'); // Setting to 512M - for pdo_sqlsrv
    ini_set('max_execution_time', 600);
    $show_stock = TRUE;

    //--- จำนวนคงเหลือ มูลค่าคงเหลือ
    $stock = $this->current_stock_report_model->get_stock_summary();

    $sumQty = !empty($stock) ? $stock->qty : 0;

    $sumCost = !empty($stock) ? $stock->amount : 0;

    //--- จำนวนรุ่นสินค้า
    $sumStyle = $this->current_stock_report_model->get_count_style();

    //--- จำนวนรายการสินค้า
    $sumItem = $this->current_stock_report_model->get_count_item();

    $page = '';
    $page .= '<div class="row">';
    $page .= '  <div class="col-sm-3 padding-5">';
    $page .= '   <div class="icon-box i-blue">Qty</div>';
    $page .= '   <div class="info-box c-blue">'.number($sumQty).'</div>';
    $page .= '  </div>';
    $page .= '  <div class="col-sm-4 padding-5">';
    $page .= '    <div class="icon-box i-green">Amount</div>';
    $page .= '    <div class="info-box c-green">'.number($sumCost, 2).'</div>';
    $page .= '  </div>';
    $page .= '  <div class="col-sm-2 col-2-harf padding-5">';
    $page .= '    <div class="icon-box i-orange">SKU</div>';
    $page .= '    <div class="info-box c-orange">'.number($sumItem).'</div>';
    $page .= '  </div>';
    $page .= '  <div class="col-sm-2 col-2-harf padding-5">';
    $page .= '    <div class="icon-box i-red width-40">Model</div>';
    $page .= '    <div class="info-box c-red width-60">'.number($sumStyle).'</div>';
    $page .= '  </div>';
    $page .= '</div>';
    $page .= '<hr/>';


    $productGroup = $this->product_group_model->get_data();

    $page .= '<div class="row">';
    $page .= '  <div class="col-sm-2 padding-5 padding-right-0">';
    $page .= '    <ul id="myTab1" class="setting-tabs" style="margin-left:0px;">';

    if(!empty($productGroup))
    {
      $i = 1;
      foreach($productGroup AS $rs)
      {
        $page .= '<li class="li-block '.($i == 1 ? 'active' : '').'">';
        $page .= ' <a href="#'.$rs->code.'" data-toggle="tab">'.$rs->name.'</a>';
        $page .= '</li>';
        $i++;
      }
    }

    $page .= '    </ul>';
    $page .= '  </div>';
    $page .= '  <div class="col-sm-10" style="border-left:solid 1px #ccc;">';
    $page .= '    <div class="tab-content" style="border:0;">';


    if(!empty($productGroup))
    {
      $i = 1;
      foreach($productGroup as $rs)
      {
        $page .= '<div role="tabpanel" class="tab-pane '.($i == 1 ? 'active' : '').'" id="'.$rs->code.'">';

        //--- จำนวนคงเหลือ มูลค่าคงเหลือ
        $stock = $this->current_stock_report_model->get_stock_summary_by_group($rs->code);
        $sumQty = !empty($stock) ? $stock->qty : 0;
        $sumCost = !empty($stock) ? $stock->amount : 0;

        //--- จำนวนรุ่นสินค้า
        $sumStyle = $this->current_stock_report_model->get_count_style_by_group($rs->code);

        //--- จำนวนรายการสินค้า
        $sumItem = $this->current_stock_report_model->get_count_item_by_group($rs->code);

        $page .= '<div class="row">';
        $page .= '  <div class="col-sm-3 padding-5">';
        $page .= '   <div class="sub-icon i-blue">Qty</div>';
        $page .= '   <div class="sub-info c-blue font-size-16">'.number($sumQty).'</div>';
        $page .= '  </div>';
        $page .= '  <div class="col-sm-4 padding-5">';
        $page .= '    <div class="sub-icon i-green">Amount</div>';
        $page .= '    <div class="sub-info c-green font-size-16">'.number($sumCost, 2).'</div>';
        $page .= '  </div>';
        $page .= '  <div class="col-sm-2 col-2-harf padding-5">';
        $page .= '    <div class="sub-icon i-orange">SKU</div>';
        $page .= '    <div class="sub-info c-orange font-size-16">'.number($sumItem).'</div>';
        $page .= '  </div>';
        $page .= '  <div class="col-sm-2 col-2-harf padding-5">';
        $page .= '    <div class="sub-icon i-red width-40">Model</div>';
        $page .= '    <div class="sub-info c-red width-60">'.number($sumStyle).'</div>';
        $page .= '  </div>';
        $page .= '</div>';
        $page .= '<hr/>';

        $style = $this->current_stock_report_model->get_sum_stock_style_by_group($rs->code);

        if(!empty($style))
        {
          $count = 1;
          foreach($style as $rb)
          {
            if($count === 1)
            {
              $page .= '<div class="row"><div calss="col-sm-12">';
            }

            $page .= '<div class="item2 col-lg-2 col-md-3 col-sm-4 col-xs-6 text-center margin-bottom-15">';
        		$page .= 	'<div class="product padding-5">';
        		$page .= 		'<div class="image">';
        		$page .= 			'<a href="javascript:void(0)" onclick="getData(\''.$rb->code.'\')">';
        		$page .=			'<img src="'.get_cover_image($rb->code, 'default').'" class="img-responsoive" />';
        		$page .=			'</a>';
        		$page .= 		'</div>';
        		$page .= 		'<div class="description font-size-12">' . $rb->code . '</div>';
						$page .= 		'<div class="price text-center">';
						$page .=			'<span class="red font-size-10">' . number($rb->qty) . '</span>';
						$page .=			' | ';
						$page .=			'<span class="blue font-size-10">' . number($rb->amount, 2) . '</span>';
						$page .=		'</div>';
        		$page .= 	'</div>';
        		$page .= '</div>';

            if($count === 6)
            {
              $page .= '</div></div>';
            }

            $count++;

            if($count > 6)
            {
              $count = 1;
            }

          } //--- end foreach

          if($count != 1)
          {
            $page .= '</div></div>';
          }
        }
				else
				{
					$page .= '<div class="row"><div class="col-sm-12 text-center"><h3>No Data</h3></div></div>';
				}


        $page .= '</div>';
        $i++;
      }
    }


    echo $page;

  }



  public function get_stock_grid()
  {
    $code = $this->input->get('style_code');
    if(!empty($code))
    {
      $this->load->library('product_grid');

      $rs = $this->product_grid->getOrderGrid($code);
      $tableWidth	= $this->products_model->countAttribute($code) == 1 ? 600 : $this->product_grid->getOrderTableWidth($code);

			if($rs != 'notfound')
			{
				$ds = array(
					'table' => $rs,
					'width' => $tableWidth,
					'code' => $code
				);

				echo json_encode($ds);
			}
			else {
				echo "ไม่พบรหัสสินค้า";
			}
    }
    else
    {
      echo "ไม่พบรหัสสินค้า";
    }
  }

} //--- end class








 ?>
