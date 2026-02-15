Dropzone.autoDiscover = false;

var myDropzone = new Dropzone("#imageForm", {
	url: HOME + 'upload_images/' + $('#order_code').val(),
	paramName: "file",
	maxFilesize: 2, // MB
	uploadMultiple: true,
	maxFiles: 5,
	acceptedFiles: "image/*",
	parallelUploads: 5,
	autoProcessQueue: false,
	addRemoveLinks: true
});

myDropzone.on('complete', function() {
	clearUploadBox();
	reloadImageTable();
});

function doUpload() {
	myDropzone.processQueue();
}

function clearUploadBox() {
	$("#uploadBox").modal('hide');
	myDropzone.removeAllFiles();
}

function showUploadBox() {
	$("#uploadBox").modal('show');
}


function removeImage(id_img) {
  swal({
    title: "คุณแน่ใจ ?",
    text: "ต้องการลบรูปภาพ หรือไม่ ?",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#FA5858",
    confirmButtonText: 'Yes',
    cancelButtonText: 'No',
    closeOnConfirm: true
  }, function() {
    load_in();

    setTimeout(() => {
      $.ajax({
        url: HOME + 'remove_image',
        type:"POST",
        cache:"false",
        data:{
          "id_image" : id_img
        },
        success: function(rs) {
          load_out();

          if( rs.trim() == 'success' )
          {
            swal({
              title:'Deleted',
              type:'success',
              timer:1000
            });

            $("#img-preview-"+id_img).remove();
          }
          else
          {
            showError(rs);
          }
        },
        error:function(rs) {
          showError(rs);
        }
      });
    }, 100);
  });
}


//function viewImage(imageUrl) {
	var image = '<img src="'+imageUrl+'" width="100%" />';
	$("#imageBody").html(image);
	$("#imageModal").modal('show');
}


function reloadImageTable() {
  let code = $('#order_code').val().trim();

  $.ajax({
    url:HOME + 'get_order_images',
    type:'POST',
    cache:false,
    data:{
      'code' : code
    },
    success:function(rs) {
      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          let source = $('#images-template').html();
          let data = ds.data;
          let output = $('#images-table');

          render(source, data, output);
        }
        else {
          showError(ds.message);
        }
      }
      else {
        showError(rs);
      }
    },
    error:function(rs) {
      showError(rs);
    }
  })
}
