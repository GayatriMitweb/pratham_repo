<?php 

include_once("../../../model/model.php");

$dest_id = $_POST['dest_id'];



//$query ="select * from gallary_master where 1 ";

if($dest_id != '') {

  $query = "select * from gallary_master where dest_id = '$dest_id'";

}

?>



<?php 

$count = 0;
$sq_gallary = mysql_query($query); ?>

		<?php

		while($row_gallary = mysql_fetch_assoc($sq_gallary)){

			    $url = $row_gallary['image_url'];
          $pos = strstr($url,'uploads');

          if ($pos != false){

              $newUrl1 = preg_replace('/(\/+)/','/',$row_gallary['image_url']); 
              $newUrl = BASE_URL.str_replace('../', '', $newUrl1);

          }

          else{

              $newUrl =  $row_gallary['image_url']; 

          }

		?>		
       <div class="col-md-3 col-sm-4">

         <div class="table-single-image mg_bt_30 mg_bt_10_sm_xs" style="width:100%;">

           <img src="<?php echo $newUrl; ?>" class="img-responsive">

           <span class="img-check-btn"><button type="button" class="btn btn-danger btn-sm" onclick="delete_image(<?php echo $row_gallary['entry_id']; ?>)" title="Remove"><i class="fa fa-times" aria-hidden="true"></i></button></span>

           <div class="table-image-btns">

             <ul>

               <li><button class="btn btn-sm" onclick="display_image(<?php echo $row_gallary['entry_id']; ?>);" title="View image"><i class="fa fa-eye"></i></button></li>

               <li><button class="btn btn-sm" onclick="update_modal(<?php echo $row_gallary['entry_id']; ?>);" title="Edit Description"><i class="fa fa-pencil"></i></button></li>

             </ul>

           </div>

         </div>

       </div>

		<?php	}	?>

		

<script>

$('#tbl_image_list').dataTable({

		"pagingType": "full_numbers"

	});

</script>