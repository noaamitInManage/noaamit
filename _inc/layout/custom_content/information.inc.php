<?php

include_once($_project_server_path."/_static/forecast.inc.php"); // $forecast 
include_once($_project_server_path."/_static/currency.inc.php"); // $currency 

$dateFormat = 'd/m/Y';
$dtObj = new DateTime();
$israelTime = $dtObj->format('H:i A');
$today = $dtObj->format($dateFormat);
$dtObj->modify("+1 day");
$tomorrow = $dtObj->format($dateFormat);
$dtObj->modify("+1 day");
$dayAfterNext = $dtObj->format($dateFormat);


$usd = number_format($currency['USD'],2)." US Dollar";
$eur = number_format($currency['EUR'],2)." Euro";
$brl = number_format($currency['BRL'],2)." Brazilian Real";
$ars = number_format($currency['ARS'],2)." Argentine Peso";

$contentInfo = new generalContentManager(33);

error_reporting(E_ALL);
ini_set('display_errors', '1');	


?>
<h3 class="weather">
	Weather in Israel</h3>
<hr />
<div class="clearAll">
	&nbsp;</div>
<ul class="columns">
	<li class="first">
		<div class="title">Tel Aviv</div>
		<div class="cont-row">
		<div class="date"><?php echo $today; ?></div>
			<div class="deg">
			<span class="colorSchema_1"><?php echo $forecast['tlv'][0]['high']; ?>&deg; / </span><?php echo $forecast['tlv'][0]['low']; ?>&deg;</div>
		</div>
		<div class="cont-row">
		<div class="date"><?php echo $tomorrow; ?></div>
			<div class="deg">
			<span class="colorSchema_1"><?php echo $forecast['tlv'][1]['high']; ?>&deg; / </span><?php echo $forecast['tlv'][1]['low']; ?>&deg;</div>
		</div>
		<div class="cont-row last">
		<div class="date"><?php echo $dayAfterNext; ?></div>
			<div class="deg">
			<span class="colorSchema_1"><?php echo $forecast['tlv'][2]['high']; ?>&deg; / </span><?php echo $forecast['tlv'][2]['low']; ?>&deg;</div>
		</div>
	</li>
	<li>
		<div class="title">Jerusalem</div>
		<div class="cont-row">
		<div class="date"><?php echo $today; ?></div>
			<div class="deg">
			<span class="colorSchema_1"><?php echo $forecast['jerusalem'][0]['high']; ?>&deg; / </span><?php echo $forecast['jerusalem'][0]['low']; ?>&deg;</div>
		</div>
		<div class="cont-row">
		<div class="date"><?php echo $tomorrow; ?></div>
			<div class="deg">
			<span class="colorSchema_1"><?php echo $forecast['jerusalem'][1]['high']; ?>&deg; / </span><?php echo $forecast['jerusalem'][1]['low']; ?>&deg;</div>
		</div>
		<div class="cont-row last">
		<div class="date"><?php echo $dayAfterNext; ?></div>
			<div class="deg">
			<span class="colorSchema_1"><?php echo $forecast['jerusalem'][2]['high']; ?>&deg; / </span><?php echo $forecast['jerusalem'][2]['low']; ?>&deg;</div>
		</div>
	</li>
	<li class="last">
		<div class="title">Eilat</div>
		<div class="cont-row">
		<div class="date"><?php echo $today; ?></div>
			<div class="deg">
			<span class="colorSchema_1"><?php echo $forecast['eilat'][0]['high']; ?>&deg; / </span><?php echo $forecast['eilat'][0]['low']; ?>&deg;</div>
		</div>
		<div class="cont-row">
		<div class="date"><?php echo $tomorrow; ?></div>
			<div class="deg">
			<span class="colorSchema_1"><?php echo $forecast['eilat'][1]['high']; ?>&deg; / </span><?php echo $forecast['eilat'][1]['low']; ?>&deg;</div>
		</div>
		<div class="cont-row last">
		<div class="date"><?php echo $dayAfterNext; ?></div>
			<div class="deg">
			<span class="colorSchema_1"><?php echo $forecast['eilat'][2]['high']; ?>&deg; / </span><?php echo $forecast['eilat'][2]['low']; ?>&deg;</div>
		</div>
	</li>
</ul>
<div class="clearAll">
	&nbsp;</div>
<ul class="columns info">
	<li class="first">
		<div class="title">
			<h3 class="time">
				Current time in Israel</h3>
		</div>
		<div class="cont-row last">
		<div id="curr_time"><?php echo $israelTime; ?></div>
			<div>
				<p class="bold">
					Time Zone</p>
				<p id="time_zone">
					IST (UTC + 2) Summer (DST)<br />
					IDT (UTC + 3)</p>
			</div>
		</div>
	</li>
	<li>
		<div class="title">
			<h3 class="currency">
				Currency</h3>
		</div>
		<div class="cont-row bold whiteMe">
		<?php echo $usd; ?> = ₪ 1</div>
		<div class="cont-row bold ">
		<?php echo $brl;  ?> = ₪ 1</div>
		<div class="cont-row bold whiteMe">
		<?php echo $eur; ?> = ₪ 1</div>
		<div class="cont-row last bold">
		<?php echo $ars;  ?> = ₪ 1</div>
	</li>
	<li class="last info-column">
		<div class="title">
			<h3 class="info"><?php echo $contentInfo->title; ?></h3>
		</div>
		<div class="cont-row last">
			<div class="imageContainer">
				<img alt="<?php echo $contentInfo->image->alt; ?>"  width="<?php echo $contentInfo->image->width; ?>" height="<?php echo $contentInfo->image->height; ?>" src="<?php echo $contentInfo->image->path; ?>" />
				<div class="infoContentFrame">
					&nbsp;</div>
			</div>
			<?php echo $contentInfo->content; ?>
		</div>
	</li>
</ul>
<p>
	&nbsp;</p>
