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
        'title' => 'סניף',
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
            'searchable'=>false // genric search
        ),
        'value' => array(
            'show' => array( // `key` is the action name
                'source' => 'editable', // choose: none|array|function|db|var, none for no visibility
                'is_lang' => true,
                'details' => "", // value is "{VALUE}" (without quotes)
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

    'bitwise_array' => array(
        'title' => 'bitwise checkbox',
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
            'type' => 'checkbox', // choose: none|text|textarea|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
            'extra_after' => '', // extra code/text after element tag
            'extra_html' => '', // extra attribues for the element tag
        ),
        'value' => array(
            'show' => array( // `key` is the action name
                'source' => 'none', // choose: none|array|function|db|var, none for no visibility
                'details' => "", // value is "{VALUE}" (without quotes)
            ),
            'new' => array( // `key` is the action name
                'source' => 'function', // choose: none|array|function|db|var|path, none for no visibility
                'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
                'details' => "drawBitwiseFlagSelection('store_properties','{VALUE}')", // value is "{VALUE}" (without quotes)
            ),
            'after' => array( // `key` is the action name
                'source' => 'function', // choose: none|post|array|function|db|var, none for no visibility
                'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
                'details' =>  "calculateBitwiseFlagSelection('store_properties')", // value is "{VALUE}" (without quotes)
            ),
        ),
    ),


    'dynamic_table'=> array(
        'title' => 'טבלה דינאמית',
        'width' => '',
        'type' => 'string', // choose: int|float|string
        'table' => '',
        'comments' => array(
            'show' => 'י',
            'add' => '',
            'new' => '',
        ),
        'input' => array(
            'type' => 'label', // choose: none|text|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
            'extra_after' => '', // extra code/text after element tag
            'extra_html' => '', // extra attribues for the element tag
            'searchable'=>false // genric search
        ),
        'value' => array(
            'show' => array( // `key` is the action name
                'source' => 'none', // choose: none|array|function|db|var, none for no visibility
                'details' => "", // value is "{VALUE}" (without quotes)
            ),
            'new' => array( // `key` is the action name
                'source' => 'function', // choose: none|array|function|db|var|path, none for no visibility
                'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
                'details' => 'draw_dynamic_table()', // value is "{VALUE}" (without quotes) ($id,$table_link='',$main_table='',$field_id_name='',$and_where=''){
            ),
            'after' => array( // `key` is the action name
                'source' => 'post', // choose: none|post|array|function|db|var, none for no visibility
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
            'type' => 'select', // choose: none|text|textarea|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
            'extra_after' => '', // extra code/text after element TAG
            'extra_html' => '', // extra attribues for the element tag
            'searchable' => false, // genric search
        ),
        'value' => array(
            'show' => array( // `key` is the action name
                'source' => 'function', // choose: none|array|function|db|var, none for no visibility
                'details' => "fields_draw_isactive_image('{VALUE}')", // value is "{VALUE}" (without quotes)
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


    'open' => array(
        'title' => 'פתוח',
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
            'type' => 'select', // choose: none|text|textarea|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
            'extra_after' => '', // extra code/text after element TAG
            'extra_html' => '', // extra attribues for the element tag
            'searchable' => false, // genric search
        ),
        'value' => array(
            'show' => array( // `key` is the action name
                'source' => 'function', // choose: none|array|function|db|var, none for no visibility
                'details' => "fields_draw_isactive_image('{VALUE}')", // value is "{VALUE}" (without quotes)
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
            'type' => 'label', // choose: none|text|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
            'extra_after' => '', // extra code/text after element tag
            'extra_html' => '', // extra attribues for the element tag
        ),
        'value' => array(
            'show' => array( // `key` is the action name
                'source' => 'function', // choose: none|array|function|db|var, none for no visibility
                'details' => "date('[H:i] d.m.Y','{VALUE}')", // value is "{VALUE}" (without quotes)
            ),
            'new' => array( // `key` is the action name
                'source' => 'none', // choose: none|array|function|db|var|path, none for no visibility
                'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
                'details' => "date('[H:i] d.m.Y','{VALUE}')", // value is "{VALUE}" (without quotes)
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