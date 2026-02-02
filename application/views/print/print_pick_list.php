<?php
$this->load->helper('print');

$page  = '';
$page .= $this->printer->doc_header();

$this->printer->add_title("Pick List");

$header = array(
  "เลขที่" => $doc->code,
  "วันที่" => thai_date($doc->date_add),
  "คลัง" => $doc->warehouse_code." | ".warehouse_name($doc->warehouse_code),
  "ช่องทางขาย" => empty($doc->channels_code) ? "-" : channels_name($doc->channels_code),
  "User" => $doc->user
);

$this->printer->add_header($header);

$total_row 	= empty($details) ? 0 :count($details);

$config = array(
  "total_row" => $total_row,
  "row" => 20,
  "font_size" => 10,
  "header_rows" => 3,
  "sub_total_row" => 1,
  "footer" => FALSE
);

$this->printer->config($config);

$row = $this->printer->row;
$total_page = $this->printer->total_page;

$thead	= array(
  array("#", "width:10mm; text-align:center; border-top:0px; border-top-left-radius:10px;"),
  array("รหัส", "width:50mm; text-align:center; border-left:solid 1px #ccc; border-top:0px;"),
  array("สินค้า", "width:90mm; text-align:center; border-left:solid 1px #ccc; border-top:0px;"),
  array("จำนวน", "width:20mm; text-align:center; border-left:solid 1px #ccc; border-top:0px;"),
  array("สต็อก", "width:20mm; text-align:center; border-left:solid 1px #ccc; border-top:0px; border-top-right-radius:10px")
);

$this->printer->add_subheader($thead);

$pattern = array(
  "text-align:center; border-top:0px;",
  "border-left:solid 1px #ccc; border-top:0px;",
  "border-left:solid 1px #ccc; border-top:0px;",
  "text-align:center; border-left: solid 1px #ccc; border-top:0px;",
  "text-align:center; border-left: solid 1px #ccc; border-top:0px;"
);

$this->printer->set_pattern($pattern);

$n = 1;
$index = 0;
$totalQty = 0;
$totalStock = 0;

while($total_page > 0 )
{
  $page .= $this->printer->page_start();
  $page .= $this->printer->top_page();
  $page .= $this->printer->content_start();
  $page .= $this->printer->table_start();
  $i = 0;

  while($i<$row)
  {
    $rs = isset($details[$index]) ? $details[$index] : FALSE;

    if( ! empty($rs) )
    {
      $stock = $this->stock_model->get_sell_stock($rs->product_code, $doc->warehouse_code);
      $data = array(
        $n,
        $rs->product_code,
        inputRow($rs->product_name),
        number($rs->qty),
        number($stock)
      );

      $totalQty += $rs->qty;
      $totalStock += $stock;
    }
    else
    {
      $data = array("", "", "", "","");
    }

    $page .= $this->printer->print_row($data);

    $n++;
    $i++;
    $index++;
  }

  $sub_qty  = '<td class="subtotal-first text-right" style="width:150mm; height:10mm; background-color:transparent; border-bottom-left:5px;">';
  $sub_qty .=  '<strong>จำนวนรวม</strong>';
  $sub_qty .= '</td>';
  $sub_qty .= '<td class="subtotal text-center" style="width:20mm; border-left:solid 1px #ccc; background-color:transparent;">';
  $sub_qty .=  number($totalQty);
  $sub_qty .= '</td>';
  $sub_qty .= '<td class="subtotal text-center" style="width:20mm; border-left:solid 1px #ccc; background-color:transparent; bottom-bottom-right:5px;">';
  $sub_qty .=  number($totalStock);
  $sub_qty .= '</td>';

  $subTotal = array(array($sub_qty));

  $page .= $this->printer->print_sub_total($subTotal);
  $page .= $this->printer->table_end();
  $page .= $this->printer->content_end();
  $page .= $this->printer->footer;
  $page .= $this->printer->page_end();

  $total_page --;
  $this->printer->current_page++;
}

$page .= $this->printer->doc_footer();

echo $page;
 ?>
