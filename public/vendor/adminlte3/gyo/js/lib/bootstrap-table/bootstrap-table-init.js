$(document).ready(function(){

/* ==========================================================================
	Tables
	========================================================================== */

	var $table = $('#table'),
		$remove = $('#remove'),
		selections = [];
	function funtio(data){

	}
	function totalTextFormatter(data) {
		return 'Total';
	}

	function totalNameFormatter(data) {
		return data.length;
	}

	function totalPriceFormatter(data) {
		var total = 0;
		$.each(data, function (i, row) {
			total += +(row.price.substring(1));
		});
		return '$' + total;
	}

	function statusFormatter(data, rowData, index) {
		var classBtn = '',
			classDropup = '',
			pageSize = 10;

		if (data !== 'Culin') classBtn = 'btn-info';
		if (data === 'Pending') classBtn = 'btn-primary';
		if (data === 'Moderation') classBtn = 'btn-warning';
		if (data === 'Published') classBtn = 'btn-success';

		if (index >= pageSize / 2) {
			classDropup = 'dropup';
		}

		return	'<div class="dropdown dropdown-status ' +
				classDropup +
				' ">' +
				'<button class="btn ' +
				classBtn +
				' dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width:200px">' +
				data +
				'</button>' +
				'</div>';
	}

	window.operateEvents = {
		'click .like': function (e, value, row, index) {
			alert('You click like action, row: ' + JSON.stringify(row));
		},
		'click .remove': function (e, value, row, index) {
			$table.bootstrapTable('remove', {
				field: 'id',
				values: [row.id]
			});
		}
	};

	function operateFormatter(value, row, index) {
		return [
			'<a class="like" href="javascript:void(0)" title="Like">',
			'<i class="glyphicon glyphicon-heart"></i>',
			'</a>  ',
			'<a class="remove" href="javascript:void(0)" title="Remove">',
			'<i class="glyphicon glyphicon-remove"></i>',
			'</a>'
		].join('');
	}

	function getIdSelections() {
		return $.map($table.bootstrapTable('getSelections'), function (row) {
			return row.id
		});
	}





	$table.bootstrapTable({
		iconsPrefix: 'font-icon',
		icons: {
			paginationSwitchDown:'font-icon-arrow-square-down',
			paginationSwitchUp: 'font-icon-arrow-square-down up',
			refresh: 'font-icon-refresh',
			toggle: 'font-icon-list-square',
			columns: 'font-icon-list-rotate',
			export: 'font-icon-download',
			detailOpen: 'font-icon-plus',
			detailClose: 'font-icon-minus-1'
		},
		paginationPreText: '<i class="font-icon font-icon-arrow-left"></i>',
		paginationNextText: '<i class="font-icon font-icon-arrow-right"></i>',
		data: data,
		columns: [
			[
				{
					field: 'state',
					checkbox: true,
					rowspan: 2,
					align: 'center',
					valign: 'middle'
				},
				{
					title: 'Item ID',
					field: 'id',
					rowspan: 2,
					align: 'center',
					valign: 'middle',
					sortable: true,
					footerFormatter: totalTextFormatter
				},
				{
					title: 'Detalles de registros',
					colspan: 2,
					align: 'center'
				}
			],
			[
				{
					field: 'name',
					title: 'Cargo',
					sortable: true,
					editable: true,
					formatter: statusFormatter,
					footerFormatter: totalNameFormatter,
					align: 'center'
				},
				{
					field: 'operate',
					title: 'Item Operate',
					align: 'center',
					events: operateEvents,
					formatter: operateFormatter
				}
			]
		]
	});

	$table.on('check.bs.table uncheck.bs.table ' +
		'check-all.bs.table uncheck-all.bs.table', function () {
		$remove.prop('disabled', !$table.bootstrapTable('getSelections').length);
		// save your data, here just save the current page
		selections = getIdSelections();
		// push or splice the selections if you want to save all data selections
	});

	$remove.click(function () {
		var ids = getIdSelections();
		$table.bootstrapTable('remove', {
			field: 'id',
			values: ids
		});
		$remove.prop('disabled', true);
	});

	$('#toolbar').find('select').change(function () {
		$table.bootstrapTable('refreshOptions', {
			exportDataType: $(this).val()
		});
	});

/* ========================================================================== */
});
