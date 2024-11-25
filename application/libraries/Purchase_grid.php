<?php
class Purchase_grid
{
  public function __construct()
  {
    // Assign the CodeIgniter super-object
    $this->ci =& get_instance();
    $this->ci->load->model('masters/products_model');
    $this->ci->load->model('masters/product_style_model');
  }


  public function getProductGrid($style_code)
	{
		$sc = '';

    $style = $this->ci->product_style_model->get($style_code);

    if(!empty($style))
    {
      $attrs = $this->getAttribute($style->code);

      if( count($attrs) == 1  )
      {
        $sc .= $this->orderGridOneAttribute($style, $attrs[0]);
      }
      else if( count( $attrs ) == 2 )
      {
        $sc .= $this->orderGridTwoAttribute($style);
      }

    }
    else
    {
      $sc = 'notfound';
    }

		return $sc;
	}




  public function orderGridOneAttribute($style, $attr)
	{
		$sc 		= '';
		$data 	= $attr == 'color' ? $this->getAllColors($style->code) : $this->getAllSizes($style->code);
		$items	= $this->ci->products_model->get_style_items($style->code);

		$i = 0;
    $r = 0; //--- row number
    $c = 0; //--- column number

    foreach($items as $item )
    {
      $id_attr	= $item->size_code === NULL OR $item->size_code === '' ? $item->color_code : $item->size_code;
      $sc 	.= $i%2 == 0 ? '<tr>' : '';

      $code = $attr == 'color' ? $item->color_code : $item->size_code;

			$sc .= '<td class="middle" style="border-right:0px;">';
			$sc .= '<strong>' .	$code.' ('.$data[$code].')' . '</strong>';
			$sc .= '</td>';
			$sc .= '<td class="middle" class="one-attribute">';
      $sc .= '<input type="number" min="0" ';
      $sc .= 'class="form-control text-center item-grid r-'.$r.' c-'.$c.'" ';
      $sc .= 'name="qty[0]['.$item->code.']" ';
      $sc .= 'id="qty-'.$r.$c.'" ';
      $sc .= 'data-code="'.$item->code.'" ';
      $sc .= 'data-name="'.$item->name.'" ';
      $sc .= 'data-cost="'.$item->cost.'" ';
      $sc .= 'data-price="'.$item->price.'" ';
      $sc .= 'data-vatcode="'.$item->purchase_vat_code.'" ';
      $sc .= 'data-vatrate="'.$item->purchase_vat_rate.'" ';
      $sc .= 'data-row="'.$r.'" data-col="'.$c.'" ';
      $sc .= 'data-limit="-1"/>';
			$sc .= '</td>';

			$i++;

			$sc 	.= $i%2 == 0 ? '</tr>' : '';
      $r++;
    }

		return $sc;
	}



  public function orderGridTwoAttribute($style)
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
					$sc .= '<td class="order-grid">';
          $sc .= '<input type="number" min="0" ';
          $sc .= 'class="form-control text-center item-grid r-'.$r.' c-'.$c.'" ';
          $sc .= 'name="qty['.$item->color_code.']['.$item->code.']" ';
          $sc .= 'id="qty-'.$r.$c.'" ';
          $sc .= 'data-code="'.$item->code.'" ';
          $sc .= 'data-name="'.$item->name.'" ';
          $sc .= 'data-cost="'.$item->cost.'" ';
          $sc .= 'data-price="'.$item->price.'" ';
          $sc .= 'data-vatcode="'.$item->purchase_vat_code.'" ';
          $sc .= 'data-vatrate="'.$item->purchase_vat_rate.'" ';
          $sc .= 'data-limit="-1" ';
          $sc .= 'data-row="'.$r.'" data-col="'.$c.'" ';
          $sc .= 'placeholder="'.$color_code.'-'.$size_code.'" />';
					$sc .= '</td>';
				}
				else
				{
          $sc .= '<td class="order-grid">';
          $sc .= '<input type="text" min="0" ';
          $sc .= 'class="form-control text-center item-grid r-'.$r.' c-'.$c.'" ';
          $sc .= 'id="qty-'.$r.$c.'" ';
          $sc .= 'data-row="'.$r.'" data-col="'.$c.'" ';
          $sc .= 'placeholder="'.$color_code.'-'.$size_code.'" value="N/A" readonly />';
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
		if( $sizes !== FALSE )
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
    $sc = 600; //--- ชั้นต่ำ
    $tdWidth = 80;  //----- แต่ละช่อง
    $padding = 80; //----- สำหรับช่องแสดงไซส์
    $color = $this->ci->products_model->count_color($style_code);
    if($color > 0)
    {
      $sc = $color * $tdWidth + $padding;
    }

    return $sc;
  }
}

 ?>
