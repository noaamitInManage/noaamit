<?php

/**
 * Modified on July 2009
 * !!! NOT COMPATIBLE FOR OLDER VERSIONS !!!
 *
 * Modified: October 2009
 * Added: Ordering by field(s)
 *
 * Modified: March 2010
 * Added: Radio field type
 * Added: Checkbox field type(same as checkbox-group type)
 *
 * Modified: May 2010
 * Fixed: Single and double quote issues in 'fields_get_form_fields' function
 *
 * Modified: March 2011
 * Fixed: Warning for label source=function 'fields_get_form_fields' function
 *
 * Modified: July 2011
 * Removed : fields_draw_isactive_image function - no use of it other than declaration
 *
 */

function fields_implode($glue = '', $arr, $requestArr = '', $isUpdateStyle = false)
{

    $result = '';
    foreach ($arr as $key => $field) {
        if ($field['value']['after']['source'] != 'none') {
            if ($requestArr == '') {
                $result .= $key . $glue;
            } else if (!($field['type'] == 'password' && $requestArr[$key] == '')) {
                $FIELDS_TMP_VAL = $FIELDS_TMP_STR = '';
                switch ($field['value']['after']['source']) {
                    case 'post':
                        $FIELDS_TMP_VAL = fields_cleanup($field, $requestArr[$key]);
                        break;
                    case 'array':
                    case 'function':
                        $FIELDS_TMP_STR = str_replace('{VALUE}', $requestArr[$key], $field['value']['after']['details']);
                        foreach ($requestArr as $post_key => $post_value) {
                            $FIELDS_TMP_STR = str_replace('{' . $post_key . '}', $post_value, $FIELDS_TMP_STR);
                        }
                        eval('$FIELDS_TMP_VAL = ' . $FIELDS_TMP_STR . ';');
                        break;
                    case 'var':
                        eval('global ' . $field['value']['after']['details'] . '; $FIELDS_TMP_VAL = ' . $field['value']['after']['details'] . ';');
                        break;
                    case 'empty':
                        $FIELDS_TMP_VAL = '';
                        break;
                }
                if ($isUpdateStyle) {
                    if ($field['table'] != '') {
                        $result .= '`' . $field['table'] . '`.';
                    }
                    $result .= '`' . $key . '` = \'' . $FIELDS_TMP_VAL . '\'' . $glue;
                } else {
                    $result .= '\'' . $FIELDS_TMP_VAL . '\'' . $glue;
                }
            }
        }
    }
    return substr($result, 0, strlen($result) - strlen($glue));
}

function fields_cleanup($field, $value)
{
    $Db = Database::getInstance();

    switch ($field['type']) {
        case 'int':
            return (int)$value;
        case 'float':
            return floatval($value);
        case 'string':
            if ($field['input']['type'] == "textarea") {
                $slashes = array("\r\n", "\\r\\n");
                $value = str_replace($slashes, "<br />", $value);
                $value = $Db->make_escape($value);
                $value = str_ireplace("<br />", "\r\n", $value);
            } else {
                $value = $Db->make_escape($value);
            }

            return $value;
        default:
            return ($value);
    }
}

function fields_get_show_rows_fields($fieldsArr, $rowArr, $isReturn = false, $update_statics_function = null)
{
    global $_Proccess_Main_DB_Table, $_Proccess_Has_MultiLangs;

    $output = '';
    $columns_count = 0;
    foreach ($fieldsArr as $key => $field) {
        if ($field['value']['show']['source'] != 'none') {
            $columns_count++;
            $output .= '<td class="dottTblS">';
            switch ($field['value']['show']['source']) {
                case 'array':
                case 'function':
                    $FIELDS_TMP_STR = str_replace('{VALUE}', addslashes($rowArr[$key]), $field['value']['show']['details']);
                    foreach ($rowArr as $row_key => $row_value) {
                        $FIELDS_TMP_STR = str_replace('{' . $row_key . '}', addslashes($row_value), $FIELDS_TMP_STR);
                    }
                    if ($field['value']['show']['source'] == 'array') {
                        eval('global ' . substr($FIELDS_TMP_STR, 0, (strpos($FIELDS_TMP_STR, '[') > 0 ? strpos($FIELDS_TMP_STR, '[') : strlen($FIELDS_TMP_STR))) . ';');
                    }
                    if ($FIELDS_TMP_STR)
                        eval('$output .= ' . $FIELDS_TMP_STR . ';');

                    break;
                case 'var':
                    eval('global ' . substr($field['value']['show']['details'], 0, (strpos($field['value']['show']['details'], '[') > 0 ? strpos($field['value']['show']['details'], '[') : strlen($field['value']['show']['details']))) . ';');
                    eval('global ' . str_replace('{VALUE}', $rowArr[$key], $field['value']['show']['details']) . '; $output .= ' . str_replace('{VALUE}', $rowArr[$key], $field['value']['show']['details']) . ';');
                    break;
                case 'db':
                    $output .= $rowArr[$key];
                    break;
                case 'empty':
                    $output .= '';
                    break;
                case 'editable':
                    switch ($field['input']['type']) {
                        case 'text':
                            $is_lang = $_Proccess_Has_MultiLangs && (isset($field['value']['show']['is_lang']) && $field['value']['show']['is_lang']) ? 'true' : 'false';
                            $output .= '<a href="javascript:;" class="salat-editable" data-field="' . $key . ' " data-islang="' . $is_lang . '">' . $rowArr[$key] . '</a>';
                            break;
                        default:
                            $output .= $rowArr[$key];
                    }

                    break;
            }
            $output .= '</td>';
        }
    }
    if ($isReturn) {
        return array('output' => $output, 'columns_count' => $columns_count);
    } else {
        echo $output;
        return $columns_count;
    }
}

function fields_get_show_heads_fields($fieldsArr, $isReturn = false, $fwParams = '')
{
    global $module_lang_id;
    $output = '';
    $columns_count = 0;
    foreach ($fieldsArr as $key => $field) {
        if ($field['value']['show']['source'] != 'none') {
            if (isset($field['order_by']) && $field['order_by']) {
                $ordertype = (isset($_REQUEST['ordertype']) && in_array($_REQUEST['ordertype'], array('', 'asc')) ? 'desc' : 'asc');
                $output .= '<td width="' . $field['width'] . '">
								<a href="?act=show&lang_id=' . $module_lang_id . '&orderby=' . $key . '&ordertype=' . $ordertype . $fwParams . '" style="color:white;">
									' . $field['title'] . '
									<img src="../images/ordering_' . $ordertype . '.gif" style="vertical-align: middle;" />
								</a>
							</td>';
            } else {
                $output .= '<td width="' . $field['width'] . '">' . $field['title'] . '</td>';
            }
        }
    }
    if ($isReturn) {
        return array('output' => $output, 'columns_count' => $columns_count);
    } else {
        echo $output;
        return $columns_count;
    }
}

/*for all searchable fields, add them to $_Proccess_FW_Params*/
function genric_search_add_fwParams(&$_Proccess_FW_Params)
{
    global $fieldsArr;
    foreach ($fieldsArr AS $key => $fieldArr) {
        if (isset($fieldArr['input']['searchable']) && $fieldArr['input']['searchable'] == true) {
            $_Proccess_FW_Params[] = $key;
        }
    }
}

function make_dynamic_table($cells, $headers = array(), $delete = true, $insert = true, $delete_extra = false, $table_class = '')
{
    global $_LANG;
    $num_rows = count($cells);
    $last_index = ($num_rows) ? ($num_rows - 1) : $num_rows;
    $htmlTable = '<table class="genricTable ' . $table_class . '" last_index="' . $last_index . '">';
    if (isset($headers) && !empty($headers)) {
        foreach ($headers as $header) {
            $htmlTable .= '<th>' . $header . '</th>';
        }
        $htmlTable .= ($delete) ? '<th width="50">פעולה</th>' : '';
    }

    foreach ($cells as $celid => $celss_array) {
        $num_columns = count($celss_array);
        $htmlHidden = '';
        $htmlTable .= '<tr>';
        if ($delete_extra) {
            $deleteArr = array_pop($celss_array);
        }
        foreach ($celss_array as $num => $cell) {

            if ($cell['type'] == "hidden") {
                $htmlHidden = '<input type="hidden" size="15" value="' . $cell['value'] . '" name="' . $cell['name'] . '" ' . $cell['extra'] . ' />';
                continue;
            }
            if ($cell['type'] == "order") {
                $cell['num_rows'] = $num_rows;
                $cell['row_number'] = $celid;
            }
            $htmlTable .= '<td>';
            $htmlTable .= one_line_dynamic($cell);
            $htmlTable .= '</td>';
        }
        if ($delete) {
            $htmlTable .= '<td width="50">';
            $delete_extra_html = ($deleteArr['extra']) ? $deleteArr['extra'] : '';
            $htmlTable .= '<input type="button" class="buttons red delRowGenTable ' . $deleteArr['class'] . '" ' . $delete_extra_html . ' value="'.$_LANG['BTN_DEL'].'" />';
            $htmlTable .= $htmlHidden;
            $htmlTable .= '</td>';
        }
        $htmlTable .= '</tr>';
    }
    if ($insert) {
        $htmlTable .= '<tr class="add_row_item">';
        $htmlTable .= '<td style="background: #3cb6d8;height: 30px;" colspan="' . ($num_columns + 1) . '">';
        $htmlTable .= '<input type="button" class="buttons addRowGenTable" value="הוסף שורה" >';
        $htmlTable .= '</td>';
        $htmlTable .= '</tr>';
    }
    $htmlTable .= '</table>';
    return $htmlTable;
}


function one_line_dynamic($line_cell)
{
    $html = '';
    $size = intval($line_cell['size']) > 0 ? $line_cell['size'] : 15;
    switch ($line_cell['type']) {
        case 'text':
            $html = '<input type="text" size="'.$size.'" value="' . $line_cell['value'] . '" name="' . $line_cell['name'] . '" ' . $line_cell['extra'] . ' style="direction:' . $line_cell['direction'] . ';" />';
            break;
        case 'textarea':
            $html = '<textarea cols="' . (($line_cell['cols']) ? $line_cell['cols'] : 10) . '" rows="' . (($line_cell['rows']) ? $line_cell['rows'] : 1) . '" name="' . $line_cell['name'] . '" style="direction:' . $line_cell['direction'] . ';" >' . $line_cell['value'] . '</textarea>';
            break;
        case 'label':
            $html = '<label>' . $line_cell['value'] . '</label><input type="hidden" value="' . $line_cell['value'] . '" name="' . $line_cell['name'] . '" ' . $line_cell['extra'] . ' />';
            break;
        case 'order':
            $html = outputOrderingArrows($line_cell['num_rows'], $line_cell['row_number'], $line_cell['idSel'], $line_cell['row_id'], $line_cell['value'], ($line_cell['extra']) ? $line_cell['extra'] : '');
            break;
        case 'select':
            $optionsHtml = '';
            $default = (isset($line_cell['default']) && $line_cell['default']) ? '<option value="">' . $line_cell['default'] . '</option>' : '';
            foreach ($line_cell['array'] as $opt_val => $option) {
                $selected = (!empty($line_cell['value']) && $line_cell['value'] == $opt_val) ? 'selected' : '';
                $optionsHtml .= '<option value="' . $opt_val . '" ' . $selected . '>' . $option . '</option>';
            }
            $width = ($line_cell['width']) ? $line_cell['width'] : '115';
            $html = ' <select style="width:' . $width . 'px;" name="' . $line_cell['name'] . '" ' . $line_cell['extra'] . ' >
						' . $default . '
                        ' . $optionsHtml . '
                    </select>';
            break;
        case 'gps':
            eval('$html=' . $line_cell['array'] . ';');
            $html = '' . $html . '';
            break;
        case 'function':
            $FIELDS_TMP_VAL = '';
            $FIELDS_TMP_STR = $line_cell['array'];
            eval('$FIELDS_TMP_VAL = ' . $FIELDS_TMP_STR . ';');
            $html = '<div ' . $line_cell['extra'] . '>' . $FIELDS_TMP_VAL . '</div>';
            break;
        case 'html':
            $html = $line_cell['value'];
            break;
        case 'active':
            $checked = ($line_cell['value'] == 1) ? "checked='checked'" : "";
            $html = "<input type='checkbox' name='" . $line_cell['name'] . "' {$checked} {$line_cell['extra']}/>" . $line_cell['extra_after'];
            break;
        case 'date':
            $html = '<input type="text" name="' . $line_cell['name'] . '" id="FIELD_' . $line_cell['name'] . '" value="' . date('H:i d-m-Y', $line_cell['value']) . '" size="50"' . $line_cell['extra_html'] . ' />
								<img class="pointer" src="../_public/datetimepicker.gif" onclick="javascript: NewCal(\'FIELD_' . $line_cell['name'] . '\', \'ddmmyyyy\', false, 24);" />';
            break;
        case 'datetime':
            $html = '<input type="text" name="' . $line_cell['name'] . '" id="FIELD_' . $line_cell['name'] . '" value="' . date('H:i d-m-Y', $line_cell['value']) . '" size="50"' . $line_cell['extra_html'] . ' />
								<img class="pointer" src="../_public/datetimepicker.gif" onclick="javascript: NewCal(\'FIELD_' . $line_cell['name'] . '\', \'ddmmyyyy\', true, 24);" />';
            break;
    }
    return $html;
}

function draw_gallery_upload($field_name, $id, $album_id, $num = '')
{
    global $mediaCategorysArr, $row;
    $inside = '<option value="0">בחר</option>';
    $album_id = ($album_id) ? $album_id : $row[$field_name];
    foreach ($mediaCategorysArr as $gallery_id => $gallery_title) {
        $selected = ($album_id == $gallery_id) ? 'selected="selected"' : '';
        $inside .= '<option value="' . $gallery_id . '" ' . $selected . '>' . $gallery_title . '</option>';
    }
    $html = <<<HTML
	<select id="{$id}" class="gallery_new_media must" name="{$field_name}">
		{$inside}
	</select>
	או תעלה גלריה חדשה:
	<input type="button" class="buttons orange uploadGallery" value="העלאה מרובה" id="uploadGallery_{$album_id}" num="{$num}" item_id={$row['id']} rel="{$album_id}" /> &nbsp;
HTML;
    return $html;
}

function get_timesArr()
{
    $timesArr = array();
    for ($i = 0; $i <= 23; $i++) {
        $i_txt = $i < 10 ? '0' . $i : $i;
        for ($j = 0; $j <= 59; $j++) {
            $j_txt = $j < 10 ? '0' . $j : $j;

            $timesArr[] = $i_txt . ':' . $j_txt;
        }
    }

    return $timesArr;
}

function get_timepicker_options_html($selected = '')
{
    $html = '';
    foreach (get_timesArr() as $value) {
        $is_selected = $value == $selected ? ' selected="selected"' : '';
        $html .= '<option value="' . $value . '"' . $is_selected . '>' . $value . '</option>';
    }

    return $html;
}

function fields_get_form_fields($fieldsArr, $rowArr, $real_action = 'new', $isReturn = false)
{
    $output = '';
    foreach ($fieldsArr as $key => $field) {
        $ext = '';
        $field['input']['extra_html'] = ($field['input']['extra_html'] == '' ? '' : ' ' . $field['input']['extra_html']);
        if ($field['value']['new']['source'] != 'none') {
            $output .= '<tr class="normTxt">
							<td class="dottTblS" width="' . $field['width'] . '">
								<b>' . $field['title'] . '</b>
								<div><small>' . $field['comments'][$real_action] . '</small></div>
							</td>
							<td class="dottTblS">';
            $FIELDS_TMP_STR = str_replace('{VALUE}', addslashes($rowArr[$key]), $field['value']['new']['details']);
            if (is_array($rowArr)) {
                foreach ($rowArr as $row_key => $row_value) {
                    $FIELDS_TMP_STR = str_replace('{' . $row_key . '}', $row_value, $FIELDS_TMP_STR);
                }
            }
            $FIELDS_TMP_FIELD = str_replace('{VALUE}', addslashes($rowArr[$key]), $field['input']['extra_after']);
            if (is_array($rowArr)) {
                foreach ($rowArr as $row_key => $row_value) {
                    $FIELDS_TMP_FIELD = str_replace('{' . $row_key . '}', $row_value, $FIELDS_TMP_FIELD);
                }
            }
            switch ($field['input']['type']) {
                case 'text':
                    global $languagesArr, $module_lang_id;
                    $inputDirection = 'ltr-text';
                    switch ($languagesArr[$module_lang_id]['direction']) {
                        case 1 :
                            $inputDirection = 'rtl-text';
                            break;
                        case 2 :
                            $inputDirection = 'ltr-text';
                            break;
                    }
                    $output .= '<input type="text" class="' . $inputDirection . '" name="' . $key . '" value="' . htmlspecialchars($field['value']['new']['source'] == 'empty' ? '' : $rowArr[$key]) . '" size="50"' . $field['input']['extra_html'] . ' />';
                    break;
                case 'htmltext':
                    $output .= '<textarea name="' . $key . '" id="htmleditor_' . $key . '" cols="90" rows="10"' . $field['input']['extra_html'] .
                        '>' . ($field['value']['new']['source'] == 'empty' ? '' : $rowArr[$key]) . '</textarea>';
                    $output .= '<script type="text/javascript">
									var objFCKeditor = new FCKeditor(\'htmleditor_' . $key .
                        '\', parseInt(document.getElementById(\'htmleditor_' . $key . '\').style.width), parseInt(document.getElementById(\'htmleditor_' . $key . '\').style.height));
									objFCKeditor.ReplaceTextarea();
								</script>';
                    break;


                case 'ckhtmltext':
                    if (!isset($field['input']['lang'])) {
                        $field['input']['lang'] = null;
                    }
                    switch ($field['input']['lang']) {

                        case "en": {
                            $output .= '<textarea name="' . $key . '" class="ckeditor" id="htmleditor_' . $key . '" cols="90" rows="10"' . $field['input']['extra_html'] .
                                '>' . ($field['value']['new']['source'] == 'empty' ? '' : addslashes($rowArr[$key])) . '</textarea>';
                            $toolBar = (isset($field['input']['toolbar'])) ? "toolbar : \'{$field['input']['toolbar']}\'," : 'toolbar : \'Full\',';
                            $output .= '<script type="text/javascript">
                              					CKEDITOR.replace( \'htmleditor_' . $key . '\',
                                                   	{
                                                   	  ' . $toolBar . '
                                                   		language: \'en\',
                                                   		width : \'95%\'
                                                   	});
                              					</script>
                              				  ';
                        }
                            break;
                        case "he": {
                            $output .= '<textarea name="' . $key . '" class="ckeditor" id="htmleditor_' . $key . '" cols="90" rows="10"' . $field['input']['extra_html'] .
                                '>' . ($field['value']['new']['source'] == 'empty' ? '' : addslashes($rowArr[$key])) . '</textarea>';
                            $toolBar = (isset($field['input']['toolbar'])) ? "toolbar : \'{$field['input']['toolbar']}\'," : 'toolbar : \'Full\',';

                            $output .= '<script type="text/javascript">
				        					try{
                              					var z=CKEDITOR.replace( \'htmleditor_' . $key . '\',
                                                   	{
                                                   	   ' . $toolBar . '
                                                   		language: \'he\',
                                                   		 width : \'95%\'
                                                   	});
                                              }
												catch(e){

												}  

                              					</script>
					                              ';
                        }
                            break;
                        default :
                            // get default language of module ($module_lang_id) - check if RTL/LTR from $languagesArr
                            global $languagesArr, $module_lang_id;
                            $editorLang = 'he';
                            switch ($languagesArr[$module_lang_id]['direction']) {
                                case 1 :
                                    $editorLang = 'he';
                                    break;
                                case 2 :
                                    $editorLang = 'en';
                                    break;
                            }

                            $output .= '<textarea name="' . $key . '" class="ckeditor" id="htmleditor_' . $key . '" cols="90" rows="10"' . $field['input']['extra_html'] .
                                '>' . ($field['value']['new']['source'] == 'empty' ? '' : addslashes($rowArr[$key])) . '</textarea>';
                            $toolBar = (isset($field['input']['toolbar'])) ? "toolbar : \'{$field['input']['toolbar']}\'," : 'toolbar : \'Full\',';

                            $output .= '<script type="text/javascript">
				        					try{
                              					var z=CKEDITOR.replace( \'htmleditor_' . $key . '\',
                                                   	{
                                                   	   ' . $toolBar . '
                                                   		language: \'' . $editorLang . '\',
                                                   		 width : \'95%\'
                                                   	});
                                              }
												catch(e){

												}  

                              					</script>
					                              ';
                            break;
                    }
                    break;
                case 'htmlbox':
                    $val = htmlspecialchars($rowArr[$key], null, 'utf8');
                    $output .= '<textarea style="display:none;" name="' . $key . '" id="' . $key . '">' . $val . '</textarea><a class="htmlbox ' . ($field['value']['new']['details']) . '" >ערוך טקסט</a>';
                    break;
                case 'ckbox':
                    $val = htmlspecialchars($rowArr[$key], null, 'utf8');
                    $output .= '<textarea style="display:none;" name="' . $key . '" id="' . $key . '">' . $val . '</textarea>' .
                        '<a class="htmlbox ' . ($field['value']['new']['details']) . '" onclick="javascript:addCkbox(\'' . $key . '\'); return false;">' . 'ערוך טקסט' . '</a>';
                    break;
                case 'textarea':
                    $output .= '<textarea name="' . $key . '" cols="90" rows="10" ' . $field['input']['extra_html'] . '>' . ($field['value']['new']['source'] == 'empty' ? '' : $rowArr[$key]) . '</textarea>';
                    break;
                case 'file':
                    $output .= '<input type="file" name="' . $key . '" value="" size="50"' . $field['input']['extra_html'] . ' />';
                    if ($field['value']['new']['source'] == 'path') {
                        if (is_file($_SERVER['DOCUMENT_ROOT'] . '/' . $FIELDS_TMP_STR)) {
                            $ext = strtolower(end(explode('.', $FIELDS_TMP_STR)));
                            $file_last_update_ts = filemtime($_SERVER['DOCUMENT_ROOT'].'/'.$FIELDS_TMP_STR);
                            $FIELDS_TMP_STR = $FIELDS_TMP_STR.'?ts='.$file_last_update_ts;
                            $output .= "<div>
											<a href=\"/{$FIELDS_TMP_STR}\" target=\"_blank\">
												<img src=\"../modules_fields/mime/{$ext}.gif\" align=\"absmiddle\" />
												לחץ כאן כדי להוריד/להציג את הקובץ
											</a>
										</div>
										<div>
											<input type=\"checkbox\" name=\"{$key}_delete\" id=\"{$key}_delete\" value=\"{$ext}\">
											<label for=\"{$key}_delete\">
												סמן כאן למחיקת הקובץ
											</label>
										</div>";
                        }
                    } elseif ($field['value']['new']['source'] == 'function') {
                        eval('$output .= ' . $FIELDS_TMP_STR . ';');
                    }
                    break;
                case 'maps':
                    if ($field['value']['new']['source'] == 'polygon') {
                        $jsonString = $FIELDS_TMP_STR;//urlencode($FIELDS_TMP_STR);
                        $GPS_Location = $rowArr['GPS_Location'];
                        $output .= <<<HTML
							<div id="inputsToGlobalTable"></div>
							<iframe id="iframe_polygon" src="/salat2/_public/polygon.php?type=iframe&gpsNavigation={$GPS_Location}&json={$jsonString}" style="width: 600px;height: 400px;"></iframe>
							<br>
							<textarea style="display:none;" id="json_polygon" name="{$key}">{$jsonString}</textarea>
							<input type="button" class="buttons" value="עדכן מפה"
									onclick="var child = window.open('/salat2/_public/polygon.php?gpsNavigation={$GPS_Location}&json='+$('#json_polygon').text(), 'polygon_win', 'height=' + screen.height + ',width=' + screen.width + ',resizable=yes,scrollbars=no,toolbar=no,menubar=no,location=no'); child.focus();" />
HTML;

                    } elseif ($field['value']['new']['source'] == 'function') {
                        eval('$output .= ' . $FIELDS_TMP_STR . ';');
                    }
                    break;
                case 'datepicker':
                    $output .= '<input type="text" name="' . $key . '" id="FIELD_' . $key . '" value="' . ($field['value']['new']['source'] == 'db' ? date($field['value']['new']['details'], $rowArr[$key]) : $rowArr[$key]) . '" size="50"' . $field['input']['extra_html'] . ' />
								<img class="pointer" src="../_public/datetimepicker.gif" onclick="javascript: NewCal(\'FIELD_' . $key . '\', \'ddmmyyyy\', false, 24);" />';
                    break;
                case 'datetimepicker':
                    $add_time = ($field['input']['add_time']) ? strtotime($field['input']['add_time'], time()) : strtotime('tomorrow 08:00');
                    $time = ($rowArr[$key] == '1202400' || $rowArr[$key] == '' || $rowArr[$key] == 0) ? $add_time : $rowArr[$key];
                    $time = $field['input']['empty_time'] ? '' : $time;

                    $output .= '<input type="text" name="'.$key.'" id="FIELD_'.$key.'" value="'.($field['value']['new']['source']=='db'?date($field['value']['new']['details'], $time):$time).'" size="50"'.$field['input']['extra_html'].' />
								<img class="pointer" src="../_public/datetimepicker.gif" onclick="javascript: NewCal(\'FIELD_'.$key.'\', \'ddmmyyyy\', true, 24);" />';
                    break;
                case 'timepicker':
                    $val = isset($rowArr[$key]) ? $rowArr[$key] : '';
                    $output .= '
                        <select name=' . $key . '>
                            ' . get_timepicker_options_html($val) . '
                        </select>
                    ';
                    break;
                case 'checkbox':
                case 'checkbox-group':
                    switch ($field['value']['new']['source']) {
                        case 'array':
                            if (is_array($FIELDS_TMP_STR)) {
                                eval('global ' . $field['value']['new']['details']['name'] . '; $FIELDS_VALUE_ARR = ' . $field['value']['new']['details']['name'] . ';');
                                if (count($FIELDS_VALUE_ARR) > 0) {
                                    foreach ($FIELDS_VALUE_ARR as $name => $value) {
                                        $output .= '<label for="CHKGROUP_' . $key . '_' . $name . '" style="direction:rtl;float:right;text-align:right;width:175px;">
														<input type="checkbox" name="' . $key . '[]" id="CHKGROUP_' . $key . '_' . $name . '" value="' . ($field['value']['new']['details']['id'] == '' ? $name : $value[$field['value']['new']['details']['id']]) . '"' . ($name == $rowArr[$key . '_' . $name] && isset($rowArr[$key . '_' . $name]) ? ' checked="checked"' : '') . $field['input']['extra_html'] . ' /> '
                                            . stripslashes($value[$field['value']['new']['details']['value']])
                                            . '</label> ';
                                    }
                                }
                            } else {
                                eval('global ' . $FIELDS_TMP_STR . '; $FIELDS_VALUE_ARR = ' . $FIELDS_TMP_STR . ';');
                                if (count($FIELDS_VALUE_ARR) > 0) {
                                    foreach ($FIELDS_VALUE_ARR as $name => $value) {
                                        $output .= '<label for="CHKGROUP_' . $key . '_' . $name . '" style="direction:rtl;float:right;text-align:right;width:175px;">
														<input type="checkbox" name="' . $key . '[]" id="CHKGROUP_' . $key . '_' . $name . '" value="' . $name . '"' . ($name == $rowArr[$key . '_' . $name] && isset($rowArr[$key . '_' . $name]) ? ' checked="checked"' : '') . $field['input']['extra_html'] . $field['input']['extra_html'] . ' /> '
                                            . stripslashes($value)
                                            . '</label> ';
                                    }
                                }
                            }
                            break;
                        case 'function':
                            eval('$output .= ' . $FIELDS_TMP_STR . ';');
                            break;
                    }
                    break;
                case 'radio':
                    switch ($field['value']['new']['source']) {
                        case 'array':
                            $isFirstRadio = true;
                            if (is_array($FIELDS_TMP_STR)) {
                                eval('global ' . $field['value']['new']['details']['name'] . '; $FIELDS_VALUE_ARR = ' . $field['value']['new']['details']['name'] . ';');
                                if (count($FIELDS_VALUE_ARR) > 0) {
                                    foreach ($FIELDS_VALUE_ARR as $name => $value) {
                                        $output .= '<label for="RADIO_' . $key . '_' . $name . '" style="direction:rtl;text-align:right;padding-left:10px;">
														<input type="radio" name="' . $key . '" id="RADIO_' . $key . '_' . $name . '" value="' . ($field['value']['new']['details']['id'] == '' ? $name : $value[$field['value']['new']['details']['id']]) . '"' . ($name == $rowArr[$key] || ($isFirstRadio && !isset($rowArr[$key])) ? ' checked="checked"' : '') . $field['input']['extra_html'] . ' /> '
                                            . stripslashes($value[$field['value']['new']['details']['value']])
                                            . '</label> ';
                                        $isFirstRadio = false;
                                    }
                                }
                            } else {
                                eval('global ' . substr($FIELDS_TMP_STR, 0, (strpos($FIELDS_TMP_STR, '[') > 0 ? strpos($FIELDS_TMP_STR, '[') : strlen($FIELDS_TMP_STR))) . ';');
                                //eval('global '.$FIELDS_TMP_STR.'; $FIELDS_VALUE_ARR = '.$FIELDS_TMP_STR.';');
                                eval('$FIELDS_VALUE_ARR = ' . $FIELDS_TMP_STR . ';');
                                if (count($FIELDS_VALUE_ARR) > 0) {
                                    foreach ($FIELDS_VALUE_ARR as $name => $value) {
                                        $output .= '<label for="RADIO_' . $key . '_' . $name . '" style="direction:rtl;text-align:right;padding-left:10px;">
														<input type="radio" name="' . $key . '" id="RADIO_' . $key . '_' . $name . '" value="' . $name . '"' . ($name == $rowArr[$key] || ($isFirstRadio && !isset($rowArr[$key])) ? ' checked="checked"' : '') . $field['input']['extra_html'] . $field['input']['extra_html'] . ' /> '
                                            . stripslashes($value)
                                            . '</label> ';
                                        $isFirstRadio = false;
                                    }
                                }
                            }
                            break;
                        case 'function':
                            eval('$output .= ' . $FIELDS_TMP_STR . ';');
                            break;
                    }
                    break;
                case 'label':
                    $FIELDS_TMP_VAL = '';
                    switch ($field['value']['new']['source']) {
                        case 'array':
                        case 'function':
                            if ($real_action == "add") {
                                $FIELDS_TMP_STR = str_replace(array('{id}','{gallery_id}'), 0, $FIELDS_TMP_STR);
                            }
                            break;
                        case 'var':
//							eval('global '.$FIELDS_TMP_STR.';');
                            eval('global ' . substr($FIELDS_TMP_STR, 0, (strpos($FIELDS_TMP_STR, '[') > 0 ? strpos($FIELDS_TMP_STR, '[') : strlen($FIELDS_TMP_STR))) . ';');
                            break;
                        case 'empty':
                            $FIELDS_TMP_STR = "''";//$FIELDS_TMP_STR = '';
                            break;
                        case 'db':
                            $FIELDS_TMP_STR = '\'' . str_replace("'", "\'", $rowArr[$key]) . '\'';
                            break;
                    }

                    eval('$FIELDS_TMP_VAL = ' . $FIELDS_TMP_STR . ';');

                    $output .= '<div' . $field['input']['extra_html'] . '>' . $FIELDS_TMP_VAL . '</div>';
                    break;
                case 'select':
                    $output .= '<select name="' . $key . '"' . $field['input']['extra_html'] . '>';
                    if ($field['value']['new']['dummy']['text'] != '') {
                        $output .= '<option value="' . $field['value']['new']['dummy']['value'] . '"' . ($field['value']['new']['dummy']['value'] == $rowArr[$key] ? ' selected="selected"' : '') . '>
										' . stripslashes($field['value']['new']['dummy']['text']) . '
									</option>';
                    }
                    switch ($field['value']['new']['source']) {
                        case 'array':
                            if (is_array($FIELDS_TMP_STR)) {
                                eval('global ' . $field['value']['new']['details']['name'] . '; $FIELDS_VALUE_ARR = ' . $field['value']['new']['details']['name'] . ';');
                                if (count($FIELDS_VALUE_ARR) > 0) {
                                    foreach ($FIELDS_VALUE_ARR as $name => $value) {
                                        $output .= '<option value="' . htmlspecialchars(($field['value']['new']['details']['id'] == '' ? $name : $value[$field['value']['new']['details']['id']])) . '"' . (in_array(($field['value']['new']['details']['id'] == '' ? $name : $value[$field['value']['new']['details']['id']]), $rowArr[$key]) || ($field['value']['new']['details']['id'] == '' ? $name : $value[$field['value']['new']['details']['id']]) == $rowArr[$key] ? ' selected="selected"' : '') . '>
														' . stripslashes($value[$field['value']['new']['details']['value']]) . '
													</option>';
                                    }
                                }
                            } else {
                                eval('global ' . $FIELDS_TMP_STR . '; $FIELDS_VALUE_ARR = ' . $FIELDS_TMP_STR . ';');
                                if (count($FIELDS_VALUE_ARR) > 0) {
                                    foreach ($FIELDS_VALUE_ARR as $name => $value) {
                                        $selected = ($name == $rowArr[$key]) ? 'selected="selected"' : '';


                                        // ? $rowArr[$key] : ( $rowArr? ' selected="selected"':'' ));
//										echo '<pre style="direction: ltr; text-align: left;">';
//											print_r( $rowArr );
//										echo '</pre>';	
//										exit();
                                        $output .= '<option value="' . htmlspecialchars($name) . '"' . $selected . '>
														' . stripslashes($value) . '
													</option>';
                                    }
                                }
                            }
                            break;
                        case 'function':
                            eval('$output .= ' . $FIELDS_TMP_STR . ';');
                            break;
                    }
                    $output .= '</select>';
                    break;
            }
            $output .= $FIELDS_TMP_FIELD . '
				</td>
			</tr>';
        }
    }
    $output = stripslashes($output);
    if ($isReturn) {
        return $output;
    } else {
        echo $output;
    }
}

function fields_draw_title_link($itemID, $itemTitle)
{
    global $_Proccess_HC_RowsID_Arr_NOT_EDITABLE, $fwParams;
    if (in_array($itemID, $_Proccess_HC_RowsID_Arr_NOT_EDITABLE)) {
        return $itemTitle;
    }
    $itemTitle = strip_tags($itemTitle);
    $itemTitle = stripslashes($itemTitle);
    return "<a href=\"?act=new&id={$itemID}{$fwParams}\">{$itemTitle}</a>";
}

function fields_draw_isactive_image($isActive)
{
    return "<img src=\"/salat2/images/isactive_{$isActive}.gif\" title=\"{$isActive}\"/>";
}

/* gneric search function  2/05/2011 */

function draw_genric_search($fieldArr, $key)
{

    // global $fieldsArr;
    $html = '';
    switch ($fieldArr['input']['type']) {
        case 'plain': //text
        case 'text': //text
            $val = (isset($_REQUEST[$key]) && $_REQUEST[$key]) ? $_REQUEST[$key] : '';
            $val = str_replace('%', '"', $val);
            $val = htmlspecialchars($val);
            $html = '<input type="text" name="' . $key . '" value="' . $val . '" />&nbsp;';
            break;
        case 'select': //select
            $arrName = is_array($fieldArr['value']['new']['details']) ? $fieldArr['value']['new']['details']['name'] : $fieldArr['value']['new']['details'];
            eval("global $arrName;");
            eval("\$selectArr= $arrName;");
            /* add on 14/12/11 support search in asoc array */
            if (is_array($fieldArr['value']['new']['details'])) {
                $tmpArr = array();
                foreach ($selectArr AS $kk => $vv) {
                    $tmpArr[$vv['id']] = $vv['title'];
                }
                $selectArr = $tmpArr;
            }
            $html = '<select name="' . $key . '" >';
            $html .= '<option value="-1"> -- Choose -- </option>';
            if (!isset($_REQUEST[$key])) {
                $_REQUEST[$key] = null;
            }
            $html .= BuildCombo_V3($selectArr, '', $_REQUEST[$key], $_REQUEST[$key] ? $_REQUEST[$key] : -1);
            $html .= '</select>';
            break;
        case 'radio': //radio
            global $_yesNo_arr;
            $html = '<select name="' . $key . '" >';
            $html .= '<option value="-1"> -- Choose -- </option>';
            $html .= BuildCombo_V3($_yesNo_arr, '', $_REQUEST[$key], $_REQUEST[$key]);
            $html .= '</select>';

            break;
        case 'htmltext': //content
        case 'ckhtmltext': //content
            $val = (isset($_REQUEST[$key]) && $_REQUEST[$key]) ? $_REQUEST[$key] : '';
            $html = '<input type="text" name="' . $key . '" value="' . $val . '" />&nbsp;';
            break;

        case 'label':
            if ($fieldArr['value']['show']['source'] == 'array') {
                $arrName = $fieldArr['value']['new']['details'];
                eval("global $arrName;");
                eval("\$selectArr= $arrName;");
                $html = '<select name="' . $key . '" >';
                $html .= '<option value="-1"> -- Choose -- </option>';
                $html .= BuildCombo_V3($selectArr, '', $_REQUEST[$key], $_REQUEST[$key]);
                $html .= '</select>';
            } else {
                $val = (isset($_REQUEST[$key]) && $_REQUEST[$key]) ? $_REQUEST[$key] : '';
                $html = '<input type="text" name="' . $key . '" value="' . $val . '" />&nbsp;';
            }


    }

    return $html;
}

function get_search_query($fieldArr, $field_type, $key, $tb_name = '')
{
    $ans = '';

    $fieldValue = isset($_REQUEST[$key]) ? str_replace('"', '%', $_REQUEST[$key]) : '';

    switch ($field_type) {
        case 'text': //text
            if (isset($_REQUEST[$key]) && $_REQUEST[$key]) {
                $search_line = (is_numeric($fieldValue)) ? "LIKE  '$fieldValue'" : "LIKE  '$fieldValue%'";


                return ($tb_name) ? " $tb_name.`" . $key . "` $search_line " : " `" . $key . "`   $search_line ";
            }
            break;
        case 'select': //select
            if (isset($_REQUEST[$key]) && strlen($_REQUEST[$key]) > 0 && $_REQUEST[$key] > -1) {
                return ($tb_name) ? " $tb_name.`" . $key . "` =  '$fieldValue' " : " `" . $key . "` =  '$fieldValue' ";
            }
            break;
        case 'label': //label
            if (isset($_REQUEST[$key]) && strlen($_REQUEST[$key]) > 0) {
                if ($_REQUEST[$key] > -1) {
                    $query = ($tb_name) ? '`' . $tb_name . '`.' : '';
                    $query .= $key . " LIKE  ";
                    $query .= (is_numeric($fieldValue)) ? "'$fieldValue' " : "'$fieldValue%' ";
                    return $query;
                }
            }
            break;
        case 'radio': //radio
            if (isset($_REQUEST[$key]) && strlen($_REQUEST[$key]) > 0) {
                if ($_REQUEST[$key] > -1) {
                    return ($tb_name) ? " $tb_name.`" . $key . "` =  '$fieldValue' " : " `" . $key . "` =  '$fieldValue' ";
                }
            }
            break;
        case 'htmltext': //content
        case 'ckhtmltext': //content
            if (isset($_REQUEST[$key]) && $_REQUEST[$key]) {
                return " `" . $key . "` LIKE  '%$fieldValue%' ";
            }
            break;
    }


    return 0;

}

/**
 * @author : gal zalait
 * @description : write  query into whereArr
 * @param  $global_tb_name = if you give name to table after L|R|IN join
 * @since : 08/06/11
 */
function genric_searchable_items($module_id, &$whereArr, $global_tb_name = '')
{
    global $fieldsArr;
    foreach ($fieldsArr AS $key => $fieldArr) {
        if (isset($fieldArr['input']['searchable']) && $fieldArr['input']['searchable'] == true) {
            $tb_name = strlen($fieldArr['table']) > 0 ? $fieldArr['table'] : $global_tb_name;
            $where = get_search_query($fieldArr, $fieldArr['input']['type'], $key, $tb_name);
            if ($where) {
                $whereArr[] = $where;
            }
        }
    }
}

function logUserEvent($process, $user_id, $action)
{
    $date = array();

    foreach ($_REQUEST as $key => $value) {
        $date[$key] = $value;
    }

    $fields = array(
        'process_id' => $process,
        'user_id' => $user_id,
        'item_id' => (isset($_REQUEST['id'])) ? $_REQUEST['id'] : '',
        'action' => $action,
        'data' => base64_encode(serialize($date)),
        'last_update' => time(),
    );
    if ($process != 15) {
        $Db = Database::getInstance();
        $Db->insert('tb_data_log', $fields);
    }
}

if ($write_logs) {
    logUserEvent($_ProcessID, $_SESSION['salatUserID'], ((isset($_REQUEST['act']) && $_REQUEST['act']) ? $_REQUEST['act'] : 'enter'));
}

function fields_draw_yesno($value)
{
    global $_yesNo_arr, $row;
    if ($value) {
        return '<strong style="color:green;" class="active_yes" rel="' . $row['id'] . '">' . $_yesNo_arr[$value] . '</strong>';
    }
    return '<strong style="color:red;" class="active_no" rel="' . $row['id'] . '">' . $_yesNo_arr[$value] . '</strong>';
}


function get_db_columns($tb_name)
{
    $fieldsArr = array();
    $Db = Database::getInstance();

    $query = "SHOW COLUMNS FROM `{$tb_name}` ";
    $result = $Db->query($query);
    while ($row = $Db->get_stream($result)) {
        $fieldsArr[$row['Field']] = $row['Field'];
    }

    return $fieldsArr;
}

function multi_lang_insert_query($tb_name, $fieldsArr, $module_lang_id)
{
    $tb_colArr = get_db_columns($tb_name);
    $tb_lang_colArr = get_db_columns($tb_name . '_lang');

    $main_tb_fieldsArr = array_intersect_key($fieldsArr, ($tb_colArr));

    $Db = Database::getInstance();


    $lang_tb_fieldsArr = array_intersect_key($fieldsArr, ($tb_lang_colArr));

    $query = "INSERT INTO {$tb_name}(" . fields_implode(', ', $main_tb_fieldsArr) . ") VALUES (" . fields_implode(',', $main_tb_fieldsArr, $_REQUEST) . ")";
    $result = $Db->query($query);

    $_REQUEST['inner_id'] = $obj_id = $Db->get_insert_id();

    $exFields = array('obj_id' => $obj_id,
        'lang_id' => $module_lang_id
    );
    $query = "INSERT INTO `{$tb_name}_lang` (" . fields_implode(', ', $lang_tb_fieldsArr) . ',' . implode(',', array_keys($exFields)) . ") 
						VALUES (" . fields_implode(',', $lang_tb_fieldsArr, $_REQUEST) . ',' . implode(',', $exFields) . ")";
    $result = $Db->query($query);


}

function multi_lang_update_query($tb_name, $obj_id, $fieldsArr, $module_lang_id)
{

    $tb_colArr = get_db_columns($tb_name);
    $tb_lang_colArr = get_db_columns($tb_name . '_lang');

    $main_tb_fieldsArr = array_intersect_key($fieldsArr, ($tb_colArr));
    $lang_tb_fieldsArr = array_intersect_key($fieldsArr, ($tb_lang_colArr));


    // original code
    //	$query="UPDATE {$tb_name} SET ".fields_implode(',', $main_tb_fieldsArr, $_REQUEST, true)." WHERE id='{$obj_id}'";
    //	$result = mysql_query($query) or db_showError(__FILE__, __LINE__, $query);

    // fix for multilang modules where ALL fields are multilang leaving only `id` in main table.
    // Lior - 10/01/2013

    $fieldsToUpdate = fields_implode(',', $main_tb_fieldsArr, $_REQUEST, true);
    $Db = Database::getInstance();
    if ($fieldsToUpdate != '') {
        $query = "UPDATE {$tb_name} SET " . $fieldsToUpdate . " WHERE id='{$obj_id}'";
        $result = $Db->query($query);
    }

    //$query="REPLACE INTO `{$tb_name}_lang` SET ".fields_implode(',', $lang_tb_fieldsArr, $_REQUEST, true)." WHERE obj_id='{$obj_id}' AND `lang_id`='{$module_lang_id}'";
    $query = "DELETE FROM `{$tb_name}_lang` WHERE `obj_id`='{$obj_id}' AND `lang_id`='{$module_lang_id}' LIMIT 1";
    $Db->query($query);

    $exFields = array('obj_id' => $obj_id,
        'lang_id' => $module_lang_id
    );

    $query = "INSERT INTO `{$tb_name}_lang` (" . fields_implode(', ', $lang_tb_fieldsArr) . ',' . implode(',', array_keys($exFields)) . ") 
		VALUES (" . fields_implode(',', $lang_tb_fieldsArr, $_REQUEST) . ',' . implode(',', $exFields) . ")";
    $Db->query($query);
}

?>
