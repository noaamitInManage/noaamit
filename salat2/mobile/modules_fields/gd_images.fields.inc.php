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

    'api_key' => array(
        'title' => 'מפתח',
        'width' => '',
        'type' => 'string', // choose: int|float|string
        'table' => '',
        'order_by' => true,
        'comments' => array(
            'show' => '',
            'add' => 'key for sending with generalDeclaration',
            'new' => 'key for sending with generalDeclaration',
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
    'type' => array(
        'title' => 'סוג',
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
                'details' => '$config_gd_images_typesArr[{VALUE}]', // value is "{VALUE}" (without quotes)
            ),
            'new' => array( // `key` is the action name
                'source' => 'array', // choose: none|array|function|db|var|path, none for no visibility
                'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
                'details' => '$config_gd_images_typesArr', // value is "{VALUE}" (without quotes)
            ),
            'after' => array( // `key` is the action name
                'source' => 'post', // choose: none|post|array|function|db|var, none for no visibility
                'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
                'details' => "", // value is "{VALUE}" (without quotes)
            ),
        ),
    ),

    'media_id' => array(
        'title' => 'תמונות',
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
            'extra_html' => 'class="regular_section"', // extra attribues for the element tag
        ),
        'value' => array(
            'show' => array( // `key` is the action name
                'source' => 'none', // choose: none|array|function|db|var, none for no visibility
                'details' => '', // value is "{VALUE}" (without quotes)
            ),
            'new' => array( // `key` is the action name
                'source' => 'function', // choose: none|array|function|db|var|path, none for no visibility
                'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
                'details' => 'mediaSelector("media_id")', // value is "{VALUE}" (without quotes)
            ),
            'after' => array( // `key` is the action name
                'source' => 'post', // choose: none|post|array|function|db|var, none for no visibility
                'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
                'details' => "", // value is "{VALUE}" (without quotes)
            ),
        ),
    ),
    '_images_resolutions' => array(
        'title' => 'תמונות',
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
            'extra_html' => 'class="full_screen_section"', // extra attribues for the element tag
        ),
        'value' => array(
            'show' => array( // `key` is the action name
                'source' => 'none', // choose: none|array|function|db|var, none for no visibility
                'details' => '', // value is "{VALUE}" (without quotes)
            ),
            'new' => array( // `key` is the action name
                'source' => 'function', // choose: none|array|function|db|var|path, none for no visibility
                'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
                'details' => 'createUploadImagesResolutions()', // value is "{VALUE}" (without quotes)
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