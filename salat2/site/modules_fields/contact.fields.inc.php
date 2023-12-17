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
    'email' => array(
        'title' => 'אימייל',
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
            'extra_html' => 'id="email"', // extra attribues for the element tag
            'searchable' => true, // gneric search
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
    /*'user_type' => array(
        'title' => 'סוג משתמש',
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
            'searchable' => false, // gneric search
        ),
        'value' => array(
            'show' => array( // `key` is the action name
                'source' => 'function', // choose: none|array|function|db|var, none for no visibility
                'details' => 'getUser("{VALUE}")', // value is "{VALUE}" (without quotes)
            ),
            'new' => array( // `key` is the action name
                'source' => 'function', // choose: none|array|function|db|var|path, none for no visibility
                'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
                'details' => 'getUser("{VALUE}")', // value is "{VALUE}" (without quotes)
            ),
            'after' => array( // `key` is the action name
                'source' => 'post', // choose: none|post|array|function|db|var, none for no visibility
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
    ),	*/
    'name' => array(
        'title' => 'שם מלא',
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

    /*	'category_id' => array(
            'title' => 'קטגוריה',
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
                'type' => 'select', // choose: none|text|textarea|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
                'extra_after' => '', // extra code/text after element tag
                'extra_html' => '', // extra attribues for the element tag
                'searchable' => false, // gneric search

            ),
            'value' => array(
                'show' => array( // `key` is the action name
                    'source' => 'array', // choose: none|array|function|db|var, none for no visibility
                    'details' => '$categoryFlatArr[{VALUE}]', // value is "{VALUE}" (without quotes)
                ),
                'new' => array( // `key` is the action name
                    'source' => 'array', // choose: none|array|function|db|var|path, none for no visibility
                    'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
                    'details' => '$categoryFlatArr', // value is "{VALUE}" (without quotes)
                ),
                'after' => array( // `key` is the action name
                    'source' => 'post', // choose: none|post|array|function|db|var, none for no visibility
                    'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
                    'details' => "", // value is "{VALUE}" (without quotes)
                ),
            ),
        ),*/

    /*'type_id' => array(
        'title' => 'סוג הפנייה',
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
            'type' => 'select', // choose: none|text|textarea|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
            'extra_after' => '', // extra code/text after element tag
            'extra_html' => '', // extra attribues for the element tag
            'searchable' => false, // gneric search

        ),
        'value' => array(
            'show' => array( // `key` is the action name
                'source' => 'array', // choose: none|array|function|db|var, none for no visibility
                'details' => '$contactArr[{VALUE}]', // value is "{VALUE}" (without quotes)
            ),
            'new' => array( // `key` is the action name
                'source' => 'array', // choose: none|array|function|db|var|path, none for no visibility
                'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
                'details' => '$contactArr', // value is "{VALUE}" (without quotes)
            ),
            'after' => array( // `key` is the action name
                'source' => 'post', // choose: none|post|array|function|db|var, none for no visibility
                'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
                'details' => "", // value is "{VALUE}" (without quotes)
            ),
        ),
    ),*/
    'phone' => array(
        'title' => 'טלפון',
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
                'source' => 'none', // choose: none|post|array|function|db|var, none for no visibility
                'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
                'details' => "", // value is "{VALUE}" (without quotes)
            ),
        ),
    ),
    /*'fbid' => array(
        'title' => 'fbid',
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
    ),*/
    'content' => array(
        'title' => 'הודעה',
        'width' => '',
        'type' => 'string', // choose: int|float|string
        'table' => '',
        'comments' => array(
            'show' => '',
            'add' => '',
            'new' => '',
        ),
        'input' => array(
            'type' => 'ckhtmltext', // choose: none|text|textarea|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
            'toolbar'=>'Full',// choose : None|Full|Basic|Inmanage,
            'extra_after' => '', // extra code/text after element tag
            'extra_html' => '', // extra attribues for the element tag
        ),
        'value' => array(
            'show' => array( // `key` is the action name
                'source' => 'none', // choose: none|array|function|db|var, none for no visibility
                'details' => "", // value is "{VALUE}" (without quotes)
            ),
            'new' => array( // `key` is the action name
                'source' => '', // choose: none|array|function|db|var|path, none for no visibility
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
    'created_ts' => array(
        'title' => 'תאריך קבלה',
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
                'source' => 'none', // choose: none|array|function|db|var|path, none for no visibility
                'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
                'details' => "date('[H:i:s] d-m-Y', '{VALUE}')", // value is "{VALUE}" (without quotes)
            ),
            'after' => array( // `key` is the action name
                'source' => 'none', // choose: none|post|array|function|db|var, none for no visibility
                'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
                'details' => "", // value is "{VALUE}" (without quotes)
            ),
        ),
    ),
    'is_done' => array(
        'title' => 'האם טופל?',
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
            'type' => 'radio', // choose: none|text|textarea|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
            'extra_after' => '', // extra code/text after element tag
            'extra_html' => '', // extra attribues for the element tag
            'searchable' => true, // genric search
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
    /*
    '_content_' => array(
        'title' => 'תוכן',
        'width' => '',
        'type' => 'string', // choose: int|float|string
        'table' => '',
        'comments' => array(
            'show' => '',
            'add' => '',
            'new' => '',
        ),
        'input' => array(
            'type' => 'ckbox', // choose: none|text|textarea|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
            'toolbar'=>'Full',// choose : None|Full|Basic|Inmanage,
            'extra_after' => '', // extra code/text after element tag
            'extra_html' => '', // extra attribues for the element tag
        ),
        'value' => array(
            'show' => array( // `key` is the action name
                'source' => 'none', // choose: none|array|function|db|var, none for no visibility
                'details' => "", // value is "{VALUE}" (without quotes)
            ),
            'new' => array( // `key` is the action name
                'source' => '', // choose: none|array|function|db|var|path, none for no visibility
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
    */
    '__contact_user__' => array(
        'title' => 'מענה',
        'width' => '',
        'type' => 'string', // choose: int|float|string
        'table' => '',
        'comments' => array(
            'show' => '',
            'add' => '',
            'new' => '',
        ),
        'input' => array(
            'type' => 'label', // choose: none|text|textarea|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
            'toolbar'=>'none',// choose : None|Full|Basic|Inmanage,
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
                'details' => 'draw_contact_button({id})', // value is "{VALUE}" (without quotes)
            ),
            'after' => array( // `key` is the action name
                'source' => 'none', // choose: none|post|array|function|db|var, none for no visibility
                'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
                'details' => "", // value is "{VALUE}" (without quotes)
            ),
        ),
    ),
    'answer' => array(
        'title' => 'קיים תשובה',
        'width' => '',
        'type' => 'string', // choose: int|float|string
        'table' => '',
        'comments' => array(
            'show' => '',
            'add' => '',
            'new' => '',
        ),
        'input' => array(
            'type' => 'label', // choose: none|text|textarea|select|radio|checkbox|timepicker|datepicker|datetimepicker|htmltext(WYSIWYG)
            'toolbar'=>'none',// choose : None|Full|Basic|Inmanage,
            'extra_after' => '', // extra code/text after element tag
            'extra_html' => '', // extra attribues for the element tag
            'searchable' => false, // gneric search
        ),
        'value' => array(
            'show' => array( // `key` is the action name
                'source' => 'array', // choose: none|array|function|db|var, none for no visibility
                'details' => 'getYesNo()', // value is "{VALUE}" (without quotes)
            ),
            'new' => array( // `key` is the action name
                'source' => 'array', // choose: none|array|function|db|var|path, none for no visibility
                'dummy' => array('value' => '{VALUE}', 'text' => ''), // e.g. "Please choose category"
                'details' => 'getYesNo()', // value is "{VALUE}" (without quotes)
            ),
            'after' => array( // `key` is the action name
                'source' => 'none', // choose: none|post|array|function|db|var, none for no visibility
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
                'source' => 'none', // choose: none|array|function|db|var, none for no visibility
                'details' => "date('[H:i:s] d-m-Y', '{VALUE}')", // value is "{VALUE}" (without quotes)
            ),
            'new' => array( // `key` is the action name
                'source' => 'none', // choose: none|array|function|db|var|path, none for no visibility
                'dummy' => array('value' => '', 'text' => ''), // e.g. "Please choose category"
                'details' => "date('[H:i:s] d-m-Y', '{VALUE}')", // value is "{VALUE}" (without quotes)
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