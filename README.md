# EDI X12 ANSI 5010
PHP Library for creating EDI X12 ANSI 837 File 5010 Version

A Simple PHP function for creating an EDI X12 ANSI 837 file version 005010X222A1

* <a href="https://www.edibasics.com/what-is-edi/" target="_blank">EDI Transactions</a>

* <a href="http://www.x12.org/examples/" target="_blank">X12 Examples</a>


## How To use
```````````
$res[] = [
				 'x12_sender_id' => '123456' ## Federal Tax ID
				,'x12_reciever_id' => '123456' ## Wharehouse Federal Tax ID not changable
				,'x12_version' => '005010X222A1'
				,'transcode' => '03' // you can put unique value here
				,'batch_number' => '4' // if you're using batch then apply this
				,'group_number' => '4'
								
				,'payer_name'=>''
				,'payer_code' => ''
				,'payer_street' => ''
				,'payer_street2' => ''
				,'payer_city' => ''
				,'payer_state' => ''
				,'payer_zip' => ''

				,'secondary_payer_name'=>''
				,'secondary_payer_code' => ''
				,'secondary_payer_street' => ''
				,'secondary_payer_street2' => ''
				,'secondary_payer_city' => ''
				,'secondary_payer_state' => ''
				,'secondary_payer_zip' => ''
				
				,'billing_provider_lastname' => ''
				,'billing_provider_npi' => '' 
				,'billing_provider_street' => ''
				,'billing_provider_street2' => ' '
				,'billing_provider_city' => ''
				,'billing_provider_state' =>''
				,'billing_provider_zip' => ''
				,'billing_provider_pin' => ''
				,'billing_provider_federal_taxid' =>''//
				
				,'subscriber_lname' => ''
				,'subscriber_fname' => '' 
				,'subscriber_mname' => ''
				,'subscriber_policy_number' => ''
				,'subscriber_address' => ''
				,'subscriber_address2' => ''
				,'subscriber_city' =>''
				,'subscriber_state' =>''
				,'subscriber_zip' => ''
				,'subscriber_relationship' => 'self'
				,'subscriber_secondary_group_number' => ''
				,'subscriber_secondary_payer_name' => ''
				,'subscriber_secondary_insurance_type' => ''
				,'subscriber_dob' =>''
				,'subscriber_gender' =>'M'
				
				,'ref_physician_lname' => ''
				,'ref_physician_fname' => ''
				,'ref_physician_mname' => ''
				,'ref_physician_npi' =>  ''
				,'referral_number' => ''				

				,'rendering_provider_lname' => ' '
				,'rendering_provider_fname' => ''
				,'rendering_provider_mname' => ''
				,'rendering_provider_npi' => ''

				,'facility_name' =>''
				,'facility_npi' => ''
				,'facility_address' =>''
				,'facility_city' =>''
				,'facility_state' =>'MI' 
				,'facility_zip' => '480736712'

				,'submitter_org_name' =>''
				,'submitter_tax_id' => '' ## Federal Tax ID
				,'submitter_name' => ''
				,'submitter_telephone' => ''
				,'submitter_email' => ''

				,'patient_id' => ''
				,'patient_mrn' => ''
				,'patient_encounter_date' => ''
				,'patient_first_encounter_date' =>''
				,'patient_last_visit_date' =>''
				,'patient_admission_date' =>''
				,'patient_discharge_date' =>''
				,'patient_paid_amt' =>'0'
			
				,'prior_auth_code' =>''
				,'original_claim_number' =>''	 //In case of resubmission have to pass original claim number		
				,'insurance_type_code' => ''				
				,'total_amount' => ''
				,'claim_type' => '1' // 1 New Claim , 7 Re submission
				,'claim_notes' => 'Sample Notes'
				,'primary_problem_type_code' =>'ICD9'
				,'primary_problem_code' => 'I10'
				,'other_diag_list' => [
						['icd_type' => 'ICD9','icd_codes' => 'D63.8']						
				],
				'procedure_codes' => [
					[	'cpt_codes' => '99235'
						,'cpt_charge' => '284'
						,'code_pointer'=>'1'
						,'dos' => '20170726'
						,'quantity' => 1
						,'facility_code'=>'21'
					]
				]
				
			];
	/****************** Element data seperator		**************/
	$eleDataSep		= "*";

	/****************** Segment Terminator			**************/
	$segTer			= "~"; 	

	/****************** Component Element seperator **************/
	$compEleSep		= ":"; //or  ^ 			
	create_x12_837_file($res,$segTer,$eleDataSep,$compEleSep);
	echo "Done";

```````````


The output file generated have a valid X12 EDI ANSI 837 file , just send the file to your insurance wharehouse

## Donation
If this project help you reduce time to develop, you can give me a cup of coffee :) 

[![paypal](https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=692Q7RBU2WG8S)




