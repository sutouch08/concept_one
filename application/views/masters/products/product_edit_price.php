<form class="form-horizontal" id="sizeForm" method="post" action="<?php echo $this->home."/update_all_cost_price_by_size"; ?>">
<div class="row">
	<div class="col-sm-6 col-xs-12 padding-5">
		<table class="table table-striped border-1">
			<thead>
				<tr>
					<th class="width-25 text-center">ไซส์</th>
					<th class="width-25 text-center">ราคาทุน</th>
					<th class="width-25 text-center">ราคาขาย</th>
					<th class="width-25 text-center"></th>
				</tr>
			</thead>
			<tbody>
	<?php if(!empty($sizes)) : ?>
		<?php $no = 1; ?>
		<?php foreach($sizes as $rs) : ?>
			<tr>
				<td class="middle text-right">
					<?php echo $rs->code; ?>
					<input type="hidden" name="size[<?php echo $no; ?>]" id="size_<?php echo $no; ?>" value="<?php echo $rs->code; ?>">
				</td>
				<td class="middle">
					<input type="number" step="any"
					class="form-control input-sm size-cost"
					id="cost_<?php echo $no; ?>"
					name="cost[<?php echo $no; ?>]"
					data-size="<?php echo $rs->code; ?>"
					data-no="<?php echo $no; ?>"
					value="<?php echo round($rs->cost,2); ?>">
				</td>
				<td class="middle">
					<input type="number" step="any"
					class="form-control input-sm size-price"
					id="price_<?php echo $no; ?>"
					name="price[<?php echo $no; ?>]"
					data-size="<?php echo $rs->code; ?>"
					data-no="<?php echo $no; ?>"
					value="<?php echo round($rs->price,2); ?>">
				</td>
				<td class="middle text-center">
					<?php if($this->pm->can_edit) : ?>
						<button type="button" class="btn btn-xs btn-success" onclick="update_size_cost_price(<?php echo $no; ?>)">
							<i class="fa fa-save"></i> อัพเดต
						</button>
					<?php endif; ?>
				</td>
			</tr>
			<?php $no++; ?>
		<?php endforeach; ?>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td class="middle text-center">
					<?php if($this->pm->can_edit) : ?>
						<button type="button" class="btn btn-xs btn-primary" onclick="update_all_size_cost_price()">
							<i class="fa fa-save"></i> อัพเดตทั้งหมด
						</button>
					<?php endif; ?>
				</td>
	<?php else : ?>
		<tr><td colspan="4" class="text-center">--- ไม่พบรายการ ---</td></tr>
	<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<input type="hidden" id="style_code" name="style_code" value="<?php echo $style->code; ?>" />
</form>
