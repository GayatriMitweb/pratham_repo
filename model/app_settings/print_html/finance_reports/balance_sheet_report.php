<?php
include "../../../model.php";
include "../print_functions.php";
?>
<script type="text/javascript">
function cal_subgroup_amount(total_subgroup_amount)
{
	var new_amount = parseFloat(total_subgroup_amount);
	return new_amount.toFixed(2);
}
function cal_head_amount(total_head_amount)
{
	var new_amount = parseFloat(total_head_amount);
	return new_amount.toFixed(2);
}
</script>
<?php  
$from_date = $_GET['from_date'];
$to_date = $_GET['to_date'];
$financial_year_id = $_GET['financial_year_id'];
$branch_admin_id = $_GET['branch_admin_id'];
$sfinancial_year_id = $_GET['sfinancial_year_id'];
$sbranch_admin_id = $_GET['sbranch_admin_id'];
$session_financial_year_id = $_SESSION['financial_year_id'];


if($from_date != '' || $to_date != ''){
	$from_date = get_date_user($from_date);
	$to_date = get_date_user($to_date);
	$pnl_date = ' ('.get_date_user($from_date).' To '.get_date_user($to_date).')';
}
else if($financial_year_id != ''){
	$sq_finance = mysql_fetch_assoc(mysql_query("select * from financial_year where financial_year_id='$financial_year_id'"));
	$pnl_date = ' ('.get_date_user($sq_finance['from_date']).' To '.get_date_user($sq_finance['to_date']).')';
}
else{
	$sq_finance = mysql_fetch_assoc(mysql_query("select * from financial_year where financial_year_id='$session_financial_year_id'"));
	$pnl_date = ' (As On '.get_date_user($sq_finance['to_date']).')';
}
?>

<section class="print_header main_block mg_bt_20">
  <div class="col-md-8 no-pad">
  <span class="title"><i class="fa fa-file-text"></i> Balance Sheet<?= $pnl_date ?></span>
    <div class="print_header_logo">
      <img src="<?php echo $admin_logo_url; ?>" class="img-responsive mg_tp_10">
    </div>
  </div>
  <div class="col-md-4 no-pad">
    <div class="print_header_contact text-right">
      <span class="title"><?php echo $app_name; ?></span><br>
      <p><?php echo ($branch_status=='yes' && $role!='Admin') ? $branch_details['address1'].','.$branch_details['address2'].','.$branch_details['city'] : $app_address ?></p>
      <p class="no-marg"><i class="fa fa-phone" style="margin-right: 5px;"></i> <?php echo ($branch_status=='yes' && $role!='Admin') ? 
       $branch_details['contact_no'] : $app_contact_no ?></p>
      <p><i class="fa fa-envelope" style="margin-right: 5px;"></i> <?php echo $app_email_id; ?></p>

    </div>
  </div>
</section>
<div class="row mg_tp_20">
<!-- //////////////////////////////////////////////////////CREDIT START//////////////////////////////////////////////////////////////////////////////////////  -->
	<div class="col-md-6 pl_sheet">
		<div class="panel panel-default main_block">
			<div class="panel-heading main_block">
				<div class="col-md-6 no-pad">
					<strong>Liabilities</strong>
				</div>
				<div class="col-md-6 no-pad text-right"><strong>Amount</strong></div>
			</div>
			<div class="quadrant main_block">				
			<!-- List Heading -->
			<?php 

			$count_d =1;
			$head_count_d =1;
			$tempq = "select * from head_master where head_id in('1','8','3','6')";
			$sq_head = mysql_query($tempq);
			//2 Heads(D2)
			while($row_head = mysql_fetch_assoc($sq_head))
			{			
				 ?>
				<div class="list_heading main_block">
					<div class="col-md-8 no-pad"><h4><?= $row_head['head_name'] ?></h4></div>
					<div class="col-md-4 no-pad text-right">
						<span class="list_heading_count" id="head_amount_d<?= $head_count_d ?>"><?= $total_head_amount_d ?></span>
					</div>
				</div>

			<?php 
			 $sq_group = mysql_query("select * from group_master where head_id='$row_head[head_id]' order by group_id desc");
			 //Groups
			 while($row_group = mysql_fetch_assoc($sq_group))
			      { 
			      	$sq_subgroup = mysql_query("select * from subgroup_master where group_id='$row_group[group_id]'");
			         
			        $total_head_amount_d = 0;
			         //Sub groups
			      	 while($row_subgroup = mysql_fetch_assoc($sq_subgroup))
			         { 	
			         	$total_sub_group_amount_d = 0;
			      	   ?>
						<!-- Part Heading -->
						<div class="part_heading main_block">
							<h4 class="main_block"><div class="col-md-6 col-md-offset-1 no-pad"><?= $row_subgroup['subgroup_name'] ?></div>
							<div class="col-md-4 no-pad text-right">
								<span class="list_heading_count" id="subgroup_amount_d<?= $count_d ?>"><?= '0.00' ?></span>
							</div></h4>								
						</div>
						<?php 			
							$sq_finance = "select * from ledger_master where group_sub_id='$row_subgroup[subgroup_id]'";
		 
						    $q = mysql_query($sq_finance);
				            while($row_q = mysql_fetch_assoc($q))
				            {
				            	$total_amount = 0;
								$debit_amount = 0;	 $credit_amount = 0;
								
								$debit_amount = ($row_q['balance_side']=='Debit') ? $row_q['balance'] : '0';
								$credit_amount = ($row_q['balance_side']=='Credit') ? $row_q['balance'] : '0';
								
				            	$q1 = "select * from finance_transaction_master where gl_id='$row_q[ledger_id]'";
								if($from_date!="" && $to_date!=""){
									$from_date = get_date_db($from_date);
									$to_date = get_date_db($to_date);
									$q1 .=" and payment_date between '$from_date' and '$to_date'";	
								}
								if($financial_year_id != ""){
									$q1 .=" and financial_year_id<='$financial_year_id'";		
								} 	
								if($branch_admin_id != "0"){
									$q1 .=" and branch_admin_id='$branch_admin_id'";		
								} 	

				            	$sq_opening_balance = mysql_query($q1);	
				            	while($row_balance = mysql_fetch_assoc($sq_opening_balance)){
				            		if($row_balance['payment_side'] == 'Debit'){
				            			$debit_amount += $row_balance['payment_amount'];
				            		}else{
				            			$credit_amount += $row_balance['payment_amount'];
				            		}
				            	}/*
				            	if($debit_amount > $credit_amount){
				            		$total_amount = $debit_amount - $credit_amount;
				            	}else{*/
				            		$total_amount = $credit_amount - $debit_amount;
				            	//}

				            	if($total_amount != ''){		
					            ?>			
					            <div class="part_entry main_block">
							        <div class="col-md-2 no-pad text-right"></div>
									<div class="col-md-8 no-pad">
							        	<span class="part_entry_text"><?= $row_q['ledger_name'] ?></span>
							        </div>
							        <div class="col-md-2 no-pad text-right">
							        	<span class="part_entry_m_count" id="subgroup_amount"><?= number_format($total_amount,2) ?></span>
							        </div>
								</div>	
								<?php 
								
								if($row_q['ledger_id'] == '165'){
								    $q1 = "select * from finance_transaction_master where gl_id='165'";
    								if($from_date!="" && $to_date!=""){
    									$from_date = get_date_db($from_date);
    									$to_date = get_date_db($to_date);
    									$q1 .=" and payment_date between '$from_date' and '$to_date'";	
    								}
    								$q1 .=" and financial_year_id='$sfinancial_year_id'";
    								$q1 .=" and branch_admin_id='$sbranch_admin_id'";
									$sq_opening_balance = mysql_query($q1);	
				            		while($row_balance = mysql_fetch_assoc($sq_opening_balance)){
					            		if($row_balance['payment_side'] == 'Debit'){ $total_sub_group_amount_d += $total_amount;  }
					            		if($row_balance['payment_side'] == 'Credit'){ $total_sub_group_amount_d += $total_amount;   } 
					            		
					            	}		            	
					            }
								else{ $total_sub_group_amount_d += $total_amount;  }
								}
							} ?>									
			         		<script>
			         			var subgroup_amount_d = cal_subgroup_amount('<?= $total_sub_group_amount_d ?>');
			         			$('#subgroup_amount_d'+<?= $count_d ?>).html(subgroup_amount_d);
			         		</script>  
					        <?php $count_d++; 						      
					        $total_head_amount_d += $total_sub_group_amount_d;					        
					    }
				      }	 ?>													
	         		<script>
	         			var head_amount_d = cal_head_amount('<?= $total_head_amount_d ?>');
	         			$('#head_amount_d'+<?= $head_count_d ?>).html(head_amount_d);	
	         			var tmp_head_amount_d1 = $('#span_total_sales_d1').html();   
	         			var tmp_head_amount_d = parseFloat(head_amount_d) + parseFloat(tmp_head_amount_d1);    	
	         			$('#span_total_sales_d1').html(tmp_head_amount_d.toFixed(2));
	         		</script>
				  <?php $head_count_d++; } ?>	

			</div>   <!-- quadrant end -->


			<!-- Total -->
			<div class="panel-footer main_block">
				<div class="row">
					<div class="col-md-8"><strong>Total :</strong></div>
					<div class="col-md-4 text-right"><strong id="span_total_sales_d1">0.00</strong></div>
				</div>
			</div>														
     		<script>
     			var head_amount_d1 = $('#head_amount_d1').html();
     			var head_amount_d2 = $('#head_amount_d2').html();
     			var head_amount_d3 = $('#head_amount_d3').html();
     			var head_amount_d4 = $('#head_amount_d4').html();
     			var tmp_head_amount_d = parseFloat(head_amount_d1) + parseFloat(head_amount_d2) + parseFloat(head_amount_d3) + parseFloat(head_amount_d4);
     			tmp_head_amount_d = parseFloat(tmp_head_amount_d);    	
     			$('#span_total_sales_d1').html(tmp_head_amount_d.toFixed(2));
     		</script>		
		</div>
	</div>
<!-- //////////////////////////////////////////////////////CREDIT END////////////////////////////////////////////////////////////////////////////////////////// -->

<!-- ////////////////////////////////////////////////////DEBIT START////////////////////////////////////////////////////////////////////////////////////////// -->
	<div class="col-md-6 pl_sheet">
		<div class="panel panel-default main_block">
			<div class="panel-heading main_block">
				<div class="col-md-6 no-pad">
					<strong>ASSETS</strong>
				</div>
				<div class="col-md-6 no-pad text-right"><strong>Amount</strong></div>
			</div>
			<div class="quadrant main_block">
			<?php 

			$count =1;
			$head_count =1;
			$sq_head = mysql_query("select * from head_master where head_id in('2','7') order by head_id desc");
			//1st Head(D1)
			while($row_head = mysql_fetch_assoc($sq_head))
			{			
				 ?>				 
				<!-- List Heading -->
				<div class="list_heading main_block">
					<div class="col-md-8 no-pad"><h4><?= $row_head['head_name'] ?></h4></div>
					<div class="col-md-4 no-pad text-right">
						<span class="list_heading_count" id="head_amount<?= $head_count ?>"><?= $total_head_amount ?></span>
					</div>
				</div>

			<?php
			 $sq_group = mysql_query("select * from group_master where head_id='$row_head[head_id]' order by group_id desc");
			 //Groups
					$total_head_amount = 0;
			 while($row_group = mysql_fetch_assoc($sq_group))
			      { 				       
			      	$sq_subgroup = mysql_query("select * from subgroup_master where group_id='$row_group[group_id]'");
			         //Sub groups
			      	 while($row_subgroup = mysql_fetch_assoc($sq_subgroup))
			         { 	
			         	$total_sub_group_amount = 0;
			      	   ?>
						<!-- Part Heading -->
						<div class="part_heading main_block">
							<h4 class="main_block"><div class="col-md-6 col-md-offset-1 no-pad"><?= $row_subgroup['subgroup_name'] ?></div>
							<div class="col-md-4 no-pad text-right">
								<span class="list_heading_count" id="subgroup_amount<?= $count ?>"><?= '0.00' ?></span>
							</div></h4>								
						</div>
						<?php 
						    $sq_finance = "select * from ledger_master where group_sub_id='$row_subgroup[subgroup_id]'"; 
						    $q = mysql_query($sq_finance);
				            while($row_q = mysql_fetch_assoc($q))
				            {
				            	$total_amount = 0;	
				            	$debit_amount = 0;	 $credit_amount = 0;
								$debit_amount = ($row_q['balance_side']=='Debit') ? $row_q['balance'] : '0';
								$credit_amount = ($row_q['balance_side']=='Credit') ? $row_q['balance'] : '0';

				            	$q1 = "select * from finance_transaction_master where gl_id='$row_q[ledger_id]'";
								if($from_date!="" && $to_date!=""){
									$from_date = get_date_db($from_date);
									$to_date = get_date_db($to_date);
									$q1 .=" and payment_date between '$from_date' and '$to_date'";	
								}
								if($financial_year_id != ""){
									$q1 .=" and financial_year_id<='$financial_year_id'";		
								} 	
								if($branch_admin_id != "0"){
									$q1 .=" and branch_admin_id='$branch_admin_id'";		
								} 	

				            	$sq_opening_balance = mysql_query($q1);	
				            	while($row_balance = mysql_fetch_assoc($sq_opening_balance)){
				            		if($row_balance['payment_side'] == 'Debit'){
				            			$debit_amount += $row_balance['payment_amount'];
				            		}else{
				            			$credit_amount += $row_balance['payment_amount'];
				            		}
				            	}
				            	$total_amount = $debit_amount - $credit_amount;

				            	if($total_amount != ''){
					            ?>					         
									<div class="part_entry main_block">
								        <div class="col-md-2 no-pad text-right"></div>
										<div class="col-md-7 no-pad">
								        	<span class="part_entry_text"><?= $row_q['ledger_name'] ?></span>
								        </div>
								        <div class="col-md-3 no-pad text-right">
								        	<span class="part_entry_m_count" id="subgroup_amount"><?= number_format($total_amount,2) ?></span>
								        </div>
									</div>
								<?php  $total_sub_group_amount += $total_amount; 
							   }
						    }?>														
			         		<script>
			         			var subgroup_amount = cal_subgroup_amount('<?= $total_sub_group_amount ?>');
			         			$('#subgroup_amount'+<?= $count ?>).html(subgroup_amount);
			         		</script>  
						    <?php 
						    $count++; 						    		
					   	$total_head_amount += $total_sub_group_amount; 						    
					   }			  
					} 	 ?>													
	         		<script>
	         			var head_amount = cal_head_amount('<?= $total_head_amount ?>');
	         			$('#head_amount'+<?= $head_count ?>).html(head_amount);
	         			var tmp_head_amount_d1 = $('#span_total_sales').html();   
	         			var tmp_head_amount_d = parseFloat(head_amount) + parseFloat(tmp_head_amount_d1);    	
	         			$('#span_total_sales').html(tmp_head_amount_d.toFixed(2));
	         		</script>

				  <?php $head_count++; } ?>	
			</div>  <!-- Quadrant End -->
			<!-- Total -->
			<div class="panel-footer main_block">
				<div class="row">
					<div class="col-md-8"><strong>Total :</strong></div>
					<div class="col-md-4 text-right"><strong id="span_total_sales">0.00</strong></div>
				</div>
			</div>													
     		<script>
     			var head_amount1 = $('#head_amount1').html();
     			var head_amount2 = $('#head_amount2').html();

     			var tmp_head_amount_d = parseFloat(head_amount1) + parseFloat(head_amount2);  
     			tmp_head_amount_d = parseFloat(tmp_head_amount_d);  	
     			$('#span_total_sales').html(tmp_head_amount_d.toFixed(2));
     		</script>
		</div>
	</div>

<!-- //////////////////////////////////////////////////////DEBIT END/////////////////////////////////////////////////////////////////////////////////////////-->
</div>

