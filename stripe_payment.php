<?php
if (Wo_IsUserPro() === true || $wo['loggedin'] == false || $wo['config']['pro'] == 0) {
	Wo_RedirectSmooth(Wo_SeoLink('index.php?link1=welcome'));
}
$wo['description'] = '';
$wo['keywords']    = '';
$wo['page']        = 'stripe_payment';
$wo['title']       = $wo['config']['siteTitle'];
$wo['content']     = Wo_LoadPage('stripe-payment/content');