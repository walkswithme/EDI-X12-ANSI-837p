<?php
/**
 * Library: EDI ANSI 005010X222A1
 * EDI Code Number: 837 
 * Description: Electronic Claim processing in 837p [Paper claim-1500]
 * Version: 1.0.0
 * Author: Jobin Jose
 * Author URI: http://walkswithme.net
 * License: GPL2
 */
## HELP LINKS
## https://provider.hpsj.com/dre/help/ansi837/ansi-matrix.htm
## http://www.medicarepaymentandreimbursement.com/2010/06/emc-loop-2000b-element-sbr01-sbr02-and.html
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);






	/* ISA Segment  - EDI-837 format */
	function create_ISA($row,$segTer,$compEleSep) {

		$ISA	 =	array();

		$ISA[0] = "ISA";							/* Interchange Control Header Segment ID */
		
		$ISA[1] = "00";								/* Author Info Qualifier */
		
		$ISA[2] = str_pad("",10," ");		/* Author Information */
		
		$ISA[3] = "00";								/*	Security Information Qualifier
														MEDI-CAL NOTE: For Leased-Line & Dial-Up use '01', 
														for BATCH use '00'.
														'00' No Security Information Present 
														(No Meaningful Information in I04)*/

		$ISA[4] = str_pad("",10," ");		/* Security Information */
		
		$ISA[5] = str_pad("30",2," ");				/* Interchange ID Qualifier 30 or 01 */
		
		$ISA[6] = str_pad($row['x12_sender_id'],15," "); /* INTERCHANGE SENDER ID */
		
		$ISA[7] = str_pad("30",2," ");				/* Interchange ID Qualifier */
		
		$ISA[8] = str_pad($row['x12_reciever_id'],15," ");	/* INTERCHANGE RECEIVER ID 
															582574363" if ISA.07 = "30" or						"NAVICURE" if ISA.07 is "ZZ"*/ 
		
		$ISA[9] = str_pad(date('ymd'),6," ");		/* Interchange Date (YYMMDD) */
		
		$ISA[10] = str_pad(date('Hi'),4," ");		/* Interchange Time (HHMM) */
		
		$ISA[11] = str_pad("^",1," ");				/* Interchange Control Standards Identifier */
		
		$ISA[12] = str_pad(substr($row['x12_version'],0,5),5," ");			/* Interchange Control Version Number */
		
		$ISA[13] = str_pad("000000001",9," ");		/* INTERCHANGE CONTROL NUMBER  */ 
		
		$ISA[14] = str_pad("1",1," ");				/* Acknowledgment Request [0= not requested, 1= requested] */ 
		
		$ISA[15] =  str_pad("P",1," ");				/* Usage Indicator [ P = Production Data, T = Test Data ] */ 
		
		$ISA['Created'] = implode('*', $ISA);		/* Data Element Separator */

		$ISA['Created'] = $ISA['Created'] ."*";

		$ISA['Created'] = $ISA ['Created'] . $compEleSep. $segTer ; 
		
		return trim($ISA['Created']);
		
	}

	/* GS Segment  - EDI-837 format */

	function create_GS($row,$segTer,$compEleSep) {

		$GS	   = array();

		$GS[0] = "GS";								/* Functional Group Header Segment ID */
		
		$GS[1] = "HC";								/* Functional ID Code [ HS = Eligibility, Coverage or Benefit Inquiry (270), HC- 837 claim ] */
		
		$GS[2] =  $row['x12_sender_id'];    		/* Application Sender’s ID */
		
		$GS[3] =  $row['x12_reciever_id'];  				/* Application Receiver’s ID */
		
		$GS[4] = date('Ymd');						/* Date [CCYYMMDD] */
		
		$GS[5] = date('His');						/* Time [HHMM] – Group Creation Time */ 
		
		$GS[6] = str_pad($row['group_number'],9,"0",STR_PAD_LEFT);/* Group Control Number */
		
		$GS[7] = "X";								/* Responsible Agency Code Accredited Standards Committee X12 ] */
		
		$GS[8] = $row['x12_version'];			/* Version –Release / Industry[ Identifier Code Query */

		$GS['Created'] = implode('*', $GS);			/* Data Element Separator */

		$GS['Created'] = $GS ['Created'] .$segTer ; 
		 
		return trim($GS['Created']);
		
	}



	/* GE Segment - EDI-837 format */
	
	function create_GE($row,$segTer,$compEleSep) {

		$GE		=	array();
		
		$GE[0]	= "GE";									/* Functional Group Trailer Segment ID */

		$GE[1]	= "1";									/* Number of included Transaction Sets */

		$GE[2]	= str_pad($row['group_number'],9,"0",STR_PAD_LEFT);	/* Group Control Number */

		$GE['Created'] = implode('*', $GE);				/* Data Element Separator */

		$GE['Created'] = $GE['Created'] . $segTer ; 
		 
		return trim($GE['Created']);
	}
	
	/* IEA Segment - EDI-837 format */

	function create_IEA($row,$segTer,$compEleSep) {

		$IEA	=	array();
		
		$IEA[0] = "IEA";								/* Interchange Control Trailer Segment ID */

		$IEA[1] = "1";									/* Number of included Functional Groups */

		$IEA[2] = "000000001";							/* Interchange Control Number */

		$IEA['Created'] = implode('*', $IEA);

		$IEA['Created'] = $IEA['Created'] . $segTer ; 
		 
		return trim($IEA['Created']);
	}

		/* ST Segment  - EDI-837 format */

	function create_ST($row,$segTer,$compEleSep) {

		$ST	   =	array();

		$ST[0] = "ST";								/* Transaction Set Header Segment ID */
		
		$ST[1] = "837";								/* Transaction Set Identifier Code (Inquiry Request) */
		
		$ST[2] = str_pad($row['batch_number'],9,"0",STR_PAD_LEFT);

		$ST[3] = $row['x12_version'];				/* Transaction Set Control Number - Must match SE's */
		
		$ST['Created'] = implode('*', $ST);			/* Data Element Separator */

		$ST['Created'] = $ST ['Created'] . $segTer ; 
		 
		return trim($ST['Created']);
				
	}

	/* BHT Segment  - EDI-837 format */

	function create_BHT($row,$segTer,$compEleSep) {

		$BHT	=	array();
		
		$BHT[0] = "BHT";							/* Beginning of Hierarchical Transaction Segment ID */

		$BHT[1] = "0019";							/* Subscriber Structure Code */  

		$BHT[2] = "00";								/* Purpose Code - This is a Request */  

		$BHT[3] = str_pad($row['transcode'],11,"0",STR_PAD_LEFT);/*  Submitter Transaction Identifier  
														This information is required by the information Receiver 
														when using Real Time transactions. 
														For BATCH this can be used for optional information.*/

		$BHT[4] = str_pad(date('Ymd'),8," ");		/* Date Transaction Set Created */
		
		$BHT[5] = str_pad(date('Hi'),4," ");		/* Time Transaction Set HHMM */

		$BHT[6] = "CH";								/* RP = reporting, CH = chargeable*/

		$BHT['Created'] = implode('*', $BHT);		/* Data Element Separator */

		$BHT['Created'] = $BHT ['Created'] . $segTer ; 
		 
		return trim($BHT['Created']);
		
	}

	/* NM1 Segment  - EDI-837 format */

	function create_NM1($row,$nm1Cast,$segTer,$compEleSep) {

		$NM1		= array();
		
		$NM1[0]		= "NM1";					/* Subscriber Name Segment ID */
		
		if($nm1Cast == 'RC')/* RECIEVER*/
		{
			$NM1[1] = "40";						/* Entity ID Code - Payer [PR Payer] */
			$NM1[2] = "2";						/* Entity Type - Non-Person */
			$NM1[3] = $row["payer_name"];		/* Organizational Name */
			$NM1[4] = "";						/* Data Element not required.*/
			$NM1[5] = "";						/* Data Element not required.*/
			$NM1[6] = "";						/* Data Element not required.*/
			$NM1[7] = "";						/* Data Element not required.*/
			$NM1[8] = "46";						/* 46 - Electronic Transmitter Identification Number (ETIN) */
			$NM1[9] = $row["payer_code"];	    /* Application Sender’s ID */
		}
		else if($nm1Cast == '87') 				/* BILLING PROVIDER NAME PAY TO PROVIDER BLOCK*/
		{
			$NM1[1] = "87";						/* Entity ID Code - Provider [1P Provider]*/
			$NM1[2] = "2";						/* Entity Type - Person */
			$NM1[3] = $row['billing_provider_lastname'];/* Organizational Name  Doctor/Org Name */
			$NM1[4] = "";						/* Data Element not required.*/
			$NM1[5] = "";						/* Data Element not required.*/
			$NM1[6] = "";						/* Data Element not required.*/
			$NM1[7] = "";						/* Data Element not required.*/
			$NM1[8] = "XX";						
			$NM1[9] = $row['billing_provider_npi'];		/* Patient Doctors NPI*/
		}
		else if($nm1Cast == 'BP') 				/* BILLING PROVIDER NAME*/
		{
			$NM1[1] = "85";						/* Entity ID Code - Provider [1P Provider]*/
			$NM1[2] = "2";						/* Entity Type - Person */
			$NM1[3] = $row['billing_provider_lastname'];/* Organizational Name  Doctor/Org Name */
			$NM1[4] = "";						/* Data Element not required.*/
			$NM1[5] = "";						/* Data Element not required.*/
			$NM1[6] = "";						/* Data Element not required.*/
			$NM1[7] = "";						/* Data Element not required.*/
			$NM1[8] = "XX";						
			$NM1[9] = $row['billing_provider_npi'];		/* Patient Doctors NPI*/
		}
		else if($nm1Cast == 'SUBMITTER')				/*Patient - Subscriber Details */
		{
			$NM1[1] = "41";							/* Insured or Subscriber */
			$NM1[2] = "2";							/* Entity Type - Person */
			$NM1[3] = $row['submitter_org_name'];	/* last Name	*/
			$NM1[4] = "";							/* first Name	*/
			$NM1[5] = "";							/* middle Name	*/
			$NM1[6] = "";							/* data element */
			$NM1[7] = "";							/* data element */
			$NM1[8] = "46";							/* Identification Code Qualifier 46 / MI */
			$NM1[9] = $row['submitter_tax_id'];/* Submitter tax ID */
		}
		else if($nm1Cast == 'SB')				/*Patient - Subscriber Details */
		{
			$NM1[1] = "41";						/* Insured or Subscriber */
			$NM1[2] = "1";						/* Entity Type - Person */
			$NM1[3] = $row['subscriber_lname'];			/* last Name	*/
			$NM1[4] = $row['subscriber_fname'];			/* first Name	*/
			$NM1[5] = $row['subscriber_mname'];			/* middle Name	*/
			$NM1[6] = "";						/* data element */
			$NM1[7] = "";						/* data element */
			$NM1[8] = "46";						/* Identification Code Qualifier 46 / MI */
			$NM1[9] = $row['subscriber_policy_number'];/* Identification Code, Its Insurance Number I think */
		}
		else if($nm1Cast == 'IL')
		{
			$NM1[1] = "IL";						/* Insured or Subscriber */
			$NM1[2] = "1";						/* Entity Type - Person */
			$NM1[3] = $row['subscriber_lname'];			/* last Name	*/
			$NM1[4] = $row['subscriber_fname'];			/* first Name	*/
			$NM1[5] = $row['subscriber_mname'];			/* middle Name	*/
			$NM1[6] = "";						/* data element */
			$NM1[7] = "";						/* data element */
			$NM1[8] = "MI";						/* Identification Code Qualifier */
			$NM1[9] = $row['subscriber_policy_number'];/* Identification Code, Its Insurance Number I think */
		}
		else if($nm1Cast == 'DN')
		{
			$NM1[1] = "DN";							/* Referring provider*/
			$NM1[2] = "1";							/* Entity Type - Person */
			$NM1[3] = $row['ref_physician_lname'];			/* last Name	*/
			$NM1[4] = $row['ref_physician_fname'];			/* first Name	*/
			$NM1[5] = $row['ref_physician_mname'];			/* middle Name	*/
			$NM1[6] = "";						/* data element */
			$NM1[7] = "";						/* data element */
			$NM1[8] = "XX";						/* Identification Code Qualifier */
			$NM1[9] = $row['ref_physician_npi'];	/*NPI*/
		}
		else if($nm1Cast == 82)
		{
			$NM1[1] = "82";							/* Rendering provider*/
			$NM1[2] = "1";							/* Entity Type - Person */
			$NM1[3] = $row['rendering_provider_lname'];			/* last Name	*/
			$NM1[4] = $row['rendering_provider_fname'];			/* first Name	*/
			$NM1[5] = $row['rendering_provider_mname'];			/* middle Name	*/
			$NM1[6] = "";						/* data element */
			$NM1[7] = "";						/* data element */
			$NM1[8] = "XX";						/* Identification Code Qualifier */
			$NM1[9] = $row['rendering_provider_npi'];	/*NPI*/
		}
		else if($nm1Cast == 77)
		{
			$NM1[1] = "82";							/* Rendering provider*/
			$NM1[2] = "2";							/* Entity Type - Person */
			$NM1[3] = $row["facility_name"];			/* last Name	*/
			$NM1[4] = "";			/* first Name	*/
			$NM1[5] = "";			/* middle Name	*/
			$NM1[6] = "";						/* data element */
			$NM1[7] = "";						/* data element */
			$NM1[8] = "XX";						/* Identification Code Qualifier */
			$NM1[9] = $row['facility_npi'];	    /*NPI*/
		}
		else if($nm1Cast == 'PR')
		{
			$NM1[1] = "PR";						/* Entity ID Code - Payer [PR Payer] */
			$NM1[2] = "2";						/* Entity Type - Non-Person */
			$NM1[3] = $row["payer_name"];		/* Organizational Name */
			$NM1[4] = "";						/* Data Element not required.*/
			$NM1[5] = "";						/* Data Element not required.*/
			$NM1[6] = "";						/* Data Element not required.*/
			$NM1[7] = "";						/* Data Element not required.*/
			$NM1[8] = "PI";						/* 46 - Electronic Transmitter Identification Number (ETIN) */
			$NM1[9] = $row["payer_code"];	    /* Application Sender’s ID */
		}
		else if($nm1Cast == 'PR2')
		{
			$NM1[1] = "PR";						/* Entity ID Code - Payer [PR Payer] */
			$NM1[2] = "2";						/* Entity Type - Non-Person */
			$NM1[3] = $row["secondary_payer_name"];		/* Organizational Name */
			$NM1[4] = "";						/* Data Element not required.*/
			$NM1[5] = "";						/* Data Element not required.*/
			$NM1[6] = "";						/* Data Element not required.*/
			$NM1[7] = "";						/* Data Element not required.*/
			$NM1[8] = "PI";						/* 46 - Electronic Transmitter Identification Number (ETIN) */
			$NM1[9] = $row["secondary_payer_code"];	 /* Application Sender’s ID */
		}
		
		$NM1['Created'] = implode('*', $NM1);	/* Data Element Separator */

		$NM1['Created'] = $NM1['Created'] .$segTer ; 
		 
		return trim($NM1['Created']);

	}

	/* PER Segment  - EDI-837 format */

	function create_PER($row,$type,$segTer,$compEleSep) {

		$PER	=	array();
		
		$PER[0] = "PER";

		$PER[1] = "IC";							 

		$PER[2] = $row['submitter_name'];			/* Biller Details  goes here */	

		if($type == 'TE'){

			$PER[3] = "TE";							  

			$PER[4] = $row['submitter_telephone'];
		}		
		if($type == 'EM'){
			$PER[3] = "EM";								
			
			$PER[4] = $row['submitter_email'];	
		}

		if($type == 'FX'){
			$PER[3] = "FX";								
			
			$PER[4] = $row['submitter_fax'];	
		}									

		$PER['Created'] = implode('*', $PER);		

		$PER['Created'] = $PER ['Created'] . $segTer ; 
		 
		return trim($PER['Created']);
		
	}

	/* HL Segment  - EDI-837 format */

	function create_HL($row, $nHlCounter,$segTer,$compEleSep) {

		$HL				= array();

		$HL[0]		= "HL";							/* Hierarchical Level Segment ID */
		$HL_LEN[0]	=  2;

		$HL[1] = $nHlCounter;						/* Hierarchical ID No. */
		
		if($nHlCounter == 1)
		{ 
			$HL[2] = ""; 
			$HL[3] = 20;	/* Description: Identifies the payor, maintainer, or source of the information.*/
			$HL[4] = 1;		/* 1 Additional Subordinate HL Data Segment in This Hierarchical Structure. */
		}
		else if($nHlCounter == 2)
		{
			$HL[2] = 1;
			$HL[3] = 22;	/* Hierarchical Level Code.'22' Subscriber */
			$HL[4] = 0;		/* 0 no Additional Subordinate in the Hierarchical Structure. */
		}
		else
		{
			$HL[2] = 2;		/* Hierarchical Parent ID Number */
			$HL[3] = 21;	/* Hierarchical Level Code. '21' Information Receiver*/
			$HL[4] = 1;		/* 1 Additional Subordinate HL Data Segment in This Hierarchical Structure. */
		}
		
		$HL['Created'] = implode('*', $HL);			/* Data Element Separator */

		$HL['Created'] = $HL ['Created'] . $segTer ; 
		 
		return trim($HL['Created']);
	
	}


		/* PRV Segment  - EDI-837 format */

	function create_PRV($row,$segTer,$compEleSep) {

		$PRV	=	array();
		
		$PRV[0] = "PRV";

		$PRV[1] = "BI";							 

		$PRV[2] = "ZZ";			/* Biller Details  goes here */				 

		$PRV[3] = $row['biller_tax_code']; /* Provider Taxonomy Code - Required if the provider has more then one specialty. */						  
		$PRV['Created'] = implode('*', $PRV);		

		$PRV['Created'] = $PRV ['Created'] . $segTer ; 
		 
		return trim($PRV['Created']);
		
	}


	/*  N3 Segment  - EDI-837 format  BILLING PROVIDER ADDRESS */
	function create_N3($row,$ref,$segTer,$compEleSep){

		$N3	=	array();
		
		$N3[0] = "N3";

		if($ref == 'SB'){ // Subscriber Details

			$N3[1] = $row['subscriber_address'];			/* Billing Provider Street (Physical address) */	 

			$N3[2] = $row['subscriber_address2'];

		}else if($ref == 'BP'){ //Billing Provider

			$N3[1] = $row['billing_provider_street'];			/* Billing Provider Street (Physical address) */	 

			$N3[2] = $row['billing_provider_street2'];			/* Billing Provider Street 2  goes here */			 

		}else if($ref == 'PR'){ //Payer Details

			$N3[1] = $row['payer_street'];			/* Billing Provider Street (Physical address) */	 

			$N3[2] = $row['payer_street2'];			/* Billing Provider Street 2  goes here */			 

		}else if($ref == 'PR2'){ //Payer Details

			$N3[1] = $row['secondary_payer_street'];			/* Billing Provider Street (Physical address) */	 

			$N3[2] = $row['secondary_payer_street2'];			/* Billing Provider Street 2  goes here */			 

		}else if($ref == 'FA'){ //Facility Hospital Details

			$N3[1] = $row['facility_address'];			/* Billing Provider Street (Physical address) */	 

			$N3[2] = "";			/* Billing Provider Street 2  goes here */			 

		}

							  
		$N3['Created'] = implode('*', $N3);		

		$N3['Created'] = $N3 ['Created'] . $segTer ; 
		 
		return trim($N3['Created']);

	}

	/*  N4 Segment  - EDI-837 format - BILLING PROVIDER CITY, STATE, ZIP CODE */
	function create_N4($row,$ref,$segTer,$compEleSep){

		$N4	=	array();
		
		$N4[0] = "N4";

		if($ref == 'SB'){ // Subscriber Details

			$N4[1] = $row['subscriber_city'];			/* Billing Provider Street (Physical address) */			

			$N4[2] = $row['subscriber_state'];		/* Billing Provider Street 2  goes here */				 

			$N4[3] = $row['subscriber_zip'];			/* Billing Provider Street 2  goes here */				 

		}else if($ref == 'BP'){ //billing provider 

			$N4[1] = $row['billing_provider_city'];			/* Billing Provider Street (Physical address) */			

			$N4[2] = $row['billing_provider_state'];		/* Billing Provider Street 2  goes here */				 

			$N4[3] = $row['billing_provider_zip'];			/* Billing Provider Street 2  goes here */		

		}else if($ref == 'PR'){ //payer 

			$N4[1] = $row['payer_city'];					

			$N4[2] = $row['payer_state'];		

			$N4[3] = $row['payer_zip'];		

			//$N4[4] = "" 									/* payer country code */
		}else if($ref == 'PR2'){ //payer 

			$N4[1] = $row['secondary_payer_city'];					

			$N4[2] = $row['secondary_payer_state'];		

			$N4[3] = $row['secondary_payer_zip'];		

			//$N4[4] = "" 									/* payer country code */
		}else if($ref == 'FA'){ //Facility or Hospital 

			$N4[1] = $row['facility_city'];					

			$N4[2] = $row['facility_state'];		

			$N4[3] = $row['facility_zip'];		

			//$N4[4] = "" 									/* payer country code */
		} 

							  
		$N4['Created'] = implode('*', $N4);		

		$N4['Created'] = $N4 ['Created'] . $segTer ; 
		 
		return trim($N4['Created']);

	}

	function create_REF($row,$ref,$segTer,$compEleSep) {

		$REF	=	array();
	
		$REF[0] = "REF";						
	
		if($ref == 'SY')
		{
			$REF[1] = $ref;						/* Billing Provider SSN */
			$REF[2] = $row['billing_provider_pin'];		/* Provider Pin. */
		}
		if($ref == 'EI')
		{
			$REF[1] = $ref;							/* Billing Provider Federal Tax ID */
			$REF[2] = $row['billing_provider_federal_taxid'];			/* Patient Account No. */
		}
		if($ref == 'G1')				/*Required when services on this claim were preauthorized */
		{
			$REF[1] = $ref;							/* Prior Authorization qualifier */
			$REF[2] = $row['prior_auth_code'];			/* Prior Authorization number */
		}
		if($ref == 'F8')				
		{		/*(Required when CLM05-03 indicates replacement or void to a previously adjudicated claim */
			$REF[1] = $ref;							
			$REF[2] = $row['original_claim_number'];			
		}
		if($ref == 'X4')				
		{		/*Facilities performing CLIA covered Laboratory services*/
			$REF[1] = $ref;							
			$REF[2] = $row['original_claim_number'];	 /*Clinical Laboratory Improvement Amendment Number*/		
		}
		if($ref == 'EA')				
		{		/*CTUAL MEDICAL RECORD OF THE PATIENT*/
			$REF[1] = $ref;							
			$REF[2] = $row['patient_mrn'];	 /*Medical record number */		
		}
		if($ref == '9F')				
		{		/***Required when Referring Provider is sent (REF*DN)*/
			$REF[1] = $ref;							
			$REF[2] = $row['referral_number'];	 /* Referral number */		
		}


		$REF['Created'] = implode('*', $REF);	/* Data Element Separator */

		$REF['Created'] = $REF['Created'] .$segTer ; 
		 
		return trim($REF['Created']);
	  
	}

	function create_SBR($row,$ref,$segTer,$compEleSep){

		$SBR	=	array();
		
		$SBR[0] = "SBR";

		if($ref == 'OT'){
			$SBR[1] = "S";	 /* Primary Payer, Secondary Payer If claim is for primary
									payer then “P” else if claim is for secondary payer then “S”. A - H P, S, T, U*/	
		}else if($ref == 'PR'){
			$SBR[1] = "P";
		}			

		$SBR[2] = translate_relationship($row['subscriber_relationship']);		
		/* 18-Self (required when subscriber is patient) */				 
		if($ref == 'OT'){
			$SBR[3] = "";
			$SBR[4] = $row['subscriber_secondary_payer_name'];
			$SBR[5] = $row['subscriber_secondary_insurance_type'];
			$SBR[6] = "";
			$SBR[7] = "";
			$SBR[8] = "";	
			$SBR[9] = "MB"; // WC, MB, MA, HM		
		}else{
			$SBR[3] = "";
			$SBR[4] = "";
			$SBR[5] = "";
			$SBR[6] = "";
			$SBR[7] = "";
			$SBR[8] = "";
			$SBR[9] = "HM"; // WC, MB, MA, HM,CI
		}

		
		/* 

		Usage: Situational
		Element : SBR05
		Value : 
		12 = Medicare Secondary Working Aged Beneficiary or Spouse with Employer Group Health Plan
		13 = Medicare Secondary End-Stage Disease Beneficiary in the 12 month coordination period with an employer's group health plan
		14 = Medicare Secondary, No-fault Insurance including Auto is Primary
		15= Medicare Secondary Worker's Compensation
		16 = Medicare Secondary Public Health Services (PHS) or Other Federal Agency
		41 = Medicare Secondary Black Lung
		42 = Medicare Secondary Veteran's Administration
		43 = Medicare Secondary Disabled Beneficiary Under Age 65 with Large group Health Plan (LGHP)
		47 = Medicare Secondary, Other Liability Insurance is Primary
		
		Usage: Situational
		Element : SBR09
		Value : 
		09 = Selfpay
		10 = Central Certification
		11 = Other Non-Federal Programs
		12 = Preferred Provider Organization (PPO)
		13 = Point of Service (POS)
		14 = Exclusive Provider Organization (EPO)
		15 = Indemnity Insurance
		16 = Health Maintenance Organization (HMO) Medicare Risk
		AM = Automobile Medical
		BL = Blue Cross/Blue Shield
		CH = Champus
		CI = Commercial Insurance Co.
		DS= Disability
		HM= Health Maintenance Organization
		LI = Liability
		LM = Liability Medical
		MB = Medicare Part B
		MC = Medicaid
		OF = Other Federal Program
		TV = Title V
		VA = Veteran Administration Plan
		WC = Workers' Compensation Health Claim
		ZZ = Mutually Defined
		*/



							  
		$SBR['Created'] = implode('*', $SBR);		

		$SBR['Created'] = $SBR ['Created'] . $segTer ; 
		 
		return trim($SBR['Created']);

	}

	function create_DMG($row,$segTer,$compEleSep){

		$DMG	=	array();
		
		$DMG[0] = "DMG";

		$DMG[1] = "D8";						/* Date [CCYYMMDD] */			

		$DMG[2] = $row['subscriber_dob'];					 

		$DMG[3] = $row['subscriber_gender'];	/* F, M, U*/
							  
		$DMG['Created'] = implode('*', $DMG);		

		$DMG['Created'] = $DMG ['Created'] . $segTer ; 
		 
		return trim($DMG['Created']);

	}

	function create_CLM($row,$segTer,$compEleSep){

		$CLM	=	array();
		
		$CLM[0] = "CLM";

		$CLM[1] = $row['patient_id'];

		$CLM[2] = $row['total_amount'];	 /*Total charges (must equal sum of the SV102's) */					 

		$CLM[3] = "";

		$CLM[4] = "";
		/*
			011X	Hospital Inpatient (Part A)
			012X	Hospital Inpatient Part B
			013X	Hospital Outpatient
			014X	Hospital Other Part B
			018X	Hospital Swing Bed
			021X	SNF Inpatient
			022X	SNF Inpatient Part B
			023X	SNF Outpatient
			028X	SNF Swing Bed
			032X	Home Health
			033X	Home Health
			034X	Home Health (Part B Only)
			041X	Religious Nonmedical Health Care Institutions
			071X	Clinical Rural Health
			072X	Clinic ESRD
			073X	Federally Qualified Health Centers
			074X	Clinic OPT
			075X	Clinic CORF
			076X	Community Mental Health Centers
			081X	Nonhospital based hospice
			082X	Hospital based hospice
			083X	Hospital Outpatient (ASC) 085X Critical Access Hospital
			085X	Critical Access Hospital
		*/
		$CLM[5] =  "11:" . "B" . ":" .$row['claim_type']; /* 7=replacement 1 - no*/

		$CLM[6] =  "Y";

		$CLM[7] =  "A"; /*A, B, C  Provider accept Medicare assignment code*/

		$CLM[8] =  "Y";

		$CLM[9] =  "Y";

		$CLM[10] =  "P";

							  
		$CLM['Created'] = implode('*', $CLM);		

		$CLM['Created'] = $CLM ['Created'] . $segTer ; 
		 
		return trim($CLM['Created']);

	}

	/* DTP Segment - EDI-837 format */
	
	function create_DTP($row,$ref,$segTer,$compEleSep,$dos = '') {

		$DTP	=	array();
		
		$DTP[0] = "DTP";								/* Date or Time or Period Segment ID */
		
		$DTP[1] = $ref;									/* Qualifier - Date of Service */
		
		$DTP[2] = "D8";									/* Date Format Qualifier - (D8 means CCYYMMDD) */
		
		if($ref == '431'){
				
			$DTP[3] = $row['patient_encounter_date'];							/* Date */
		}
		if($ref == '472'){
				
			$DTP[3] = $dos;				/* Date of service*/
		}
		if($ref == '454'){
				
			$DTP[3] = $row['patient_first_encounter_date'];							/* Date */
		}
		if($ref == '304'){
				
			$DTP[3] = $row['patient_last_visit_date'];							/* Date */
		}
		if($ref == '435'){
				
			$DTP[3] = $row['patient_admission_date'];							/* Date */
		}
		if($ref == '096'){
				
			$DTP[3] = $row['patient_discharge_date'];							/* Date */
		}

		$DTP['Created'] = implode('*', $DTP);			/* Data Element Separator */

		$DTP['Created'] = $DTP['Created'] . $segTer ; 
		 
		return trim($DTP['Created']);
	}
		/*PATIENT AMOUNT PAID 2300 */
	function create_AMT($row,$segTer,$compEleSep){

		$AMT	=	array();
		
		$AMT[0] = "AMT";								
		
		$AMT[1] = "F5";									
		
		$AMT[2] = $row['patient_paid_amt'];												

		$AMT['Created'] = implode('*', $AMT);			/* Data Element Separator */

		$AMT['Created'] = $AMT['Created'] . $segTer ; 
		 
		return trim($AMT['Created']);
	}

		/*Additional notes */
	function create_NTE($row,$segTer,$compEleSep){

		$NTE	=	array();
		
		$NTE[0] = "NTE";								
		
		$NTE[1] = "ADD";									
		
		$NTE[2] = $row['claim_notes'];												

		$NTE['Created'] = implode('*', $NTE);			/* Data Element Separator */

		$NTE['Created'] = $NTE['Created'] . $segTer ; 
		 
		return trim($NTE['Created']);
	}

	function create_HI($row,$primary,$segTer,$compEleSep){

		$HI	=	array();
		
		$HI[0] = "HI";								
		if($primary == 1){
			$icd_type = ($row['primary_problem_type_code'] == "ICD9") ? "BK" : "ABK";
			$HI[1] = $icd_type.":".$row['primary_problem_code'];
		}else{
			$loopvar = 1; //Max loop 12
			if(sizeof($row['other_diag_list'])){
				foreach($row['other_diag_list'] as $items){
					$icd_type = ($items['icd_type'] == "ICD9") ? "BF" : "ABF";
					$HI[$loopvar] = $icd_type.":".$items['icd_codes'];
					$loopvar++;	
				}
			}
			
		}									
		
														

		$HI['Created'] = implode('*', $HI);			/* Data Element Separator */

		$HI['Created'] = $HI['Created'] . $segTer ; 
		 
		return trim($HI['Created']);
	}

	function translate_relationship($relationship) {
		switch ($relationship) {
			case "spouse":
				return "01";
				break;
			case "child":
				return "19";
				break;
			case "self":
				return "18";
			default:
				return "G8";
		}
	}

	function create_OI($row,$segTer,$compEleSep){

		$OI	=	array();
		
		$OI[0] = "OI";								
		
		$OI[1] = "";									
		
		$OI[2] = "";

		$OI[3] = "Y";		 /*Y, N, W  YES/NO CONDITION REPONSE Assignment of Benefits Indicator*/										
		$OI[4] = "";

		$OI[5] = "";

		$OI[6] = "Y";			/*I, Y Release of Information Code */

		$OI['Created'] = implode('*', $OI);			/* Data Element Separator */

		$OI['Created'] = $OI['Created'] . $segTer ; 
		 
		return trim($OI['Created']);

	}

	function create_LX($row,$loopcount,$segTer,$compEleSep){

		$LX	=	array();
		
		$LX[0] = "LX";								
		
		$LX[1] = $loopcount;		

		$LX['Created'] = implode('*', $LX);			/* Data Element Separator */

		$LX['Created'] = $LX['Created'] . $segTer ; 
		 
		return trim($LX['Created']);
	}

	function create_SV1($row,$items,$segTer,$compEleSep){

		$SV1	=	array();
		
		$SV1[0] = "SV1";								
		
		$SV1[1] = "HC:".$items['cpt_codes'];

		$SV1[2] = $items['cpt_charge'];

		$SV1[3] = "UN";

		$SV1[4] = $items['quantity'];
		$SV1[5] = $items['facility_code'];
		$SV1[6] = "";
		$SV1[7] = $items['code_pointer'];

		$SV1['Created'] = implode('*', $SV1);			/* Data Element Separator */

		$SV1['Created'] = $SV1['Created'] . $segTer ; 
		 
		return trim($SV1['Created']);
	}


	/* SE Segment - EDI-837 format */
	
	function create_SE($row,$segmentcount,$segTer,$compEleSep) {

		$SE				=	array();
		
		$SE[0] = "SE";									/* Transaction Set Trailer Segment ID */

		$SE[1] = $segmentcount;							/* Segment Count */

		$SE[2] = str_pad($row['batch_number'],9,"0",STR_PAD_LEFT);/* Transaction Set Control Number - Must match ST's */

		$SE['Created'] = implode('*', $SE);				/* Data Element Separator */

		$SE['Created'] = $SE['Created'] .$segTer ; 
		 
		return trim($SE['Created']);
	}


	function create_x12_837_file($res,$segTer,$eleDataSep,$compEleSep, $minified=false){

		$file_data = "";
		$loopcounter = 0;
		$eol = $minified ?"":PHP_EOL;
		foreach($res as $row){ 
			//print_r($row);exit;
			
			$file_data .= create_ISA($row,$segTer,$compEleSep).$eol;
			$file_data .= create_GS($row,$segTer,$compEleSep).$eol;
			## ST Count Should start from here including ST			
			$file_data .= create_ST($row,$segTer,$compEleSep).$eol;  ++$loopcounter;
			$file_data .= create_BHT($row,$segTer,$compEleSep).$eol; ++$loopcounter;

			## SUBMITTER NAME-1000A
			$file_data .= create_NM1($row,'SUBMITTER',$segTer,$compEleSep).$eol; ++$loopcounter;

			if(!empty($row['submitter_telephone'])){
				## SUBMITTER EDI CONTACT INFORMATION-1000A
				$file_data .= create_PER($row,'TE',$segTer,$compEleSep).$eol; ++$loopcounter;
			}
			if(!empty($row['submitter_email'])){
				## SUBMITTER EDI CONTACT INFORMATION-1000A
				$file_data .= create_PER($row,'EM',$segTer,$compEleSep).$eol; ++$loopcounter;
			}
			if(!empty($row['submitter_fax'])){
				## SUBMITTER EDI CONTACT INFORMATION-1000A
				$file_data .= create_PER($row,'FX',$segTer,$compEleSep).$eol; ++$loopcounter;
			}

			## RECEIVER NAME-1000B
			$file_data .= create_NM1($row,'RC',$segTer,$compEleSep).$eol; ++$loopcounter;

			## 2010AA BILLING PROVIDER HL LOOP
			$file_data .= create_HL($row,1,$segTer,$compEleSep).$eol; ++$loopcounter;			
			## BILLING PROVIDER NAME 2010AA
			$file_data .= create_NM1($row,'BP',$segTer,$compEleSep).$eol; ++$loopcounter;
			## BILLING PROVIDER ADDRESS
			$file_data .= create_N3($row,'BP',$segTer,$compEleSep).$eol; ++$loopcounter;
			## BILLING PROVIDER CITY, STATE, ZIP CODE
			$file_data .= create_N4($row,'BP',$segTer,$compEleSep).$eol; ++$loopcounter;

			## Situational PRV segment for provider taxonomy code for Medicaid. only applicable for MC
			if(!empty($row['biller_tax_code'])){
				$file_data .= create_PRV($row,$segTer,$compEleSep).$eol; ++$loopcounter;
			}

			
			## BILLING PROVIDER SECONDARY IDENTIFICATION- S
			if(!empty($row['billing_provider_federal_taxid'])){

				$file_data .= create_REF($row,'EI',$segTer,$compEleSep).$eol; ++$loopcounter;
			}elseif(!empty($row['billing_provider_pin'])){

				$file_data .= create_REF($row,'SY',$segTer,$compEleSep).$eol; ++$loopcounter;
			}

			## 2010AB PAY-TO PROVIDER
			## PAY-TO PROVIDER NAME 2010AB
			$file_data .= create_NM1($row,'87',$segTer,$compEleSep).$eol; ++$loopcounter;
			## PAY-TO PROVIDER ADDRESS
			$file_data .= create_N3($row,'BP',$segTer,$compEleSep).$eol; ++$loopcounter;
			## PAY-TO PROVIDER CITY, STATE, ZIP CODE
			$file_data .= create_N4($row,'BP',$segTer,$compEleSep).$eol; ++$loopcounter;


			## SUBSCRIBER HIERARCHICAL LEVEL 2000B
			$file_data .= create_HL($row,2,$segTer,$compEleSep).$eol; ++$loopcounter;
			## SUBSCRIBER INFORMATION 2000B
			$file_data .= create_SBR($row,'PR',$segTer,$compEleSep).$eol; ++$loopcounter;
			## SUBSCRIBER SECONDARY IDENTIFICATION 2010BA
			$file_data .= create_NM1($row,'IL',$segTer,$compEleSep).$eol; ++$loopcounter;
			## SUBSCRIBER ADDRESS 2010BA
			$file_data .= create_N3($row,'SB',$segTer,$compEleSep).$eol; ++$loopcounter;
			## SUBSCRIBER ADDRESS 2010BA
			$file_data .= create_N4($row,'SB',$segTer,$compEleSep).$eol; ++$loopcounter;
			## SUBSCRIBER DEMOGRAPHIC INFORMATION 2010BA
			$file_data .= create_DMG($row,$segTer,$compEleSep).$eol; ++$loopcounter;

			## PAYER NAME Loop 2010BB
			$file_data .= create_NM1($row,'PR',$segTer,$compEleSep).$eol; ++$loopcounter;
			## PAYER ADDRESS 2010BB
			$file_data .= create_N3($row,'PR',$segTer,$compEleSep).$eol; ++$loopcounter;
			## PAYER CITY, STATE, ZIP CODE
			$file_data .= create_N4($row,'PR',$segTer,$compEleSep).$eol; ++$loopcounter;

			## CLAIM INFORMATION 2300
			$file_data .= create_CLM($row,$segTer,$compEleSep).$eol; ++$loopcounter;

			## DATE ONSET OF CURRENT ILLNESS OR SYMPTOM
			$file_data .= create_DTP($row,'431',$segTer,$compEleSep).$eol;/* S */ ++$loopcounter;
			## DATE - INITIAL TREATMENT DATE 2300
			$file_data .= create_DTP($row,'454',$segTer,$compEleSep).$eol;/* S */ ++$loopcounter;
			## DATE - LAST SEEN DATE 2300
			$file_data .= create_DTP($row,'304',$segTer,$compEleSep).$eol; ++$loopcounter;
			
			if(!empty($row['patient_discharge_date'])){
				## DATE OF DISCHARGE 2300
				$file_data .= create_DTP($row,'096',$segTer,$compEleSep).$eol;/* S */ ++$loopcounter;
			}

			if(!empty($row['patient_admission_date'])){
				## DATE OF ADMISSION 2300
				$file_data .= create_DTP($row,'435',$segTer,$compEleSep).$eol;/* S */ ++$loopcounter;
			}


			if(!empty($row['patient_paid_amt'])){
			## PATIENT AMOUNT PAID 2300
				$file_data .= create_AMT($row,$segTer,$compEleSep).$eol; ++$loopcounter;
			}

			if(!empty($row['referral_number'])){
			## REFERRAL NUMBER 2300 **Required when Referring Provider is sent (REF*DN)
				$file_data .= create_REF($row,'9F',$segTer,$compEleSep).$eol; /* S */ ++$loopcounter;
			}

			if(!empty($row['prior_auth_code'])){
			## PRIOR AUTHORIZATION 2300
				$file_data .= create_REF($row,'G1',$segTer,$compEleSep).$eol; ++$loopcounter;
			}

			if($row['claim_type'] != 1){				
			## PAYER CLAIM CONTROL NUMBER 2300 (Required when CLM05-03 indicates replacement or void
				$file_data .= create_REF($row,'F8',$segTer,$compEleSep).$eol; ++$loopcounter;
			}

			if(!empty($row['original_claim_number'])){
			## CLINICAL LABORATORY IMPROVEMENT AMENDMENT Facilities performing CLIA covered Laboratory services S
				$file_data .= create_REF($row,'X4',$segTer,$compEleSep).$eol; ++$loopcounter;
			}

			if(!empty($row['patient_mrn'])){
				## MEDICAL RECORD NUMBER 2300
				$file_data .= create_REF($row,'EA',$segTer,$compEleSep).$eol; ++$loopcounter;
			}

			if(!empty($row['claim_notes'])){
			## CLAIM NOTE 2300
				$file_data .= create_NTE($row,$segTer,$compEleSep).$eol; ++$loopcounter;
			} 
			## HEALTH CARE DIAGNOSIS CODE 2300
			$file_data .= create_HI($row,1,$segTer,$compEleSep).$eol;
			++$loopcounter;
				 /* Primary Problem code ICD 9 or ICD 10*/

			if(sizeof($row['other_diag_list'])){
				$file_data .= create_HI($row,2,$segTer,$compEleSep).$eol;
				++$loopcounter; /* secondart type code ICD 9 or ICD 10*/
			}

			## REFERRING PROVIDER NAME 2310A 
			$file_data .= create_NM1($row,'DN',$segTer,$compEleSep).$eol; ++$loopcounter;/* S */
			//Rendering Provider - S
			// Per the implementation guide lines, only include this information if it is different
	    	// RENDERING PROVIDER NAME  Loop 2310B than the Loop 2010AA information
			//$file_data .= create_NM1($row,82,$segTer,$compEleSep); ++$loopcounter;

			// Loop 2310C is omitted in the case of home visits (POS=12).
			## SERVICE FACILITY LOCATION 2310C
			//$file_data .= create_NM1($row,77,$segTer,$compEleSep); ++$loopcounter;
			//$file_data .= create_N3($row,'FA',$segTer,$compEleSep); /* Facility */ ++$loopcounter;
			//$file_data .= create_N4($row,'FA',$segTer,$compEleSep); /* Facility */ ++$loopcounter;

			/* THE BELOW CODE BLOCK IS REQUIRED IF THE PATIENT HAD MORETHAN ONE POLICY */

			/*S OTHER SUBSCRIBER INFORMATION 2320*/
			if(!empty($row['subscriber_secondary_group_number'])){
				$file_data .= create_SBR($row,'OT',$segTer,$compEleSep).$eol; ++$loopcounter;

				## Other Insurance Coverage Information
				//$file_data .= create_OI($row,$segTer,$compEleSep); ++$loopcounter;
				## OTHER SUBSCRIBER NAME 2330A
				## When passing secondary info policy number also should be changed
				$row['subscriber_policy_number'] = $row['subscriber_secondary_group_number'];
				$file_data .= create_NM1($row,'IL',$segTer,$compEleSep).$eol; ++$loopcounter;			
				$file_data .= create_N3($row,'SB',$segTer,$compEleSep).$eol; ++$loopcounter;
				$file_data .= create_N4($row,'SB',$segTer,$compEleSep).$eol; ++$loopcounter;

				## OTHER PAYER NAME 2330B
				if(!empty($row['secondary_payer_name'])){
					$file_data .= create_NM1($row,'PR2',$segTer,$compEleSep).$eol; ++$loopcounter;
				}
				if(!empty($row['secondary_payer_street'])){
					$file_data .= create_N3($row,'PR2',$segTer,$compEleSep).$eol; ++$loopcounter;
				}
				if(!empty($row['secondary_payer_city'])){
					$file_data .= create_N4($row,'PR2',$segTer,$compEleSep).$eol; ++$loopcounter;
				}
			}
			
			

			/* THE ABOVE CODE BLOCK IS REQUIRED IF THE PATIENT HAD MORETHAN ONE POLICY */

			if(sizeof($row['procedure_codes'])){
				$loopindex = 1;
				foreach($row['procedure_codes'] as $proc_items){
					## SERVICE LINE NUMBER 2400 - Loop this with all procedure codes
					$file_data .= create_LX($row,$loopindex,$segTer,$compEleSep).$eol; ++$loopcounter;
					## PROFESSIONAL SERVICE 2400
					$file_data .= create_SV1($row,$proc_items, $segTer,$compEleSep).$eol; ++$loopcounter;
					$file_data .= create_DTP($row,472,$segTer,$compEleSep,$proc_items['dos']).$eol; ++$loopcounter;
					/* The encounter loops ends here */
					$loopindex++;
				}
			}

			if(!empty($row['claim_notes'])){
				$file_data .= create_NTE($row,$segTer,$compEleSep).$eol; ++$loopcounter;
			}
			## Segment count should start from ST to SE
			++$loopcounter;
			$file_data .= create_SE($row,$loopcounter,$segTer,$compEleSep).$eol; 
			$file_data .= create_GE($row,$segTer,$compEleSep).$eol; 
			$file_data .= create_IEA($row,$segTer,$compEleSep).$eol;
		}

		$file = fopen("2021121519.837","w");
		fwrite($file,$file_data);
		fclose($file);

	}



	

	


	?>