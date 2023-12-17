<?php 

if($_Proccess_Has_MultiLangs){
	$META=$Db->query("SELECT * FROM tb_metatags WHERE((inner_id='".$_REQUEST['id']."') AND (module_id='".$_ProcessID."'))  AND `lang_id`='{$module_lang_id}'");
}else{
	$META=$Db->query("SELECT * FROM tb_metatags WHERE((inner_id='".$_REQUEST['id']."') AND (module_id='".$_ProcessID."'))");
}

$META=$Db->get_stream($META);
/* Check if this is an advanced module */
$module = array();
$sql = "SELECT * FROM `tb_sitepages` WHERE `salat_module`={$_ProcessID} AND `is_advanced`=1";
$res = $Db->query($sql);
if ($res){
    $module = $Db->get_stream($res);
}
?>
<tr class="dottTbl"><td class="" colspan="2"><b>מערכת תגי מטה</b></tr>
<?php if (!isset($module['is_advanced'])){ ?>

<tr class="normTxt">
	<td class="dottTblS" width="" valign="top"><b>כתובת עמוד (url)</b></td>
	<td class="dottTblS" align="right">
		<input dir="rtl" value="<?php echo htmlspecialchars($META['meta_urlalias']);?>" size="45" type="text" name="meta_urlalias" onmousedown="return (noRightButton(event,'כתובת עמוד'));" onkeydown="return (noPaste(event,'כתובת עמוד'));" oncontextmenu="return (false);" />
		<br><small style="color:red;">על מנת להגיע לעמוד המבוקש תוקלד כתובת האתר - קו נטוי - כתובת העמוד<br />לדוגמא: <span dir="ltr" style="font-size:10px;"><u>http://<?php echo $_SERVER['HTTP_HOST']?>/מאמרים</u></span></small>
	</td>
</tr>
<? } ?>
<tr class="normTxt">
	<td class="dottTblS"><b>כותרת</b></td>
	<td class="dottTblS" align="right" width="">
		<input dir="rtl" value="<?php echo htmlspecialchars($META['meta_title']);?>" size="45" type="text" name="meta_title" />
	</td>
</tr>
<tr class="normTxt">
	<td class="dottTblS" valign="top"><b>תאור</b></td>
	<td class="dottTblS" align="right"  width="">
		<textarea  rows="4" cols="30" name="meta_description"><?php echo htmlspecialchars(stripslashes($META['meta_description']));?></textarea>
	</td>
</tr>
<tr class="normTxt">
	<td class="dottTblS"><b>מילות מפתח</b></td>
	<td class="dottTblS" align="right" width="">
		<input dir="rtl" value="<?php echo htmlspecialchars($META['meta_keywords']);?>" size="45" type="text" name="meta_keywords" />
		<br><small style="color:red">מילות מפתח יש להפריד בפסיקים</small>
	</td>
</tr>
<tr class="normTxt">
	<td class="dottTblS" valign="top"><b>no index</b></td>
	<td class="dottTblS" align="right"  width="">
		<input dir="rtl" value="1" type="checkbox" name="noindex" <? if ($META['noindex']){?> checked="checked" <? } ?>  />
		<br><small style="color:red">no-index meta tag</small>
	</td>
</tr>
<tr class="normTxt">
	<td class="dottTblS" valign="top"><b>canonical</b></td>
	<td class="dottTblS" align="right"  width="">
		<input style="direction:ltr;" value="<?=$META['canonical'];?>" type="type" name="canonical"   />
		<br><small style="color:red">http://www.sitedomain.com/link</small>
	</td>
</tr>

<?php  if ($_ProcessID>0){ ?>
<tr class="normTxt">
	<td class="dottTblS" valign="top"><b>עדיפות במפת אתר</b></td>
	<td class="dottTblS" align="right"  width="">
		<input dir="rtl" value="<?php echo $META['sm_priority'];?>" size="5" type="text" name="sm_priority" />
		<br><small style="color:red">ערך עשרוני מ- 0.01 ועד 0.99 (עמוד הבית אוטומטית מוגדר כ-1.0)<br />הסבר מפורט במודול Google Sitemap</small>
	</td>
</tr>
<?php  } ?>
<input type="hidden"  name="inner_id" value="<?php echo $_REQUEST['id']?>" />
<input type="hidden" name="module_id" value="<?php echo $_ProcessID?>" />
<br />
<script language="JavaScript" type="text/javascript">
	<?php  if($_META_TITLE!=''){?>
	if(<?php echo $_META_FORM?>.<?php echo $_META_TITLE?>){
	<?php echo $_META_FORM?>.<?php echo $_META_TITLE?>.onblur=function(){
		if(<?php echo $_META_FORM?>.<?php echo $_META_TITLE?>.value!=<?php echo $_META_FORM?>.meta_title.value ){
			<?php echo ($META['meta_title']!=''?"if(confirm('האם לעדכן את ערכי תגי המטה?')){":'');?>
				<?php echo $_META_FORM?>.meta_title.value=this.value;
			<?php echo ($META['meta_title']!=''?"}":'');?>
		}
		if(meta_CreateAlias(<?php echo $_META_FORM?>.<?php echo $_META_TITLE?>.value)!=<?php echo $_META_FORM?>.meta_urlalias.value ){
			<?php echo ($META['meta_urlalias']!=''?"if(confirm('האם לעדכן את כתובת העמוד?')){":'');?>
				<?php echo $_META_FORM?>.meta_urlalias.value=meta_CreateAlias(this.value);
			<?php echo ($META['meta_urlalias']!=''?"}":'');?>
		}
	}
	}
	<?php  }?>
	
	<?php  if($_META_DESC!=''){?>
	if(<?php echo $_META_FORM?>.<?php echo $_META_DESC?>){
	<?php echo $_META_FORM?>.<?php echo $_META_DESC?>.onblur=function(){
		if(<?php echo $_META_FORM?>.<?php echo $_META_DESC?>.value!=<?php echo $_META_FORM?>.meta_description.value){
			<?php echo ($META['meta_description']!=''?"if(confirm('האם לעדכן את ערכי תגי המטה?')){":'');?>
				<?php echo $_META_FORM?>.meta_description.value=this.value;
			<?php echo ($META['meta_description']!=''?"}":'');?>
		}
	}
	}
	<?php  }?>
</script>
<script type="text/javascript" language="javascript" src="/salat2/_inc/metascript.js"></script>