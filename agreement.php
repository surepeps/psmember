<?php

    error_reporting (E_ALL ^ E_NOTICE);
    require_once('assets/init.php');
    $id = filter('id');

    $contact = getTableData('contact', ['id' => $id], 1);
    $file = "upload/files/contact/Agreement-Signature-{$id}.png";

?>

<h2 class="text-center">BUYER/INVESTOR AGREEMENT</h2>
<h3 class="text-center">THIS IS A LEGALLY BINDING AGREEMENT</h3>

<p class="text-center">
    I agree to conduct all inquires into and discussions about this property solely through 
    PropertySalers Team Or Active Members of PropertySalers and shall not directly contact 
    the seller! I also agree to NOT re-promote ANY properties unless you are a PropertySalers 
    Member which gives you consent and authority to promote our properties with restrictions. 
    Any unauthorized contact with Seller will be considered intentional interference with a 
    contract. I agree to pay damages of $100,000 to PropertySalers LLC if I attempt to 
    circumvent or intentionally interfere with this Contract or/and publicly Re-promote our 
    properties online or offline. 
</p>



<div class="filler">
    <ul>
        <li>
            <h2>Buyer Detail:</h2>
            <div class="border">
                <span>Signature:</span>
                <span class="me">
                    <img width="100" src="<?= $file ?>" alt="">
                </span>

            </div>
            <div class="border">
                <span>Printed Name:</span>
                <span class="me"><?= $contact['firstname'] . ' ' . $contact['lastname'] ?></span>
                
            </div>
            <div class="border">
                <span>Date:</span>
                <span class="me"><?= date('Y-m-d H:i:s') ?></span>
            </div>
        </li>
    </ul>
</div>

