<?php

$frm_name  = "АНДЕРСЕН";
$email 	   = 'info@det-sad-andersen.ru';
$recepient = "speeker@inbox.ru, kailina2007@mail.ru";
$sitename  = "Детский сад 'АндерсеН'";
$subject   = "Новая заявка с сайта \"$sitename\"";

$name = trim($_POST["name"]);
$phone = trim($_POST["phone"]);
$formname = trim($_POST["form-name"]);

$message = "
Имя: $name <br>
Телефон: $phone <br>
";

mail($recepient, $subject, $message, "From: $frm_name <$email>" . "\r\n" . "Reply-To: $email" . "\r\n" . "X-Mailer: PHP/" . phpversion() . "\r\n" . "Content-type: text/html; charset=\"utf-8\"");
