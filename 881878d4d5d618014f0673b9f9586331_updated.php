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
        $data['error']  = $code['ERROR_NAME'];
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
            $lang_update_queries[] = Wo_UpdateLangs($value, 'accept_group_chat_request', 'تم قبول دعوة الدردشة الجماعية.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'jobs', 'وظائف');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'post_job_text', 'انشر وظيفة لـ {{page_name}} للوصول إلى المتقدمين المناسبين');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'create_job', 'خلق وظيفة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_title', 'عنوان وظيفي');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'salary_range', 'نطاق الراتب');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'minimum', 'الحد الأدنى');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'maximum', 'أقصى');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_hour', 'في الساعة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_day', 'في اليوم');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_week', 'في الاسبوع');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_month', 'كل شهر');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_year', 'كل سنة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_type', 'نوع الوظيفة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'full_time', 'وقت كامل');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'part_time', 'دوام جزئى');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'internship', 'فترة تدريب');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'volunteer', 'تطوع');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'contract', 'عقد');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_des_text', 'صف المسؤوليات والمهارات المفضلة لهذه الوظيفة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_add_iamge', 'أضف صورة لمساعدة المتقدمين على معرفة كيفية العمل في هذا الموقع.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'use_cover_photo', 'استخدام صورة الغلاف');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'questions', 'الأسئلة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'free_text_question', 'نص حر السؤال');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'yes_no_question', 'نعم / لا سؤال');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'multiple_choice_question', 'سؤال متعدد الخيارات');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'add_question', 'أضف سؤال');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'add_an_answers', 'أضف إجابات');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'question_one', 'سؤال واحد');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'question_two', 'السؤال الثاني');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'question_three', 'السؤال الثالث');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'edit_job', 'تحرير الوظيفة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'delete_job', 'حذف الوظيفة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'apply_now', 'قدم الآن');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'experience', 'تجربة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'add_experience', 'إضافة الخبرة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'position', 'موضع');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'where_did_you_work', 'أين عملت؟');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_currently_work', 'أنا حاليا أعمل هنا');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_answer_questions', 'الرجاء الإجابة على الأسئلة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'you_apply_this_job', 'لقد تقدمت بالفعل لهذه الوظيفة.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'search_for_jobs', 'البحث عن وظائف');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_available_jobs', 'لا توجد وظائف متاحة للعرض.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'load_more_jobs', 'تحميل المزيد من الوظائف');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'show_apply', 'عرض تطبيق');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'common_things', 'الأشياء المشتركة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'thing_in_common', 'شيء مشترك');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'things_in_common', 'أشياء مشتركة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'weather_unit', 'وحدة الطقس');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_applied', 'تطبق بالفعل');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'apply_your_job', 'تطبق على طلب عملك.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'apply_job_successfully', 'لقد تقدمت بنجاح إلى هذه الوظيفة.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_successfully_created', 'تم إنشاء طلب الوظيفة بنجاح.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_successfully_edited', 'تم تحديث طلب الوظيفة بنجاح.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'invited_to_group', 'دعاك للانضمام إلى الدردشة الجماعية.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'declined_group_chat_request', 'رفض دعوة الدردشة الجماعية.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'offer_job', 'اعرض عمل');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'posted_job_offer', 'نشر عرض عمل.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'verified_page', 'صفحة موثوقة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'unfriend', 'الغاء الصداقه');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'funding', 'بالتمويل');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'my_funding', 'طلبات التمويل الخاصة بي');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'create_new_funding', 'إنشاء طلب تمويل جديد');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'funding_created', 'تم إنشاء طلب التمويل بنجاح.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_funding_found', 'لم يتم العثور على تمويل');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'delete_fund', 'حذف');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirm_delete_fund', 'هل أنت متأكد أنك تريد حذف طلب التمويل هذا؟');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'funding_edited', 'تم تحديث طلب التمويل بنجاح.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'most_recent_funding', 'أحدث تمويل');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'raised_of', 'أثارت من');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'donate', 'تبرع');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'donated_to', 'تبرعت لطلب التمويل الخاص بك.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'total_donations', 'مجموع التبرعات');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'recent_donations', 'التبرعات الأخيرة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'instagram', 'إينستاجرام');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'created_donation_request', 'خلق طلب التبرع');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'people_with_common', 'التقي بأشخاص لديهم أشياء مشتركة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'donated_to_request', 'تبرعت لطلب التمويل');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'you_cant_pay', 'لا يمكنك الدفع أكثر من {{money}}');
    $lang_update_queries[] = Wo_UpdateLangs($value, 'confirm_delete_job', 'هل أنت متأكد أنك تريد حذف هذه الوظيفة؟');
        } else if ($value == 'dutch') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'accept_group_chat_request', 'Uw uitnodiging voor groepschat geaccepteerd.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'jobs', 'Jobs');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'post_job_text', 'Plaats een vacature voor {{page_name}} om de juiste sollicitanten te bereiken op');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'create_job', 'Taak maken');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_title', 'Functietitel');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'salary_range', 'Salaris schaal');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'minimum', 'Minimum');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'maximum', 'maximaal');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_hour', 'Per uur');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_day', 'Per dag');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_week', 'Per week');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_month', 'Per maand');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_year', 'Per jaar');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_type', 'Soort baan');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'full_time', 'Full time');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'part_time', 'Deeltijd');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'internship', 'Stage');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'volunteer', 'Vrijwilliger');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'contract', 'Contract');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_des_text', 'Beschrijf de verantwoordelijkheden en voorkeursvaardigheden voor deze functie');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_add_iamge', 'Voeg een afbeelding toe om aanvragers te helpen zien hoe het is om op deze locatie te werken.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'use_cover_photo', 'Gebruik omslagfoto');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'questions', 'vragen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'free_text_question', 'Vrije tekst vraag');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'yes_no_question', 'Ja-nee-vraag');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'multiple_choice_question', 'Meerkeuze vraag');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'add_question', 'Vraag toevoegen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'add_an_answers', 'Antwoorden toevoegen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'question_one', 'Vraag een');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'question_two', 'Vraag twee');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'question_three', 'Vraag drie');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'edit_job', 'Job bewerken');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'delete_job', 'Taak verwijderen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'apply_now', 'Nu toepassen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'experience', 'Ervaring');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'add_experience', 'Voeg ervaring toe');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'position', 'Positie');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'where_did_you_work', 'Waar heb je gewerkt?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_currently_work', 'Ik werk momenteel hier');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_answer_questions', 'Beantwoord alstublieft de vragen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'you_apply_this_job', 'Je hebt al gesolliciteerd voor deze functie.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'search_for_jobs', 'Zoek voor banen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_available_jobs', 'Geen beschikbare taken om te tonen.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'load_more_jobs', 'Laad meer taken');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'show_apply', 'Tonen Toepassen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'common_things', 'Gewone dingen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'thing_in_common', 'Ding gemeen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'things_in_common', 'Gemeenschappelijke dingen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'weather_unit', 'Weereenheid');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_applied', 'Al toegepast');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'apply_your_job', 'toegepast op uw sollicitatie.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'apply_job_successfully', 'Je hebt met succes gesolliciteerd op deze functie.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_successfully_created', 'Taakaanvraag succesvol aangemaakt.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_successfully_edited', 'Taakaanvraag succesvol bijgewerkt.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'invited_to_group', 'heeft u uitgenodigd om deel te nemen aan de groepschat.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'declined_group_chat_request', 'uw uitnodiging voor groepschat geweigerd.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'offer_job', 'Bied een baan aan');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'posted_job_offer', 'een vacature geplaatst.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'verified_page', 'Geverifieerde pagina');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'unfriend', 'Unfriend');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'funding', 'financieringen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'my_funding', 'Mijn financieringsverzoeken');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'create_new_funding', 'Maak een nieuw financieringsverzoek');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'funding_created', 'Financieringsaanvraag is succesvol aangemaakt.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_funding_found', 'Geen financiering gevonden');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'delete_fund', 'Verwijder');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirm_delete_fund', 'Weet u zeker dat u dit financieringsverzoek wilt verwijderen?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'funding_edited', 'Financieringsaanvraag is succesvol bijgewerkt.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'most_recent_funding', 'Meest recente financiering');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'raised_of', 'Opgeheven van');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'donate', 'schenken');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'donated_to', 'gedoneerd aan uw financieringsverzoek.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'total_donations', 'Totaal aantal donaties');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'recent_donations', 'Recente donaties');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'instagram', 'Instagram');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'created_donation_request', 'een donatieverzoek gemaakt');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'people_with_common', 'Ontmoet mensen met dingen gemeen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'donated_to_request', 'gedoneerd aan een financieringsverzoek');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'you_cant_pay', 'U kunt niet meer betalen dan {{money}}');
    $lang_update_queries[] = Wo_UpdateLangs($value, 'confirm_delete_job', 'Weet u zeker dat u deze taak wilt verwijderen?');
        } else if ($value == 'french') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'accept_group_chat_request', 'Accepté votre invitation à la discussion de groupe.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'jobs', 'Emplois');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'post_job_text', 'Déposez une offre pour {{nom_page}} afin d\'atteindre les bons candidats le');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'create_job', 'Créer un emploi');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_title', 'Profession');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'salary_range', 'Échelle salariale');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'minimum', 'Le minimum');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'maximum', 'Maximum');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_hour', 'Par heure');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_day', 'Par jour');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_week', 'Par semaine');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_month', 'Par mois');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_year', 'Par an');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_type', 'Type d\'emploi');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'full_time', 'À plein temps');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'part_time', 'À temps partiel');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'internship', 'Stage');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'volunteer', 'Bénévole');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'contract', 'Contrat');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_des_text', 'Décrivez les responsabilités et les compétences préférées pour cet emploi.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_add_iamge', 'Ajoutez une image pour aider les candidats à voir à quoi ça ressemble de travailler à cet endroit.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'use_cover_photo', 'Utiliser la photo de couverture');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'questions', 'Des questions');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'free_text_question', 'Question de texte libre');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'yes_no_question', 'Question oui / non');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'multiple_choice_question', 'Question à choix multiples');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'add_question', 'Ajouter une question');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'add_an_answers', 'Ajouter des réponses');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'question_one', 'Question une');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'question_two', 'Deuxième question');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'question_three', 'Troisième question');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'edit_job', 'Modifier le travail');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'delete_job', 'Supprimer le travail');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'apply_now', 'Appliquer maintenant');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'experience', 'Expérience');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'add_experience', 'Ajouter une expérience');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'position', 'Position');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'where_did_you_work', 'Où avez-vous travaillé?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_currently_work', 'Je travaille actuellement ici');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_answer_questions', 'S\'il vous plaît répondre aux questions');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'you_apply_this_job', 'Vous avez déjà postulé pour ce travail.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'search_for_jobs', 'Chercher du travail');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_available_jobs', 'Aucun emploi disponible à afficher.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'load_more_jobs', 'Charger plus de jobs');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'show_apply', 'Afficher appliquer');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'common_things', 'Choses communes');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'thing_in_common', 'Chose en commun');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'things_in_common', 'Choses en commun');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'weather_unit', 'Unité météorologique');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_applied', 'Déjà appliqué');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'apply_your_job', 'appliqué à votre demande d\'emploi.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'apply_job_successfully', 'Vous avez postulé avec succès à ce travail.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_successfully_created', 'Demande de travail créée avec succès.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_successfully_edited', 'Demande de travail mise à jour avec succès.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'invited_to_group', 'vous invite à rejoindre le chat en groupe.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'declined_group_chat_request', 'a refusé votre invitation à la discussion de groupe.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'offer_job', 'Offrir un emploi');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'posted_job_offer', 'posté une offre d\'emploi.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'verified_page', 'Page vérifiée');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'unfriend', 'Désamie');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'funding', 'Des financements');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'my_funding', 'Mes demandes de financement');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'create_new_funding', 'Créer une nouvelle demande de financement');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'funding_created', 'La demande de financement a été créée avec succès.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_funding_found', 'Aucun financement trouvé');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'delete_fund', 'Effacer');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirm_delete_fund', 'Êtes-vous sûr de vouloir supprimer cette demande de financement?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'funding_edited', 'La demande de financement a été mise à jour avec succès.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'most_recent_funding', 'Financement le plus récent');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'raised_of', 'Élevé de');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'donate', 'Faire un don');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'donated_to', 'fait un don à votre demande de financement.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'total_donations', 'Total des dons');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'recent_donations', 'Dons récents');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'instagram', 'Instagram');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'created_donation_request', 'créé une demande de don');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'people_with_common', 'Rencontrer des gens avec des choses en commun');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'donated_to_request', 'donné à une demande de financement');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'you_cant_pay', 'Vous ne pouvez pas payer plus que {{money}}');
    $lang_update_queries[] = Wo_UpdateLangs($value, 'confirm_delete_job', 'Êtes-vous sûr de vouloir supprimer ce travail?');
        } else if ($value == 'german') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'accept_group_chat_request', 'Hat Ihre Einladung zum Gruppenchat angenommen.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'jobs', 'Arbeitsplätze');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'post_job_text', 'Veröffentlichen Sie einen Job für {{page_name}}, um die richtigen Bewerber am zu erreichen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'create_job', 'Job erstellen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_title', 'Berufsbezeichnung');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'salary_range', 'Gehaltsspanne');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'minimum', 'Minimum');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'maximum', 'Maximal');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_hour', 'Pro Stunde');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_day', 'Pro Tag');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_week', 'Pro Woche');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_month', 'Pro Monat');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_year', 'Pro Jahr');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_type', 'Auftragstyp');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'full_time', 'Vollzeit');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'part_time', 'Teilzeit');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'internship', 'Praktikum');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'volunteer', 'Freiwillige');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'contract', 'Vertrag');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_des_text', 'Beschreiben Sie die Verantwortlichkeiten und bevorzugten Fähigkeiten für diesen Job');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_add_iamge', 'Fügen Sie ein Bild hinzu, damit Bewerber sehen, wie es ist, an diesem Standort zu arbeiten.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'use_cover_photo', 'Titelbild verwenden');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'questions', 'Fragen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'free_text_question', 'Freitext-Frage');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'yes_no_question', 'Ja-nein Frage');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'multiple_choice_question', 'Multiple-Choice-Frage');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'add_question', 'Frage hinzufügen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'add_an_answers', 'Antworten hinzufügen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'question_one', 'Frage eins');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'question_two', 'Frage zwei');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'question_three', 'Frage drei');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'edit_job', 'Job bearbeiten');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'delete_job', 'Job löschen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'apply_now', 'Jetzt bewerben');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'experience', 'Erfahrung');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'add_experience', 'Erfahrung hinzufügen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'position', 'Position');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'where_did_you_work', 'Wo hast du gearbeitet?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_currently_work', 'Ich arbeite zurzeit hier');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_answer_questions', 'Bitte beantworten Sie die Fragen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'you_apply_this_job', 'Sie haben sich bereits auf diese Stelle beworben.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'search_for_jobs', 'Nach Jobs suchen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_available_jobs', 'Keine verfügbaren Jobs zum Anzeigen.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'load_more_jobs', 'Weitere Jobs laden');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'show_apply', 'Show Übernehmen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'common_things', 'Allgemeine Dinge');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'thing_in_common', 'Gemeinsamkeiten');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'things_in_common', 'Gemeinsame Dinge');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'weather_unit', 'Weather Unit');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_applied', 'Bereits angewendet');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'apply_your_job', 'auf Ihre Stellenanfrage angewendet.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'apply_job_successfully', 'Sie haben sich erfolgreich auf diese Stelle beworben.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_successfully_created', 'Jobanfrage erfolgreich erstellt.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_successfully_edited', 'Jobanfrage erfolgreich aktualisiert.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'invited_to_group', 'hat Sie zum Gruppenchat eingeladen.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'declined_group_chat_request', 'hat Ihre Gruppenchat-Einladung abgelehnt.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'offer_job', 'Biete einen Job an');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'posted_job_offer', 'hat ein Stellenangebot gepostet.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'verified_page', 'Verifizierte Seite');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'unfriend', 'Unfreund');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'funding', 'Förderungen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'my_funding', 'Meine Finanzierungsanfragen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'create_new_funding', 'Erstellen Sie eine neue Finanzierungsanfrage');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'funding_created', 'Finanzierungsantrag wurde erfolgreich erstellt.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_funding_found', 'Keine Finanzierung gefunden');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'delete_fund', 'Löschen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirm_delete_fund', 'Möchten Sie diesen Finanzierungsantrag wirklich löschen?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'funding_edited', 'Der Finanzierungsantrag wurde erfolgreich aktualisiert.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'most_recent_funding', 'Letzte Finanzierung');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'raised_of', 'Erzogen von');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'donate', 'Spenden');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'donated_to', 'gespendet auf Ihre Finanzierungsanfrage.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'total_donations', 'Gesamtspenden');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'recent_donations', 'Aktuelle Spenden');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'instagram', 'Instagram');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'created_donation_request', 'hat eine Spendenanfrage erstellt');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'people_with_common', 'Treffen Sie Menschen mit Gemeinsamkeiten');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'donated_to_request', 'an einen Förderantrag gespendet');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'you_cant_pay', 'Sie können nicht mehr als {{money}} bezahlen');
    $lang_update_queries[] = Wo_UpdateLangs($value, 'confirm_delete_job', 'Möchten Sie diesen Job wirklich löschen?');
        } else if ($value == 'italian') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'accept_group_chat_request', 'Ha accettato l\'invito alla chat di gruppo.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'jobs', 'Lavori');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'post_job_text', 'Pubblica un lavoro per {{page_name}} per raggiungere i candidati giusti');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'create_job', 'Crea lavoro');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_title', 'Titolo di lavoro');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'salary_range', 'Fascia di reddito');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'minimum', 'Minimo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'maximum', 'Massimo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_hour', 'All\'ora');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_day', 'Al giorno');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_week', 'A settimana');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_month', 'Al mese');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_year', 'Per anno');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_type', 'Tipo di lavoro');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'full_time', 'Tempo pieno');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'part_time', 'Mezza giornata');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'internship', 'tirocinio');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'volunteer', 'Volontario');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'contract', 'Contrarre');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_des_text', 'Descrivi le responsabilità e le competenze preferite per questo lavoro');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_add_iamge', 'Aggiungi un\'immagine per aiutare i candidati a vedere com\'è lavorare in questa posizione.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'use_cover_photo', 'Usa foto di copertina');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'questions', 'Domande');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'free_text_question', 'Domanda a testo libero');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'yes_no_question', 'Si nessuna domanda');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'multiple_choice_question', 'Domanda a scelta multipla');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'add_question', 'Aggiungi domanda');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'add_an_answers', 'Aggiungi risposte');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'question_one', 'Domanda uno');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'question_two', 'Domanda due');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'question_three', 'Domanda tre');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'edit_job', 'Modifica lavoro');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'delete_job', 'Elimina lavoro');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'apply_now', 'Applica ora');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'experience', 'Esperienza');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'add_experience', 'Aggiungi esperienza');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'position', 'Posizione');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'where_did_you_work', 'Dove lavori?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_currently_work', 'Attualmente lavoro qui');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_answer_questions', 'Per favore, rispondi alle domande');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'you_apply_this_job', 'Hai già fatto domanda per questo lavoro.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'search_for_jobs', 'Cerca lavoro');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_available_jobs', 'Nessun lavoro disponibile da mostrare.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'load_more_jobs', 'Carica più lavori');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'show_apply', 'Mostra Applica');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'common_things', 'Cose comuni');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'thing_in_common', 'Cosa in comune');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'things_in_common', 'Cose in comune');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'weather_unit', 'Unità meteorologica');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_applied', 'Già applicato');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'apply_your_job', 'applicato alla tua richiesta di lavoro.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'apply_job_successfully', 'Hai fatto domanda con successo a questo lavoro.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_successfully_created', 'Richiesta di lavoro creata correttamente.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_successfully_edited', 'Richiesta di lavoro aggiornata correttamente.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'invited_to_group', 'ti ha invitato a unirti alla chat di gruppo.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'declined_group_chat_request', 'rifiutato l\'invito alla chat di gruppo.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'offer_job', 'Offri un lavoro');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'posted_job_offer', 'pubblicato un\'offerta di lavoro.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'verified_page', 'Pagina verificata');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'unfriend', 'Unfriend');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'funding', 'finanziamenti');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'my_funding', 'Le mie richieste di finanziamento');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'create_new_funding', 'Crea una nuova richiesta di finanziamento');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'funding_created', 'La richiesta di finanziamento è stata creata correttamente.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_funding_found', 'Nessun finanziamento trovato');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'delete_fund', 'Elimina');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirm_delete_fund', 'Sei sicuro di voler eliminare questa richiesta di finanziamento?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'funding_edited', 'La richiesta di finanziamento è stata aggiornata correttamente.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'most_recent_funding', 'Finanziamento più recente');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'raised_of', 'Allevato di');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'donate', 'Donare');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'donated_to', 'donato alla tua richiesta di finanziamento.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'total_donations', 'Donazioni totali');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'recent_donations', 'Donazioni recenti');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'instagram', 'Instagram');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'created_donation_request', 'ha creato una richiesta di donazione');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'people_with_common', 'Incontra persone con cose in comune');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'donated_to_request', 'donato a una richiesta di finanziamento');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'you_cant_pay', 'Non puoi pagare più di {{money}}');
    $lang_update_queries[] = Wo_UpdateLangs($value, 'confirm_delete_job', 'Sei sicuro di voler eliminare questo lavoro?');
        } else if ($value == 'portuguese') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'accept_group_chat_request', 'Aceitou seu convite para bate-papo em grupo.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'jobs', 'Empregos');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'post_job_text', 'Publique uma tarefa para {{page_name}} alcançar os candidatos certos em');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'create_job', 'Criar trabalho');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_title', 'Titulo do trabalho');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'salary_range', 'Faixa salarial');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'minimum', 'Mínimo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'maximum', 'Máximo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_hour', 'Por hora');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_day', 'Por dia');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_week', 'Por semana');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_month', 'Por mês');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_year', 'Por ano');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_type', 'Tipo de emprego');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'full_time', 'Tempo total');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'part_time', 'Meio período');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'internship', 'Estágio');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'volunteer', 'Voluntário');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'contract', 'Contrato');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_des_text', 'Descreva as responsabilidades e habilidades preferidas para este trabalho');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_add_iamge', 'Adicione uma imagem para ajudar os candidatos a ver como é trabalhar neste local.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'use_cover_photo', 'Usar Foto de Capa');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'questions', 'Questões');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'free_text_question', 'Pergunta de texto livre');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'yes_no_question', 'Sim / Não Pergunta');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'multiple_choice_question', 'Pergunta de múltipla escolha');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'add_question', 'Adicionar pergunta');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'add_an_answers', 'Adicionar respostas');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'question_one', 'Pergunta um');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'question_two', 'Pergunta dois');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'question_three', 'Pergunta três');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'edit_job', 'Editar trabalho');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'delete_job', 'Excluir trabalho');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'apply_now', 'Aplique agora');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'experience', 'Experiência');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'add_experience', 'Adicionar Experiência');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'position', 'Posição');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'where_did_you_work', 'Onde você trabalhou?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_currently_work', 'eu trabalho aqui atualmente');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_answer_questions', 'Por favor responda as perguntas');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'you_apply_this_job', 'Você já se inscreveu para este trabalho.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'search_for_jobs', 'Procure por empregos');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_available_jobs', 'Não há trabalhos disponíveis para mostrar.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'load_more_jobs', 'Carregar mais trabalhos');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'show_apply', 'Mostrar Aplicar');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'common_things', 'Coisas comuns');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'thing_in_common', 'Coisa em comum');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'things_in_common', 'Coisas em comum');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'weather_unit', 'Unidade Meteorológica');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_applied', 'Já aplicado');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'apply_your_job', 'aplicada à sua solicitação de emprego.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'apply_job_successfully', 'Você se inscreveu com sucesso neste trabalho.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_successfully_created', 'Solicitação de tarefa criada com sucesso.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_successfully_edited', 'Solicitação de tarefa atualizada com sucesso.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'invited_to_group', 'convidou você para participar do chat em grupo.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'declined_group_chat_request', 'recusou seu convite para bate-papo em grupo.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'offer_job', 'Oferecer um emprego');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'posted_job_offer', 'postou uma oferta de emprego.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'verified_page', 'Página verificada');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'unfriend', 'Anular');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'funding', 'Financiamentos');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'my_funding', 'Minhas solicitações de financiamento');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'create_new_funding', 'Criar nova solicitação de financiamento');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'funding_created', 'A solicitação de financiamento foi criada com sucesso.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_funding_found', 'Nenhum financiamento encontrado');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'delete_fund', 'Excluir');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirm_delete_fund', 'Tem certeza de que deseja excluir esta solicitação de financiamento?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'funding_edited', 'A solicitação de financiamento foi atualizada com sucesso.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'most_recent_funding', 'Financiamento mais recente');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'raised_of', 'Levantado de');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'donate', 'Doar');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'donated_to', 'doado para sua solicitação de financiamento.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'total_donations', 'Total de doações');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'recent_donations', 'Doações recentes');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'instagram', 'Instagram');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'created_donation_request', 'criou uma solicitação de doação');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'people_with_common', 'Conheça pessoas com coisas em comum');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'donated_to_request', 'doado a uma solicitação de financiamento');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'you_cant_pay', 'Você não pode pagar mais do que {{money}}');
    $lang_update_queries[] = Wo_UpdateLangs($value, 'confirm_delete_job', 'Tem certeza de que deseja excluir este trabalho?');
        } else if ($value == 'russian') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'accept_group_chat_request', 'Принято приглашение в групповой чат.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'jobs', 'работы');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'post_job_text', 'Опубликуйте вакансию для {{page_name}}, чтобы найти нужных кандидатов на');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'create_job', 'Создать работу');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_title', 'Должность');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'salary_range', 'Диапазон зарплаты');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'minimum', 'минимальный');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'maximum', 'максимальная');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_hour', 'В час');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_day', 'В день');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_week', 'В неделю');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_month', 'В месяц');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_year', 'В год');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_type', 'Тип задания');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'full_time', 'На постоянной основе');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'part_time', 'Неполная занятость');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'internship', 'производственная практика');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'volunteer', 'доброволец');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'contract', 'контракт');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_des_text', 'Опишите обязанности и предпочтительные навыки для этой работы');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_add_iamge', 'Добавьте изображение, чтобы помочь кандидатам увидеть, каково это работать в этом месте.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'use_cover_photo', 'Использовать обложку');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'questions', 'Вопросы');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'free_text_question', 'Свободный текстовый вопрос');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'yes_no_question', 'Да / нет вопрос');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'multiple_choice_question', 'Вопрос с множественным выбором');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'add_question', 'Добавить вопрос');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'add_an_answers', 'Добавить ответы');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'question_one', 'Вопрос первый');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'question_two', 'Вопрос второй');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'question_three', 'Вопрос третий');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'edit_job', 'Редактировать работу');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'delete_job', 'Удалить работу');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'apply_now', 'Применить сейчас');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'experience', 'Опыт');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'add_experience', 'Добавить опыт');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'position', 'Позиция');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'where_did_you_work', 'Где ты работаешь?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_currently_work', 'я сейчас работаю здесь');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_answer_questions', 'Пожалуйста, ответьте на вопросы');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'you_apply_this_job', 'Вы уже подали заявку на эту работу.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'search_for_jobs', 'Поиск работы');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_available_jobs', 'Нет доступных рабочих мест, чтобы показать.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'load_more_jobs', 'Загрузить больше вакансий');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'show_apply', 'Показать Применить');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'common_things', 'Общие вещи');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'thing_in_common', 'Вещь общая');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'things_in_common', 'Общие вещи');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'weather_unit', 'Погодная единица');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_applied', 'Уже применено');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'apply_your_job', 'применяется к вашей заявке на работу.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'apply_job_successfully', 'Вы успешно подали заявку на эту работу.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_successfully_created', 'Запрос на работу успешно создан.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_successfully_edited', 'Запрос на работу успешно обновлен.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'invited_to_group', 'пригласил вас присоединиться к групповому чату.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'declined_group_chat_request', 'отклонил приглашение в групповой чат.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'offer_job', 'Предложить работу');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'posted_job_offer', 'опубликовал предложение о работе.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'verified_page', 'Проверенная страница');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'unfriend', 'Unfriend');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'funding', 'сделкам финансирования');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'my_funding', 'Мои заявки на финансирование');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'create_new_funding', 'Создать новый запрос на финансирование');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'funding_created', 'Запрос на финансирование был успешно создан.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_funding_found', 'Финансирование не найдено');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'delete_fund', 'удалять');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirm_delete_fund', 'Вы уверены, что хотите удалить этот запрос на финансирование?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'funding_edited', 'Запрос на финансирование был успешно обновлен.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'most_recent_funding', 'Самое последнее финансирование');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'raised_of', 'Поднял из');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'donate', 'жертвовать');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'donated_to', 'пожертвовал на ваш запрос на финансирование.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'total_donations', 'Всего пожертвований');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'recent_donations', 'Недавние пожертвования');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'instagram', 'Instagram');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'created_donation_request', 'создал запрос на пожертвование');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'people_with_common', 'Встретить людей с общими вещами');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'donated_to_request', 'пожертвовал на запрос финансирования');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'you_cant_pay', 'Вы не можете заплатить больше, чем {{money}}');
    $lang_update_queries[] = Wo_UpdateLangs($value, 'confirm_delete_job', 'Вы уверены, что хотите удалить эту работу?');
        } else if ($value == 'spanish') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'accept_group_chat_request', 'Aceptó su invitación de chat grupal.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'jobs', 'Trabajos');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'post_job_text', 'Publique un trabajo para {{page_name}} para llegar a los solicitantes correctos en');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'create_job', 'Crear trabajo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_title', 'Título profesional');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'salary_range', 'Rango salarial');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'minimum', 'Mínimo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'maximum', 'Máximo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_hour', 'Por hora');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_day', 'Por día');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_week', 'Por semana');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_month', 'Por mes');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_year', 'Por año');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_type', 'Tipo de empleo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'full_time', 'Tiempo completo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'part_time', 'Medio tiempo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'internship', 'Internado');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'volunteer', 'Voluntario');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'contract', 'Contrato');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_des_text', 'Describa las responsabilidades y habilidades preferidas para este trabajo.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_add_iamge', 'Agregue una imagen para ayudar a los solicitantes a ver cómo es trabajar en esta ubicación.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'use_cover_photo', 'Usar foto de portada');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'questions', 'Preguntas');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'free_text_question', 'Pregunta de texto libre');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'yes_no_question', 'Pregunta de sí o no');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'multiple_choice_question', 'Pregunta de opción múltiple');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'add_question', 'Añadir pregunta');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'add_an_answers', 'Agregar respuestas');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'question_one', 'Pregunta uno');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'question_two', 'Pregunta dos');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'question_three', 'Pregunta tres');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'edit_job', 'Editar trabajo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'delete_job', 'Eliminar trabajo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'apply_now', 'Aplica ya');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'experience', 'Experiencia');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'add_experience', 'Añadir experiencia');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'position', 'Posición');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'where_did_you_work', '¿Donde trabajaste?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_currently_work', 'actualmente trabajo aquí');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_answer_questions', 'Por favor contesta las preguntas');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'you_apply_this_job', 'Ya has solicitado este trabajo.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'search_for_jobs', 'Búsqueda de empleo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_available_jobs', 'No hay trabajos disponibles para mostrar.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'load_more_jobs', 'Cargar más trabajos');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'show_apply', 'Mostrar Aplicar');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'common_things', 'Cosas en común');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'thing_in_common', 'Cosa en común');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'things_in_common', 'Cosas en comun');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'weather_unit', 'Unidad de clima');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_applied', 'Ya aplicado');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'apply_your_job', 'aplicado a su solicitud de trabajo.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'apply_job_successfully', 'Has aplicado con éxito a este trabajo.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_successfully_created', 'Solicitud de trabajo creada con éxito.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_successfully_edited', 'Solicitud de trabajo actualizada correctamente.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'invited_to_group', 'te invitó a unirte al chat grupal.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'declined_group_chat_request', 'rechazó su invitación de chat grupal.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'offer_job', 'Ofrecer un trabajo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'posted_job_offer', 'publicó una oferta de trabajo.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'verified_page', 'Página verificada');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'unfriend', 'No amigo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'funding', 'Financiaciones');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'my_funding', 'Mis solicitudes de financiamiento');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'create_new_funding', 'Crear nueva solicitud de financiación');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'funding_created', 'La solicitud de financiación se ha creado con éxito.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_funding_found', 'No se encontraron fondos.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'delete_fund', 'Borrar');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirm_delete_fund', '¿Está seguro de que desea eliminar esta solicitud de financiación?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'funding_edited', 'La solicitud de financiación se ha actualizado correctamente.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'most_recent_funding', 'Financiamiento más reciente');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'raised_of', 'Criado de');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'donate', 'Donar');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'donated_to', 'donado a su solicitud de financiación.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'total_donations', 'Donaciones totales');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'recent_donations', 'Donaciones recientes');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'instagram', 'Instagram');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'created_donation_request', 'creó una solicitud de donación');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'people_with_common', 'Conoce gente con cosas en común');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'donated_to_request', 'donado a una solicitud de financiación');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'you_cant_pay', 'No puede pagar más de {{money}}');
    $lang_update_queries[] = Wo_UpdateLangs($value, 'confirm_delete_job', '¿Estás seguro de que deseas eliminar este trabajo?');
        } else if ($value == 'turkish') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'accept_group_chat_request', 'Grup sohbeti davetinizi kabul ettiniz.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'jobs', 'Meslekler');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'post_job_text', 'Doğru başvuru sahiplerine ulaşmak için {{page_name}} için bir iş gönderin.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'create_job', 'İş Oluştur');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_title', 'İş ismi');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'salary_range', 'Maaş aralığı');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'minimum', 'minimum');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'maximum', 'Maksimum');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_hour', 'Saat başı');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_day', 'Günlük');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_week', 'Haftada');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_month', 'Her ay');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_year', 'Yıl başına');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_type', 'Meslek türü');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'full_time', 'Tam zamanlı');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'part_time', 'Yarı zamanlı');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'internship', 'Staj');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'volunteer', 'Gönüllü');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'contract', 'sözleşme');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_des_text', 'Bu iş için sorumlulukları ve tercih edilen becerileri tanımlayın');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_add_iamge', 'Başvuru sahiplerinin bu konumda çalışmanın nasıl bir şey olduğunu görmelerine yardımcı olmak için bir resim ekleyin.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'use_cover_photo', 'Kapak Fotoğrafını Kullan');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'questions', 'Sorular');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'free_text_question', 'Serbest Metin Sorusu');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'yes_no_question', 'Evet soru yok');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'multiple_choice_question', 'Çoktan seçmeli soru');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'add_question', 'Soru ekle');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'add_an_answers', 'Cevap ekle');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'question_one', 'Birinci soru');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'question_two', 'Soru İki');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'question_three', 'Soru Üç');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'edit_job', 'İşi Düzenle');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'delete_job', 'İşi Sil');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'apply_now', 'Şimdi Uygula');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'experience', 'Deneyim');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'add_experience', 'Deneyim ekle');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'position', 'pozisyon');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'where_did_you_work', 'Nerede çalıştın?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_currently_work', 'şuanda burada çalışıyorum');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_answer_questions', 'Lütfen soruları cevaplayınız');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'you_apply_this_job', 'Bu iş için zaten başvurdunuz.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'search_for_jobs', 'İş aramak');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_available_jobs', 'Gösterilecek müsait iş yok.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'load_more_jobs', 'Daha fazla iş yükle');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'show_apply', 'Göster Uygula');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'common_things', 'Ortak şeyler');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'thing_in_common', 'Ortak şey');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'things_in_common', 'Ortak şeyler');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'weather_unit', 'Hava Durumu Birimi');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_applied', 'Zaten uygulandı');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'apply_your_job', 'iş isteğinize uygulanır.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'apply_job_successfully', 'Bu işe başarıyla başvurdunuz.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_successfully_created', 'İş isteği başarıyla oluşturuldu.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_successfully_edited', 'İş isteği başarıyla güncellendi.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'invited_to_group', 'sizi grup sohbetine katılmaya davet etti.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'declined_group_chat_request', 'grup sohbeti davetinizi reddetti.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'offer_job', 'Bir iş teklif');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'posted_job_offer', 'bir iş teklifi yayınladı.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'verified_page', 'Doğrulanmış sayfa');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'unfriend', 'Arkadaşlıktan Çıkar');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'funding', 'Fonlar');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'my_funding', 'Finansman İsteklerim');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'create_new_funding', 'Yeni fon talebi yarat');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'funding_created', 'Finansman isteği başarıyla oluşturuldu.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_funding_found', 'Fon bulunamadı');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'delete_fund', 'silmek');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirm_delete_fund', 'Bu fon talebini silmek istediğinize emin misiniz?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'funding_edited', 'Fon talebi başarıyla güncellendi.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'most_recent_funding', 'En yeni finansman');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'raised_of', 'Yükseltilmiş');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'donate', 'bağışlamak');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'donated_to', 'fon talebinize bağışta bulunabilirsiniz.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'total_donations', 'Toplam bağış');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'recent_donations', 'Son bağışlar');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'instagram', 'Instagram');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'created_donation_request', 'bağış isteği oluşturdu');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'people_with_common', 'Ortak şeyleri olan insanlarla tanışın');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'donated_to_request', 'fon talebine bağışlandı');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'you_cant_pay', '{{Money}} \'dan daha fazla ödeyemezsiniz.');
    $lang_update_queries[] = Wo_UpdateLangs($value, 'confirm_delete_job', 'Bu işi silmek istediğinden emin misin?');
        } else if ($value == 'english') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'accept_group_chat_request', 'Accepted your group chat invitation.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'jobs', 'Jobs');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'post_job_text', 'Post a job for {{page_name}} to reach the right applicants on ');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'create_job', 'Create Job');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_title', 'Job Title');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'salary_range', 'Salary Range');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'minimum', 'Minimum');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'maximum', 'Maximum');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_hour', 'Per Hour');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_day', 'Per Day');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_week', 'Per Week');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_month', 'Per Month');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_year', 'Per Year');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_type', 'Job Type');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'full_time', 'Full Time');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'part_time', 'Part Time');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'internship', 'Internship');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'volunteer', 'Volunteer');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'contract', 'Contract');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_des_text', 'Describe the responsibilities and preferred skills for this job');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_add_iamge', 'Add an image to help applicants see what it\'s like to work at this location.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'use_cover_photo', 'Use Cover Photo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'questions', 'Questions');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'free_text_question', 'Free Text Question');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'yes_no_question', 'Yes/No Question');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'multiple_choice_question', 'Multiple Choice Question');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'add_question', 'Add Question');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'add_an_answers', 'Add answers');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'question_one', 'Question One');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'question_two', 'Question Two');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'question_three', 'Question Three');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'edit_job', 'Edit Job');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'delete_job', 'Delete Job');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'apply_now', 'Apply Now');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'experience', 'Experience');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'add_experience', 'Add Experience');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'position', 'Position');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'where_did_you_work', 'Where did you work?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_currently_work', 'I currently work here');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_answer_questions', 'Please answer the questions');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'you_apply_this_job', 'You have already applied for this job.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'search_for_jobs', 'Search for jobs');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_available_jobs', 'No available jobs to show.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'load_more_jobs', 'Load more jobs');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'show_apply', 'Show Applies');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'common_things', 'Common Things');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'thing_in_common', 'Thing in common');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'things_in_common', 'Things in common');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'weather_unit', 'Weather Unit');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_applied', 'Already applied');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'apply_your_job', 'applied to your job request.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'apply_job_successfully', 'You have successfully applied to this job.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_successfully_created', 'Job request successfully created.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_successfully_edited', 'Job request successfully updated.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'invited_to_group', 'invited you to join the group chat.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'declined_group_chat_request', 'declined your group chat invitation.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'offer_job', 'Offer a job');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'posted_job_offer', 'posted a job offer');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'verified_page', 'Verified Page');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'unfriend', 'Unfriend');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'funding', 'Fundings');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'my_funding', 'My Funding Requests');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'create_new_funding', 'Create new funding request');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'funding_created', 'Funding request has been successfully created.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_funding_found', 'No funding found');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'delete_fund', 'Delete');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirm_delete_fund', 'Are you sure that you want to delete this funding request?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'funding_edited', 'Funding request has been successfully updated.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'most_recent_funding', 'Most recent funding');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'raised_of', 'Raised of');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'donate', 'Donate');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'donated_to', 'donated to your funding request.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'total_donations', 'Total donations');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'recent_donations', 'Recent donations');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'instagram', 'Instagram');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'created_donation_request', 'created a donation request');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'people_with_common', 'Meet people with things in common');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'donated_to_request', 'donated to a funding request');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'you_cant_pay', 'You can`t donate more than {{money}}');
    $lang_update_queries[] = Wo_UpdateLangs($value, 'confirm_delete_job', 'Are you sure that you want to delete this job?');
        } else if ($value != 'english') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'accept_group_chat_request', 'Accepted your group chat invitation.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'jobs', 'Jobs');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'post_job_text', 'Post a job for {{page_name}} to reach the right applicants on ');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'create_job', 'Create Job');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_title', 'Job Title');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'salary_range', 'Salary Range');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'minimum', 'Minimum');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'maximum', 'Maximum');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_hour', 'Per Hour');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_day', 'Per Day');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_week', 'Per Week');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_month', 'Per Month');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'per_year', 'Per Year');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_type', 'Job Type');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'full_time', 'Full Time');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'part_time', 'Part Time');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'internship', 'Internship');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'volunteer', 'Volunteer');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'contract', 'Contract');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_des_text', 'Describe the responsibilities and preferred skills for this job');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_add_iamge', 'Add an image to help applicants see what it\'s like to work at this location.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'use_cover_photo', 'Use Cover Photo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'questions', 'Questions');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'free_text_question', 'Free Text Question');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'yes_no_question', 'Yes/No Question');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'multiple_choice_question', 'Multiple Choice Question');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'add_question', 'Add Question');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'add_an_answers', 'Add answers');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'question_one', 'Question One');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'question_two', 'Question Two');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'question_three', 'Question Three');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'edit_job', 'Edit Job');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'delete_job', 'Delete Job');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'apply_now', 'Apply Now');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'experience', 'Experience');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'add_experience', 'Add Experience');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'position', 'Position');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'where_did_you_work', 'Where did you work?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_currently_work', 'I currently work here');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_answer_questions', 'Please answer the questions');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'you_apply_this_job', 'You have already applied for this job.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'search_for_jobs', 'Search for jobs');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_available_jobs', 'No available jobs to show.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'load_more_jobs', 'Load more jobs');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'show_apply', 'Show Applies');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'common_things', 'Common Things');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'thing_in_common', 'Thing in common');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'things_in_common', 'Things in common');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'weather_unit', 'Weather Unit');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_applied', 'Already applied');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'apply_your_job', 'applied to your job request.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'apply_job_successfully', 'You have successfully applied to this job.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_successfully_created', 'Job request successfully created.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'job_successfully_edited', 'Job request successfully updated.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'invited_to_group', 'invited you to join the group chat.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'declined_group_chat_request', 'declined your group chat invitation.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'offer_job', 'Offer a job');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'posted_job_offer', 'posted a job offer');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'verified_page', 'Verified Page');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'unfriend', 'Unfriend');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'funding', 'Fundings');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'my_funding', 'My Funding Requests');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'create_new_funding', 'Create new funding request');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'funding_created', 'Funding request has been successfully created.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'no_funding_found', 'No funding found');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'delete_fund', 'Delete');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirm_delete_fund', 'Are you sure that you want to delete this funding request?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'funding_edited', 'Funding request has been successfully updated.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'most_recent_funding', 'Most recent funding');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'raised_of', 'Raised of');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'donate', 'Donate');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'donated_to', 'donated to your funding request.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'total_donations', 'Total donations');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'recent_donations', 'Recent donations');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'instagram', 'Instagram');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'created_donation_request', 'created a donation request');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'people_with_common', 'Meet people with things in common');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'donated_to_request', 'donated to a funding request');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'you_cant_pay', 'You can`t donate more than {{money}}');
    $lang_update_queries[] = Wo_UpdateLangs($value, 'confirm_delete_job', 'Are you sure that you want to delete this job?');
        }
    }
    if (!empty($lang_update_queries)) {
        foreach ($lang_update_queries as $key => $query) {
            $sql = mysqli_query($sqlConnect, $query);
        }
        $job_lang = array(
            'Accounting & Finance',
            'Admin & Office',
            'Art & Design',
            'Business Operations',
            'Cleaning & Facilities',
            'Community & Social Services',
            'Computer & Data',
            'Construction & Mining',
            'Education',
            'Farming & Forestry',
            'Healthcare',
            'Installation, Maintenance & Repair',
            'Legal',
            'Management',
            'Manufacturing',
            'Media & Communication',
            'Personal Care',
            'Protective Services',
            'Restaurant & Hospitality',
            'Retail & Sales',
            'Science & Engineering',
            'Sports & Entertainment',
            'Transportation'
        );
        foreach ($job_lang as $key => $value) {
            $id = $db->insert(T_LANGS, array(
                'english' => $value,
                'type' => 'category'
            ));
            $db->where('id', $id)->update(T_LANGS, array(
                'lang_key' => $id
            ));
            $db->insert(T_JOB_CATEGORY, array(
                'lang_key' => $id
            ));
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
                     <h2 class="light">Update to v2.4 </span></h2>
                     <div class="setting-well">
                        <h4>Changelog</h4>
                        <ul class="wo_update_changelog">
                            <li> [Added] the ability to send messages to pages.</li>
                                <li> [Added] the ability to accept or decline a group chat invitation.</li>
                                <li> [Added] job system, users can now create jobs and hire.</li>
                                <li> [Added] weather plugin to sidebar. </li>
                                <li> [Added] common things page, now you can find users that matches your information.</li>
                                <li> [Added] funding system, users can create funds, and get paid.</li>
                                <li> [Added] new APIs. </li>
                                <li> [Fixed] 20+ reported bugs.</li>
                                <li> [Fixed] bugs in API.</li>
                                <li> [Improved] speed.</li>
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
    "UPDATE `Wo_Config` SET `value` = '2.4' WHERE `name` = 'version';",
    "ALTER TABLE `Wo_Messages` ADD `page_id` INT(11) NOT NULL DEFAULT '0' AFTER `group_id`;",
    "ALTER TABLE `Wo_UsersChat` ADD `page_id` INT(11) NOT NULL DEFAULT '0' AFTER `conversation_user_id`;",
    "ALTER TABLE `Wo_Notifications` ADD `group_chat_id` INT(11) NOT NULL DEFAULT '0' AFTER `group_id`;",
    "CREATE TABLE `Wo_Job_Categories` (`id` int(11) NOT NULL AUTO_INCREMENT,`lang_key` varchar(160) NOT NULL DEFAULT '',PRIMARY KEY (`id`)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'job_system', '1');",
    "ALTER TABLE `Wo_Posts` ADD `job_id` INT(11) NOT NULL DEFAULT '0' AFTER `color_id`;",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'weather_widget', '1');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'common_things', '1');",
    "ALTER TABLE `Wo_Users` ADD `weather_unit` VARCHAR(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'us' AFTER `school_completed`;",
    "CREATE TABLE `Wo_Job` (`id` int(11) NOT NULL AUTO_INCREMENT,`user_id` int(11) DEFAULT '0',`page_id` int(11) NOT NULL DEFAULT '0',`title` varchar(200) NOT NULL DEFAULT '',`location` varchar(100) NOT NULL DEFAULT '',`lat` varchar(50) NOT NULL DEFAULT '',`lng` varchar(50) NOT NULL DEFAULT '',`minimum` varchar(50) NOT NULL DEFAULT '0',`maximum` varchar(50) NOT NULL DEFAULT '0',`salary_date` varchar(50) NOT NULL DEFAULT '',`job_type` varchar(50) NOT NULL DEFAULT '',`category` varchar(50) NOT NULL DEFAULT '',`question_one` varchar(200) NOT NULL DEFAULT '',`question_one_type` varchar(100) NOT NULL DEFAULT '',`question_one_answers` text,`question_two` varchar(200) NOT NULL DEFAULT '',`question_two_type` varchar(100) NOT NULL DEFAULT '',`question_two_answers` text,`question_three` varchar(200) NOT NULL DEFAULT '',`question_three_type` varchar(100) NOT NULL DEFAULT '',`question_three_answers` text,`description` text,`image` varchar(300) NOT NULL DEFAULT '',`image_type` varchar(11) NOT NULL DEFAULT '',`status` int(11) NOT NULL DEFAULT '1',`time` varchar(50) NOT NULL DEFAULT '',PRIMARY KEY (`id`),KEY `user_id` (`user_id`),KEY `page_id` (`page_id`)) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;",
    "CREATE TABLE `Wo_Job_Apply` (`id` int(11) NOT NULL AUTO_INCREMENT,`user_id` int(11) NOT NULL DEFAULT '0',`job_id` int(11) NOT NULL DEFAULT '0',`page_id` int(11) NOT NULL DEFAULT '0',`user_name` varchar(100) NOT NULL DEFAULT '',`phone_number` varchar(50) NOT NULL DEFAULT '',`location` varchar(50) NOT NULL DEFAULT '',`email` varchar(100) NOT NULL DEFAULT '',`question_one_answer` varchar(200) NOT NULL DEFAULT '',`question_two_answer` varchar(200) NOT NULL DEFAULT '',`question_three_answer` varchar(200) NOT NULL DEFAULT '',`position` varchar(100) NOT NULL DEFAULT '',`where_did_you_work` varchar(100) NOT NULL DEFAULT '',`experience_description` varchar(300) NOT NULL DEFAULT '',`experience_start_date` varchar(50) NOT NULL DEFAULT '',`experience_end_date` varchar(50) NOT NULL DEFAULT '',`time` varchar(50) NOT NULL DEFAULT '',PRIMARY KEY (`id`),KEY `user_id` (`user_id`),KEY `job_id` (`job_id`),KEY `page_id` (`page_id`)) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;",
    "ALTER TABLE `Wo_Job` ADD `currency` VARCHAR(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' AFTER `image_type`;",
    "CREATE TABLE `Wo_Blog_Reaction` (`id` int(11) NOT NULL AUTO_INCREMENT,`user_id` int(11) NOT NULL DEFAULT '0',`blog_id` int(11) NOT NULL DEFAULT '0',`comment_id` int(11) NOT NULL DEFAULT '0',`reply_id` int(11) NOT NULL DEFAULT '0',`reaction` varchar(50) NOT NULL DEFAULT '',`time` varchar(50) NOT NULL DEFAULT '',PRIMARY KEY (`id`),KEY `user_id` (`user_id`),KEY `blog_id` (`blog_id`),KEY `comment_id` (`comment_id`),KEY `reply_id` (`reply_id`)) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'funding_system', '1');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'weather_key', '');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'funding_request', 'all');",
    "CREATE TABLE `Wo_Funding` (`id` int(11) NOT NULL AUTO_INCREMENT,`hashed_id` varchar(100) NOT NULL DEFAULT '',`title` varchar(100) NOT NULL DEFAULT '',`description` varchar(600) NOT NULL DEFAULT '',`amount` varchar(11) NOT NULL DEFAULT '0',`user_id` int(11) NOT NULL DEFAULT '0',`image` varchar(200) NOT NULL DEFAULT '',`time` varchar(50) NOT NULL DEFAULT '',PRIMARY KEY (`id`),KEY `hashed_id` (`hashed_id`),KEY `user_id` (`user_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
    "CREATE TABLE `Wo_Funding_Raise` (`id` int(11) NOT NULL AUTO_INCREMENT,`funding_id` int(11) NOT NULL DEFAULT '0',`user_id` int(11) NOT NULL DEFAULT '0',`amount` varchar(11) NOT NULL DEFAULT '0',`time` varchar(50) NOT NULL DEFAULT '',PRIMARY KEY (`id`),KEY `user_id` (`user_id`),KEY `funding_id` (`funding_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'donate_percentage', '0');",
    "ALTER TABLE `bank_receipts` ADD `fund_id` INT(11) NOT NULL DEFAULT '0' AFTER `user_id`;",
    "ALTER TABLE `Wo_Posts` ADD `fund_raise_id` INT(11) NOT NULL DEFAULT '0' AFTER `job_id`;",
    "ALTER TABLE `Wo_Users` ADD `ref_user_id` INT(11) NOT NULL DEFAULT '0' AFTER `referrer`;",
    "ALTER TABLE `Wo_Posts` ADD `fund_id` INT(11) NOT NULL DEFAULT '0' AFTER `fund_raise_id`;",
    "ALTER TABLE `Wo_Posts` ADD INDEX(`fund_raise_id`);",
    "ALTER TABLE `Wo_Posts` ADD INDEX(`fund_id`);",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'accept_group_chat_request');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'jobs');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'post_job_text');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'create_job');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'job_title');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'salary_range');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'minimum');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'maximum');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'per_hour');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'per_day');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'per_week');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'per_month');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'per_year');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'job_type');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'full_time');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'part_time');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'internship');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'volunteer');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'contract');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'job_des_text');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'job_add_iamge');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'use_cover_photo');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'questions');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'free_text_question');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'yes_no_question');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'multiple_choice_question');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'add_question');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'add_an_answers');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'question_one');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'question_two');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'question_three');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'edit_job');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'delete_job');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'apply_now');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'experience');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'add_experience');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'position');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'where_did_you_work');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'i_currently_work');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'please_answer_questions');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'you_apply_this_job');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'search_for_jobs');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'no_available_jobs');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'load_more_jobs');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'show_apply');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'common_things');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'thing_in_common');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'things_in_common');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'weather_unit');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'job_applied');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'apply_your_job');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'apply_job_successfully');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'job_successfully_created');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'job_successfully_edited');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'invited_to_group');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'declined_group_chat_request');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'offer_job');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'posted_job_offer');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'verified_page');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'unfriend');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'funding');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'my_funding');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'create_new_funding');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'funding_created');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'no_funding_found');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'delete_fund');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'confirm_delete_fund');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'funding_edited');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'most_recent_funding');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'raised_of');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'donate');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'donated_to');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'total_donations');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'recent_donations');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'instagram');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'created_donation_request');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'people_with_common');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'donated_to_request');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'you_cant_pay');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'confirm_delete_job');",
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