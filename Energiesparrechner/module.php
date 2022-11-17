<?php

declare(strict_types=1);
	class Energiesparrechner extends IPSModule
	{
		const PREFIX = "ESR";

		public function Create()
		{
			//Never delete this line!
			parent::Create();

			$this->RegisterPropertyInteger("MeterVariableID", 0);
			$this->RegisterPropertyInteger("TemperatureVariableID", 0);
			$this->RegisterPropertyInteger("PeriodType", 0);
			$this->RegisterPropertyInteger("UpdateInterval", 24 );

			$this->RegisterProfile(2, static::PREFIX.".DegreeDays", "Temperature", "", " Kd", 0, 0, 0, 1);

			$variables = $this->GetVariableList();
			$this->RegisterPropertyString("Variables", json_encode ( $variables ) );

			$this->RegisterTimer("UpdateTimer", 0, static::PREFIX ."_Update(\$_IPS['TARGET']);");
		}

		public function Destroy()
		{
			//Never delete this line!
			parent::Destroy();
		}

		public function ApplyChanges()
		{
			//Never delete this line!
			parent::ApplyChanges();


			// Get Variable list
			$variables = json_decode( $this->ReadPropertyString("Variables"), true);


			// Check for new Variables in case of a module update
			// Get variable list template
			$variableList = $this->GetVariableList();

			if ( count( $variables) != count($variableList) )
			{
				$variables = $this->UpdateVariableList();
			}



			// Create Control Variables based on Period Type

			switch ( $this->ReadPropertyInteger("PeriodType") )
			{
				case 0:
					$StartDate = true;
					$EndDate = true;
					$PeriodLength = false;
					break;

				case 1:
					$StartDate = true;
					$EndDate = false;
					$PeriodLength = false;
					break;

				case 2:
					$StartDate = false;
					$EndDate = false;
					$PeriodLength = true;
					break;

				default:
					throw new Exception("Invalid PeriodType");
			}

			
			$this->MaintainVariable ("StartDate", $this->translate("begin of period"), 1, "~UnixTimestampDate", 0, $StartDate ); 
			$this->MaintainVariable ("EndDate", $this->translate("end of period"), 1, "~UnixTimestampDate", 1, $EndDate); 
			$this->MaintainVariable ("PeriodLength", $this->translate("period length in days"), 1, "", 1, $PeriodLength); 

			if ($StartDate ) 
			{ 
				$this->EnableAction("StartDate"); 
			}

			if ($EndDate ) 
			{ 
				$this->EnableAction("EndDate"); 
			}

			if ($PeriodLength ) 
			{ 
				$this->EnableAction("PeriodLength"); 
			}			

			// Create Status Variables
			$meterVariableAvailable = false;
			$meterVariableProfile = "";
			
			$meterVariableID = $this->ReadPropertyInteger("MeterVariableID");

			if ( IPS_VariableExists($meterVariableID) )
			{
				if (IPS_GetVariable( $meterVariableID )["VariableType"] == 2)
				{
					$meterVariableProfile = IPS_GetVariable( $meterVariableID )["VariableCustomProfile"];

					if ( $meterVariableProfile == "" )
					{
						$meterVariableProfile = IPS_GetVariable( $meterVariableID )["VariableProfile"];
					}
				}
				
				$meterVariableAvailable = true;
			}

			$temperatureVariableID = $this->ReadPropertyInteger("TemperatureVariableID");


			foreach( $variables as $variable)
			{
				$variableProfile = $variable["VariableProfile"];

				if ($variable["VariableProfile"] == "")
				{
					$variableProfile = $meterVariableProfile;
				} 

				$this->MaintainVariable ($variable["Ident"], $this->translate( $variable["Name"] ), $variable["VariableType"], $variableProfile, $variable["Position"], $variable["Active"] && $meterVariableAvailable);
			}

			// Set update timer

			if ($this->ReadPropertyInteger("UpdateInterval") > 0)
			{
            	$this->SetTimerInterval("UpdateTimer", $this->ReadPropertyInteger("UpdateInterval") * 60 * 60 * 1000);
			} 
			else
			{
				$this->SetTimerInterval("UpdateTimer", 0);
			}
		}


		public function RequestAction($Ident, $Value) {

			switch($Ident) {
				case "StartDate":
				case "EndDate":
				case "PeriodLength":
					$this->SetValue($Ident, $Value);
					$this->Update();
					break;
				default:
					throw new Exception("Invalid Ident");
			}
			
		}

		public function Update()
		{
	
			$meterID = $this->ReadPropertyInteger("MeterVariableID");
			$temperatureID = $this->ReadPropertyInteger("TemperatureVariableID");

			switch ( $this->ReadPropertyInteger("PeriodType") )
			{
				case 0:
					$start = $this->GetValue("StartDate");
					$end = $this->GetValue("EndDate");
					break;

				case 1:
					$start = $this->GetValue("StartDate");
					$end = time();
					break;

				case 2:
					$end = time();
					$start = $end - $this->GetValue("PeriodLength")*24*60*60;
					break;

				default:
					throw new Exception("Invalid PeriodType");
			}

			$temperatureCorrection = false;

			if ( IPS_VariableExists( $temperatureID ) )
			{
				$temperatureCorrection = true;
			}
			
			$periodLength = $end - $start;

			$currentPeriodStart = $start;
			$currentPeriodEnd = $end;

			$lastPeriodStart = $start - $periodLength;
			$lastPeriodEnd = $start;

			$lastYearsPeriodStart = $start - 365 * 24 * 60 * 60;
			$lastYearsPeriodEnd = $end - 365 * 24 * 60 * 60;

			$data["EnergyCurrentPeriod"] = $this->CalculateEnergy( $meterID, $currentPeriodStart, $currentPeriodEnd);
			$data["EnergyLastPeriod"]  = $this->CalculateEnergy( $meterID, $lastPeriodStart, $lastPeriodEnd);
			$data["EnergyLastYearsPeriod"]  = $this->CalculateEnergy( $meterID, $lastYearsPeriodStart, $lastYearsPeriodEnd);

			if ($data["EnergyLastPeriod"]  != 0 ) 
			{
				$data["PercentLastPeriod"] = ($data["EnergyCurrentPeriod"] / $data["EnergyLastPeriod"] - 1 )*100;
			}

			if ($data["EnergyLastYearsPeriod"]  != 0 ) 
			{
				$data["PercentLastYearsPeriod"] = ($data["EnergyCurrentPeriod"] / $data["EnergyLastYearsPeriod"] - 1 )*100;
			}


			if ($temperatureCorrection)
			{
				$data["DegreeDaysCurrentPeriod"]  = $this->CalculatedegreeDays ( $temperatureID, $currentPeriodStart, $currentPeriodEnd);
				$data["DegreeDaysLastPeriod"] = $this->CalculatedegreeDays ( $temperatureID, $lastPeriodStart , $lastPeriodEnd);
				$data["DegreeDaysLastYearsPeriod"] = $this->CalculatedegreeDays ( $temperatureID, $lastYearsPeriodStart , $lastYearsPeriodEnd);

				$data["EnergyLastPeriodCorrected"] = $data["EnergyLastPeriod"] / $data["DegreeDaysLastPeriod"] * $data["DegreeDaysCurrentPeriod"];
				$data["EnergyLastYearsPeriodCorrected"] = $data["EnergyLastYearsPeriod"] / $data["DegreeDaysLastYearsPeriod"] * $data["DegreeDaysCurrentPeriod"];

				if ($data["EnergyLastPeriodCorrected"]  != 0 ) 
				{
					$data["PercentLastPeriodCorrected"] = ($data["EnergyCurrentPeriod"] / $data["EnergyLastPeriodCorrected"] - 1 )*100;
				}

				if ($data["EnergyLastYearsPeriodCorrected"]  != 0 ) 
				{
					$data["PercentLastYearsPeriodCorrected"] = ($data["EnergyCurrentPeriod"] / $data["EnergyLastYearsPeriodCorrected"] - 1 )*100;
				}
			}

			$this->SendDebug( "Calculation",  print_r($data, true), 0);

			$variables = json_decode ( $this->ReadPropertyString("Variables"), true );

			foreach( $variables as $variable )
			{
				$ident = $variable["Ident"];
				$value = 0;

				if (array_key_exists( $ident, $data) )
				{
					$value  = $data[ $ident ];
				}


				if ( $variable["Active"] && @$this->GetIDForIdent($ident) )
				{
					$this->SetValue( $ident, $value);
				}
				
			}

		}

		public function UpdateVariableList()
		{
			// Get current variable list
			$variables = json_decode( $this->ReadPropertyString("Variables"), true);

			// Get variable list  template
			$variableList = $this->GetVariableList();

			// Generate a new Variable List from template
			foreach ($variableList as $index => $newVariable)
			{
				
				// If variable already existed, keep Active parameter
				$variablesIndex = array_search( $newVariable['Ident'], array_column( $variables, 'Ident') );
				if ($variablesIndex !== false)
				{
					$variableList[$index]['Active']  = $variables[$variablesIndex]['Active'];
				}
			}
			
			IPS_SetProperty( $this->InstanceID, "Variables", json_encode ( $variableList ) );
			IPS_ApplyChanges( $this->InstanceID );	

			return $variableList;
		}

		public function ResetVariableList( )
		{
			$variables = $this->GetVariableList();

			foreach ($variables as $index => $variable)
			{
				if ( $varID = @$this->GetIDForIdent( $variable['Ident'] )  )
				{
					IPS_SetName( $varID, $variable['Name'] );
				}
			}
	

			IPS_SetProperty( $this->InstanceID, "Variables", json_encode ( $variables ) );
			IPS_ApplyChanges( $this->InstanceID );
		}

		private function GetVariableList()
		{
	
			$file = __DIR__ . "/../libs/variables.json";
			if (is_file($file))
			{
				$data = json_decode(file_get_contents($file), true);
			}
			else
			{
				$data = array();
			}
	
			foreach ($data as $index => $variable)
			{
				$data[$index]['Name'] = $this->Translate( $variable['Name'] ) ;
			}

			return $data;
		}


		private function CalculateEnergy( $varID, $startTime, $endTime)
		{
			$archiveID = $this->GetArchiveID();
		
			if ( AC_GetLoggingStatus( $archiveID, $varID ) === false ) throw new Exception("Logging ist not available for meter variable");

			$data = AC_GetAggregatedValues( $archiveID, $varID, 1, $startTime, $endTime, 0); // Tägliche Daten der letzten 30 Tage
		
			$duration = 0;
			$value = 0;
			foreach ( $data as $dp) 
			{
				$value += $dp['Avg'];
				$duration += $dp['Duration'];
			}
		
			return $value;
		}
		
		private function CalculatedegreeDays( $varID, $startTime, $endTime )
		{
			$archiveID = $this->GetArchiveID();
		
			if ( AC_GetLoggingStatus( $archiveID, $varID ) === false ) throw new Exception("Logging ist not available for temperature variable");

			$data = AC_GetAggregatedValues( $archiveID, $varID, 1, $startTime, $endTime, 0);
		
			$degreedays = 0;
		
			foreach( $data as $dp)
			{
				if ($dp['Avg'] < 15)
				{
					$degreedays += 20 - $dp['Avg'];
				}
			}
		
			if ($degreedays == 0) {$degreedays = 1;}

			return $degreedays;
		}
		

		private function GetArchiveID()
		{
			return IPS_GetInstanceListByModuleID( "{43192F0B-135B-4CE7-A0A7-1475603F3060}" )[0];
		}


		protected function RegisterProfile($VarTyp, $Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $StepSize, $Digits = 0)
		{
			if (!IPS_VariableProfileExists($Name)) {
				IPS_CreateVariableProfile($Name, $VarTyp);
			} else {
				$profile = IPS_GetVariableProfile($Name);
				if ($profile['ProfileType'] != $VarTyp) {
					throw new \Exception('Variable profile type does not match for profile ' . $Name, E_USER_WARNING);
				}
			}
	
			IPS_SetVariableProfileIcon($Name, $Icon);
			IPS_SetVariableProfileText($Name, $Prefix, $Suffix);
			switch ($VarTyp) {
				case VARIABLETYPE_FLOAT:
					IPS_SetVariableProfileDigits($Name, $Digits);
					// no break
				case VARIABLETYPE_INTEGER:
					IPS_SetVariableProfileValues($Name, $MinValue, $MaxValue, $StepSize);
					break;
			}
		}
	}

