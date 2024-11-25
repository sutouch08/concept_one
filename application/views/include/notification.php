<script>
  var refresh_rate = 300000;
  var limit_rows = 4;
</script>
<?php
if($this->notibars == 1)
{
  if($this->WC->can_approve)
  {
    $this->load->view('notifications/wc');
  }

  if($this->WT->can_approve)
  {
    $this->load->view('notifications/wt');
    $this->load->view('notifications/tr_draft');
  }

  if($this->WS->can_approve)
  {
    $this->load->view('notifications/ws');
  }

  if($this->WU->can_approve)
  {
    $this->load->view('notifications/wu');
  }


  if($this->WQ->can_approve)
  {
    $this->load->view('notifications/wq');
  }

  if($this->WV->can_approve)
  {
    $this->load->view('notifications/wv');
  }

  if($this->WL->can_approve)
  {
    $this->load->view('notifications/wl');
  }

  if($this->WL->can_approve)
  {
    $this->load->view('notifications/rr');
  }
}



?>
