<?php
$this->load->helper('print');

$page  = '';
$page .= $this->printer->doc_header();

$this->printer->add_title("Pick Orders List");

$header = array(
  "เลขที่" => $doc->code,
  "วันที่" => thai_date($doc->date_add),
  "คลัง" => $doc->warehouse_code." | ".warehouse_name($doc->warehouse_code),
  "User" => $doc->user
);

$this->printer->add_header($header);

$total_row 	= empty($details) ? 0 :count($details);

$config = array(
  "total_row" => $total_row,
  "row" => 7,
  "row_height" => 20,
  "font_size" => 14,
  "header_rows" => 1,
  "sub_total_row" => 0,
  "footer" => FALSE
);

// echo "<pre>"; print_r($details); echo "</pre>"; exit();

$this->printer->config($config);

$row = $this->printer->row;
$total_page = $this->printer->total_page;

$thead	= '<table class="table" style="margin-bottom:-2px;">';
$thead .= '<thead class="hide">';
$thead .= '<tr style="height:10mm; line-height:10mm;">';
$thead .= '<th style="width:47.5mm; text-align:center; border-top:0px; border-top-left-radius:10px;">&nbsp;</th>';
$thead .= '<th style="width:47.5mm; text-align:center; border-left:solid 1px #ccc;  border-top:0px;"></th>';
// $thead .= '<th style="width:47.5mm; text-align:center; border-left:solid 1px #ccc;  border-top:0px;"></th>';
$thead .= '<th style="width:47.5mm; text-align:center; border-left: solid 1px #ccc; border-top:0px; border-top-right-radius:10px;"></th>';
$thead .= '</tr>';
$thead .= '</thead>';

$this->printer->sub_header = $thead;

$pattern = array(
  "width:47.5mm; text-align:center; border-top:0px;",
  "width:47.5mm; text-align:center; border-top:0px;",
  // "width:47.5mm; text-align:center; border-left:solid 1px #ccc; border-top:0px;",
  "width:47.5mm; text-align:center; border-top:0px;"
);

$this->printer->set_pattern($pattern);

$n = 1;
$index = 0;

while($total_page > 0 )
{
  $page .= $this->printer->page_start();
  $page .= $this->printer->top_page();
  $page .= $this->printer->content_start();
  $page .= $this->printer->table_start();
  $i = 0;

  while($i < $row)
  {
    $rs = isset($details[$index]) ? $details[$index] : FALSE;

    if( ! empty($rs) )
    {
      $data = [];
      $m = 0;

      foreach($rs as $rd)
      {
        $data[] = barcodeImage($rd->order_code, 15);
        $m++;
      }

      while($m < 3)
      {
        $data[] = "";
        $m++;
      }
    }
    else
    {
      $data = array("", "", "");
    }

    $page .= $this->printer->print_row($data);

    $n++;
    $i++;
    $index++;
  }

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
