<?php 
include_once('../../../../model/model.php');
include_once('../../../../model/visa_password_ticket/ticket/ticket_refund.php');
include_once('../../../../model/app_settings/transaction_master.php');
include_once('../../../../model/app_settings/bank_cash_book_master.php');

$ticket_refund = new ticket_refund;
$ticket_refund->ticket_refund_save();
?>