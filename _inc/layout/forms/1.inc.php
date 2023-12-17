<?include_once($_SERVER['DOCUMENT_ROOT'].'/_static/countries.inc.php');//$countriesArr?>
<form class="frm" id="frmSitePrivate" method="post" action="">
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
				<input type="text" name="cityPref" class="formFields W_43 must numOnly" style="margin:0 3px 0 4px;" tabindex="5"  maxlength="4">
				<input type="text" name="phone" class="formFields W_183 must numOnly" tabindex="6"  maxlength="20">
				<div class="err_text"><?=lang('phone_err')?></div>
			</div>
			<label class="siteFormsLabel err_wrap">
				<span class="formLabel floatLeft"><span class="ast">* </span>Country</span>
				<?=comboSelect('user_country',$countriesArr,'formFields combo_287 floatLeft','country_select','Type the country you live in','must');?>
				<div class="clearAll"></div>
				<div class="err_text"><?=lang('country_err')?></div>
			</label>
			<div class="clearAll"></div>
			<label class="siteFormsLabel err_wrap">
				<span class="formLabel forTextAr"><span class="ast">* </span>Please detail your customer request</span>
				<textarea name="special_requests" class="formFields textAr_287 must" tabindex="16"></textarea>
				<div class="err_text"><?=lang('notes_err')?></div>
			</label>
			<div class="formErrors colorSchema_5"></div>
			<div class="floatLeft agreementWrap">
				<label class="err_wrap">
					<input id="agreementChk" name="agreementChk" type="checkbox"> <span>I aprove terms and conditions</span>
					<div class="agree_err err_text"><?=lang('agree_err')?></div>
				</label>
			</div>
			<div id="submitPrivateBtn" class="btns smallBtn floatRight submitBtn">
				Send
			</div>
			<input type="hidden" name="form_id" value="1">
			<input type="hidden" name="page_title" value="<?=$Cont->title?>">
	</fieldset>
</form>