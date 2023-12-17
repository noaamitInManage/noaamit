<?php
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
        'title' => 'Title',
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

    'link' => array(
        'title' => 'Link',
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
            'extra_html' => 'style="width: 563px"', // extra attribues for the element tag
            'searchable'=>false // genric search
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

    'icon_id' => array(
        'title' => 'Icon',
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
        ),
        'value' => array(
            'show' => array( // `key` is the action name
                'source' => 'none', // choose: none|array|function|db|var, none for no visibility
                'details' => '', // value is "{VALUE}" (without quotes)
            ),
            'new' => array( // `key` is the action name
                'source' => 'function', // choose: none|array|function|db|var|path, none for no visibility
                'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
                'details' => 'mediaSelector("icon_id")', // value is "{VALUE}" (without quotes)
            ),
            'after' => array( // `key` is the action name
                'source' => 'post', // choose: none|post|array|function|db|var, none for no visibility
                'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
                'details' => "", // value is "{VALUE}" (without quotes)
            ),
        ),
    ),

    'login_only' => array(
        'title' => 'View for Connected Users',
        'width' => '',
        'type' => 'int', // choose: int|float|string
        'table' => '',
        'order_by' => true,
        'comments' => array(
            'show' => '',
            'add' => 'הצגה לכלל המשתמשים או למשתמשים מחוברים בלבד',
            'new' => 'הצגה לכלל המשתמשים או למשתמשים מחוברים בלבד',
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

//    'is_website' => array(
//        'title' => 'Website Menu',
//        'width' => '',
//        'type' => 'int', // choose: int|float|string
//        'table' => '',
//        'order_by' => true,
//        'comments' => array(
//            'show' => '',
//            'add' => '',
//            'new' => '',
//        ),
//        'input' => array(
//            'type' => 'radio', // choose: none|text|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
//            'extra_after' => '', // extra code/text after element tag
//            'extra_html' => '', // extra attribues for the element tag
//        ),
//        'value' => array(
//            'show' => array( // `key` is the action name
//                'source' => 'array', // choose: none|array|function|db|var, none for no visibility
//                'details' => '$_yesNo_arr[{VALUE}]', // value is "{VALUE}" (without quotes)
//            ),
//            'new' => array( // `key` is the action name
//                'source' => 'array', // choose: none|array|function|db|var|path, none for no visibility
//                'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
//                'details' => '$_yesNo_arr', // value is "{VALUE}" (without quotes)
//            ),
//            'after' => array( // `key` is the action name
//                'source' => 'post', // choose: none|post|array|function|db|var, none for no visibility
//                'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
//                'details' => "", // value is "{VALUE}" (without quotes)
//            ),
//        ),
//    ),
//
//    'show_on_user_drop' => array(
//        'title' => 'Show on User Drop',
//        'width' => '',
//        'type' => 'int', // choose: int|float|string
//        'table' => '',
//        'order_by' => true,
//        'comments' => array(
//            'show' => '',
//            'add' => 'רלוונטי רק לקישורי תפריט אתר',
//            'new' => 'רלוונטי רק לקישורי תפריט אתר',
//        ),
//        'input' => array(
//            'type' => 'radio', // choose: none|text|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
//            'extra_after' => '', // extra code/text after element tag
//            'extra_html' => '', // extra attribues for the element tag
//        ),
//        'value' => array(
//            'show' => array( // `key` is the action name
//                'source' => 'array', // choose: none|array|function|db|var, none for no visibility
//                'details' => '$_yesNo_arr[{VALUE}]', // value is "{VALUE}" (without quotes)
//            ),
//            'new' => array( // `key` is the action name
//                'source' => 'array', // choose: none|array|function|db|var|path, none for no visibility
//                'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
//                'details' => '$_yesNo_arr', // value is "{VALUE}" (without quotes)
//            ),
//            'after' => array( // `key` is the action name
//                'source' => 'post', // choose: none|post|array|function|db|var, none for no visibility
//                'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
//                'details' => "", // value is "{VALUE}" (without quotes)
//            ),
//        ),
//    ),

    'last_update' => array(
        'title' => 'Last Update',
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