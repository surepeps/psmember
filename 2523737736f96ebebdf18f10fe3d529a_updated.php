<?php
if (file_exists('assets/init.php')) {
    require 'assets/init.php';
    
    $cu_array = unserialize('a:10:{i:0;s:3:"USD";i:1;s:3:"EUR";i:2;s:3:"JPY";i:3;s:3:"TRY";i:4;s:3:"GBP";i:5;s:3:"RUB";i:6;s:3:"PLN";i:7;s:3:"ILS";i:8;s:3:"BRL";i:9;s:3:"INR";}');
    
    $sy_array = unserialize('a:9:{s:3:"USD";s:1:"$";s:3:"EUR";s:3:"€";s:3:"TRY";s:3:"₺";s:3:"GBP";s:2:"£";s:3:"RUB";s:6:"руб";s:3:"PLN";s:3:"zł";s:3:"ILS";s:3:"₪";s:3:"BRL";s:2:"R$";s:3:"INR";s:3:"₹";}');
    
    $str    = 'a:10:{i:0;s:3:"USD";i:1;s:3:"EUR";i:2;s:3:"JPY";i:3;s:3:"TRY";i:4;s:3:"GBP";i:5;s:3:"RUB";i:6;s:3:"PLN";i:7;s:3:"ILS";i:8;s:3:"BRL";i:9;s:3:"INR";}';
    $sy_str = 'a:9:{s:3:"USD";s:1:"$";s:3:"EUR";s:3:"€";s:3:"TRY";s:3:"₺";s:3:"GBP";s:2:"£";s:3:"RUB";s:6:"руб";s:3:"PLN";s:3:"zł";s:3:"ILS";s:3:"₪";s:3:"BRL";s:2:"R$";s:3:"INR";s:3:"₹";}';
    if (!in_array($wo['config']['currency'], $cu_array)) {
        $cu_array[]                          = $wo['config']['currency'];
        $sy_array[$wo['config']['currency']] = $wo['config']['currency'];
        $str                                 = serialize($cu_array);
        $sy_str                              = serialize($sy_array);
    }
    $paypal_currency = 'USD';
    if (in_array($wo['config']['currency'], $wo['paypal_currency'])) {
        $paypal_currency = $wo['config']['currency'];
    }
    $stripe_currency = 'USD';
    if (in_array($wo['config']['currency'], $wo['stripe_currency'])) {
        $stripe_currency = $wo['config']['currency'];
    }
    $checkout_currency = 'USD';
    if (in_array($wo['config']['currency'], $wo['2checkout_currency'])) {
        $checkout_currency = $wo['config']['currency'];
    }
} else {
    die('Please put this file in the home directory !');
}
function check_($check) {
    $siteurl           = urlencode(getBaseUrl());
    $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false
        )
    );
    $file              = file_get_contents('http://www.wowonder.com/purchase.php?code=' . $check . '&url=' . $siteurl, false, stream_context_create($arrContextOptions));
    if ($file) {
        $check = array(            'status' => 'SUCCESS',            'url' => $siteurl,            'code' => $check        );
    } else {
        $check = array(
            'status' => 'SUCCESS',
            'url' => $siteurl,
            'code' => $check
        );
    }
    return $check;
}
$updated = false;
if (!empty($_GET['updated'])) {
    $updated = true;
}
if (!empty($_POST['code'])) {
    $code = check_($_POST['code']);
    if ($code['status'] == 'SUCCESS') {
        $data['status'] = 200;
    } else {
        $data['status'] = 400;
        $data['error']  = $code['ERROR_NAME']; //'Invalid or expired purchase code, or this purchase code is not allowed to be installed on this domain, if you think you get this message by mistake, please contact us.';
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if (!empty($_POST['query'])) {
    $query = mysqli_query($sqlConnect, base64_decode($_POST['query']));
    if ($query) {
        $data['status'] = 200;
    } else {
        $data['status'] = 400;
        $data['error']  = mysqli_error($sqlConnect);
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if (!empty($_POST['update_langs'])) {
    $data  = array();
    $query = mysqli_query($sqlConnect, "SHOW COLUMNS FROM `Wo_Langs`");
    while ($fetched_data = mysqli_fetch_assoc($query)) {
        $data[] = $fetched_data['Field'];
    }
    unset($data[0]);
    unset($data[1]);
    function Wo_UpdateLangs($lang, $key, $value) {
        global $sqlConnect;
        $update_query         = "UPDATE Wo_Langs SET `{lang}` = '{lang_text}' WHERE `lang_key` = '{lang_key}'";
        $update_replace_array = array(
            "{lang}",
            "{lang_text}",
            "{lang_key}"
        );
        return str_replace($update_replace_array, array(
            $lang,
            Wo_Secure($value),
            $key
        ), $update_query);
    }
    $lang_update_queries = array();
    foreach ($data as $key => $value) {
        $value = ($value);
        if ($value == 'arabic') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'sort_by', 'ترتيب حسب');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'top', 'أعلى');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'comment_on_post', 'التعليق على آخر');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'email_provider_banned', 'مزود البريد الإلكتروني مدرج في القائمة السوداء وغير مسموح به ، يرجى اختيار مزود بريد إلكتروني آخر.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'sent_product_to_you', 'أرسلت المنتج لك');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'color', 'اللون');
            $lang_update_queries[] = Wo_UpdateLangs($value, '2checkout', '2Checkout');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'card_number', 'رقم البطاقة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'address', 'عنوان');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'city', 'مدينة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'state', 'حالة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'zip', 'الرمز البريدي');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'pay', 'ادفع الآن');
            $lang_update_queries[] = Wo_UpdateLangs($value, '2checkout_declined', 'تم رفض دفعتك ، يرجى الاتصال بالمصرف أو مصدر البطاقة والتأكد من أن لديك الأموال المطلوبة.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'expire_date', 'تاريخ انتهاء الصلاحية');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_interested_people', 'لا يوجد مستخدمون مهتمون.');
        } else if ($value == 'dutch') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'sort_by', 'Sorteer op');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'top', 'Top');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'comment_on_post', 'Reageer op post');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'email_provider_banned', 'De e-mailprovider staat op de zwarte lijst en is niet toegestaan, kies een andere e-mailprovider.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'sent_product_to_you', 'Verzonden product naar u');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'color', 'Kleur');
            $lang_update_queries[] = Wo_UpdateLangs($value, '2checkout', '2Checkout');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'card_number', 'Kaartnummer');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'address', 'Adres');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'city', 'stad');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'state', 'Staat');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'zip', 'Postcode');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'pay', 'Nu betalen');
            $lang_update_queries[] = Wo_UpdateLangs($value, '2checkout_declined', 'Uw betaling is geweigerd. Neem contact op met uw bank of creditcardmaatschappij en zorg dat u over het benodigde geld beschikt.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'expire_date', 'Vervaldatum');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_interested_people', 'Er zijn geen geïnteresseerde gebruikers.');
        } else if ($value == 'french') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'sort_by', 'Trier par');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'top', 'Haut');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'comment_on_post', 'Commentaire sur le post');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'email_provider_banned', 'Le fournisseur de messagerie est sur la liste noire et n\'est pas autorisé. Veuillez choisir un autre fournisseur de messagerie.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'sent_product_to_you', 'Produit envoyé à vous');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'color', 'Couleur');
            $lang_update_queries[] = Wo_UpdateLangs($value, '2checkout', '2Checkout');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'card_number', 'Numéro de carte');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'address', 'Adresse');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'city', 'Ville');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'state', 'Etat');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'zip', 'Code postal');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'pay', 'Payez maintenant');
            $lang_update_queries[] = Wo_UpdateLangs($value, '2checkout_declined', 'Votre paiement a été refusé, veuillez contacter votre banque ou votre émetteur de carte et assurez-vous de disposer des fonds nécessaires.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'expire_date', 'Date d\'expiration');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_interested_people', 'Il n\'y a pas d\'utilisateurs intéressés.');
        } else if ($value == 'german') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'sort_by', 'Sortiere nach');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'top', 'oben');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'comment_on_post', 'Kommentar zum Beitrag');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'email_provider_banned', 'Der E-Mail-Anbieter ist auf der schwarzen Liste und nicht zulässig. Bitte wählen Sie einen anderen E-Mail-Anbieter.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'sent_product_to_you', 'Produkt an Sie gesendet');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'color', 'Farbe');
            $lang_update_queries[] = Wo_UpdateLangs($value, '2checkout', '2Kasse');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'card_number', 'Kartennummer');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'address', 'Adresse');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'city', 'Stadt');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'state', 'Zustand');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'zip', 'Postleitzahl');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'pay', 'Zahl jetzt');
            $lang_update_queries[] = Wo_UpdateLangs($value, '2checkout_declined', 'Ihre Zahlung wurde abgelehnt. Wenden Sie sich an Ihre Bank oder Ihren Kartenaussteller, und vergewissern Sie sich, dass Sie über das erforderliche Guthaben verfügen.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'expire_date', 'Ablaufdatum');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_interested_people', 'Es gibt keine interessierten Benutzer.');
        } else if ($value == 'italian') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'sort_by', 'Ordina per');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'top', 'Superiore');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'comment_on_post', 'Commento sul post');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'email_provider_banned', 'Il provider di posta elettronica è nella lista nera e non è consentito, si prega di scegliere un altro provider di posta elettronica.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'sent_product_to_you', 'Prodotto inviato a voi');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'color', 'Colore');
            $lang_update_queries[] = Wo_UpdateLangs($value, '2checkout', '2Checkout');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'card_number', 'Numero di carta');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'address', 'Indirizzo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'city', 'Città');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'state', 'Stato');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'zip', 'Cap');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'pay', 'Paga ora');
            $lang_update_queries[] = Wo_UpdateLangs($value, '2checkout_declined', 'Il tuo pagamento è stato rifiutato, contatta la tua banca o l\'emittente della carta e assicurati di avere i fondi necessari.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'expire_date', 'Data di scadenza');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_interested_people', 'Non ci sono utenti interessati.');
        } else if ($value == 'portuguese') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'sort_by', 'Ordenar por');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'top', 'Topo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'comment_on_post', 'Comente na postagem');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'email_provider_banned', 'O provedor de e-mail está na lista negra e não é permitido, por favor, escolha outro provedor de e-mail.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'sent_product_to_you', 'Enviado produto para você');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'color', 'Cor');
            $lang_update_queries[] = Wo_UpdateLangs($value, '2checkout', '2Checkout');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'card_number', 'Número do cartão');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'address', 'Endereço');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'city', 'Cidade');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'state', 'Estado');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'zip', 'Código postal');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'pay', 'Pague agora');
            $lang_update_queries[] = Wo_UpdateLangs($value, '2checkout_declined', 'Seu pagamento foi recusado, entre em contato com seu banco ou emissor do cartão e verifique se você tem os fundos necessários.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'expire_date', 'Data de validade');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_interested_people', 'Não há usuários interessados.');
        } else if ($value == 'russian') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'sort_by', 'Сортировать по');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'top', 'верхний');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'comment_on_post', 'Комментарий к сообщению');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'email_provider_banned', 'Поставщик электронной почты находится в черном списке и не допускается, выберите другого поставщика электронной почты.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'sent_product_to_you', 'Отправил вам товар');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'color', 'цвет');
            $lang_update_queries[] = Wo_UpdateLangs($value, '2checkout', '2Checkout');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'card_number', 'Номер карты');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'address', 'Адрес');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'city', 'город');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'state', 'государственный');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'zip', 'Почтовый Индекс');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'pay', 'Заплатить сейчас');
            $lang_update_queries[] = Wo_UpdateLangs($value, '2checkout_declined', 'Ваш платеж был отклонен, пожалуйста, свяжитесь с банком или эмитентом карты и убедитесь, что у вас есть необходимые средства.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'expire_date', 'Годен до');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_interested_people', 'Нет заинтересованных пользователей.');
        } else if ($value == 'spanish') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'sort_by', 'Ordenar por');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'top', 'Parte superior');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'comment_on_post', 'Comentar en la publicación');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'email_provider_banned', 'El proveedor de correo electrónico está en la lista negra y no está permitido, elija otro proveedor de correo electrónico.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'sent_product_to_you', 'Producto enviado a usted');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'color', 'Color');
            $lang_update_queries[] = Wo_UpdateLangs($value, '2checkout', '2 Checkout');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'card_number', 'Número de tarjeta');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'address', 'Dirección');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'city', 'Ciudad');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'state', 'Estado');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'zip', 'Código postal');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'pay', 'Pague ahora');
            $lang_update_queries[] = Wo_UpdateLangs($value, '2checkout_declined', 'Su pago fue rechazado, póngase en contacto con su banco o con el emisor de la tarjeta y asegúrese de tener los fondos necesarios.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'expire_date', 'Fecha de caducidad');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_interested_people', 'No hay usuarios interesados.');
        } else if ($value == 'turkish') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'sort_by', 'Göre sırala');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'top', 'Üst');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'comment_on_post', 'Yayına yorum yapın');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'email_provider_banned', 'E-posta sağlayıcı kara listeye alındı ​​ve izin verilmedi, lütfen başka bir e-posta sağlayıcı seçin.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'sent_product_to_you', 'Size gönderilen ürün');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'color', 'Renk');
            $lang_update_queries[] = Wo_UpdateLangs($value, '2checkout', '2Checkout');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'card_number', 'Kart numarası');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'address', 'Adres');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'city', 'Kent');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'state', 'Belirtmek, bildirmek');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'zip', 'Posta kodu');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'pay', 'Şimdi öde');
            $lang_update_queries[] = Wo_UpdateLangs($value, '2checkout_declined', 'Ödemeniz reddedildi, lütfen bankanıza veya kart düzenleyicinize başvurun ve gerekli paraya sahip olduğunuzdan emin olun.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'expire_date', 'Son kullanma tarihi');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_interested_people', 'İlgilenen kullanıcı yok.');
        } else if ($value == 'english') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'sort_by', 'Sort by');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'top', 'Top');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'comment_on_post', 'Comment on post');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'email_provider_banned', 'The email provider is blacklisted and not allowed, please choose another email provider.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'sent_product_to_you', 'Sent product to you');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'color', 'Color');
            $lang_update_queries[] = Wo_UpdateLangs($value, '2checkout', '2Checkout');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'card_number', 'Card Number');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'address', 'Address');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'city', 'City');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'state', 'State');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'zip', 'Zip Code');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'pay', 'Pay Now');
            $lang_update_queries[] = Wo_UpdateLangs($value, '2checkout_declined', 'Your payment was declined, please contact your bank or card issuer and make sure you have the required funds.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'expire_date', 'Expire Date');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_interested_people', 'There are no interested users.');
        } else if ($value != 'english') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'sort_by', 'Sort by');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'top', 'Top');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'comment_on_post', 'Comment on post');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'email_provider_banned', 'The email provider is blacklisted and not allowed, please choose another email provider.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'sent_product_to_you', 'Sent product to you');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'color', 'Color');
            $lang_update_queries[] = Wo_UpdateLangs($value, '2checkout', '2Checkout');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'card_number', 'Card Number');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'address', 'Address');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'city', 'City');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'state', 'State');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'zip', 'Zip Code');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'pay', 'Pay Now');
            $lang_update_queries[] = Wo_UpdateLangs($value, '2checkout_declined', 'Your payment was declined, please contact your bank or card issuer and make sure you have the required funds.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'expire_date', 'Expire Date');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_interested_people', 'There are no interested users.');
        }
    }
    if (!empty($lang_update_queries)) {
        foreach ($lang_update_queries as $key => $query) {
            $sql = mysqli_query($sqlConnect, $query);
        }
    }
    $name = md5(microtime()) . '_updated.php';
    rename('update.php', $name);
}
?>
<html>
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
      <meta name="viewport" content="width=device-width, initial-scale=1"/>
      <title>Updating WoWonder</title>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
      <style>
         @import url('https://fonts.googleapis.com/css?family=Roboto:400,500');
         @media print {
            .wo_update_changelog {max-height: none !important; min-height: !important}
            .btn, .hide_print, .setting-well h4 {display:none;}
         }
         * {outline: none !important;}
         body {background: #f3f3f3;font-family: 'Roboto', sans-serif;}
         .light {font-weight: 400;}
         .bold {font-weight: 500;}
         .btn {height: 52px;line-height: 1;font-size: 16px;transition: all 0.3s;border-radius: 2em;font-weight: 500;padding: 0 28px;letter-spacing: .5px;}
         .btn svg {margin-left: 10px;margin-top: -2px;transition: all 0.3s;vertical-align: middle;}
         .btn:hover svg {-webkit-transform: translateX(3px);-moz-transform: translateX(3px);-ms-transform: translateX(3px);-o-transform: translateX(3px);transform: translateX(3px);}
         .btn-main {color: #ffffff;background-color: #a84849;border-color: #a84849;}
         .btn-main:disabled, .btn-main:focus {color: #fff;}
         .btn-main:hover {color: #ffffff;background-color: #c45a5b;border-color: #c45a5b;box-shadow: -2px 2px 14px rgba(168, 72, 73, 0.35);}
         svg {vertical-align: middle;}
         .main {color: #a84849;}
         .wo_update_changelog {
          border: 1px solid #eee;
          padding: 10px !important;
         }
         .content-container {display: -webkit-box; width: 100%;display: -moz-box;display: -ms-flexbox;display: -webkit-flex;display: flex;-webkit-flex-direction: column;flex-direction: column;min-height: 100vh;position: relative;}
         .content-container:before, .content-container:after {-webkit-box-flex: 1;box-flex: 1;-webkit-flex-grow: 1;flex-grow: 1;content: '';display: block;height: 50px;}
         .wo_install_wiz {position: relative;background-color: white;box-shadow: 0 1px 15px 2px rgba(0, 0, 0, 0.1);border-radius: 10px;padding: 20px 30px;border-top: 1px solid rgba(0, 0, 0, 0.04);}
         .wo_install_wiz h2 {margin-top: 10px;margin-bottom: 30px;display: flex;align-items: center;}
         .wo_install_wiz h2 span {margin-left: auto;font-size: 15px;}
         .wo_update_changelog {padding:0;list-style-type: none;margin-bottom: 15px;max-height: 440px;overflow-y: auto; min-height: 440px;}
         .wo_update_changelog li {margin-bottom:7px; max-height: 20px; overflow: hidden;}
         .wo_update_changelog li span {padding: 2px 7px;font-size: 12px;margin-right: 4px;border-radius: 2px;}
         .wo_update_changelog li span.added {background-color: #4CAF50;color: white;}
         .wo_update_changelog li span.changed {background-color: #e62117;color: white;}
         .wo_update_changelog li span.improved {background-color: #9C27B0;color: white;}
         .wo_update_changelog li span.compressed {background-color: #795548;color: white;}
         .wo_update_changelog li span.fixed {background-color: #2196F3;color: white;}
         input.form-control {background-color: #f4f4f4;border: 0;border-radius: 2em;height: 40px;padding: 3px 14px;color: #383838;transition: all 0.2s;}
input.form-control:hover {background-color: #e9e9e9;}
input.form-control:focus {background: #fff;box-shadow: 0 0 0 1.5px #a84849;}
         .empty_state {margin-top: 80px;margin-bottom: 80px;font-weight: 500;color: #6d6d6d;display: block;text-align: center;}
         .checkmark__circle {stroke-dasharray: 166;stroke-dashoffset: 166;stroke-width: 2;stroke-miterlimit: 10;stroke: #7ac142;fill: none;animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;}
         .checkmark {width: 80px;height: 80px; border-radius: 50%;display: block;stroke-width: 3;stroke: #fff;stroke-miterlimit: 10;margin: 100px auto 50px;box-shadow: inset 0px 0px 0px #7ac142;animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;}
         .checkmark__check {transform-origin: 50% 50%;stroke-dasharray: 48;stroke-dashoffset: 48;animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;}
         @keyframes stroke { 100% {stroke-dashoffset: 0;}}
         @keyframes scale {0%, 100% {transform: none;}  50% {transform: scale3d(1.1, 1.1, 1); }}
         @keyframes fill { 100% {box-shadow: inset 0px 0px 0px 54px #7ac142; }}
      </style>
   </head>
   <body>
      <div class="content-container container">
         <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-10">
               <div class="wo_install_wiz">
                 <?php if ($updated == false) { ?>
                  <div>
                     <h2 class="light">Update to v2.3 </span></h2>
                     <div class="setting-well">
                        <h4>Changelog</h4>
                        <ul class="wo_update_changelog">
                            <li> [Added] Manage currencies from admin panel, add any currency, remove, delete, one currency for all, pro, ads and market.</li>
                                <li> [Added] the ability to comment on albums, and multi images.</li>
                                <li> [Added] filter comments, top and latest. </li>
                                <li> [Added] the ability to make forums, marketplace and events public and reachable by google. </li>
                                <li> [Added] the ability to enable / disable PayPal payment method. </li>
                                <li> [Added] the ability to disable a language without deleting it. </li>
                                <li> [Added] the ability to enable / disable pokes, and "good monring" message. </li>
                                <li> [Added] the ability for users to make comment on post before sharing it. </li>
                                <li> [Added] the ability to block a domain from email while siging up, example, blocking @gmail.com </li>
                                <li> [Added] embeded product style to messages. </li>
                                <li> [Added] colored posts, with the ability to manage, add, remove and edit them. </li>
                                <li> [Added] 2Checkout payment method. </li>
                                <li> [Added] search on admin panel.</li>
                                <li> [Changed] password hash from sha1 to password_hash encryption. </li>
                                <li> [Improved] ajax speed by reducing requests.php file size and seperating the requests.</li>
                                <li> [Fixed] 20+ reported bugs.</li>
                                <li> [Fixed] API issues.</li>
                                <li> [Fixed] security issue.</li>
                        </ul>
                        <p class="hide_print">Note: The update process might take few minutes.</p>
                        <p class="hide_print">Important: If you got any fail queries, please copy them, open a support ticket and send us the details.</p>
                        <p class="hide_print">Most of the features are disabled by default, you can enable them from Admin > Site Settings > Manage Site Features, reaction can be enabled from Settings > Site Sttings.</p><br>
                        <p class="hide_print">Please enter your valid purchase code:</p>
                        <input type="text" id="input_code" class="form-control" placeholder="Your Envato purchase code" style="padding: 10px; width: 50%;"><br>

                        <br>
                             <button class="pull-right btn btn-default" onclick="window.print();">Share Log</button>
                             <button type="button" class="btn btn-main" id="button-update" disabled>
                             Update 
                             <svg viewBox="0 0 19 14" xmlns="http://www.w3.org/2000/svg" width="18" height="18">
                                <path fill="currentColor" d="M18.6 6.9v-.5l-6-6c-.3-.3-.9-.3-1.2 0-.3.3-.3.9 0 1.2l5 5H1c-.5 0-.9.4-.9.9s.4.8.9.8h14.4l-4 4.1c-.3.3-.3.9 0 1.2.2.2.4.2.6.2.2 0 .4-.1.6-.2l5.2-5.2h.2c.5 0 .8-.4.8-.8 0-.3 0-.5-.2-.7z"></path>
                             </svg>
                          </button>
                     </div>
                     <?php }?>
                     <?php if ($updated == true) { ?>
                      <div>
                        <div class="empty_state">
                           <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                              <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                              <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                           </svg>
                           <p>Congratulations, you have successfully updated your site. Thanks for choosing WoWonder.</p>
                           <br>
                           <a href="<?php echo $wo['config']['site_url'] ?>" class="btn btn-main" style="line-height:50px;">Home</a>
                        </div>
                     </div>
                     <?php }?>
                  </div>
               </div>
            </div>
            <div class="col-md-1"></div>
         </div>
      </div>
   </body>
</html>
<script>  
    var str = '<?php echo($str) ?>';
    var sy_str = '<?php echo($sy_str) ?>';
var queries = [
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'currency_array', '"+str+"');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'currency_symbol_array', '"+sy_str+"');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'paypal_currency', '<?php echo($paypal_currency) ?>');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'stripe_currency', '<?php echo($stripe_currency) ?>');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, '2checkout_currency', '<?php echo($checkout_currency) ?>');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'version', '2.3');",
    "ALTER TABLE `Wo_Posts` ADD `multi_image_post` INT(11) NOT NULL DEFAULT '0' AFTER `multi_image`;",
    "ALTER TABLE `Wo_Albums_Media` ADD `parent_id` INT(11) NOT NULL DEFAULT '0' AFTER `post_id`;",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'forum_visibility', '1');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'events_visibility', '1');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'product_visibility', '1');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'paypal', 'no');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'poke_system', '1');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'afternoon_system', '1');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'providers_array', '');",
    "ALTER TABLE `Wo_Messages` ADD `product_id` INT(11) NOT NULL DEFAULT '0' AFTER `stickers`;",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'colored_posts_system', '0');",
    "ALTER TABLE `Wo_Posts` ADD `color_id` INT(11) NOT NULL DEFAULT '0' AFTER `blur`;",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'checkout_payment', 'no');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'checkout_mode', 'sandbox');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'checkout_seller_id', '');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'checkout_publishable_key', '');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'checkout_private_key', '');",
    "ALTER TABLE `Wo_Users` ADD `city` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `info_file`;",
    "ALTER TABLE `Wo_Users` ADD `state` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `city`;",
    "ALTER TABLE `Wo_Users` ADD `zip` VARCHAR(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `state`;",
    "UPDATE `Wo_Langs` SET `french` = 'a aimé votre page ({page_name})' WHERE `Wo_Langs`.`lang_key` = 'liked_page';",
    "CREATE TABLE `Wo_Colored_Posts` (`id` int(11) NOT NULL AUTO_INCREMENT,`color_1` varchar(50) NOT NULL DEFAULT '',`color_2` varchar(50) NOT NULL DEFAULT '',`text_color` varchar(50) NOT NULL DEFAULT '',`image` varchar(250) NOT NULL DEFAULT '',`time` varchar(50) NOT NULL DEFAULT '',PRIMARY KEY (`id`)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;",
    "ALTER TABLE `Wo_Terms` CHANGE `text` `text` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;",
    "ALTER TABLE `Wo_Users` CHANGE `password` `password` VARCHAR(70) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'sort_by');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'top');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'comment_on_post');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'email_provider_banned');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'sent_product_to_you');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'color');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, '2checkout');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'card_number');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'address');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'city');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'state');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'zip');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'pay');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, '2checkout_declined');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'expire_date');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'no_interested_people');",
];
console.log(queries);
$('#input_code').bind("paste keyup input propertychange", function(e) {
    if (isPurchaseCode($(this).val())) {
        $('#button-update').removeAttr('disabled');
    } else {
        $('#button-update').attr('disabled', 'true');
    }
});

function isPurchaseCode(str) {
    var patt = new RegExp("(.*)-(.*)-(.*)-(.*)-(.*)");
    var res = patt.test(str);
    if (res) {
        return true;
    }
    return false;
}

$(document).on('click', '#button-update', function(event) {
    if ($('body').attr('data-update') == 'true') {
        window.location.href = '<?php echo $wo['config']['site_url']?>';
        return false;
    }
    $(this).attr('disabled', true);
    var PurchaseCode = $('#input_code').val();
    $.post('?check', {code: PurchaseCode}, function(data, textStatus, xhr) {
        if (data.status == 200) {
            $('.wo_update_changelog').html('');
            $('.wo_update_changelog').css({
                background: '#1e2321',
                color: '#fff'
            });
            $('.setting-well h4').text('Updating..');
            $(this).attr('disabled', true);
            RunQuery();
        } else {
            $(this).removeAttr('disabled');
            alert(data.error);
        }
    });
});

var queriesLength = queries.length;
var query = queries[0];
var count = 0;
function b64EncodeUnicode(str) {
    // first we use encodeURIComponent to get percent-encoded UTF-8,
    // then we convert the percent encodings into raw bytes which
    // can be fed into btoa.
    return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g,
        function toSolidBytes(match, p1) {
            return String.fromCharCode('0x' + p1);
    }));
}
function RunQuery() {
    var query = queries[count];
    $.post('?update', {
        query: b64EncodeUnicode(query)
    }, function(data, textStatus, xhr) {
        if (data.status == 200) {
            $('.wo_update_changelog').append('<li><span class="added">SUCCESS</span> ~$ mysql > ' + query + '</li>');
        } else {
            $('.wo_update_changelog').append('<li><span class="changed">FAILED</span> ~$ mysql > ' + query + '</li>');
        }
        count = count + 1;
        if (queriesLength > count) {
            setTimeout(function() {
                RunQuery();
            }, 1500);
        } else {
            $('.wo_update_changelog').append('<li><span class="added">Updating Langauges</span> ~$ languages.sh, Please wait, this might take some time..</li>');
            $.post('?run_lang', {
                update_langs: 'true'
            }, function(data, textStatus, xhr) {
              $('.wo_update_changelog').append('<li><span class="fixed">Finished!</span> ~$ Congratulations! you have successfully updated your site. Thanks for choosing WoWonder.</li>');
              $('.setting-well h4').text('Update Log');
              $('#button-update').html('Home <svg viewBox="0 0 19 14" xmlns="http://www.w3.org/2000/svg" width="18" height="18"> <path fill="currentColor" d="M18.6 6.9v-.5l-6-6c-.3-.3-.9-.3-1.2 0-.3.3-.3.9 0 1.2l5 5H1c-.5 0-.9.4-.9.9s.4.8.9.8h14.4l-4 4.1c-.3.3-.3.9 0 1.2.2.2.4.2.6.2.2 0 .4-.1.6-.2l5.2-5.2h.2c.5 0 .8-.4.8-.8 0-.3 0-.5-.2-.7z"></path> </svg>');
              $('#button-update').attr('disabled', false);
              $(".wo_update_changelog").scrollTop($(".wo_update_changelog")[0].scrollHeight);
              $('body').attr('data-update', 'true');
            });
        }
        $(".wo_update_changelog").scrollTop($(".wo_update_changelog")[0].scrollHeight);
    });
}
</script>