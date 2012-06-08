<?php
require_once 'ajaxMain.php';

if (isset($_POST['artikul']) && !empty($_POST['artikul']) 
        && isset($_POST['commentIndex']) && is_numeric($_POST['commentIndex'])
        && isset($_POST['status']) && ($_POST['status'] == 'yes' || $_POST['status'] == 'no') 
        
        ) {
   $artikul = $_POST['artikul'];
   $commentIndex = $_POST['commentIndex'];
   
   if (($row = $db->fetchRow("SELECT `id`, `tip_helpful_yes`, `tip_helpful_no` FROM `comments` WHERE `id` = '$commentIndex' AND `goods_artikul` = '$artikul'"))) {
      
      $tipHelpFulYes = intval($row['tip_helpful_yes']);      
      $tipHelpFulNo = intval($row['tip_helpful_no']);
      if ($_POST['status'] == 'yes') {         
         $tipHelpFulYes ++;         
      }
      
      if ($_POST['status'] == 'no') {
         $tipHelpFulNo ++;
      }
      
      if (!isset( $_SESSION['is_tip_helpful'][$commentIndex][$artikul])) {
         $db->update('comments', array('tip_helpful_yes'=>$tipHelpFulYes, 'tip_helpful_no'=>$tipHelpFulNo), "id=$row[id]");
      }
      
      if ($_POST['status'] == 'yes') {
         echo $tipHelpFulYes;
         $_SESSION['is_tip_helpful'][$commentIndex][$artikul] = true;
      }
      
      if ($_POST['status'] == 'no') {
         $_SESSION['is_tip_helpful'][$commentIndex][$artikul] = true;
         echo $tipHelpFulNo;
      }
      
   }
}

?>
