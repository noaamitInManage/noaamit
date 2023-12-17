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

	'title' => array(
		'title' => 'כותרת',
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
			'type' => 'text', // choose: none|text|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
			'extra_after' => '', // extra code/text after element tag
			'extra_html' => '', // extra attribues for the element tag
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
	'url' => array(
		'title' => 'כתובת URL',
		'width' => '',
		'type' => 'string', // choose: int|float|string
		'table' => '',
		'order_by' => true,
		'comments' => array(
			'show' => '',
			'add' => 'אנא הזן כתובת מלאה עם תחילת
<p style="direction:ltr;">http://</p>',
			'new' => 'אנא הזן כתובת מלאה עם תחילת
<p style="direction:ltr;">http://</p>',
		),
		'input' => array(
			'type' => 'text', // choose: none|text|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
			'extra_after' => '', // extra code/text after element tag
			'extra_html' => 'style="direction:ltr; text-align:left;"', // extra attribues for the element tag
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

	'media_id' => array(
		'title' => 'תמונה ראשית',
		'width' => '',
		'type' => 'int', // choose: int|float|string
		'table' => '',
		'order_by' => false,
		'comments' => array(
			'show' => '',
			'add' => 'יש להעלות תמונות ברזולוציה המתאימה בלבד <br/><b>(1000x1080)</b>',
			'new' => 'יש להעלות תמונות ברזולוציה המתאימה בלבד <br/><b>(1000x1080)</b>',
		),
		'input' => array(
			'type' => 'label', // choose: none|text|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
			'extra_after' => '', // extra code/text after element tag
			'extra_html' => 'style="position:relative;"', // extra attribues for the element tag
		),
		'value' => array(
			'show' => array( // `key` is the action name
				'source' => 'none', // choose: none|array|function|db|var, none for no visibility
				'details' => "", // value is "{VALUE}" (without quotes)
			),
			'new' => array( // `key` is the action name
				'source' => 'function', // choose: none|array|function|db|var|path, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => "mediaSelector('media_id',array('media_id'=>array('iphone'=>'640x1136','android'=>'1000x1080')))",
			),
			'after' => array( // `key` is the action name
				'source' => 'post', // choose: none|post|array|function|db|var, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => "", // value is "{VALUE}" (without quotes)
			),
		),
	),

	'media_iphone4_640_960' => array(
		'title' => 'iphone4 640x960',
		'width' => '',
		'type' => 'int', // choose: int|float|string
		'table' => '',
		'order_by' => false,
		'comments' => array(
			'show' => 'יש להעלות תמונות ברזולוציה המתאימה בלבד <br/><b>(640x960)</b>',
			'add' => 'יש להעלות תמונות ברזולוציה המתאימה בלבד <br/><b>(640x960)</b>',
			'new' => 'יש להעלות תמונות ברזולוציה המתאימה בלבד <br/><b>(640x960)</b>',
		),
		'input' => array(
			'type' => 'label', // choose: none|text|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
			'extra_after' => '', // extra code/text after element tag
			'extra_html' => 'style="position:relative;"', // extra attribues for the element tag
		),
		'value' => array(
			'show' => array( // `key` is the action name
				'source' => 'none', // choose: none|array|function|db|var, none for no visibility
				'details' => "", // value is "{VALUE}" (without quotes)
			),
			'new' => array( // `key` is the action name
				'source' => 'function', // choose: none|array|function|db|var|path, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => "mediaSelector('media_iphone4_640_960', array(), array(), '', false)", // value is "{VALUE}" (without quotes) 'media_id',array('media_id'=>array('iphone3'=>'200x350','hdpi'=>'450x150'))
			),
			'after' => array( // `key` is the action name
				'source' => 'post', // choose: none|post|array|function|db|var, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => "", // value is "{VALUE}" (without quotes)
			),
		),
	),

	'media_iphone5_640_1136' => array(
		'title' => 'iphone5 640x1136',
		'width' => '',
		'type' => 'int', // choose: int|float|string
		'table' => '',
		'order_by' => false,
		'comments' => array(
			'show' => 'יש להעלות תמונות ברזולוציה המתאימה בלבד <br/><b>(640x1136)</b>',
			'add' => 'יש להעלות תמונות ברזולוציה המתאימה בלבד <br/><b>(640x1136)</b>',
			'new' => 'יש להעלות תמונות ברזולוציה המתאימה בלבד <br/><b>(640x1136)</b>',
		),
		'input' => array(
			'type' => 'label', // choose: none|text|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
			'extra_after' => '', // extra code/text after element tag
			'extra_html' => 'style="position:relative;"', // extra attribues for the element tag
		),
		'value' => array(
			'show' => array( // `key` is the action name
				'source' => 'none', // choose: none|array|function|db|var, none for no visibility
				'details' => "", // value is "{VALUE}" (without quotes)
			),
			'new' => array( // `key` is the action name
				'source' => 'function', // choose: none|array|function|db|var|path, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => "mediaSelector('media_iphone5_640_1136', array(), array(), '', false)", // value is "{VALUE}" (without quotes) 'media_id',array('media_id'=>array('iphone3'=>'200x350','hdpi'=>'450x150'))
			),
			'after' => array( // `key` is the action name
				'source' => 'post', // choose: none|post|array|function|db|var, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => "", // value is "{VALUE}" (without quotes)
			),
		),
	),

	'media_iphone6_750_1334' => array(
		'title' => 'iphone6 750x1334',
		'width' => '',
		'type' => 'int', // choose: int|float|string
		'table' => '',
		'order_by' => false,
		'comments' => array(
			'show' => 'יש להעלות תמונות ברזולוציה המתאימה בלבד <br/><b>(750x1334)</b>',
			'add' => 'יש להעלות תמונות ברזולוציה המתאימה בלבד <br/><b>(750x1334)</b>',
			'new' => 'יש להעלות תמונות ברזולוציה המתאימה בלבד <br/><b>(750x1334)</b>',
		),
		'input' => array(
			'type' => 'label', // choose: none|text|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
			'extra_after' => '', // extra code/text after element tag
			'extra_html' => 'style="position:relative;"', // extra attribues for the element tag
		),
		'value' => array(
			'show' => array( // `key` is the action name
				'source' => 'none', // choose: none|array|function|db|var, none for no visibility
				'details' => "", // value is "{VALUE}" (without quotes)
			),
			'new' => array( // `key` is the action name
				'source' => 'function', // choose: none|array|function|db|var|path, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => "mediaSelector('media_iphone6_750_1334', array(), array(), '', false)", // value is "{VALUE}" (without quotes) 'media_id',array('media_id'=>array('iphone3'=>'200x350','hdpi'=>'450x150'))
			),
			'after' => array( // `key` is the action name
				'source' => 'post', // choose: none|post|array|function|db|var, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => "", // value is "{VALUE}" (without quotes)
			),
		),
	),

	'media_iphone6_plus_1242_2208' => array(
		'title' => 'iphone6 plus 1242x2208',
		'width' => '',
		'type' => 'int', // choose: int|float|string
		'table' => '',
		'order_by' => false,
		'comments' => array(
			'show' => 'יש להעלות תמונות ברזולוציה המתאימה בלבד <br/><b>(1242x2208)</b>',
			'add' => 'יש להעלות תמונות ברזולוציה המתאימה בלבד <br/><b>(1242x2208)</b>',
			'new' => 'יש להעלות תמונות ברזולוציה המתאימה בלבד <br/><b>(1242x2208)</b>',
		),
		'input' => array(
			'type' => 'label', // choose: none|text|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
			'extra_after' => '', // extra code/text after element tag
			'extra_html' => 'style="position:relative;"', // extra attribues for the element tag
		),
		'value' => array(
			'show' => array( // `key` is the action name
				'source' => 'none', // choose: none|array|function|db|var, none for no visibility
				'details' => "", // value is "{VALUE}" (without quotes)
			),
			'new' => array( // `key` is the action name
				'source' => 'function', // choose: none|array|function|db|var|path, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => "mediaSelector('media_iphone6_plus_1242_2208', array(), array(), '', false)", // value is "{VALUE}" (without quotes) 'media_id',array('media_id'=>array('iphone3'=>'200x350','hdpi'=>'450x150'))
			),
			'after' => array( // `key` is the action name
				'source' => 'post', // choose: none|post|array|function|db|var, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => "", // value is "{VALUE}" (without quotes)
			),
		),
	),

	'media_android_1080_1920' => array(
		'title' => 'android 1080x1920',
		'width' => '',
		'type' => 'int', // choose: int|float|string
		'table' => '',
		'order_by' => false,
		'comments' => array(
			'show' => 'יש להעלות תמונות ברזולוציה המתאימה בלבד <br/><b>(1080x1920)</b>',
			'add' => 'יש להעלות תמונות ברזולוציה המתאימה בלבד <br/><b>(1080x1920)</b>',
			'new' => 'יש להעלות תמונות ברזולוציה המתאימה בלבד <br/><b>(1080x1920)</b>',
		),
		'input' => array(
			'type' => 'label', // choose: none|text|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
			'extra_after' => '', // extra code/text after element tag
			'extra_html' => 'style="position:relative;"', // extra attribues for the element tag
		),
		'value' => array(
			'show' => array( // `key` is the action name
				'source' => 'none', // choose: none|array|function|db|var, none for no visibility
				'details' => "", // value is "{VALUE}" (without quotes)
			),
			'new' => array( // `key` is the action name
				'source' => 'function', // choose: none|array|function|db|var|path, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => "mediaSelector('media_android_1080_1920', array(), array(), '', false)", // value is "{VALUE}" (without quotes) 'media_id',array('media_id'=>array('iphone3'=>'200x350','hdpi'=>'450x150'))
			),
			'after' => array( // `key` is the action name
				'source' => 'post', // choose: none|post|array|function|db|var, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => "", // value is "{VALUE}" (without quotes)
			),
		),
	),

	'_preview_' => array(
		'title' => 'תצוגה מקדימה',
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
			'type' => 'label', // choose: none|text|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
			'extra_after' => '', // extra code/text after element tag
			'extra_html' => '', // extra attribues for the element tag
			'searchable' => false, // extra attribues for the element tag
		),
		'value' => array(
			'show' => array( // `key` is the action name
				'source' => 'function', // choose: none|array|function|db|var, none for no visibility
				'details' => 'draw_image_preview({media_id})', // value is "{VALUE}" (without quotes)
			),
			'new' => array( // `key` is the action name
				'source' => 'none', // choose: none|array|function|db|var|path, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => '$mediaActionArr', // value is "{VALUE}" (without quotes)
			),
			'after' => array( // `key` is the action name
				'source' => 'none', // choose: none|post|array|function|db|var, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => "", // value is "{VALUE}" (without quotes)
			),
		),
	),
	'active' => array(
		'title' => 'פעיל',
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
			'type' => 'text', // choose: none|text|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
			'extra_after' => '', // extra code/text after element tag
			'extra_html' => '', // extra attribues for the element tag
		),
		'value' => array(
			'show' => array( // `key` is the action name
				'source' => 'none', // choose: none|array|function|db|var, none for no visibility
				'details' => "fields_draw_title_link({id}, '{VALUE}')", // value is "{VALUE}" (without quotes)
			),
			'new' => array( // `key` is the action name
				'source' => 'none', // choose: none|array|function|db|var|path, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => '', // value is "{VALUE}" (without quotes)
			),
			'after' => array( // `key` is the action name
				'source' => 'function', // choose: none|post|array|function|db|var, none for no visibility
				'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
				'details' => "time()", // value is "{VALUE}" (without quotes)
			),
		),
	),

);

?>