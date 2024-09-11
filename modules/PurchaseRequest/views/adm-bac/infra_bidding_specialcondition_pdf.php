<?php

use NumberToWords\NumberToWords;


/* @var $this yii\web\View */
/* @var $model app\modules\PurchaseRequest\models\PurchaseRequest */


function convertNumberToWord($num = false)
{
	$num = str_replace(array(',', ' '), '', trim($num));
	if (!$num) {
		return false;
	}
	$num = (int) $num;
	$words = array();
	$list1 = array(
		'', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
		'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'
	);
	$list2 = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety', 'hundred');
	$list3 = array(
		'', 'thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion', 'sextillion', 'septillion',
		'octillion', 'nonillion', 'decillion', 'undecillion', 'duodecillion', 'tredecillion', 'quattuordecillion',
		'quindecillion', 'sexdecillion', 'septendecillion', 'octodecillion', 'novemdecillion', 'vigintillion'
	);
	$num_length = strlen($num);
	$levels = (int) (($num_length + 2) / 3);
	$max_length = $levels * 3;
	$num = substr('00' . $num, -$max_length);
	$num_levels = str_split($num, 3);
	for ($i = 0; $i < count($num_levels); $i++) {
		$levels--;
		$hundreds = (int) ($num_levels[$i] / 100);
		$hundreds = ($hundreds ? ' ' . $list1[$hundreds] . ' hundred' . ' ' : '');
		$tens = (int) ($num_levels[$i] % 100);
		$singles = '';
		if ($tens < 20) {
			$tens = ($tens ? ' ' . $list1[$tens] . ' ' : '');
		} else {
			$tens = (int)($tens / 10);
			$tens = ' ' . $list2[$tens] . ' ';
			$singles = (int) ($num_levels[$i] % 10);
			$singles = ' ' . $list1[$singles] . ' ';
		}
		$words[] = $hundreds . $tens . $singles . (($levels && (int) ($num_levels[$i])) ? ' ' . $list3[$levels] . ' ' : '');
	} //end for loop
	$commas = count($words);
	if ($commas > 1) {
		$commas = $commas - 1;
	}
	return implode(' ', $words);
}
?>

<div class="bid-specialcondition-pdf" style="font-size: 12pt;">
	<div style="text-align:center; padding-top: 40%; padding-bottom: 95%">
		<h1><strong><i>Section V. Special Conditions of Contract</i></strong></h1>
	</div>

	<div style="text-align:center; font-size: 12pt">
		<strong>Special Conditions of Contract</strong>
	</div>

	<div style="text-align:justify;">
		<table style="border-collapse: collapse; border: 1px solid;  font-family: Arial, Helvetica, sans-serif; width: 100%; text-align:justify;  table-layout: fixed; font-size: 12pt ">
			<tr style="border: 1px solid black; padding: 1%">
				<th style="width: 10%; border-right: 1px solid;  font-size: small">GCC Clause</th>
				<th style="width: 90%; border-right: 1px solid;  font-size: small"></th>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">1.17</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The Intended Completion Date is on _________________.
				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">1.22</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The <strong>Procuring Entity </strong> is -<i> <strong>Industrial Technology Development Institute (ITDI)</strong></i></td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">1.23</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 5%">The <strong>Procuring Entity’s Representative</strong><br><br>
					<strong><i>ANNABELLE V. BRIONES, Ph.D.</i></strong><br><br>
					<i>Address: Metrology Bldg.,DOST Compound, Gen. Santos Ave., Bicutan, Taguig, City.</i>
				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">1.24</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The <strong> Site </strong> is located at ITDI, <strong><i>DOST Compound, Gen. Santos Ave., Bicutan, Taguig, </i></strong></td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">1.28</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The <strong>Start Date</strong> is 7 days from <strong> the date of receipt of the Notice to Proceed (NTP) by the Contractor</strong> </td>
			</tr>
			<tr style="border: 1px solid black; ">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">1.31</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The Works include but is not limited to the <strong> "<?= $items->item_name ?>" </strong>.
				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">5.1</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">Simultaneous with the issuance of NTP
				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">6.5</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">
					The Contractor shall employ the following Key Personnel: <br><br>
					<u>Key Personnel and Qualifications</u><br>
					<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;a. &nbsp;&nbsp;&nbsp; Project Manager – A licensed Civil Engineer or Architect with at least 10 years of experience as project manager of similar projects.
					<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;b. &nbsp;&nbsp;&nbsp; Project Architect – A licensed Architect with at least 8 years of experience as project architect of similar projects.
					<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;c. &nbsp;&nbsp;&nbsp; Mechanical Engineer - A licensed Mechanical Engineer with 5-10 years as mechanical engineer of similar projects, specifically related to design and installation of steam & water lines.
					<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;d. &nbsp;&nbsp;&nbsp; Electrical Engineer – A licensed Electrical Engineer with 5-10 years as electrical engineer of similar projects.
					<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;e. &nbsp;&nbsp;&nbsp; Safety and Health Officer – With 3-5 years of experience as safety officer duly accredited by the Department of Labor and Employment (DOLE)
					<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;f. &nbsp;&nbsp;&nbsp; Sanitary Engineer – A licensed Sanitary Engineer with 5-10 years of experience as Sanitary Engineer of similar projects.
					<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;g. &nbsp;&nbsp;&nbsp; Office Engineer – Graduate of civil engineering or architecture degree and skilled CAD operator, with 3-5 years of experience as office engineer of similar projects.
					<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;h. &nbsp;&nbsp;&nbsp; Foreman – With 5-10 years of experience as foreman of similar projects.
					<br><br>
					<i>NOTE: The names of the Key Personnel and their designation shall be filled out by winning contractor prior to contract signing.</i>
				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">7.4(c)</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%"><i>Specify additional conditions, if any, that must be met prior to the release of the performance security, otherwise, state “No further instructions.”</i></td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">7.7</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">No further instructions.</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">8.1</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%"> No further instructions.</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">10.0</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The site inspection is necessary.</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">12.3</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%"><i>In case of permanent structures, such as buildings of types 4 and 5 as classified under the National Building Code of the Philippines and other structures made of steel, iron, or concrete which comply with relevant structural codes (e.g., DPWH Standard Specifications), such as, but not limited to, steel/concrete bridges, flyovers, aircraft movement areas, ports, dams, tunnels, filtration and treatment plants, sewerage systems, power plants, transmission and communication towers, railway system, and other similar permanent structures: Fifteen (15) years.
						<br><br>
						In case of semi-permanent structures, such as buildings of types 1, 2, and 3 as classified under the National Building Code of the Philippines, concrete/asphalt roads, concrete river control, drainage, irrigation lined canals, river landing, deep wells, rock causeway, pedestrian overpass, and other similar semi-permanent structures: Five (5) years.
						<br><br>
						In case of other structures, such as Bailey and wooden bridges, shallow wells, spring developments, and other similar structures: Two (2) years.</i>
				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">13</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%"><i>If the Contractor is a joint venture,</i> “All partners to the joint venture shall be jointly and severally liable to the Procuring Entity.”</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">18.3(h)(i)</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">No further instructions.</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">21.2</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The Arbiter is: <i>Construction Industry Arbitration Commission</i>
					<br><br><i>[Insert address]</i>
				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">31.1</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The Contractor shall submit the Program of Work to the Procuring Entity’s Representative within 30 calendar days from issuance of the Notice to Proceed (NTP)
					<br><br>
					The program of work shall include among others, update of the PERT/CPM network. Bar/ Gantt Chart, Manpower and Equipment Utilization Schedules which were previously submitted pursuant to ITB Clause 31.4 of Instruction to Bidders.
				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">31.3</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The period between Program of Work updates is 30 calendar days.
				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">34.3</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The Funding Source is the <i> Industrial Technology Development Institute (ITDI) </i>
				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">39.1</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The amount of the advance payment is 15% of the contract price and to be recouped every progress billing to be made as herein schedule.
				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">40.1</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">Materials and equipment delivered on the site but not completely put in place shall not be included for payment.
				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">51.1</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The date by which operating and maintenance manuals are required is when 95% of the Total Contract Works is completed.<br><br>
					The date by which “as built” drawings are required is 95% of the Total Contract Works is completed.
				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">51.2</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The amount to be withheld for failing to produce “as built” drawings and/or operating and maintenance manuals by the date required is the total amount of the final billing.
				</td>
			</tr>


		</table>
	</div>
</div>