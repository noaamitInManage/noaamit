<?php

$fieldsArr = array(
	'id' => array(
		'title' => 'קוד',
		'width' => '10',
		'type' => 'int', // choose: int|float|string
		'table' => '',
		'order_by' => true,
		'comments' => array(
			'show' => '',
			'add' => '',
			'new' => '',
		),
		'input' => array(
			'type' => 'text', // choose: none|text|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
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
		'title' => 'כותרת',
		'width' => '',
		'type' => 'string', // choose: int|float|string
		'table' => '',
		'comments' => array(
			'show' => '',
			'add' => '',
			'edit' => '',
		),
		'input' => array(
			'type' => 'text', // choose: none|text|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
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
	'fieldtype' => array(
		'title' => 'סוג',
		'width' => '50',
		'type' => 'int', // choose: int|float|string
		'table' => '',
		'comments' => array(
			'show' => '',
			'add' => '',
			'edit' => '',
		),
		'input' => array(
			'type' => 'select', // choose: none|text|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
			'extra_after' => '', // extra code/text after element tag
			'extra_html' => 'style="width: 280px;" id="fieldType"', // extra attribues for the element tag
		),
		'value' => array(
			'show' => array( // `key` is the action name
				'source' => 'none', // choose: none|array|function|db|var, none for no visibility
				'details' => "fields_draw_settings_type_link({id}, '{VALUE}')", // value is "{VALUE}" (without quotes)
			),
			'new' => array( // `key` is the action name
				'source' => 'array', // choose: none|array|function|db|var|path, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => '$_keyType_arr', // value is "{VALUE}" (without quotes)
			),
			'after' => array( // `key` is the action name
				'source' => 'function', // choose: none|post|array|function|db|var, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => "saveFieldType()", // value is "{VALUE}" (without quotes)
			),
		),
	),
	'combokey' => array(
		'title' => 'combokey',
		'width' => '50',
		'type' => 'string', // choose: int|float|string
		'table' => '',
		'comments' => array(
			'show' => '',
			'add' => '',
			'edit' => '',
		),
		'input' => array(
			'type' => 'none', // choose: none|text|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
			'extra_after' => '', // extra code/text after element tag
			'extra_html' => 'style="display: none;"', // extra attribues for the element tag
		),
		'value' => array(
			'show' => array( // `key` is the action name
				'source' => 'none', // choose: none|array|function|db|var, none for no visibility
				'details' => "fields_draw_settings_type_link({id}, '{VALUE}')", // value is "{VALUE}" (without quotes)
			),
			'new' => array( // `key` is the action name
				'source' => 'none', // choose: none|array|function|db|var|path, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => '$_keyType_arr', // value is "{VALUE}" (without quotes)
			),
			'after' => array( // `key` is the action name
				'source' => 'post', // choose: none|post|array|function|db|var, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => "", // value is "{VALUE}" (without quotes)
			),
		),
	)
);

if($_SERVER['REMOTE_ADDR'] == "62.219.212.139"){
	$fieldsArr['keyname'] = array(
		'title' => 'מפתח (אנגלית)',
		'width' => '',
		'type' => 'string', // choose: int|float|string
		'table' => '',
		'comments' => array(
			'show' => '',
			'add' => '',
			'new' => '',
		),
		'input' => array(
			'type' => 'text', // choose: none|text|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
			'extra_after' => '', // extra code/text after element tag
			'extra_html' => 'style="direction: ltr; text-align: left;"', // extra attribues for the element tag
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
	);
}

$fieldsEditArr = array(
	'title' => array(
		'title' => 'כותרת',
		'width' => '100',
		'type' => 'string', // choose: int|float|string
		'table' => '',
		'comments' => array(
			'show' => '',
			'add' => '',
			'edit' => '',
		),
		'input' => array(
			'type' => 'label', // choose: none|text|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
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
				'source' => 'none', // choose: none|post|array|function|db|var, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => "", // value is "{VALUE}" (without quotes)
			),
		),
	),
   'content' => array(
		'title' => 'תוכן',
		'width' => '50',
		'type' => 'string', // choose: int|float|string
		'table' => '',
		'comments' => array(
			'show' => '',
			'add' => '',
			'edit' => '',
		),
		'input' => array(
			'type' => '', // choose: none|text|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
			'extra_after' => '', // extra code/text after element tag
			'extra_html' => '', // extra attribues for the element tag
		),
		'value' => array(
			'show' => array( // `key` is the action name
				'source' => 'none', // choose: none|array|function|db|var, none for no visibility
				'details' => "fields_draw_settings_type_link({id}, '{VALUE}')", // value is "{VALUE}" (without quotes)
			),
			'new' => array( // `key` is the action name
				'source' => 'function', // choose: none|array|function|db|var|path, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => "drawContentCombo('{VALUE}')", // value is "{VALUE}" (without quotes)
			),
			'after' => array( // `key` is the action name
				'source' => 'post', // choose: none|post|array|function|db|var, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => "", // value is "{VALUE}" (without quotes)
			),
		),
	)
);
?>