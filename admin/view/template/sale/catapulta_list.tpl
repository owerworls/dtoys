<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right"><a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
				<button onclick="$('#form').attr('action', '<?php echo $delete; ?>');
						$('#form').attr('target', '_self');
						$('#form').submit();" type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-manufacturer').submit() : false;"><i class="fa fa-trash-o"></i></button>
			</div>
			<h1><?php echo $heading_title; ?></h1>
			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
				<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<div class="container-fluid">
		<?php if ($module_install) { ?>
			<?php if ($error_warning) { ?>
			<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
				<button type="button" class="close" data-dismiss="alert">&times;</button>
			</div>
			<?php } ?>
			<?php if ($success) { ?>
			<div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
				<button type="button" class="close" data-dismiss="alert">&times;</button>
			</div>
			<?php } ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
				</div>
				<div class="panel-body">
					<form action="" method="post" enctype="multipart/form-data" id="form">
						<div class="table-responsive">
							<table class="table table-bordered table-hover">
							<thead>
							<tr>
								<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
								<td class="right"><?php if ($sort == 'order_id') { ?>
									<a href="<?php echo $sort_order; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_order_id; ?></a>
									<?php } else { ?>
									<a href="<?php echo $sort_order; ?>"><?php echo $column_order_id; ?></a>
									<?php } ?></td>
								<td class="right"><?php if ($sort == 'contact') { ?>
									<a href="<?php echo $sort_contact; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_contact; ?></a>
									<?php } else { ?>
									<a href="<?php echo $sort_contact; ?>"><?php echo $column_contact; ?></a>
									<?php } ?></td>
								<td class="right">Тип связи</td>
								<td class="right"><?php if ($sort == 'product_name') { ?>
									<a href="<?php echo $sort_product_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_product_name; ?></a>
									<?php } else { ?>
									<a href="<?php echo $sort_product_name; ?>"><?php echo $column_product_name; ?></a>
									<?php } ?></td>
								<td class="right">Размер</td>
								<td class="right"><?php if ($sort == 'total') { ?>
									<a href="<?php echo $sort_total; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_total; ?></a>
									<?php } else { ?>
									<a href="<?php echo $sort_total; ?>"><?php echo $column_total; ?></a>
									<?php } ?></td>
								<td class="left"><?php if ($sort == 'date_added') { ?>
									<a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_added; ?></a>
									<?php } else { ?>
									<a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
									<?php } ?></td>
								<td class="right"><?php echo $column_action; ?></td>
							</tr>
							</thead>
							<tbody>
							<!--tr-- class="filter">
								<td></td>
								<td align="right"><input type="text" name="filter_order_id" value="<?php echo $filter_order_id; ?>" size="4" style="text-align: right;" /></td>
								<td align="right"><input type="text" name="filter_contact" value="<?php echo $filter_contact; ?>" /></td>
								<td align="right"><input type="text" name="filter_product_name" value="<?php echo $filter_product_name; ?>" /></td>
								<td align="right"><input type="text" name="filter_total" value="<?php echo $filter_total; ?>" size="4" style="text-align: right;" /></td>
								<td><input type="text" name="filter_date_added" value="<?php echo $filter_date_added; ?>" size="12" class="date" /></td>
								<td align="right"><a onclick="filter();" class="button"><?php echo $button_filter; ?></a></td>
							</tr-->
							<?php if ($orders) { ?>
							<?php foreach ($orders as $order) { ?>
							<tr>
								<td style="text-align: center;"><?php if ($order['selected']) { ?>
									<input type="checkbox" name="selected[]" value="<?php echo $order['order_id']; ?>" checked="checked" />
									<?php } else { ?>
									<input type="checkbox" name="selected[]" value="<?php echo $order['order_id']; ?>" />
									<?php } ?></td>
								<td class="right"><?php echo $order['order_id']; ?></td>
								<td class="right"><?php echo $order['contact']; ?></td>
								<td class="right"><?php echo $order['contact_type']; ?></td>
								<td class="right"><a href="<?php echo $order['product_href']; ?>"><?php echo $order['product_name']; ?></a></td>
								<td class="right"><?php echo $order['size']; ?></td>
								<td class="right"><?php echo $order['total']; ?></td>
								<td class="left"><?php echo $order['date_added']; ?></td>
								<td class="right"><?php foreach ($order['action'] as $action) { ?>
									[ <a href="<?php echo $action['href']; ?>"><?php echo $action['text']; ?></a> ]
									<?php } ?></td>
							</tr>
							<?php } ?>
							<?php } else { ?>
							<tr>
								<td class="center" colspan="7"><?php echo $text_no_results; ?></td>
							</tr>
							<?php } ?>
							</tbody>
						</table>
						</div>
					</form>
					<div class="pagination"><?php echo $pagination; ?></div>
				</div>
			</div>
		<?php } else { ?>
			<div class="warning"><?php echo $text_module_not_exists; ?></div>
		<?php } ?>
	</div>
</div>

<script type="text/javascript"><!--
	function filter() {
		url = 'index.php?route=catalog/catapulta&token=<?php echo $token; ?>';

		var filter_order_id = $('input[name=\'filter_order_id\']').attr('value');

		if (filter_order_id) {
			url += '&filter_order_id='+encodeURIComponent(filter_order_id);
		}

		var filter_contact = $('input[name=\'filter_contact\']').attr('value');

		if (filter_contact) {
			url += '&filter_contact='+encodeURIComponent(filter_contact);
		}

		var filter_product_name = $('input[name=\'filter_product_name\']').attr('value');

		if (filter_product_name) {
			url += '&filter_product_name='+encodeURIComponent(filter_product_name);
		}

		var filter_total = $('input[name=\'filter_total\']').attr('value');

		if (filter_total) {
			url += '&filter_total='+encodeURIComponent(filter_total);
		}

		var filter_date_added = $('input[name=\'filter_date_added\']').attr('value');

		if (filter_date_added) {
			url += '&filter_date_added='+encodeURIComponent(filter_date_added);
		}

		location = url;
	}
	//--></script>
<script type="text/javascript"><!--
	$(document).ready(function () {
		$('.date').datepicker({dateFormat: 'yy-mm-dd'});
	});
	//--></script>
<script type="text/javascript"><!--
	$('#form input').keydown(function (e) {
		if (e.keyCode==13) {
			filter();
		}
	});
	//--></script>
<script type="text/javascript"><!--
	$('input[name=\'filter_product_name\']').autocomplete({
		delay: 500,
		source: function(request, response) {
			$.ajax({
				url: 'index.php?route=catalog/catapulta/autocomplete&token=<?php echo $token; ?>&filter_product_name=' +  encodeURIComponent(request.term),
				dataType: 'json',
				success: function(json) {
					response($.map(json, function(item) {
						return {
							label: item.name,
							value: item.product_id
						}
					}));
				}
			});
		},
		select: function(event, ui) {
			$('input[name=\'filter_product_name\']').val(ui.item.label);

			return false;
		},
		focus: function(event, ui) {
			return false;
		}
	});
	//--></script>
<?php echo $footer; ?>
