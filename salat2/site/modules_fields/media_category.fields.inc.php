<?
$fieldsArr = array(
	'id' => array(
		'title' => 'ID',
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

	'title' => array(
		'title' => 'Title',
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
			'type' => 'text', // choose: none|text|textarea|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
			'extra_after' => '', // extra code/text after element tag
			'extra_html' => '', // extra attribues for the element tag
			'searchable' => false, // gneric search			
			
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
	'mobile' => array(
		'title' => 'Has Mobile Version',
		'width' => '',
		'type' => 'int', // choose: int|float|string
		'table' => '',
		'order_by' => true,
		'comments' => array(
			'show' => '',
			'add' => '',
			'new' => '',
		),
		'input' => array(
			'type' => 'radio', // choose: none|text|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
			'extra_after' => '', // extra code/text after element tag
			'extra_html' => '', // extra attribues for the element tag
		),
		'value' => array(
			'show' => array( // `key` is the action name
				'source' => 'array', // choose: none|array|function|db|var, none for no visibility
				'details' => '$_yesNo_arr[{VALUE}]', // value is "{VALUE}" (without quotes)
			),
			'new' => array( // `key` is the action name
				'source' => 'array', // choose: none|array|function|db|var|path, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => '$_yesNo_arr', // value is "{VALUE}" (without quotes)
			),
			'after' => array( // `key` is the action name
				'source' => 'post', // choose: none|post|array|function|db|var, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => "", // value is "{VALUE}" (without quotes)
			),
		),
	),


);
	
?>