<?
include_once($_SERVER['DOCUMENT_ROOT'].'/_static/countries.inc.php');//$countriesArr
include_once($_SERVER['DOCUMENT_ROOT'].'/_static/cities.en.inc.php');//$citiesArr
?>
<form class="frm" id="frmSiteGroups" method="post" action="">
<fieldset>
	<label class="siteFormsLabel err_wrap">
		<span class="formLabel"><span class="ast">* </span>Agency Name</span>
		<input type="text" name="agency" class="formFields W_287 must" tabindex="1">
		<div class="err_text"><?=lang('agency_err')?></div>
	</label>
	<label class="siteFormsLabel err_wrap">
		<span class="formLabel"><span class="ast">* </span>Contact Person</span>
		<input type="text" name="contact" class="formFields W_287 must" tabindex="2">
		<div class="err_text"><?=lang('contact_err')?></div>
	</label>
	<label class="siteFormsLabel err_wrap">
		<span class="formLabel"><span class="ast">* </span>Email</span>
		<input type="text" id="email" name="email" class="formFields W_287 must" tabindex="3">
		<div class="err_text"><?=lang('email_err')?></div>
	</label>
	<div class="siteFormsLabel err_wrap">
		<span class="formLabel"><span class="ast">* </span>Phone</span>
		<input type="text" name="countryPref" class="formFields W_43 must numOnly" tabindex="4" maxlength="4">
		<input type="text" name="cityPref" class="formFields W_43 must numOnly" style="margin:0 3px 0 4px;" tabindex="5" maxlength="4">
		<input type="text" name="phone" class="formFields W_183 must numOnly" tabindex="6" maxlength="20">
		<div class="err_text"><?=lang('phone_err')?></div>
	</div>
	<div class="siteFormsLabel err_wrap">
		<span class="formLabel"><span class="ast">* </span>Address</span> 
		<input type="text" name="address" class="formFields W_287 must" tabindex="7">		
		<div class="err_text"><?=lang('address_err')?></div>
	</div>
	<div class="clearAll"></div>
	<label class="siteFormsLabel err_wrap">
		<span class="formLabel floatLeft"><span class="ast">* </span>Country</span>
		<?=comboSelect('user_country',$countriesArr,'formFields combo_287 floatLeft','country_select','Type the country you live in','must');?>
		<div class="clearAll"></div>
		<div class="err_text"><?=lang('country_err')?></div>
	</label>
	<div class="clearAll"></div>
	<div class="siteFormsLabel err_wrap">
		<span class="formLabel"><span class="ast">* </span>Arrival Date</span>
		<input type="text" id="arrival" name="arrival" class="formFields date_93 must hasDatepick" tabindex="8">
		<span class="formLabel forDate"><span class="ast">* </span>Departure Date</span>
		<input type="text" id="departure" name="departure" class="formFields date_93 must hasDatepick" tabindex="9">
		<div class="err_text"><?=lang('dates_err')?></div>
	</div>
	<label class="siteFormsLabel err_wrap">
		<span class="formLabel"><span class="ast">* </span>Group Type</span>
		<input type="text" name="group_type" class="formFields W_287 must" tabindex="10">
		<div class="err_text"><?=lang('group_err')?></div>
	</label>
	<div class="clearfix err_wrap">
		<label class="siteFormsLabel floatLeft">
			<span class="formLabel"><span class="ast">* </span>Adults</span>
			<input type="text" name="adults" class="formFields W_96 must numOnly" tabindex="11">
		</label>
		<label class="siteFormsLabel floatLeft">
			<span class="formLabel centerFor96"><span class="ast">* </span>Children</span>
			<input type="text" name="children" class="formFields W_96 must numOnly" tabindex="12">
		</label>
		<div class="clearAll"></div>
		<div class="err_text"><?=lang('people_num_err')?></div>
	</div>
	<label class="siteFormsLabel err_wrap">
		<span class="formLabel"><span class="ast">* </span>No. of rooms</span>
		<input type="text" name="num_of_rooms" class="formFields W_287 must numOnly" tabindex="13">
		<div class="err_text"><?=lang('rooms_num_err')?></div>
	</label>
	
	<div class="destinationsCont">
		<div class="siteFormsLabel err_wrap">
			<span class="formLabel floatLeft"><span class="ast">* </span>Region</span>
			<?=comboSelect('city[]',$citiesArr,'formFields combo_137 floatLeft','accomodation','Choose Accomodation','must');?>
			<input type="text" name="nightsNum[]" class="formFields W_137 must autoclear numOnly default" style="margin-left:14px;" tabindex="14" value="Type number of nights" defaultvalue="Type number of nights">
			<div class="err_text"><?=lang('destination_err')?></div>
		</div>
	</div>
	
	<label class="siteFormsLabel">
		Add another destination
		<span class="addDestinationBtn"></span>
	</label>
	<div class="clearfix err_wrap">
		<label class="siteFormsLabel floatLeft">
			<span class="formLabel"><span class="ast">* </span>Hotel category</span>
			<input type="text" name="hotel_category" class="formFields W_96 must autoclear numOnly default" tabindex="14" value="Type number" defaultvalue="Type number">
		</label>
		<label class="siteFormsLabel floatLeft">
			<span class="formLabel centerFor96"><span class="ast">* </span>No. of rooms</span>
			<input type="text" name="num_of_rooms" class="formFields W_96 must autoclear numOnly default" tabindex="15" value="Type number" defaultvalue="Type number">
		</label>
		<div class="clearAll"></div>
		<div class="err_text"><?=lang('hotel_err')?></div>
	</div>
	<div>
		<div class="siteFormsLabel boardCont">
			<span class="formLabel">Board</span>
			<label><input type="radio" name="board" value="BB" checked="checked"> BB</label>
			<label><input type="radio" name="board" value="HF"> HF</label>
			<label><input type="radio" name="board" value="FB"> FB</label>
		</div>
		<label class="siteFormsLabel err_wrap">
			<span class="formLabel forTextAr"><span class="ast">* </span>Special requests</span>
			<textarea name="special_requests" class="formFields textAr_287 must" tabindex="16"></textarea>
			<div class="err_text"><?=lang('notes_err')?></div>
		</label>
		<div class="formErrors colorSchema_5"></div>
		<div class="floatLeft agreementWrap">
			<label err_wrap>
				<input id="agreementChk" name="agreementChk" type="checkbox"> <span>I aprove terms and conditions</span>
				<div class="err_text agree_err"><?=lang('agree_err')?></div>
			</label>
		</div>
	 	<div id="submitGroupsBtn" class="btns smallBtn floatRight submitBtn">
			Send
		</div>								
	</div>
	<input type="hidden" name="form_id" value="2">
	<input type="hidden" name="page_title" value="<?=$Cont->title?>">
	</fieldset>
</form>