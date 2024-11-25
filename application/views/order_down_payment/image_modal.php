<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">×</button>
			</div>
			<form class="no-margin" id="imageForm">
				<div class="modal-body">
					<div style="width:75%;margin-left:12%;">
						<label id="btn-select-file" class="ace-file-input ace-file-multiple">
							<input type="file" name="image" id="image" accept="image/*" style="display:none;" />
							<span class="ace-file-container" data-title="Click to choose new Image">
								<span class="ace-file-name" data-title="No File ...">
									<i class=" ace-icon ace-icon fa fa-picture-o"></i>
								</span>
							</span>
						</label>
						<div id="block-image" style="opacity:0;">
							<div id="previewImg" class="width-100 center"></div>
							<span onClick="removeFile()" style="position:absolute; left:385px; top:1px; cursor:pointer; color:red;">
								<i class="fa fa-times fa-2x"></i>
							</span>
						</div>
					</div>
				</div>
				<div class="modal-footer center">
					<button type="button" class="btn btn-sm btn-success" onclick="getImage()"><i class="ace-icon fa fa-check"></i> Submit</button>
					<button type="button" class="btn btn-sm" data-dismiss="modal"><i class="ace-icon fa fa-times"></i> Cancel</button>
				</div>
			</form>
		</div>
	</div>
</div>
