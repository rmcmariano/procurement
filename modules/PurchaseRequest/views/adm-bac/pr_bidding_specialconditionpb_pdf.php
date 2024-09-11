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

<div class="bid-specialcondition-pdf">
	<div style="text-align:center; padding-top: 40%; padding-bottom: 95%">
		<h1><strong><i>Section V. Special Conditions of Contract</i></strong></h1>
	</div>

	<div style="text-align:center; ">
		<strong>Special Conditions of Contract</strong>
	</div>

	<div style="text-align:justify;">
		<table style="border-collapse: collapse; border: 1px solid;  font-family: Arial, Helvetica, sans-serif; width: 100%; text-align:justify;  table-layout: fixed;  ">
			<tr style="border: 1px solid black; padding: 1%">
				<th style="width: 10%; border-right: 1px solid;  font-size: small">GCC Clause</th>
				<th style="width: 90%; border-right: 1px solid;  font-size: small"></th>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">1.1(g)</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The Procuring Entity is:
					<br><br> <strong><i> INDUSTRIAL TECHNOLOGY DEVELOPMENT INSTITUTE </i></strong>
				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">1.1(i)</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The Supplier is <i> [to be inserted at the time of contract award].</i></td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">1.1(j)</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 5%">The Funding Source is:
					<br><br>The Government of the Philippines (GOP) through <strong>“<?= $purchaserequest->chargedisplay->project_title ?> ”</strong> in the total amount of <strong> <?= NumberToWords::transformNumber('en', $items->total_cost) ?> <?= ($items->totalCostDecimal != 0 && $items->totalCostDecimal != null) ? ' pesos and ' . NumberToWords::transformNumber('en', $items->totalCostDecimal) . ' centavos' : '' ?> (Php <?= number_format($items->total_cost, '2') ?> ) </strong>
					<br><br>The Project is as follows:
					<br><br> &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; 1. &nbsp;&nbsp; <strong> “<?= $items->bid_title ?> of <br> &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; <?= ucwords(convertNumberToWord($items->quantity)) ?> (<?= $items->quantity ?>) <?= $items->unit ?> <?= $items->itemexplode ?>” </strong>
				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">1.1(k)</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The Project Site is: <i>ITDI, DOST Comp. Gen. Santos Ave., Bicutan, Taguig City </i></td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">2</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">No further instructions.</td>
			</tr>
			<tr style="border: 1px solid black; ">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">5.1</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The Procuring Entity’s address for Notices is:
					<br><br><strong>ANNABELLE V. BRIONES, Ph.D</strong>
					<br><br>Director, ITDI
					<br><br>DOST Comp. Gen. Santos Ave., Bicutan, Taguig City
					<br><br>8837-20-71 loc. 2221
					<br><br>Email address: admbac@itdi.dost.gov.ph
				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">6.2</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%"><strong> Delivery and Documents </strong>
					<br><br>For purposes of the Contract, “EXW,” “FOB,” “FCA,” “CIF,” “CIP,” “DDP” and other trade terms used to describe the obligations of the parties shall have the meanings assigned to them by the current edition of INCOTERMS published by the International Chamber of Commerce, Paris. The Delivery terms of this Contract shall be as follows
					<br><br>“The delivery terms applicable to this Contract are DDP delivered to <i> ITDI, DOST Comp. Gen. Santos Ave., Bicutan, Taguig City.</i> In accordance with INCOTERMS.”
					<br><br>Delivery of the Goods shall be made by the Supplier in accordance with the terms specified in Section VI. Schedule of Requirements. The details of shipping and/or other documents to be furnished by the Supplier are as follows:
					<br><br><i>For Goods supplied from within the Philippines:</i>
					<br><br>Upon delivery of the Goods to the Project Site, the Supplier shall notify the Procuring Entity and present the following documents to the Procuring Entity:
					<br><br>(i) &nbsp;&nbsp;&nbsp; Original and four copies of the Supplier’s invoice showing Goods’ description, quantity, unit price, and total amount;
					<br><br>(ii) &nbsp;&nbsp;&nbsp; Original and four copies delivery receipt/note, railway receipt, or truck receipt;
					<br><br>(iii) &nbsp;&nbsp;&nbsp; Original Supplier’s factory inspection report;
				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td style="width: 10%; border-right: 1px solid;  font-size: small"></td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">
					<br><br>(iv) &nbsp;&nbsp;&nbsp; Original and four copies of the Manufacturer’s and/or Supplier’s warranty certificate;
					<br><br>(v) &nbsp;&nbsp;&nbsp; Original and four copies of the certificate of origin (for imported Goods);
					<br><br>(vi) &nbsp;&nbsp;&nbsp; Delivery receipt detailing number and description of items received signed by the authorized receiving personnel;
					<br><br>(vii) &nbsp;&nbsp;&nbsp; Certificate of Acceptance/Inspection Report signed by the Procuring Entity’s representative at the Project Site; and
					<br><br>(viii) &nbsp;&nbsp;&nbsp; Four copies of the Invoice Receipt for Property signed by the Procuring Entity’s representative at the Project Site.
					<br><br><i>For Goods supplied from abroad:</i>
					<br><br>Upon shipment, the Supplier shall notify the Procuring Entity and the insurance company by cable the full details of the shipment, including Contract Number, description of the Goods, quantity, vessel, bill of lading number and date, port of loading, date of shipment, port of discharge etc. Upon delivery to the Project Site, the Supplier shall notify the Procuring Entity and present the following documents as applicable with the documentary requirements of any letter of credit issued taking precedence:
					<br><br>(i) &nbsp;&nbsp;&nbsp; Original and four copies of the Supplier’s invoice showing Goods’ description, quantity, unit price, and total amount;
					<br><br>(ii) &nbsp;&nbsp;&nbsp; Original and four copies of the negotiable, clean shipped on board bill of lading marked “freight pre-paid” and five copies of the non-negotiable bill of lading ;
					<br><br>(iii) &nbsp;&nbsp;&nbsp; Original Supplier’s factory inspection report;
					<br><br>(iv) &nbsp;&nbsp;&nbsp; Original and four copies of the Manufacturer’s and/or Supplier’s warranty certificate;
					<br><br>(v) &nbsp;&nbsp;&nbsp; Original and four copies of the certificate of origin (for imported Goods);
					<br><br>(vi) &nbsp;&nbsp;&nbsp; Delivery receipt detailing number and description of items received signed by the Procuring Entity’s representative at the Project Site;
					<br><br>(vii) &nbsp;&nbsp;&nbsp; Certificate of Acceptance/Inspection Report signed by the Procuring Entity’s representative at the Project Site; and
					<br><br>(viii) &nbsp;&nbsp;&nbsp; Four copies of the Invoice Receipt for Property signed by the Procuring Entity’s representative at the Project Site.
					<br><br>For purposes of this Clause the Procuring Entity’s Representative at the Project Site is <i> [insert name(s)].</i>
					<br><br><strong>Incidental Services –</strong>
					<br><br>The Supplier is required to provide all of the following services, including additional services, if any, specified in Section VI. Schedule of Requirements.
					<br><br><i>Select appropriate requirements and delete the rest.</i>
					<br><br>(a) &nbsp;&nbsp;&nbsp; performance or supervision of on-site assembly and/or start up of the supplied Goods;
					<br><br>(b) &nbsp;&nbsp;&nbsp; furnishing of tools required for assembly and/or maintenance of the supplied Goods;
					<br><br>(c) &nbsp;&nbsp;&nbsp; furnishing of a detailed operations and maintenance manual for each appropriate unit of the supplied Goods;
					<br><br>(d) &nbsp;&nbsp;&nbsp; performance or supervision or maintenance and/or repair of the supplied Goods, for a period of time agreed by the parties, provided that this service shall not relieve the Supplier of any warranty obligations under this Contract; and
				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td style="width: 10%; border-right: 1px solid;  font-size: small"></td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">
					<br><br>(e) &nbsp;&nbsp;&nbsp; training of the Procuring Entity’s personnel, at the Supplier’s plant and/or on-site, in assembly, start-up, operation, maintenance, and/or repair of the supplied Goods.
					<br><br>The Contract price for the Goods shall include the prices charged by the Supplier for incidental services and shall not exceed the prevailing rates charged to other parties by the Supplier for similar services.

					<br><br><strong>Spare Parts –</strong>
					<br><br>The Supplier is required to provide all of the following materials, notifications, and information pertaining to spare parts manufactured or distributed by the Supplier:
					<br><br><i>Select appropriate requirements and delete the rest.</i>
					<br><br>(a) &nbsp;&nbsp;&nbsp; such spare parts as the Procuring Entity may elect to purchase from the Supplier, provided that this election shall not relieve the Supplier of any warranty obligations under this Contract; and
					<br><br>(b) &nbsp;&nbsp;&nbsp; in the event of termination of production of the spare parts:
					<br><br> &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;  i. &nbsp;&nbsp;&nbsp; advance notification to the Procuring Entity of the pending termination, in sufficient time to permit the Procuring Entity to procure needed requirements; and
					<br><br> &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;  ii. &nbsp;&nbsp;&nbsp;	following such termination, furnishing at no cost to the Procuring Entity, the blueprints, drawings, and specifications of the spare parts, if requested.
					<br><br>The spare parts required are listed in Section VI. Schedule of Requirements and the cost thereof are included in the Contract Price
					<br><br>The Supplier shall carry sufficient inventories to assure ex-stock supply of consumable spares for the Goods for a period of <i> [insert here the time period specified. If not used insert time period of three times the warranty period].  </i>
					<br><br>Other spare parts and components shall be supplied as promptly as possible, but in any case within [insert appropriate time period] months of placing the order.

					<br><br><strong>Packaging –</strong>
					<br><br>The Supplier shall provide such packaging of the Goods as is required to prevent their damage or deterioration during transit to their final destination, as indicated in this Contract.  The packaging shall be sufficient to withstand, without limitation, rough handling during transit and exposure to extreme temperatures, salt and precipitation during transit, and open storage.  Packaging case size and weights shall take into consideration, where appropriate, the remoteness of the GOODS’ final destination and the absence of heavy handling facilities at all points in transit.
					<br><br>The packaging, marking, and documentation within and outside the packages shall comply strictly with such special requirements as shall be expressly provided for in the Contract, including additional requirements, if any, specified below, and in any subsequent instructions ordered by the Procuring Entity.
					<br><br>The outer packaging must be clearly marked on at least four (4) sides as follows:
					<br><br>Name of the Procuring 
					<br><br>Name of the Supplier
					<br><br>Contract Description
					<br><br>Final Destination
					<br><br>Gross weight
					<br><br>Any special lifting instructions	
					<br><br>Any special handling instructions
					<br><br>Any relevant HAZCHEM classifications
					<br><br>A packaging list identifying the contents and quantities of the package is to be placed on an accessible point of the outer packaging if practical. If not practical the packaging list is to be placed inside the outer packaging but outside the secondary packaging.
				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td style="width: 10%; border-right: 1px solid;  font-size: small"></td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%"><br><br><strong>Transportation –</strong>
					<br><br>Where the Supplier is required under Contract to deliver the Goods CIF, CIP or DDP, transport of the Goods to the port of destination or such other named place of destination in the Philippines, as shall be specified in this Contract, shall be arranged and paid for by the Supplier, and the cost thereof shall be included in the Contract Price.
					<br><br>Where the Supplier is required under this Contract to transport the Goods to a specified place of destination within the Philippines, defined as the Project Site, transport to such place of destination in the Philippines, including insurance and storage, as shall be specified in this Contract, shall be arranged by the Supplier, and related costs shall be included in the Contract Price.
					<br><br>Where the Supplier is required under Contract to deliver the Goods CIF, CIP or DDP, Goods are to be transported on carriers of Philippine registry. In the event that no carrier of Philippine registry is available, Goods may be shipped by a carrier which is not of Philippine registry provided that the Supplier obtains and presents to the Procuring Entity certification to this effect from the nearest Philippine consulate to the port of dispatch. In the event that carriers of Philippine registry are available but their schedule delays the Supplier in its performance of this Contract the period from when the Goods were first ready for shipment and the actual date of shipment the period of delay will be considered force majeure in accordance with GCC Clause 12.
					<br><br>The Procuring Entity accepts no liability for the damage of Goods during transit other than those prescribed by INCOTERMS for DDP Deliveries. In the case of Goods supplied from within the Philippines or supplied by domestic Suppliers risk and title will not be deemed to have passed to the Procuring Entity until their receipt and final acceptance at the final destination.

					<br><br><strong>Patent Rights –</strong>
					<br><br>The Supplier shall indemnify the Procuring Entity against all third party claims of infringement of patent, trademark, or industrial design rights arising from use of the Goods or any part thereof.
				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">10.4</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">"Payment shall be made in <i> Philippine Peso.</i></td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">10.5</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">“Payment using LC is not allowed”</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">13.4(c)</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%"> “No further instructions”.</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">16.1</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%"> All services and goods to be delivered shall be subject for inspection.</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">17.3</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%"><i> If the Goods pertain to Expendable Supplies: </i>  Three (3) months after acceptance by the Procuring Entity of the delivered Goods or after the Goods are consumed, whichever is earlier. 
				<br><br><i> If the Goods pertain to Non-expendable Supplies: </i>  One (1) year after acceptance by the Procuring Entity of the delivered Goods. </td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">17.4</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">Repair/replacement of the defective goods or parts without cost to the procuring entity. </td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">21.1</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%"><i> If the Supplier is a joint venture, </i> “All partners to the joint venture shall be jointly and severally liable to the Procuring Entity.” </td>
			</tr>

		</table>
	</div>
</div>