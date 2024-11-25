<?php
class Items_grid
{
  public function __construct()
  {
    // Assign the CodeIgniter super-object
    $this->ci =& get_instance();
    $this->ci->load->model('masters/products_model');
    $this->ci->load->model('masters/product_style_model');
    $this->ci->load->model('stock/stock_model');
    $this->ci->load->model('orders/orders_model');
  }


  public function getProductGrid($style_code, $whsCode = NULL)
	{
		$sc = '';

    $style = $this->ci->product_style_model->get($style_code);

    if(!empty($style))
    {
      $attrs = $this->getAttribute($style->code);

      if( count($attrs) == 1  )
      {
        $sc .= $this->orderGridOneAttribute($style, $attrs[0], $whsCode);
      }
      else if( count( $attrs ) == 2 )
      {
        $sc .= $this->orderGridTwoAttribute($style, $whsCode);
      }

    }
    else
    {
      $sc = 'notfound';
    }

		return $sc;
	}




  public function orderGridOneAttribute($style, $attr, $whsCode = NULL)
	{
		$sc 		= '';

		$data 	= $attr == 'color' ? $this->getAllColors($style->code) : $this->getAllSizes($style->code);
		$items	= $this->ci->products_model->get_style_items($style->code);

		$i = 0;
    $r = 0; //--- row number
    $c = 0; //--- column number

    foreach($items as $item )
    {
      // $id_attr	= $item->size_code === NULL OR $item->size_code === '' ? $item->color_code : $item->size_code;
      $sc 	.=  '<tr>';
      $active	= $item->active == 0 ? 'Inactive' : ( $item->can_sell == 0 ? 'Not for sell' : ( $item->is_deleted == 1 ? 'Deleted' : TRUE ) );
      $stock	= $item->count_stock ? ( $active === TRUE ? $this->ci->stock_model->get_stock($item->code) : 0 ) : 0; //---- สต็อกทั้งหมดทุกคลัง
      $qty = $item->count_stock ? ( $active === TRUE ? $this->get_sell_stock($item->code, $whsCode)  : 0 ) : FALSE; //--- สต็อกที่สั่งซื้อได้

      if( $qty < 1 && $active === TRUE )
      {
        $txt = '<span class="font-size-12 red">Sold out</span>';
      }
      else
      {
        $txt = $active === TRUE ? '' : '<span class="font-size-12 blue">'.$active.'</span>';
      }

      $available = $qty === FALSE && $active === TRUE ? '' : ( ($qty < 1 || $active !== TRUE ) ? $txt : number($qty));

      $code = $attr == 'color' ? $item->color_code.' ('.$data[$item->color_code].')' : $item->size_code;

			$sc .= '<td class="text-center middle r" >';
			$sc .= '<strong>' .	$code. '</strong>';
			$sc .= '</td>';
			$sc .= '<td class="order-grid fix-width-100 middle">';
      $sc .= $item->count_stock ? '<center><span class="font-size-10 blue pointer" onClick="viewStock(\''.$item->code.'\')">('.number($stock).')</span></center>' : '<center><span class="font-size-10 blue pointer" >(0)</span></center>';
      $sc .= '<input type="number" min="0" ';
      $sc .= 'class="form-control text-center item-grid r-'.$r.' c-'.$c.'" ';
      $sc .= 'name="qty[0]['.$item->code.']" ';
      $sc .= 'id="qty-'.$r.$c.'" ';
      $sc .= 'data-code="'.$item->code.'" ';
      $sc .= 'data-name="'.$item->name.'" ';
      $sc .= 'data-style="'.$item->style_code.'" ';
      $sc .= 'data-uom="'.$item->unit_code.'" ';
      $sc .= 'data-cost="'.$item->cost.'" ';
      $sc .= 'data-price="'.$item->price.'" ';
      $sc .= 'data-vatcode="'.$item->purchase_vat_code.'" ';
      $sc .= 'data-vatrate="'.$item->purchase_vat_rate.'" ';
      $sc .= 'data-count="'.$item->count_stock.'" ';
      $sc .= 'data-row="'.$r.'" data-col="'.$c.'" ';
      $sc .= 'data-limit="-1"/>';
      $sc .= $item->count_stock ? '<center>'.$available.'</center>' : '<center class="not-show">0</center>';
			$sc .= '</td>';

			$i++;

			$sc .= '</tr>';
      $r++;
    }

		return $sc;
	}



  public function orderGridTwoAttribute($style, $whsCode = NULL)
	{
		$colors	= $this->getAllColors($style->code);
		$sizes 	= $this->getAllSizes($style->code);
		$sc = '';
		$sc .= $this->gridHeader($colors);

    $r = 0; //-- row number

		foreach( $sizes as $size_code => $size )
		{
      $c = 0; //-- column number

      $bg_color = '';
			$sc 	.= '<tr style="font-size:12px; '.$bg_color.'">';
			$sc 	.= '<td class="text-center middle r"><strong>'.$size_code.'</strong></td>';

			foreach( $colors as $color_code => $color )
			{
        $item = $this->ci->products_model->get_item_by_color_and_size($style->code, $color_code, $size_code);

				if( ! empty($item) )
				{
          $active	= $item->active == 0 ? 'Inactive' : ( $item->can_sell == 0 ? 'Not for sell' : ( $item->is_deleted == 1 ? 'Deleted' : TRUE ) );
          $stock	= $item->count_stock ? ( $active === TRUE ? $this->ci->stock_model->get_stock($item->code) : 0 ) : 0; //---- สต็อกทั้งหมดทุกคลัง
          $qty = $item->count_stock ? ( $active === TRUE ? $this->get_sell_stock($item->code, $whsCode)  : 0 ) : FALSE; //--- สต็อกที่สั่งซื้อได้

          if( $qty < 1 && $active === TRUE )
          {
            $txt = '<span class="font-size-12 red">Sold out</span>';
          }
          else
          {
            $txt = $active === TRUE ? '' : '<span class="font-size-12 blue">'.$active.'</span>';
          }

          $available = $qty === FALSE && $active === TRUE ? '' : ( ($qty < 1 || $active !== TRUE ) ? $txt : number($qty));


					$sc .= '<td class="order-grid">';
          $sc .= $item->count_stock ? '<center><span class="font-size-10 blue pointer" onClick="viewStock(\''.$item->code.'\')">('.number($stock).')</span></center>' : '<center><span class="font-size-10 blue pointer" >(0)</span></center>';
          $sc .= '<input type="number" min="0" ';
          $sc .= 'class="form-control text-center item-grid r-'.$r.' c-'.$c.'" ';
          $sc .= 'name="qty['.$item->color_code.']['.$item->code.']" ';
          $sc .= 'id="qty-'.$r.$c.'" ';
          $sc .= 'data-code="'.$item->code.'" ';
          $sc .= 'data-name="'.$item->name.'" ';
          $sc .= 'data-style="'.$item->style_code.'" ';
          $sc .= 'data-uom="'.$item->unit_code.'" ';
          $sc .= 'data-cost="'.$item->cost.'" ';
          $sc .= 'data-price="'.$item->price.'" ';
          $sc .= 'data-vatcode="'.$item->purchase_vat_code.'" ';
          $sc .= 'data-vatrate="'.$item->purchase_vat_rate.'" ';
          $sc .= 'data-count="'.$item->count_stock.'" ';
          $sc .= 'data-row="'.$r.'" data-col="'.$c.'" ';
          $sc .= 'placeholder="'.$color_code.'-'.$size_code.'" />';
          $sc .= $item->count_stock ? '<center>'.$available.'</center>' : '';
					$sc .= '</td>';
				}
				else
				{
          $sc .= '<td class="order-grid middle">';
          $sc .= '<center><span class="font-size-10 not-show" >(0)</span></center>';
          $sc .= '<input type="text" min="0" ';
          $sc .= 'class="form-control text-center item-grid r-'.$r.' c-'.$c.'" ';
          $sc .= 'id="qty-'.$r.$c.'" ';
          $sc .= 'data-row="'.$r.'" data-col="'.$c.'" ';
          $sc .= 'placeholder="'.$color_code.'-'.$size_code.'" value="N/A" readonly />';
          $sc .= '<center class="not-show">0</center>';
					$sc .= '</td>';
				}

        $c++;
			} //--- End foreach $colors

			$sc .= '</tr>';

      $r++;
		} //--- end foreach $sizes

    return $sc;
	}


  public function getAttribute($style_code)
  {
    $sc = array();

    $color = $this->ci->products_model->count_color($style_code);
    $size  = $this->ci->products_model->count_size($style_code);

    if( $color > 0 )
    {
      $sc[] = "color";
    }

    if( $size > 0 )
    {
      $sc[] = "size";
    }


    return $sc;
  }



  public function gridHeader(array $colors)
  {
    $sc = '<tr class="font-size-12"><td style="width:80px;">&nbsp;</td>';
    foreach( $colors as $code => $name )
    {
      $sc .= '<td class="text-center middle c" style="width:80px; white-space:normal;">'.$code . '<br/>'. $name.'</td>';
    }
    $sc .= '</tr>';
    return $sc;
  }


  public function getAllColors($style_code)
	{
		$sc = array();
    $colors = $this->ci->products_model->get_all_colors($style_code);
    if($colors !== FALSE)
    {
      foreach($colors as $color)
      {
        $sc[$color->code] = $color->name;
      }
    }

    return $sc;
	}


  public function getAllSizes($style_code)
	{
		$sc = array();

		$sizes = $this->ci->products_model->get_all_sizes($style_code);

		if( ! empty($sizes) )
		{
      foreach($sizes as $size)
      {
        $sc[$size->code] = $size->name;
      }
		}

		return $sc;
	}


  public function getSizeColor($size_code)
  {
    $colors = array(
      'XS' => '#DFAAA9',
      'S' => '#DFC5A9',
      'M' => '#DEDFA9',
      'L' => '#C3DFA9',
      'XL' => '#A9DFAA',
      '2L' => '#A9DFC5',
      '3L' => '#A9DDDF',
      '5L' => '#A9C2DF',
      '7L' => '#ABA9DF'
    );

    if(isset($colors[$size_code]))
    {
      return $colors[$size_code];
    }

    return FALSE;
  }



  public function getGridTableWidth($style_code)
  {
    $sc = 300; //--- ชั้นต่ำ
    $tdWidth = 80;  //----- แต่ละช่อง
    $padding = 80; //----- สำหรับช่องแสดงไซส์
    $color = $this->ci->products_model->count_color($style_code);
    if($color > 0)
    {
      $sc = $color * $tdWidth + $padding;
    }

    return $sc;
  }


  public function get_sell_stock($item_code, $warehouse = NULL)
  {
    $sell_stock = $this->ci->stock_model->get_sell_stock($item_code, $warehouse, NULL);
    $reserv_stock = $this->ci->orders_model->get_reserv_stock($item_code, $warehouse, NULL);
    $availableStock = $sell_stock - $reserv_stock;
		return $availableStock < 0 ? 0 : $availableStock;
  }
}

 ?>
