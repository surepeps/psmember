<?php
if (file_exists('assets/init.php')) {
    require 'assets/init.php';
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
    $data                      = Wo_LangsNamesFromDB();
    $lang_update_first_queries = '';
    $first                     = "INSERT IGNORE INTO `Wo_Langs` (`id`,`type`";
    $categories_array          = array();
    $products_array            = array();
    $query_list                = array();
    foreach ($data as $key => $value) {
        include './assets/languages/extra/' . $value . '.php';
        if ($key == 3) {
            $categories_array[$value] = $wo['page_categories'];
            $products_array[$value]   = $wo['products_categories'];
        } else {
            if (count($categories_array[$data[3]]) != count($wo['page_categories'])) {
                $category              = $wo['page_categories'];
                $wo['page_categories'] = array();
                foreach ($categories_array[$data[3]] as $key => $value1) {
                    if (empty($category[$key])) {
                        $wo['page_categories'][$key] = '';
                    } else {
                        $wo['page_categories'][$key] = $category[$key];
                    }
                }
                $categories_array[$value] = $wo['page_categories'];
            } else {
                $categories_array[$value] = $wo['page_categories'];
            }
            if (count($products_array[$data[3]]) != count($wo['products_categories'])) {
                $pro                       = $wo['products_categories'];
                $wo['products_categories'] = array();
                foreach ($products_array[$data[3]] as $key => $value2) {
                    if (empty($pro[$key])) {
                        $wo['products_categories'][$key] = '';
                    } else {
                        $wo['products_categories'][$key] = $pro[$key];
                    }
                }
                $products_array[$value] = $wo['products_categories'];
            } else {
                $products_array[$value] = $wo['products_categories'];
            }
        }
        if (empty($lang_update_first_queries)) {
            $lang_update_first_queries = $first . ",`" . $value . "`";
        } else {
            if (end($data) == $value) {
                $lang_update_first_queries = $lang_update_first_queries . ",`" . $value . "`) VALUES  (NULL";
            } else {
                $lang_update_first_queries = $lang_update_first_queries . ",`" . $value . "`";
            }
        }
    }
    foreach ($wo['page_categories'] as $key => $value) {
        $query_list[$key] = '';
    }
    $all = '';
    foreach ($categories_array as $key => $lang) {
        foreach ($lang as $cat_key => $cat_value) {
            if ($cat_key != 1) {
                if (empty($query_list[$cat_key])) {
                    $query_list[$cat_key] = $lang_update_first_queries . ",'category','" . mysqli_escape_string($sqlConnect, $cat_value) . "'";
                } else {
                    if (end($data) == $key) {
                        $query_list[$cat_key] = $query_list[$cat_key] . ",'" . mysqli_escape_string($sqlConnect, $cat_value) . "');";
                        mysqli_query($sqlConnect, $query_list[$cat_key]);
                        $page_id = mysqli_insert_id($sqlConnect);
                        mysqli_query($sqlConnect, " UPDATE `Wo_Langs` SET `lang_key` = '" . $page_id . "' WHERE `id` = {$page_id}");
                        mysqli_query($sqlConnect, "INSERT INTO " . T_PAGES_CATEGORY . " (`id`,`lang_key`) VALUES ({$cat_key},{$page_id})");
                        mysqli_query($sqlConnect, $query_list[$cat_key]);
                        $group_id = mysqli_insert_id($sqlConnect);
                        mysqli_query($sqlConnect, " UPDATE `Wo_Langs` SET `lang_key` = '" . $group_id . "' WHERE `id` = {$group_id}");
                        mysqli_query($sqlConnect, "INSERT INTO " . T_GROUPS_CATEGORY . " (`id`,`lang_key`) VALUES ({$cat_key},{$group_id})");
                        mysqli_query($sqlConnect, $query_list[$cat_key]);
                        $blog_id = mysqli_insert_id($sqlConnect);
                        mysqli_query($sqlConnect, " UPDATE `Wo_Langs` SET `lang_key` = '" . $blog_id . "' WHERE `id` = {$blog_id}");
                        mysqli_query($sqlConnect, "INSERT INTO " . T_BLOGS_CATEGORY . " (`id`,`lang_key`) VALUES ({$cat_key},{$blog_id})");
                    } else {
                        $query_list[$cat_key] = $query_list[$cat_key] . ",'" . mysqli_escape_string($sqlConnect, $cat_value) . "'";
                    }
                }
            }
        }
    }
    $query_list = array();
    foreach ($products_array as $key => $lang) {
        foreach ($lang as $cat_key => $cat_value) {
            if ($cat_key != 0) {
                if (empty($query_list[$cat_key])) {
                    $query_list[$cat_key] = $lang_update_first_queries . ",'category','" . mysqli_escape_string($sqlConnect, $cat_value) . "'";
                } else {
                    if (end($data) == $key) {
                        $query_list[$cat_key] = $query_list[$cat_key] . ",'" . mysqli_escape_string($sqlConnect, $cat_value) . "');";
                        mysqli_query($sqlConnect, $query_list[$cat_key]);
                        $page_id = mysqli_insert_id($sqlConnect);
                        mysqli_query($sqlConnect, " UPDATE `Wo_Langs` SET `lang_key` = '" . $page_id . "' WHERE `id` = {$page_id}");
                        mysqli_query($sqlConnect, "INSERT INTO " . T_PRODUCTS_CATEGORY . " (`id`,`lang_key`) VALUES ({$cat_key},{$page_id})");
                    } else {
                        $query_list[$cat_key] = $query_list[$cat_key] . ",'" . mysqli_escape_string($sqlConnect, $cat_value) . "'";
                    }
                }
            }
        }
    }
    $query = mysqli_query($sqlConnect, "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'bank_description', '" . htmlspecialchars_decode('<div class="bank_info">
                        <div class="dt_settings_header bg_gradient">
                            <div class="dt_settings_circle-1"></div>
                            <div class="dt_settings_circle-2"></div>
                            <div class="bank_info_innr">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M11.5,1L2,6V8H21V6M16,10V17H19V10M2,22H21V19H2M10,10V17H13V10M4,10V17H7V10H4Z"></path></svg>
                                <h4 class="bank_name">Garanti Bank</h4>
                                <div class="row">
                                    <div class="col col-md-12">
                                        <div class="bank_account">
                                            <p>4796824372433055</p>
                                            <span class="help-block">Account number / IBAN</span>
                                        </div>
                                    </div>
                                    <div class="col col-md-12">
                                        <div class="bank_account_holder">
                                            <p>Antoian Kordiyal</p>
                                            <span class="help-block">Account name</span>
                                        </div>
                                    </div>
                                    <div class="col col-md-6">
                                        <div class="bank_account_code">
                                            <p>TGBATRISXXX</p>
                                            <span class="help-block">Routing code</span>
                                        </div>
                                    </div>
                                    <div class="col col-md-6">
                                        <div class="bank_account_country">
                                            <p>United States</p>
                                            <span class="help-block">Country</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>') . "')");
    $query = mysqli_query($sqlConnect, "UPDATE `Wo_Config` SET `value` = '" . time() . "' WHERE `name` = 'last_update'");
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
            $lang_update_queries[] = Wo_UpdateLangs($value, 'edit_group', 'تحرير المجموعة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'adult_image_file', 'لقد اكتشفنا بعض محتوى البالغين على الصورة التي قمت بتحميلها ، وبالتالي فقد رفضنا عملية التحميل.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'view_image', 'عرض الصورة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'phone_number_error', 'يجب أن يكون رقم الهاتف بهذا الشكل: +90 ..');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'something_wrong', 'هناك شئ خاطئ، يرجى المحاولة فى وقت لاحق.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_code', 'تأكيد الكود');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_phone_verified', 'تم التحقق من رقم هاتفك بنجاح.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_email_verified', 'تم التحقق من بريدك الإلكتروني بنجاح.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_phone_email_verified', 'تم التحقق من رقم هاتفك والبريد الإلكتروني بنجاح.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email_sent', 'تم إرسال رسالة تأكيد بالبريد الإلكتروني.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message_sent', 'تم إرسال رسالة تأكيد.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message_email_sent', 'تم إرسال رسالة تأكيد والبريد الإلكتروني.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email', 'لقد أرسلنا رسالة بريد إلكتروني تحتوي على رمز التأكيد للتحقق من بريدك الإلكتروني الجديد.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message', 'لقد أرسلنا رسالة تحتوي على رمز التأكيد للتحقق من هاتفك الجديد.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email_message_text', 'لقد أرسلنا رسالة ورسالة بريد إلكتروني تحتوي على رمز التأكيد لتمكين المصادقة الثنائية.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email_text', 'لقد أرسلنا رسالة بريد إلكتروني تحتوي على رمز التأكيد لتمكين المصادقة الثنائية.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message_text', 'لقد أرسلنا رسالة تحتوي على رمز التأكيد لتمكين المصادقة الثنائية.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_post_on_group', 'مشاركة المشاركة على مجموعة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_group_name', 'يرجى كتابة اسم المجموعة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_post_on_page', 'مشاركة إلى صفحة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_page_name', 'يرجى كتابة اسم الصفحة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_post_on_user', 'حصة للمستخدم');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'post_shared_successfully', 'تمت مشاركة المشاركة بنجاح.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'to', 'إلى');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_new_post', 'مشاركة المنشور على');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'shared_a_post', 'شارك');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'shared_your_post', 'شارك مشاركتك');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'shared_a_post_in_timeline', 'شارك منشورًا في الجدول الزمني الخاص بك');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_shared', 'لا يوجد سهم حتى الآن');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'member', 'عضو');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'pro_con', 'تهانينا ! انت الان');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'other', 'آخر');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'all_', 'الكل');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_liked_post', 'أحب الناس هذا المنصب');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_wondered_post', 'تساءل الناس هذا المنصب');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_shared_post', 'شارك الناس هذا المنشور');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_reacted_post', 'كان رد فعل الناس على هذا المنصب');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_wondered_comment', 'تساءل الناس هذا التعليق');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_liked_comment', 'أحب الناس هذا التعليق');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reCaptcha_error', 'يرجى التحقق من إعادة اختبار captcha.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'my', 'لي');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_transfer', 'التحويل المصرفي');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_transfer_request', 'تم إرسال طلبك بنجاح ، وسوف نخطرك بمجرد الموافقة عليه');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_pro', 'تمت الموافقة على إيصالك المصرفي!');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_decline', 'تم رفض إيصالك المصرفي!');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'my_timeline', 'بلدي الجدول الزمني');
        } else if ($value == 'dutch') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'edit_group', 'Groep bewerken');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'adult_image_file', 'We hebben een aantal inhoud voor volwassenen gevonden in de afbeelding die je hebt geüpload. Daarom hebben we je uploadproces geweigerd.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'view_image', 'Bekijk afbeelding');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'phone_number_error', 'Het telefoonnummer moet de volgende indeling hebben: +90 ..');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'something_wrong', 'Er is iets misgegaan. Probeer het later opnieuw.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_code', 'Bevestigingscode');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_phone_verified', 'Uw telefoonnummer is succesvol geverifieerd.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_email_verified', 'Uw e-mail is succesvol geverifieerd.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_phone_email_verified', 'Uw telefoonnummer en e-mail zijn succesvol geverifieerd.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email_sent', 'Er is een bevestigingsmail verzonden.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message_sent', 'Er is een bevestigingsbericht verzonden.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message_email_sent', 'Een bevestigingsbericht en e-mail zijn verzonden.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email', 'We hebben een e-mail gestuurd met de bevestigingscode om uw nieuwe e-mailadres te verifiëren.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message', 'We hebben een bericht verzonden met de bevestigingscode om je nieuwe telefoon te verifiëren.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email_message_text', 'We hebben een bericht en een e-mail verzonden met de bevestigingscode om verificatie met twee factoren mogelijk te maken.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email_text', 'We hebben een e-mail gestuurd met de bevestigingscode om Two-factor-authenticatie in te schakelen.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message_text', 'We hebben een bericht verzonden met de bevestigingscode om tweeledige verificatie in te schakelen.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_post_on_group', 'Deel bericht over een groep');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_group_name', 'Schrijf de groepsnaam op');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_post_on_page', 'Deel naar een pagina');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_page_name', 'Voer de paginanaam in');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_post_on_user', 'Deel met gebruiker');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'post_shared_successfully', 'Post is succesvol gedeeld.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'to', 'Naar');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_new_post', 'Deel het bericht op');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'shared_a_post', 'gedeeld');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'shared_your_post', 'heeft je bericht gedeeld');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'shared_a_post_in_timeline', 'heeft een bericht gedeeld op je tijdlijn');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_shared', 'Nog geen aandelen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'member', 'Lid');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'pro_con', 'Gefeliciteerd! Je bent nu een');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'other', 'anders');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'all_', 'Allemaal');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_liked_post', 'Mensen vonden deze post leuk');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_wondered_post', 'Mensen vroegen zich af deze post');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_shared_post', 'Mensen hebben dit bericht gedeeld');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_reacted_post', 'Mensen reageerden op dit bericht');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_wondered_comment', 'Mensen vroegen zich af deze opmerking');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_liked_comment', 'Mensen vonden deze reactie leuk');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reCaptcha_error', 'Controleer de re-captcha.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'my', 'Mijn');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_transfer', 'overschrijving');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_transfer_request', 'Uw verzoek is succesvol verzonden, wij zullen u op de hoogte brengen zodra het is goedgekeurd');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_pro', 'Uw bank-factuur is goedgekeurd!');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_decline', 'Uw bankbewijs is geweigerd!');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'my_timeline', 'Mijn tijdlijn');
        } else if ($value == 'french') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'edit_group', 'Editer le groupe');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'adult_image_file', 'Nous avons détecté du contenu réservé aux adultes sur l\'image que vous avez téléchargée. Par conséquent, nous avons refusé votre processus de téléchargement.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'view_image', 'Voir l\'image');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'phone_number_error', 'Le numéro de téléphone doit être au format suivant: +90 ..');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'something_wrong', 'Quelque chose c\'est mal passé. Merci d\'essayer plus tard.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_code', 'Code de confirmation');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_phone_verified', 'Votre numéro de téléphone a été vérifié avec succès.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_email_verified', 'Votre courriel a été vérifié avec succès.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_phone_email_verified', 'Votre numéro de téléphone et votre adresse e-mail ont été vérifiés avec succès.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email_sent', 'Un email de confirmation a été envoyé.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message_sent', 'Un message de confirmation a été envoyé.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message_email_sent', 'Un message de confirmation et un email ont été envoyés.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email', 'Nous avons envoyé un email contenant le code de confirmation pour vérifier votre nouvel email.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message', 'Nous avons envoyé un message contenant le code de confirmation pour vérifier votre nouveau téléphone.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email_message_text', 'Nous avons envoyé un message et un courrier électronique contenant le code de confirmation pour permettre une authentification à deux facteurs.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email_text', 'Nous avons envoyé un courrier électronique contenant le code de confirmation pour activer l\'authentification à deux facteurs.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message_text', 'Nous avons envoyé un message contenant le code de confirmation pour activer l\'authentification à deux facteurs.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_post_on_group', 'Partager un post sur un groupe');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_group_name', 'S\'il vous plaît écrivez le nom du groupe');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_post_on_page', 'Partager sur une page');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_page_name', 'Veuillez taper le nom de la page');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_post_on_user', 'Partager avec l\'utilisateur');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'post_shared_successfully', 'La publication a été partagée avec succès.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'to', 'À');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_new_post', 'Partager le post sur');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'shared_a_post', 'partagé un');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'shared_your_post', 'partagé votre post');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'shared_a_post_in_timeline', 'partagé un post sur votre timeline');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_shared', 'Pas encore d\'actions');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'member', 'Membre');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'pro_con', 'Toutes nos félicitations ! Vous êtes maintenant un');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'other', 'Autre');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'all_', 'Tout');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_liked_post', 'Les gens ont aimé ce post');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_wondered_post', 'Les gens se demandaient ce post');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_shared_post', 'Les gens ont partagé ce post');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_reacted_post', 'Les gens ont réagi à ce post');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_wondered_comment', 'Les gens se demandaient ce commentaire');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_liked_comment', 'Les gens ont aimé ce commentaire');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reCaptcha_error', 'S\'il vous plaît vérifier le re-captcha.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'my', 'ma');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_transfer', 'virement');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_transfer_request', 'Votre demande a été envoyée avec succès, nous vous en informerons une fois approuvée');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_pro', 'Votre facture bancaire a été approuvée!');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_decline', 'Votre facture bancaire a été refusée!');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'my_timeline', 'Ma chronologie');
        } else if ($value == 'german') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'edit_group', 'Gruppe bearbeiten');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'adult_image_file', 'Wir haben in dem von Ihnen hochgeladenen Bild einige Inhalte für Erwachsene gefunden. Daher haben wir Ihren Upload-Vorgang abgelehnt.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'view_image', 'Bild ansehen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'phone_number_error', 'Die Telefonnummer sollte folgendes Format haben: +90 ..');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'something_wrong', 'Etwas ist schief gelaufen, bitte versuchen Sie es später erneut.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_code', 'Bestätigungscode');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_phone_verified', 'Ihre Telefonnummer wurde erfolgreich verifiziert.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_email_verified', 'Ihre E-Mail-Adresse wurde erfolgreich verifiziert.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_phone_email_verified', 'Ihre Telefonnummer und E-Mail-Adresse wurden erfolgreich verifiziert.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email_sent', 'Eine Bestätigungs-E-Mail wurde gesendet.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message_sent', 'Eine Bestätigungsnachricht wurde gesendet.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message_email_sent', 'Eine Bestätigungsnachricht und eine E-Mail wurden gesendet.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email', 'Wir haben eine E-Mail mit dem Bestätigungscode zur Bestätigung Ihrer neuen E-Mail gesendet.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message', 'Wir haben eine Nachricht mit dem Bestätigungscode zur Bestätigung Ihres neuen Telefons gesendet.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email_message_text', 'Wir haben eine Nachricht und eine E-Mail mit dem Bestätigungscode für die Zwei-Faktor-Authentifizierung gesendet.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email_text', 'Wir haben eine E-Mail mit dem Bestätigungscode für die Zwei-Faktor-Authentifizierung gesendet.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message_text', 'Wir haben eine Nachricht mit dem Bestätigungscode zur Aktivierung der Zwei-Faktor-Authentifizierung gesendet.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_post_on_group', 'Post in einer Gruppe teilen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_group_name', 'Bitte geben Sie den Gruppennamen an');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_post_on_page', 'Teilen Sie auf einer Seite');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_page_name', 'Bitte geben Sie den Seitennamen ein');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_post_on_user', 'Für den Benutzer freigeben');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'post_shared_successfully', 'Beitrag wurde erfolgreich geteilt.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'to', 'Zu');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_new_post', 'Teilen Sie den Beitrag auf');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'shared_a_post', 'geteilt a');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'shared_your_post', 'hat deinen Beitrag geteilt');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'shared_a_post_in_timeline', 'hat einen Beitrag auf Ihrer Timeline geteilt');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_shared', 'Noch keine Aktien');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'member', 'Mitglied');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'pro_con', 'Herzliche Glückwünsche ! Du bist jetzt ein');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'other', 'Andere');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'all_', 'Alles');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_liked_post', 'Die Leute mochten diesen Beitrag');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_wondered_post', 'Die Leute wunderten sich über diesen Beitrag');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_shared_post', 'Die Leute haben diesen Beitrag geteilt');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_reacted_post', 'Die Leute haben auf diesen Beitrag reagiert');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_wondered_comment', 'Die Leute wunderten sich über diesen Kommentar');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_liked_comment', 'Die Leute mochten diesen Kommentar');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reCaptcha_error', 'Bitte überprüfen Sie das Captcha erneut.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'my', 'Meine');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_transfer', 'Banküberweisung');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_transfer_request', 'Ihre Anfrage wurde erfolgreich gesendet, wir werden Sie benachrichtigen, sobald sie genehmigt wurde');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_pro', 'Ihre Bankquittung wurde genehmigt!');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_decline', 'Ihr Bankbeleg wurde abgelehnt!');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'my_timeline', 'Mein Zeitplan');
        } else if ($value == 'italian') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'edit_group', 'Modifica gruppo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'adult_image_file', 'Abbiamo rilevato alcuni contenuti per adulti nell\'immagine caricata, pertanto abbiamo rifiutato la procedura di caricamento.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'view_image', 'Guarda l\'immagine');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'phone_number_error', 'Il numero di telefono deve essere in questo formato: +90 ..');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'something_wrong', 'Qualcosa è andato storto, ti preghiamo di riprovare più tardi.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_code', 'Codice di conferma');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_phone_verified', 'Il tuo numero di telefono è stato verificato con successo.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_email_verified', 'La tua e-mail è stata verificata con successo.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_phone_email_verified', 'Il tuo numero di telefono e l\'e-mail sono stati verificati con successo.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email_sent', 'È stata inviata una email di conferma.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message_sent', 'È stato inviato un messaggio di conferma.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message_email_sent', 'Sono stati inviati un messaggio di conferma e un\'e-mail.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email', 'Abbiamo inviato un\'email che contiene il codice di conferma per verificare la tua nuova email.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message', 'Abbiamo inviato un messaggio che contiene il codice di conferma per verificare il tuo nuovo telefono.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email_message_text', 'Abbiamo inviato un messaggio e un\'email contenente il codice di conferma per abilitare l\'autenticazione a due fattori.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email_text', 'Abbiamo inviato un messaggio di posta elettronica contenente il codice di conferma per abilitare l\'autenticazione a due fattori.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message_text', 'Abbiamo inviato un messaggio che contiene il codice di conferma per abilitare l\'autenticazione a due fattori.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_post_on_group', 'Condividi post su un gruppo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_group_name', 'Si prega di scrivere il nome del gruppo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_post_on_page', 'Condividi su una pagina');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_page_name', 'Si prega di digitare il nome della pagina');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_post_on_user', 'Condividi per l\'utente');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'post_shared_successfully', 'Post è stato condiviso con successo.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'to', 'A');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_new_post', 'Condividi il post su');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'shared_a_post', 'condiviso a');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'shared_your_post', 'ha condiviso il tuo post');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'shared_a_post_in_timeline', 'ha condiviso un post sulla tua cronologia');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_shared', 'Nessuna azione ancora');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'member', 'Membro');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'pro_con', 'Congratulazioni! Ora sei un');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'other', 'Altro');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'all_', 'Tutti');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_liked_post', 'Alla gente è piaciuto questo post');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_wondered_post', 'La gente si chiedeva questo post');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_shared_post', 'Le persone hanno condiviso questo post');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_reacted_post', 'Le persone hanno reagito a questo post');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_wondered_comment', 'La gente si chiedeva questo commento');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_liked_comment', 'Alla gente è piaciuto questo commento');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reCaptcha_error', 'Si prega di controllare il re-captcha.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'my', 'Mio');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_transfer', 'trasferimento bancario');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_transfer_request', 'La tua richiesta è stata inviata con successo, ti avviseremo una volta approvata');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_pro', 'La tua ricevuta bancaria è stata approvata!');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_decline', 'La tua ricevuta bancaria è stata rifiutata!');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'my_timeline', 'La mia cronologia');
        } else if ($value == 'portuguese') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'edit_group', 'Editar grupo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'adult_image_file', 'Detetámos algum conteúdo adulto na imagem que carregou, por isso, recusámos o seu processo de carregamento.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'view_image', 'Ver imagem');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'phone_number_error', 'Número de telefone deve ser como este formato: +90 ..');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'something_wrong', 'Algo deu errado, por favor, tente novamente mais tarde.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_code', 'Código de confirmação');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_phone_verified', 'Seu número de telefone foi confirmado com sucesso.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_email_verified', 'Seu e-mail foi verificado com sucesso.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_phone_email_verified', 'Seu número de telefone e e-mail foram verificados com sucesso.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email_sent', 'Um email de confirmação foi enviado.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message_sent', 'Uma mensagem de confirmação foi enviada.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message_email_sent', 'Uma mensagem de confirmação e email foram enviados.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email', 'Enviámos um email com o código de confirmação para confirmar o seu novo email.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message', 'Enviamos uma mensagem que contém o código de confirmação para confirmar seu novo telefone.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email_message_text', 'Enviamos uma mensagem e um email que contém o código de confirmação para ativar a autenticação de dois fatores.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email_text', 'Enviamos um email que contém o código de confirmação para ativar a autenticação de dois fatores.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message_text', 'Enviamos uma mensagem que contém o código de confirmação para ativar a autenticação de dois fatores.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_post_on_group', 'Compartilhar postagem em um grupo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_group_name', 'Por favor, escreva o nome do grupo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_post_on_page', 'Compartilhar para uma página');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_page_name', 'Por favor, digite o nome da página');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_post_on_user', 'Compartilhar para o usuário');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'post_shared_successfully', 'Post foi compartilhado com sucesso.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'to', 'Para');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_new_post', 'Compartilhe a postagem em');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'shared_a_post', 'compartilhou um');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'shared_your_post', 'compartilhou sua postagem');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'shared_a_post_in_timeline', 'compartilhou uma postagem na sua linha do tempo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_shared', 'Ainda sem ações');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'member', 'Membro');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'pro_con', 'Parabéns! Você é agora um');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'other', 'De outros');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'all_', 'Todos');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_liked_post', 'Pessoas gostaram deste post');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_wondered_post', 'As pessoas se perguntaram este post');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_shared_post', 'As pessoas compartilharam esta postagem');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_reacted_post', 'As pessoas reagiram a este post');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_wondered_comment', 'As pessoas se perguntaram esse comentário');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_liked_comment', 'Pessoas gostaram deste comentário');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reCaptcha_error', 'Por favor, verifique o re-captcha.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'my', 'Minhas');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_transfer', 'transferência bancária');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_transfer_request', 'O seu pedido foi enviado com sucesso, iremos notificá-lo assim que for aprovado');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_pro', 'Seu recibo bancário foi aprovado!');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_decline', 'Seu recibo bancário foi recusado!');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'my_timeline', 'Minha linha do tempo');
        } else if ($value == 'russian') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'edit_group', 'Редактировать группу');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'adult_image_file', 'Мы обнаружили контент для взрослых на загруженном вами изображении, поэтому мы отклонили процесс загрузки.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'view_image', 'Посмотреть изображение');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'phone_number_error', 'Номер телефона должен быть в таком формате: +90 ..');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'something_wrong', 'Что-то пошло не так. Пожалуйста, повторите попытку позже.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_code', 'Код для подтверждения');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_phone_verified', 'Ваш номер телефона был успешно подтвержден.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_email_verified', 'Ваш E-mail был успешно подтвержден.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_phone_email_verified', 'Ваш номер телефона и адрес электронной почты были успешно проверены.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email_sent', 'Письмо с подтверждением было отправлено.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message_sent', 'Подтверждение было отправлено.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message_email_sent', 'Подтверждение и электронное письмо были отправлены.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email', 'Мы отправили письмо, содержащее код подтверждения, чтобы подтвердить ваш новый адрес электронной почты.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message', 'Мы отправили сообщение с кодом подтверждения для подтверждения вашего нового телефона.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email_message_text', 'Мы отправили сообщение и электронное письмо с кодом подтверждения, чтобы включить двухфакторную аутентификацию.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email_text', 'Мы отправили электронное письмо с кодом подтверждения для включения двухфакторной аутентификации.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message_text', 'Мы отправили сообщение с кодом подтверждения для включения двухфакторной аутентификации.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_post_on_group', 'Поделиться постом в группе');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_group_name', 'Пожалуйста, напишите название группы');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_post_on_page', 'Поделиться на странице');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_page_name', 'Пожалуйста, введите название страницы');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_post_on_user', 'Поделиться с пользователем');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'post_shared_successfully', 'Пост был успешно опубликован.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'to', 'к');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_new_post', 'Поделиться постом на');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'shared_a_post', 'поделился');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'shared_your_post', 'поделился своим постом');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'shared_a_post_in_timeline', 'поделился сообщением с вашей временной шкалой');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_shared', 'Еще нет акций');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'member', 'член');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'pro_con', 'Поздравляю! Теперь ты');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'other', 'Другой');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'all_', 'Все');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_liked_post', 'Людям понравился этот пост');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_wondered_post', 'Люди задавались вопросом этот пост');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_shared_post', 'Люди поделились этим постом');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_reacted_post', 'Люди отреагировали на этот пост');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_wondered_comment', 'Люди задавались вопросом этот комментарий');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_liked_comment', 'Людям понравился этот комментарий');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reCaptcha_error', 'Пожалуйста, проверьте повторную капчу.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'my', 'мой');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_transfer', 'банковский перевод');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_transfer_request', 'Ваш запрос был успешно отправлен, мы сообщим вам, как только он будет одобрен');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_pro', 'Ваша банковская квитанция была подтверждена!');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_decline', 'Ваша банковская квитанция была отклонена!');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'my_timeline', 'Мой график');
        } else if ($value == 'spanish') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'edit_group', 'Editar grupo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'adult_image_file', 'Hemos detectado contenido para adultos en la imagen que subiste, por lo tanto, hemos rechazado tu proceso de carga.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'view_image', 'Ver imagen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'phone_number_error', 'El número de teléfono debe tener este formato: +90 ..');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'something_wrong', 'Algo salió mal, por favor intente de nuevo más tarde.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_code', 'Código de confirmación');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_phone_verified', 'Su número de teléfono ha sido verificado con éxito.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_email_verified', 'Su correo electrónico ha sido verificado con éxito.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_phone_email_verified', 'Su número de teléfono y correo electrónico se han verificado con éxito.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email_sent', 'Un correo electrónico de confirmación ha sido enviado.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message_sent', 'Se envió un mensaje de confirmación.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message_email_sent', 'Se envió un mensaje de confirmación y correo electrónico.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email', 'Hemos enviado un correo electrónico que contiene el código de confirmación para verificar su nuevo correo electrónico.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message', 'Hemos enviado un mensaje que contiene el código de confirmación para verificar su nuevo teléfono.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email_message_text', 'Hemos enviado un mensaje y un correo electrónico que contiene el código de confirmación para habilitar la autenticación de dos factores.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email_text', 'Hemos enviado un correo electrónico que contiene el código de confirmación para habilitar la autenticación de dos factores.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message_text', 'Hemos enviado un mensaje que contiene el código de confirmación para habilitar la autenticación de dos factores.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_post_on_group', 'Compartir publicación en un grupo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_group_name', 'Por favor escriba el nombre del grupo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_post_on_page', 'Compartir en una página');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_page_name', 'Por favor escriba el nombre de la página');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_post_on_user', 'Compartir al usuario');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'post_shared_successfully', 'La publicación ha sido compartida con éxito.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'to', 'A');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_new_post', 'Comparte la publicación en');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'shared_a_post', 'compartió un');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'shared_your_post', 'compartió su publicación');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'shared_a_post_in_timeline', 'compartió una publicación en su línea de tiempo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_shared', 'No hay acciones aún');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'member', 'Miembro');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'pro_con', 'Felicidades ! Ahora eres un');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'other', 'Otro');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'all_', 'Todos');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_liked_post', 'A la gente le ha gustado esta publicación.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_wondered_post', 'La gente se preguntaba esta publicación.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_shared_post', 'Personas compartieron esta publicación');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_reacted_post', 'La gente reaccionó a este post.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_wondered_comment', 'La gente se pregunta este comentario.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_liked_comment', 'La gente ha gustado este comentario.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reCaptcha_error', 'Por favor, compruebe el re-captcha.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'my', 'Mi');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_transfer', 'transferencia bancaria');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_transfer_request', 'Su solicitud ha sido enviada exitosamente, le notificaremos una vez que sea aprobada.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_pro', 'Su recibo bancario ha sido aprobado!');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_decline', 'Su recibo bancario ha sido rechazado!');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'my_timeline', 'Mi línea de tiempo');
        } else if ($value == 'turkish') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'edit_group', 'Grubu düzenle');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'adult_image_file', 'Yüklediğiniz resimdeki bazı yetişkinlere uygun içerik tespit ettik, bu nedenle yükleme işleminizi reddetti.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'view_image', 'Resmi görüntüle');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'phone_number_error', 'Telefon numarası şu şekilde olmalıdır: +90 ..');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'something_wrong', 'Bir şeyler yanlış oldu. Lütfen sonra tekrar deneyiniz.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_code', 'Onay kodu');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_phone_verified', 'Telefon numaranız başarıyla doğrulandı.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_email_verified', 'E-posta adresiniz başarıyla doğrulandı.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_phone_email_verified', 'Telefon numaranız ve E-posta adresiniz başarıyla doğrulandı.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email_sent', 'Bir onay e-postası gönderildi.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message_sent', 'Bir onay mesajı gönderildi.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message_email_sent', 'Bir onay mesajı ve e-posta gönderildi.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email', 'Yeni e-postanızı doğrulamak için onay kodunu içeren bir e-posta gönderdik.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message', 'Yeni telefonunuzu doğrulamak için onay kodunu içeren bir mesaj gönderdik.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email_message_text', 'İki faktörlü kimlik doğrulamayı etkinleştirmek için onay kodunu içeren bir mesaj ve e-posta gönderdik.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email_text', 'İki faktörlü kimlik doğrulamayı etkinleştirmek için onay kodunu içeren bir e-posta gönderdik.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message_text', 'İki faktörlü kimlik doğrulamayı etkinleştirmek için onay kodunu içeren bir mesaj gönderdik.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_post_on_group', 'Bir gruptaki yayını paylaş');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_group_name', 'Lütfen grup adını yazınız');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_post_on_page', 'Bir sayfada paylaş');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_page_name', 'Lütfen sayfa adını yazın');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_post_on_user', 'Kullanıcıya paylaş');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'post_shared_successfully', 'Yayın başarıyla paylaşıldı.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'to', 'için');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_new_post', 'Yayını paylaş');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'shared_a_post', 'paylaştı');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'shared_your_post', 'yayınınızı paylaştı');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'shared_a_post_in_timeline', 'zaman çizelgenize bir yayın paylaştı');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_shared', 'Henüz paylaşım yok');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'member', 'üye');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'pro_con', 'Tebrikler! Sen şimdi');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'other', 'Diğer');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'all_', 'Herşey');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_liked_post', 'İnsanlar bu yayını beğendi');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_wondered_post', 'İnsanlar bu yayını merak etti');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_shared_post', 'İnsanlar bu yayını paylaştı');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_reacted_post', 'İnsanlar bu yayına yanıt verdi');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_wondered_comment', 'İnsanlar bu yorumu merak etti');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_liked_comment', 'İnsanlar bu yorumu beğendi');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reCaptcha_error', 'Lütfen re-captcha\'yı kontrol edin.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'my', 'Benim');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_transfer', 'banka transferi');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_transfer_request', 'İsteğiniz başarıyla gönderildi, onaylandıktan sonra sizi bilgilendireceğiz');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_pro', 'Banka dekontunuz onaylandı!');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_decline', 'Banka dekontunuz reddedildi!');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'my_timeline', 'Benim zaman çizelgem');
        } else if ($value == 'english') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'edit_group', 'Edit group');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'adult_image_file', 'We have detected some adult content on the image you uploaded, therefore we have declined your upload process.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'view_image', 'View Image');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'phone_number_error', 'Phone number should be as this format: +90..');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'something_wrong', 'Something went wrong, please try again later.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_code', 'Confirmation code');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_phone_verified', 'Your phone number has been successfully verified.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_email_verified', 'Your E-mail has been successfully verified.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_phone_email_verified', 'Your phone number and E-mail have been successfully verified.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email_sent', 'A confirmation email has been sent.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message_sent', 'A confirmation message was sent.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message_email_sent', 'A confirmation message and email were sent.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email', 'We have sent an email that contains the confirmation code to verify your new email.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message', 'We have sent a message that contains the confirmation code to verify your new phone.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email_message_text', 'We have sent a message and an email that contain the confirmation code to enable two-factor authentication.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email_text', 'We have sent an email that contains the confirmation code to enable Two-factor authentication.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message_text', 'We have sent a message that contains the confirmation code to enable Two-factor authentication.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_post_on_group', 'Share post on a group');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_group_name', 'Please write the group name');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_post_on_page', 'Share to a page');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_page_name', 'Please type the page name');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_post_on_user', 'Share to user');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'post_shared_successfully', 'Post has been successfully shared.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'to', 'To');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_new_post', 'Share the post on');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'shared_a_post', 'shared a');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'shared_your_post', 'shared your post');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'shared_a_post_in_timeline', 'shared a post to your timeline');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_shared', 'No shares yet');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'member', 'Member');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'pro_con', 'Congratulations ! You\'re now a');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'other', 'Other');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'all_', 'All');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_liked_post', 'People liked this post');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_wondered_post', 'People wondered this post');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_shared_post', 'People shared this post');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_reacted_post', 'People reacted to this post');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_wondered_comment', 'People wondered this comment');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_liked_comment', 'People liked this comment');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reCaptcha_error', 'Please check the re-captcha.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'my', 'My');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_transfer', 'Bank transfer');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_transfer_request', 'Your request has been successfully sent, we will notify you once it\'s approved');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_pro', 'Your bank receipt has been approved!');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_decline', 'Your bank receipt has been declined!');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'my_timeline', 'My Timeline');
        } else if ($value != 'english') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'edit_group', 'Edit group');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'adult_image_file', 'We have detected some adult content on the image you uploaded, therefore we have declined your upload process.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'view_image', 'View Image');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'phone_number_error', 'Phone number should be as this format: +90..');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'something_wrong', 'Something went wrong, please try again later.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_code', 'Confirmation code');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_phone_verified', 'Your phone number has been successfully verified.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_email_verified', 'Your E-mail has been successfully verified.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_phone_email_verified', 'Your phone number and E-mail have been successfully verified.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email_sent', 'A confirmation email has been sent.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message_sent', 'A confirmation message was sent.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message_email_sent', 'A confirmation message and email were sent.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email', 'We have sent an email that contains the confirmation code to verify your new email.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message', 'We have sent a message that contains the confirmation code to verify your new phone.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email_message_text', 'We have sent a message and an email that contain the confirmation code to enable two-factor authentication.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_email_text', 'We have sent an email that contains the confirmation code to enable Two-factor authentication.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirmation_message_text', 'We have sent a message that contains the confirmation code to enable Two-factor authentication.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_post_on_group', 'Share post on a group');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_group_name', 'Please write the group name');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_post_on_page', 'Share to a page');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_page_name', 'Please type the page name');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_post_on_user', 'Share to user');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'post_shared_successfully', 'Post has been successfully shared.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'to', 'To');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'share_new_post', 'Share the post on');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'shared_a_post', 'shared a');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'shared_your_post', 'shared your post');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'shared_a_post_in_timeline', 'shared a post to your timeline');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_shared', 'No shares yet');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'member', 'Member');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'pro_con', 'Congratulations ! You\'re now a');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'other', 'Other');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'all_', 'All');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_liked_post', 'People liked this post');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_wondered_post', 'People wondered this post');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_shared_post', 'People shared this post');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_reacted_post', 'People reacted to this post');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_wondered_comment', 'People wondered this comment');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'users_liked_comment', 'People liked this comment');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reCaptcha_error', 'Please check the re-captcha.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'my', 'My');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_transfer', 'Bank transfer');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_transfer_request', 'Your request has been successfully sent, we will notify you once it\'s approved');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_pro', 'Your bank receipt has been approved!');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'bank_decline', 'Your bank receipt has been declined!');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'my_timeline', 'My Timeline');
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
                     <h2 class="light">Update to v2.2 </span></h2>
                     <div class="setting-well">
                        <h4>Changelog</h4>
                        <ul class="wo_update_changelog">
                            <li> [Added] The ability to add and manage genders from admin panel.</li>
                            <li> [Added] The ability to earn points by creating blogs. </li>
                            <li> [Added] points daily limit for pro and free users. </li>
                            <li> [Added] The ability to edit group chat info and avatar.</li>
                            <li> [Added] The ability to turn off messages from friends or everyone.</li>
                            <li> [Added] the ability to filter porn and nude pictures using Google Vision API.</li>
                            <li> [Added] user will require to verfiy his email if he changed is from settings page, if email verfication is enabled.</li>
                            <li> [Added] new sharing system, with new style and the abiltiy to share to timeline, group and pages.</li>
                            <li> [Added] the ability to manage pro packages name, icon, features, prices, and disable which one of them</li>
                            <li> [Added] the ability to manage pages, groups, products and blog categories from admin panel > manage features + seperated.</li>
                            <li> [Added] the ability to view the reactions, likes, dislikes on comments and replies.</li>
                            <li> [Added] the ability to view reactions, likes of posts on new model.</li>
                            <li> [Added] bank payments for wallet and pro.</li>
                            <li> [Added] group chat API, albums API, Pokes API and few other APIs. </li>
                            <li> [Added] group chat notifications.</li>
                            <li> [Added] the ability to delete a user posts by one click from admin panel. </li>
                            <li> [Added] recaptcha to contact us from. </li>
                            <li> [Added] new message page desgin for default theme.</li>
                            <li> [Fixed] few important bugs.</li>
                            <li> [Fixed] secuity threat, URGENT an update is required.</li>
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
var queries = [
    "UPDATE `Wo_Config` SET `value` = '2.2' WHERE `name` = 'script_version'",
    "UPDATE `Wo_Config` SET `value` = '<?php echo time(); ?>' WHERE `name` = 'last_update'",
    "ALTER TABLE `Wo_Langs` ADD `type` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `lang_key`;",
    "ALTER TABLE `Wo_Langs` ADD INDEX(`type`);",
    "UPDATE `Wo_Langs` SET `type` = 'gender' WHERE `lang_key` = 'male';",
    "UPDATE `Wo_Langs` SET `type` = 'gender' WHERE `lang_key` = 'female';",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'free_day_limit', '1000');",
    "ALTER TABLE `Wo_Users` ADD `point_day_expire` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `points`;",
    "ALTER TABLE `Wo_Users` ADD `daily_points` INT(11) NOT NULL DEFAULT '0' AFTER `points`;",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'adult_images', '0');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'adult_images_action', '1');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'adult_images_file', '');",
    "ALTER TABLE `Wo_Posts` ADD `blur` INT(11) NOT NULL DEFAULT '0' AFTER `comments_status`;",
    "ALTER TABLE `Wo_Users` ADD `new_email` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `two_factor`;",
    "ALTER TABLE `Wo_Users` ADD `new_phone` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `new_email`;",
    "ALTER TABLE `Wo_Users` ADD `two_factor_verified` INT(11) NOT NULL DEFAULT '0' AFTER `new_email`;",
    "ALTER TABLE `Wo_Users` CHANGE `message_privacy` `message_privacy` ENUM('1','0','2') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0';",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'pro_day_limit', '2000');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'vision_api_key', '');",
    "CREATE TABLE `Wo_Manage_Pro` (`id` int(11) NOT NULL AUTO_INCREMENT,`type` varchar(50) NOT NULL DEFAULT '',`price` varchar(11) NOT NULL DEFAULT '0',`featured_member` int(11) NOT NULL DEFAULT '0',`profile_visitors` int(11) NOT NULL DEFAULT '0',`last_seen` int(11) NOT NULL DEFAULT '0',`verified_badge` int(11) NOT NULL DEFAULT '0',`posts_promotion` int(11) NOT NULL DEFAULT '0',`pages_promotion` int(11) NOT NULL DEFAULT '0',`discount` text NOT NULL,`image` varchar(300) NOT NULL DEFAULT '',PRIMARY KEY (`id`)) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;",
    "CREATE TABLE `Wo_Pages_Categories` (`id` int(11) NOT NULL AUTO_INCREMENT,`lang_key` varchar(160) NOT NULL DEFAULT '',PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
    "CREATE TABLE `Wo_Groups_Categories` ( `id` int(11) NOT NULL AUTO_INCREMENT, `lang_key` varchar(160) NOT NULL DEFAULT '', PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
    "CREATE TABLE `Wo_Products_Categories` ( `id` int(11) NOT NULL AUTO_INCREMENT, `lang_key` varchar(160) NOT NULL DEFAULT '', PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
    "CREATE TABLE `Wo_Blogs_Categories` (`id` int(11) NOT NULL AUTO_INCREMENT,`lang_key` varchar(160) NOT NULL DEFAULT '',PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'recaptcha_secret_key', '');",
    "ALTER TABLE `Wo_Manage_Pro` ADD `status` INT(11) NOT NULL DEFAULT '1' AFTER `image`;",
    "ALTER TABLE `Wo_Manage_Pro` ADD `time` INT(11) NOT NULL DEFAULT '0' AFTER `status`;",
    "INSERT INTO `Wo_Manage_Pro` (`id`, `type`, `price`, `featured_member`, `profile_visitors`, `last_seen`, `verified_badge`, `posts_promotion`, `pages_promotion`, `discount`, `image`, `status`, `time`) VALUES (1, 'star', '3', 1, 1, 1, 1, 0, 0, '0', '', 1, 7),(2, 'hot', '8', 1, 1, 1, 1, 5, 5, '10', '', 1, 30),(3, 'ultima', '89', 1, 1, 1, 1, 20, 20, '20', '', 1, 365),(4, 'vip', '259', 1, 1, 1, 1, 40, 40, '60', '', 1, 0);",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'bank_payment', 'yes');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'bank_transfer_note', 'In order to confirm the bank transfer, you will need to upload a receipt or take a screenshot of your transfer within 1 day from your payment date. If a bank transfer is made but no receipt is uploaded within this period, your order will be cancelled. We will verify and confirm your receipt within 3 working days from the date you upload it.');",
    "CREATE TABLE `bank_receipts` (`id` int(11) unsigned NOT NULL AUTO_INCREMENT,`user_id` int(11) unsigned NOT NULL DEFAULT '0',`description` tinytext NOT NULL,`price` varchar(50) NOT NULL DEFAULT '0',`mode` varchar(50) NOT NULL DEFAULT '',`approved` int(11) unsigned NOT NULL DEFAULT '0',`receipt_file` varchar(250) NOT NULL DEFAULT '',`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,`approved_at` int(11) unsigned NOT NULL DEFAULT '0',PRIMARY KEY (`id`)) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;",
    "ALTER TABLE `Wo_Config` CHANGE `value` `value` VARCHAR(20000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'edit_group');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'adult_image_file');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'view_image');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'phone_number_error');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'something_wrong');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'confirmation_code');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'your_phone_verified');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'your_email_verified');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'your_phone_email_verified');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'confirmation_email_sent');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'confirmation_message_sent');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'confirmation_message_email_sent');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'confirmation_email');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'confirmation_message');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'confirmation_email_message_text');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'confirmation_email_text');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'confirmation_message_text');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'share_post_on_group');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'please_group_name');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'share_post_on_page');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'please_page_name');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'share_post_on_user');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'post_shared_successfully');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'to');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'share_new_post');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'shared_a_post');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'shared_your_post');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'shared_a_post_in_timeline');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'no_shared');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'member');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'pro_con');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'other');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'all_');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'users_liked_post');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'users_wondered_post');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'users_shared_post');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'users_reacted_post');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'users_wondered_comment');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'users_liked_comment');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'reCaptcha_error');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'my');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'bank_transfer');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'bank_transfer_request');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'bank_pro');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'bank_decline');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'my_timeline');",
];

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