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

    'json_polygon' => array(
        'title' => 'קורדנטות ',
        'width' => '',
        'type' => 'string', // choose: int|float|string
        'table' => '',
        'comments' => array(
            'show' => '',
            'add' => '',
            'new' => '',
        ),
        'input' => array(
            'type' => 'maps', // choose: none|text|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
            'extra_after' => '', // extra code/text after element tag
            'extra_html' => '', // extra attribues for the element tag
        ),
        'value' => array(
            'show' => array( // `key` is the action name
                'source' => 'none', // choose: none|array|function|db|var, none for no visibility
                'details' => "fields_draw_title_link({MEIT_Number}, '{VALUE}')", // value is "{VALUE}" (without quotes)
            ),
            'new' => array( // `key` is the action name
                'source' => 'polygon', // choose: none|array|function|db|var|path, none for no visibility
                'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
                'details' => '{VALUE}', // value is "{VALUE}" (without quotes)
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