<?php

function get_available_stock($item)
{
  $CI =& get_instance();
  $CI->load->model('stock/stock_model');
  $CI->load->model('orders/orders_model');
  $sell_stock = $CI->stock_model->get_sell_stock($item);
  $reserv_stock = $CI->orders_model->get_reserv_stock($item);
  $availableStock = $sell_stock - $reserv_stock;
  return $availableStock < 0 ? 0 : $availableStock;
}

?>
