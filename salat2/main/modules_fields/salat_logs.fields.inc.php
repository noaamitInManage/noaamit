<?
$fieldsArr = array(
	'id' => array(
		'title' => 'קוד',
		'width' => '50',
		'type' => 'int', // choose: int|float|string
		'table' => '',
		'comments' => array(
			'show' => '',
			'add' => '',
			'new' => '',
		),
		'input' => array(
			'type' => 'text', // choose: none|text|textarea|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
			'extra_after' => '', // extra code/text after element tag
			'extra_html' => '', // extra attribues for the element tag
		),
		'value' => array(
			'show' => array( // `key` is the action name
				'source' => 'db', // choose: none|array|function|db|var, none for no visibility
				'details' => "", // value is "{VALUE}" (without quotes)
			),
			'new' => array( // `key` is the action name
				'source' => 'none', // choose: none|array|function|db|var|path, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => '', // value is "{VALUE}" (without quotes)
			),
			'after' => array( // `key` is the action name
				'source' => 'none', // choose: none|post|array|function|db|var, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => "", // value is "{VALUE}" (without quotes)
			),
		),
	),

	'action' => array(
		'title' => 'פעולה',
		'width' => '',
		'type' => 'string', // choose: int|float|string
		'table' => '',
		'order_by' => false,
		'comments' => array(
			'show' => '',
			'add' => '',
			'new' => '',
		),
		'input' => array(
			'type' => 'label', // choose: none|text|textarea|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
			'extra_after' => '', // extra code/text after element tag
			'extra_html' => '', // extra attribues for the element tag
		),
		'value' => array(
			'show' => array( // `key` is the action name
				'source' => 'function', // choose: none|array|function|db|var, none for no visibility
				'details' => "fields_draw_title_link({id}, '{VALUE}')", // value is "{VALUE}" (without quotes)
			),
			'new' => array( // `key` is the action name
				'source' => 'db', // choose: none|array|function|db|var|path, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => '', // value is "{VALUE}" (without quotes)
			),
			'after' => array( // `key` is the action name
				'source' => 'post', // choose: none|post|array|function|db|var, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => "", // value is "{VALUE}" (without quotes)
			),
		),
	),
	'process_id' => array(
		'title' => 'מודול',
		'width' => '',
		'type' => 'string', // choose: int|float|string
		'table' => '',
		'order_by' => false,
		'comments' => array(
			'show' => '',
			'add' => '',
			'new' => '',
		),
		'input' => array(
			'type' => 'label', // choose: none|text|textarea|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
			'extra_after' => '', // extra code/text after element tag
			'extra_html' => '', // extra attribues for the element tag
			'searchable' => true, // gneric search
		),
		'value' => array(
			'show' => array( // `key` is the action name
				'source' => 'array', // choose: none|array|function|db|var, none for no visibility
				'details' => '$sys_modules[{VALUE}]', // value is "{VALUE}" (without quotes)
			),
			'new' => array( // `key` is the action name
				'source' => 'array', // choose: none|array|function|db|var|path, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => '$sys_modules', // value is "{VALUE}" (without quotes)
			),
			'after' => array( // `key` is the action name
				'source' => 'post', // choose: none|post|array|function|db|var, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => "", // value is "{VALUE}" (without quotes)
			),
		),
	),	
	'data' => array(
		'title' => 'נתונים',
		'width' => '',
		'type' => 'string', // choose: int|float|string
		'table' => '',
		'order_by' => false,
		'comments' => array(
			'show' => '',
			'add' => '',
			'new' => '',
		),
		'input' => array(
			'type' => 'label', // choose: none|text|textarea|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
			'extra_after' => '', // extra code/text after element tag
			'extra_html' => '', // extra attribues for the element tag
		),
		'value' => array(
			'show' => array( // `key` is the action name
				'source' => 'none', // choose: none|array|function|db|var, none for no visibility
				'details' => '', // value is "{VALUE}" (without quotes)
			),
			'new' => array( // `key` is the action name
				'source' => 'function', // choose: none|array|function|db|var|path, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => 'draw_data({VALUE})', // value is "{VALUE}" (without quotes)
			),
			'after' => array( // `key` is the action name
				'source' => 'post', // choose: none|post|array|function|db|var, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => "", // value is "{VALUE}" (without quotes)
			),
		),
	),	
	'item_id' => array(
		'title' => 'פריט',
		'width' => '',
		'type' => 'string', // choose: int|float|string
		'table' => '',
		'order_by' => false,
		'comments' => array(
			'show' => '',
			'add' => '',
			'new' => '',
		),
		'input' => array(
			'type' => 'label', // choose: none|text|textarea|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
			'extra_after' => '', // extra code/text after element tag
			'extra_html' => '', // extra attribues for the element tag
			'searchable' => true, // gneric search
		),
		'value' => array(
			'show' => array( // `key` is the action name
				'source' => 'function', // choose: none|array|function|db|var, none for no visibility
				'details' => "draw_item('{VALUE}')", // value is "{VALUE}" (without quotes)
			),
			'new' => array( // `key` is the action name
				'source' => 'function', // choose: none|array|function|db|var|path, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => "draw_item('{VALUE}')", // value is "{VALUE}" (without quotes)
			),
			'after' => array( // `key` is the action name
				'source' => 'post', // choose: none|post|array|function|db|var, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => "", // value is "{VALUE}" (without quotes)
			),
		),
	),
	'user_id' => array(
		'title' => 'משתמש',
		'width' => '',
		'type' => 'string', // choose: int|float|string
		'table' => '',
		'order_by' => false,
		'comments' => array(
			'show' => '',
			'add' => '',
			'new' => '',
		),
		'input' => array(
			'type' => 'label', // choose: none|text|textarea|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
			'extra_after' => '', // extra code/text after element tag
			'extra_html' => '', // extra attribues for the element tag
			'searchable' => true, // gneric search
		),
		'value' => array(
			'show' => array( // `key` is the action name
				'source' => 'array', // choose: none|array|function|db|var, none for no visibility
				'details' => '$sys_users[{VALUE}]', // value is "{VALUE}" (without quotes)
			),
			'new' => array( // `key` is the action name
				'source' => 'array', // choose: none|array|function|db|var|path, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => '$sys_users', // value is "{VALUE}" (without quotes)
			),
			'after' => array( // `key` is the action name
				'source' => 'post', // choose: none|post|array|function|db|var, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => "", // value is "{VALUE}" (without quotes)
			),
		),
	),	
	'last_update' => array(
		'title' => 'תאריך ושעה',
		'width' => '',
		'type' => 'string', // choose: int|float|string
		'table' => '',
		'order_by' => true,
		'comments' => array(
			'show' => '',
			'add' => '',
			'new' => '',
		),
		'input' => array(
			'type' => 'label', // choose: none|text|textarea|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
			'extra_after' => '', // extra code/text after element tag
			'extra_html' => '', // extra attribues for the element tag
		),
		'value' => array(
			'show' => array( // `key` is the action name
				'source' => 'function', // choose: none|array|function|db|var, none for no visibility
				'details' => "date('[H:i:s] d-m-Y', '{VALUE}')", // value is "{VALUE}" (without quotes)
			),
			'new' => array( // `key` is the action name
				'source' => 'function', // choose: none|array|function|db|var|path, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => "date('[H:i:s] d-m-Y', '{VALUE}')", // value is "{VALUE}" (without quotes)
			),
			'after' => array( // `key` is the action name
				'source' => 'db', // choose: none|post|array|function|db|var, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => "date('[H:i:s] d-m-Y', '{VALUE}')", // value is "{VALUE}" (without quotes)
			),
		),
	),
	
);
	
?>