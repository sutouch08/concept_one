<li class="light-blue" id="wc-result">
  <a data-toggle="dropdown" class="dropdown-toggle" href="#" aria-expanded="false">
    WC
    <span class="badge badge-primary">0</span>
  </a>
</li>



<script id="wc-template" type="text/x-handlebarsTemplate">
<a data-toggle="dropdown" class="dropdown-toggle" href="#" aria-expanded="false">
  WC
  <span class="badge badge-primary">{{rows}}</span>
</a>

<ul class="dropdown-menu-left dropdown-navbar dropdown-menu dropdown-caret dropdown-close">
  <li class="dropdown-header">
    <center>รออนุมัติ  {{rows}}  รายการ</center>
  </li>

  <li class="dropdown-content ace-scroll" style="position: relative;">
    <div class="scroll-track" style="display: none;">
    <div class="scroll-bar"></div>
    </div>
    <div class="scroll-content" style="">
    <ul class="dropdown-menu dropdown-navbar">
    {{#if this.data}}
      {{#each this.data}}
        <li>
          <a href="javascript:void(0)" onclick="viewConsignSODetail('{{code}}')">
            <div class="clearfix">
            <b class="blue">{{code}}</b> &nbsp; {{customer}}
            </div>
          </a>
        </li>
      {{/each}}
    {{else}}
      <li>
        <a href="javascript:void(0)" class="clearfix">
          <center><b class="blue">No Data</b></center>
        </a>
      </li>
    {{/if}}
      </ul>
    </div>
  </li>

  <li class="dropdown-footer">
    <a href="javascript:void(0)" onclick="viewAllConsignSO()">
      ดูรายการทั้งหมด
      <i class="ace-icon fa fa-arrow-right"></i>
    </a>
  </li>
</ul>
</script>

<script>

$(document).ready(function(){
  get_wc();
});

var wc = setInterval(function(){
  get_wc();
}, refresh_rate);

function get_wc(){
  $.ajax({
    url:BASE_URL + 'orders/consign_so/get_un_approve_list',
    type:'GET',
    cache:false,
    data:{
      'limit' : limit_rows
    },
    success:function(rs){
      if(isJson(rs)){
        let source = $('#wc-template').html();
        let data = $.parseJSON(rs);
        let output = $('#wc-result');
        render(source, data, output);
      }
    }
  })
}



function viewConsignSODetail(code){
  //--- properties for print
  var center    = ($(document).width() - 900)/2;
  var prop 			= "width=900, height=900. left="+center+", scrollbars=yes";
	var target = BASE_URL + 'orders/consign_so/edit_order/' + code + '/approve?nomenu';
	window.open(target, "_blank", prop);
}

function viewAllConsignSO(){
  window.open(BASE_URL + 'orders/consign_so', "_blank");
}
</script>
