<?php

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

	'message' => array(
		'title' => 'הודעה',
		'width' => '',
		'type' => 'string', // choose: int|float|string
		'table' => '',
		'comments' => array(
			'show' => '',
			'add' => 'אורך הודעה מקסימלי: 100 תווים',
			'new' => 'אורך הודעה מקסימלי: 100 תווים',
		),
		'input' => array(
			'type' => 'text', // choose: none|text|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
			'extra_after' => ' <span class="countChar">100</span>', // extra code/text after element tag
			'extra_html' => 'onkeyup="countChar(this)" style="width:500px;"', // extra attribues for the element tag
			'searchable'=>true // genric search
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

	'users' => array(
		'title' => 'משתמשים ספציפיים',
		'width' => '',
		'type' => 'string', // choose: int|float|string
		'table' => '',
		'comments' => array(
			'show' => '',
			'add' => 'יש להכניס user id לדוגמא: 1,5,548',
			'new' => 'יש להכניס user id לדוגמא: 1,5,548',
		),
		'input' => array(
			'type' => 'text', // choose: none|text|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
			'extra_after' => '', // extra code/text after element tag
			'extra_html' => 'placeholder="השאר ריק לשליחה לכלל משתמשי המערכת"', // extra attribues for the element tag
			'searchable'=>true // genric search
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

	'gender' => array(
		'title' => 'מין',
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
			'type' => 'radio', // choose: none|text|textarea|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
			'extra_after' => '', // extra code/text after element tag
			'extra_html' =>  '', // extra attribues for the element tag
			'searchable'=>false // genric search
		),
		'value' => array(
			'show' => array( // `key` is the action name
				'source' => 'array', // choose: none|array|function|db|var, none for no visibility
				'details' => '$gendersArr["{VALUE}"]', // value is "{VALUE}" (without quotes)
			),
			'new' => array( // `key` is the action name
				'source' => 'array', // choose: none|array|function|db|var|path, none for no visibility
				'dummy' => array('value' => '', 'text' => '--בחר מין--'), // e.g. "Please choose category"
				'details' => '$gendersArr', // value is "{VALUE}" (without quotes)
			),
			'after' => array( // `key` is the action name
				'source' => 'post', // choose: none|post|array|function|db|var, none for no visibility
				'dummy' => array('value' => '', 'text' => '--בחר מין--'), // e.g. "Please choose category"
				'details' => "", // value is "{VALUE}" (without quotes)
			),
		),
	),

	'device_type' => array(
		'title' => 'פלטפורמה',
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
			'type' => 'radio', // choose: none|text|textarea|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
			'extra_after' => '', // extra code/text after element tag
			'extra_html' =>  '', // extra attribues for the element tag
			'searchable'=>false // genric search
		),
		'value' => array(
			'show' => array( // `key` is the action name
				'source' => 'array', // choose: none|array|function|db|var, none for no visibility
				'details' => '$platformsArr["{VALUE}"]', // value is "{VALUE}" (without quotes)
			),
			'new' => array( // `key` is the action name
				'source' => 'array', // choose: none|array|function|db|var|path, none for no visibility
				'dummy' => array('value' => '', 'text' => '--בחר פלטפורמה--'), // e.g. "Please choose category"
				'details' => '$platformsArr', // value is "{VALUE}" (without quotes)
			),
			'after' => array( // `key` is the action name
				'source' => 'post', // choose: none|post|array|function|db|var, none for no visibility
				'dummy' => array('value' => '', 'text' => '--בחר פלטפורמה--'), // e.g. "Please choose category"
				'details' => "", // value is "{VALUE}" (without quotes)
			),
		),
	),

	'gender' => array(
		'title' => 'מין',
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
			'type' => 'radio', // choose: none|text|textarea|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
			'extra_after' => '', // extra code/text after element tag
			'extra_html' =>  '', // extra attribues for the element tag
			'searchable'=>false // genric search
		),
		'value' => array(
			'show' => array( // `key` is the action name
				'source' => 'array', // choose: none|array|function|db|var, none for no visibility
				'details' => '$gendersArr["{VALUE}"]', // value is "{VALUE}" (without quotes)
			),
			'new' => array( // `key` is the action name
				'source' => 'array', // choose: none|array|function|db|var|path, none for no visibility
				'dummy' => array('value' => '', 'text' => '--בחר מין--'), // e.g. "Please choose category"
				'details' => '$gendersArr', // value is "{VALUE}" (without quotes)
			),
			'after' => array( // `key` is the action name
				'source' => 'post', // choose: none|post|array|function|db|var, none for no visibility
				'dummy' => array('value' => '', 'text' => '--בחר מין--'), // e.g. "Please choose category"
				'details' => "", // value is "{VALUE}" (without quotes)
			),
		),
	),


	'csv_file' => array(
		'title' => 'קובץ CSV',
		'width' => '',
		'type' => 'string', // choose: int|float|string
		'table' => '',
		'order_by' => true,
		'comments' => array(
			'show' => '',
			'add' => 'בעת העלאת קובץ, המערכת תתעלם מהסינונים שהוגדרו',
			'new' => 'בעת העלאת קובץ, המערכת תתעלם מהסינונים שהוגדרו',
		),
		'input' => array(
			'type' => 'file', // choose: none|text|textarea|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
			'extra_after' => ' <a target="_blank" href="/salat2/_public/example.csv">קובץ לדוגמא</a>', // extra code/text after element tag
			'extra_html' =>  '', // extra attribues for the element tag
			'searchable'=>false // genric search
		),
		'value' => array(
			'show' => array( // `key` is the action name
				'source' => 'none', // choose: none|array|function|db|var, none for no visibility
				'details' => '$gendersArr["{VALUE}"]', // value is "{VALUE}" (without quotes)
			),
			'new' => array( // `key` is the action name
				'source' => 'db', // choose: none|array|function|db|var|path, none for no visibility
				'dummy' => array('value' => '', 'text' => '--בחר מין--'), // e.g. "Please choose category"
				'details' => '', // value is "{VALUE}" (without quotes)
			),
			'after' => array( // `key` is the action name
				'source' => 'post', // choose: none|post|array|function|db|var, none for no visibility
				'dummy' => array('value' => '', 'text' => '--בחר מין--'), // e.g. "Please choose category"
				'details' => "", // value is "{VALUE}" (without quotes)
			),
		),
	),

	'json' => array(
		'title' => 'json',
		'width' => '',
		'type' => 'string', // choose: int|float|string
		'table' => '',
		'comments' => array(
			'show' => '',
			'add' => 'לדוגמא: {"key": "value"}',
			'new' => 'לדוגמא: {"key": "value"}'
		),
		'input' => array(
			'type' => 'text', // choose: none|text|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
			'extra_after' => '', // extra code/text after element tag
			'extra_html' => '', // extra attribues for the element tag
			'searchable'=>true // genric search
		),
		'value' => array(
			'show' => array( // `key` is the action name
				'source' => 'none', // choose: none|array|function|db|var, none for no visibility
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

	'send_from_ts' => array(
		'title' => 'זמן שליחה',
		'width' => '',
		'type' => 'int', // choose: int|float|string
		'table' => '',
		'order_by' => false,
		'comments' => array(
			'show' => '',
			'add' => '',
			'new' => '',
		),
		'input' => array(
			'type' => 'datetimepicker', // choose: none|text|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
			'extra_after' => '', // extra code/text after element tag
			'extra_html' => '', // extra attribues for the element tag
		),
		'value' => array(
			'show' => array( // `key` is the action name
				'source' => 'function', // choose: none|array|function|db|var, none for no visibility
				'details' => "date('d/m/Y H:i:s', '{VALUE}')", // value is "{VALUE}" (without quotes)
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

	'last_update' => array(
		'title' => 'עדכון אחרון',
		'width' => '',
		'type' => 'string', // choose: int|float|string
		'table' => '',
		'comments' => array(
			'show' => '',
			'add' => '',
			'new' => '',
		),
		'input' => array(
			'type' => 'label', // choose: none|text|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
			'extra_after' => '', // extra code/text after element tag
			'extra_html' => '', // extra attribues for the element tag
		),
		'value' => array(
			'show' => array( // `key` is the action name
				'source' => 'function', // choose: none|array|function|db|var, none for no visibility
				'details' => "date('[H:i] d.m.Y',{VALUE})", // value is "{VALUE}" (without quotes)
			),
			'new' => array( // `key` is the action name
				'source' => 'none', // choose: none|array|function|db|var|path, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => 'date("[H:i] d.m.Y",{VALUE})', // value is "{VALUE}" (without quotes)
			),
			'after' => array( // `key` is the action name
				'source' => 'function', // choose: none|post|array|function|db|var, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => "time()", // value is "{VALUE}" (without quotes)
			),
		),
	)
);

?>