<?php

    global $wo, $sqlConnect;
    $root=__DIR__;
    require_once($root.'/config.php');
    require_once('assets/init.php');


    $contacts = getTableData('contact', [
        'type' => 2
    ]);

    foreach($contacts as $contact) {
        $contact_id = $contact['id'];

        updatedBuyerTags($contact_id);
    }

    exit; 