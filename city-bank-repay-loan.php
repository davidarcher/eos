<?php require 'include/prehtml.php'; ?>
<?php require_active_firm(); ?>
<?php
	$sql = "SELECT name, cash, loan, networth FROM firms WHERE id = $eos_firm_id";
	$firm = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
	if(empty($firm)){
		echo "Error encountered, please report to admin. Error code CBRL-07.";
		exit();
	}else{
		$firm_name = $firm["name"];
		$firm_cash = $firm["cash"];
		$firm_loan = $firm["loan"];
		$firm_networth = $firm["networth"];
	}
	$min_xfund = 0;
	$max_xfund = min($firm_cash, $firm_loan);
?>
		<script type="text/javascript">
			var xfund, xfund_temp, lastRan = 0;
			var xfund_max = <?= $max_xfund ?>;
			var xfund_min = <?= $min_xfund ?>;

			firmController.allowance = <?= ($ctrl_daily_allowance == -1) ? -1 : $ctrl_leftover_allowance ?>;

			function xFundMax(){
				xfund = xfund_max;
				document.getElementById('xfund').value = xfund;
				checkXFund();
			}
			function xFundMin(){
				xfund = xfund_min;
				document.getElementById('xfund').value = xfund;
				checkXFund();
			}
			function checkXFund(){
				xfund = Math.floor(stripCommas(document.getElementById('xfund').value));
				document.getElementById('xfund').value = xfund;
				document.getElementById('xfund_visible').value = xfund/100;
				jQuery("#slider_target").slider("value", xfund);
			}
			function updateXFund(){
				xfund_temp = document.getElementById('xfund_visible').value;
				if(xfund_temp.charAt(xfund_temp.length-1) == ".") {
					return false;
				}
				xfund = Math.round(stripCommas(xfund_temp)*100);
				if(xfund > 0 && !isNaN(xfund)){
					if(xfund > xfund_max){
						xfund = xfund_max;
						document.getElementById('xfund_visible').value = xfund/100;
					}
					if(xfund < xfund_min){
						xfund = xfund_min;
						document.getElementById('xfund_visible').value = xfund/100;
					}
					document.getElementById('xfund').value = xfund;
					checkXFund();
				}
			}
			var checkXFundTimeout;
			function initUpdateXFund(skipTimeout){
				clearTimeout(checkXFundTimeout);
				if(typeof(skipTimeout) !== "undefined" && skipTimeout){
					updateXFund();
				}else{
					checkXFundTimeout = setTimeout("updateXFund();", 1000);
				}
			}
		</script>
<?php require 'include/stats_fbox.php'; ?>
	<h3>Repay Loan</h3>
<?php
	if(!$ctrl_admin){
?>
		You are not authorized to perform this action.
<?php
	}else if($max_xfund <= 0){
		if($firm_loan > 0){
			echo 'Stop wasting our time! Your company does not have ANY cash left!';
		}else{
			echo 'Your company does not have any outstanding loan.';
		}
	}else{
?>
	<div id="repay_loan_form">
		<form id="slider_form_1" class="default_slider_form" onsubmit="firmController.repayLoan();return false;">
			Current interest rate: 1.0% / day<br /><br />
			Interest is calculated at the start of each day, so if you repay your loan now, you will not accumulate any additional interest on the next day.<br /><br />
			<div style="line-height:48px;" class="vert_middle">
				<div style="float:left;width:80px;"><img src="images/slider_min.gif" style="cursor:pointer;" onclick="xFundMin();" /></div>
				<div id="slider_target" class="slider_target"></div>
				<div style="float:left;width:80px;"><img src="images/slider_max.gif" style="cursor:pointer;margin-left: 10px;" onclick="xFundMax();" /></div>
				<div style="float:left;margin-left:80px;width:180px;" class="vert_middle">
					<input id="xfund_visible" type="text" style="border: 2px solid #997755;text-align:center;" value="0" size="17" maxlength="17" onkeyup="initUpdateXFund();" onchange="updateXFund();" />
					<input id="xfund" type="hidden" style="display:none;" value="0" maxlength="20" />
				</div>
				<div class="clearer"></div>
			</div>
			<br />
			<img class="big_action_button" src="images/button-trade-big.gif" id="fund_start_button" title="Confirm" onclick="firmController.repayLoan();" />
		</form>
		<script type="text/javascript">
			jQuery("#slider_target").slider({
				value: 0,
				min: xfund_min,
				max: xfund_max,
				slide: function(event, ui){
					jQuery("#xfund").val(ui.value);
					checkXFund();
				}
			});
		</script>
	</div>
<?php
	}
?>
		<br /><br />
		<input type="button" class="bigger_input jqDialog-close-btn" value="Close" />
<?php require 'include/foot_fbox.php'; ?>