function goBack(){
  var id = $('#id').val();
  window.location.href = BASE_URL + 'masters/products/edit/' + id + '/itemTab';
}


$('#items-wizard')
.ace_wizard({
  //step: 2 //optional argument. wizard will jump to step "2" at first
  //buttons: '.my-action-buttons' //which is possibly located somewhere else and is not a sibling of wizard
})
.on('actionclicked.fu.wizard' , function(e, info) {
   //info.step
   //info.direction

   //use e.preventDefault to cancel
})
.on('changed.fu.wizard', function() {
   //after step has changed
})
.on('finished.fu.wizard', function(e) {
   //do something when finish button is clicked
   genItems();
}).on('stepclick.fu.wizard', function(e) {
   //e.preventDefault();//this will prevent clicking and selecting steps
});




$('.colorBox').change(function(){
  let code = $(this).val();
  let name = $(this).data('name');
  let id = $(this).data('id');

  if($(this).is(':checked')) {
    let  text = $('#co-'+id).text();
    $('#colorBox').append('<input type="hidden" id="col-'+id+'" class="color" data-code="'+code+'" data-name="'+name+'" data-id="'+id+'" value="'+code+'" />');
    $('.imageBox').append('<option data-colorid="'+id+'" data-colorcode="'+code+'" value="'+id+'">'+text+'</option>');
    //imageBoxInit();
  }
  else {
    $('#col-'+id).remove();
    $(".imageBox option[value='"+id+"']").remove();
    //imageBoxInit();
  }

  preGen();
});



$('.sizeBox').change(function(){
  let code = $(this).val();
  let name = $(this).data('name');
  let id = $(this).data('id');

  if($(this).is(':checked')) {
    let text = $('#si-'+id).text();
    $('#sizeBox').append('<input type="hidden" id="size-'+ id +'" class="size" value="'+code+'" />');
  }
  else {
    $('#size-'+ id).remove();
  }

  preGen();
});


function preGen(){
  let code = $('#code').val();
  let name = $('#name').val();
  let countColor = $('.color').length;
  let countSize = $('.size').length;

  $('#preGen').html('');

  if(countColor > 0 && countSize > 0){
    genColorAndSize(code, name);
  }

  if(countColor > 0 && countSize == 0){
    genColorOnly(code, name);
  }

  if(countColor == 0 && countSize > 0){
    genSizeOnly(code, name);
  }
}


function genColorAndSize(code, name){
  let no = 1;
  $('.color').each(function() {
    let color = $(this).val();
    let colorName = $(this).data('name');
    let colorId = $(this).data('id');

    $('.size').each(function() {
      let size = $(this).val();
      let itemCode = code + '-' + color + '-' + size;
      let itemName = name + ' สี'+colorName + ' ไซส์ '+size;

      addItemRow(itemCode, color, size, itemName, colorId, no);
      no++;
    });
  });
}


function genColorOnly(style, name) {
  let no = 1;
  $('.color').each(function() {
    let color = $(this).val();
    let colorId = $(this).data('id');
    let colorName = $(this).data('name');
    let itemCode = style + '-' + color;
    let itemName = name + ' สี'+colorName;
    addItemRow(itemCode, color, '', itemName, colorId, no);
    no++;
  });
}



function genSizeOnly(style, name){
  let no = 1;
  $('.size').each(function(){
    let size = $(this).val();
    let itemCode = style + '-' + size;
    let itemName = name + ' ไซส์ '+size;
    addItemRow(itemCode, '', size, itemName, '', no);
    no++;
  })
}

function addItemRow(itemCode, color, size, name, colorId, no)
{
  let cost = $('#cost').val();
  let price = $('#price').val();
  let style = $('#code').val();

  let row = {
    'no' : no,
    'code' : itemCode,
    'name' : name,
    'style' : style,
    'price' : price,
    'cost' : cost,
    'color' : color,
    'size' : size,
    'colorId' : colorId,
  }

  let source = $('#row-template').html();
  let output = $('#preGen');

  render_append(source, row, output);
  // $('#preGen').append(row);
}


$('.imageBox').change(function() {
  let id = $(this).data('id');
  let colorId = $('option:selected', this).data('colorid');
  let url = $('#img-'+ id).attr('src');
  let img = '<img src="'+url+'" id="se-'+id+'" class="se-'+id+'" style="width:50px;" />';

  if(colorId !="" && colorId != undefined) {
    $('.td-'+colorId).each(function() {
      let no = $(this).data('no');
      $(this).html(img);
      $('#item-'+no).data('img', id);
    })
  }
  else {
    $('.se-'+id).remove();
  }
});


function updateItemCost(no) {
  let cost = parseDefault(parseFloat($('#cost-'+no).val()), 0);

  $('#item-'+no).data('cost', cost);
}

function updateItemPrice(no) {
  let price = parseDefault(parseFloat($('#price-'+no).val()), 0);

  $('#item-'+no).data('price', price);
}


function genItems() {
  let code = $('#code').val();
  let id = $('#id').val();
  let countColor = $('.color').length;
  let countSize = $('.size').length;

  let h = {
    'id' : id,
    'code' : code,
    'name' : $('#name').val(),
    'items' : []
  }

  if(code.length == 0){
    swal('ไม่พบรุ่นสินค้า');
    return false;
  }

  if(countColor == 0 && countSize == 0) {
    swal({
      title:'Error!',
      text:'ต้องกำหนดสีหรือไซส์อย่างน้อย 1 รายการ',
      type:'error'
    });

    return false;
  }

 $('.item-gen').each(function() {
   let el = $(this);

   let item = {
     'code' : el.data('code'),
     'name' : el.data('name'),
     'color_code' : el.data('color'),
     'size_code' : el.data('size'),
     'id_image' : el.data('img'),
     'cost' : el.data('cost'),
     'price' : el.data('price'),
     'style_code' : el.data('style')
   }

   h.items.push(item);
 });

 
 if(h.items.length == 0) {
   swal({
     title:'Error!',
     text:'ไม่พบรายการสินค้า',
     type:'error'
   })

   return false;
 }

  load_in();

  $.ajax({
    url:BASE_URL + 'masters/products/gen_items',
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(rs == 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(() => {
          window.location.href = BASE_URL + 'masters/products/edit/'+id+'/itemTab';
        }, 1200);
      }
      else {
        swal({
          title:'Error',
          text:rs,
          type:'error'
        });
      }
    },
    error:function(xhr) {
      load_out();

      swal({
        title:'Errro!',
        text:xhr.responseText,
        type:'error'
      })
    }
  })
}
