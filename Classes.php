<?php
        
//File Description @0-AEB1E349
//======================================================
//
//  This file contains the following classes:
//      class clsSQLParameters
//      class clsSQLParameter
//      class clsControl
//      class clsField
//      class clsButton
//      class clsFileUpload
//      class clsDatePicker
//      class clsErrors
//
//======================================================
//End File Description

//Constant List @0-EB100734

// ------- Controls ---------------
define("ccsLabel",        1);
define("ccsLink",         2);
define("ccsTextBox",      3);
define("ccsTextArea",     4);
define("ccsListBox",      5);
define("ccsRadioButton",  6);
define("ccsButton",       7);
define("ccsCheckBox",     8);
define("ccsImage",        9);
define("ccsImageLink",    10);
define("ccsHidden",       11);
define("ccsCheckBoxList", 12);

$ControlTypes = array(
  "", "Label","Link","TextBox","TextArea","ListBox","RadioButton",
  "Button","CheckBox","Image","ImageLink","Hidden","CheckBoxList"
);


// ------- Operators --------------
define("opEqual",              1);
define("opNotEqual",           2);
define("opLessThan",           3);
define("opLessThanOrEqual",    4);
define("opGreaterThan",        5);
define("opGreaterThanOrEqual", 6);
define("opBeginsWith",         7);
define("opNotBeginsWith",      8);
define("opEndsWith",           9);
define("opNotEndsWith",        10);
define("opContains",           11);
define("opNotContains",        12);
define("opIsNull",             13);
define("opNotNull",            14);

// ------- Datasource types -------
define("dsTable",        1);
define("dsSQL",          2);
define("dsProcedure",    3);
define("dsListOfValues", 4);
define("dsEmpty",        5);

// ------- CheckBox states --------
define("ccsChecked", true);
define("ccsUnchecked", false);


//End Constant List

//CCCheckValue @0-962BACE6
function CCCheckValue($Value, $DataType)
{
  $result = false;
  if($DataType == ccsInteger)
    $result = is_int($Value); 
  else if($DataType == ccsFloat)
    $result = is_float($Value);
  else if($DataType == ccsDate)
    $result = (is_array($Value) || is_int($Value));
  else if($DataType == ccsBoolean)
    $result = is_bool($Value); 
  return $result;
}
//End CCCheckValue

//clsSQLParameters Class @0-6A4B5147

class clsSQLParameters
{
  
  var $Connection;
  var $Criterion;
  var $AssembledWhere;
  var $Errors;
  var $DataSource;
  var $AllParametersSet;
  var $ErrorBlock;

  var $Parameters;

  function clsSQLParameters($ErrorBlock = "")
  {
    $this->ErrorBlock = $ErrorBlock;
  }

  function SetParameters($Name, $NewParameter)
  {
    $this->Parameters[$Name] = $NewParameter;
  }

  function AddParameter($ParameterID, $ParameterSource, $DataType, $Format, $DBFormat, $InitValue, $DefaultValue, $UseIsNull = false)
  {
    $this->Parameters[$ParameterID] = new clsSQLParameter($ParameterSource, $DataType, $Format, $DBFormat, $InitValue, $DefaultValue, $UseIsNull, $this->ErrorBlock);
  }

  function AllParamsSet()
  {
    $blnResult = true;

    if(isset($this->Parameters) && is_array($this->Parameters))
    {
      reset($this->Parameters);
      while ($blnResult && list ($key, $Parameter) = each ($this->Parameters)) 
      {
        if($Parameter->GetValue() === "" && $Parameter->GetValue() !== false && $Parameter->UseIsNull === false)
          $blnResult = false;
      }
    }
     return $blnResult;
  }

  function GetDBValue($ParameterID)
  {
    return $this->Parameters[$ParameterID]->GetDBValue();
  }

  function opAND($Brackets, $strLeft, $strRight)
  {
    $strResult = "";
    if (strlen($strLeft))
    {
      if (strlen($strRight)) 
      {
        $strResult = $strLeft . " AND " . $strRight;
        if ($Brackets) 
          $strResult = " (" . $strResult . ") ";
      }
      else
      {
        $strResult = $strLeft;
      }
    }
    else
    {
      if (strlen($strRight)) 
        $strResult = $strRight;
    }
    return $strResult;
  }

  function opOR($Brackets, $strLeft, $strRight)
  {
    $strResult = "";
    if (strlen($strLeft))
    {
      if (strlen($strRight))
      {
        $strResult = $strLeft . " OR " . $strRight;
        if ($Brackets) 
          $strResult = " (" . $strResult . ") ";
      }
      else
      {
        $strResult = $strLeft;
      }
    }
    else
    {
      if (strlen($strRight))
        $strResult = $strRight;
    }
    return $strResult;
  }

  function Operation($Operation, $FieldName, $DBValue, $SQLText, $UseIsNull = false)
  {
    $Result = "";

    if(strlen($DBValue) || $DBValue === false)
    {
      $SQLValue = $SQLText;
      if(substr($SQLValue, 0, 1) == "'")
        $SQLValue = substr($SQLValue, 1, strlen($SQLValue) - 2);

      switch ($Operation)
      {
        case opEqual:
          $Result = $FieldName . " = " . $SQLText;
          break;
        case opNotEqual:
          $Result = $FieldName . " <> " . $SQLText;
          break;
        case opLessThan:
          $Result = $FieldName . " < " . $SQLText;
          break;
        case opLessThanOrEqual:
          $Result = $FieldName . " <= " . $SQLText;
          break;
        case opGreaterThan:
          $Result = $FieldName . " > " . $SQLText;
          break;
        case opGreaterThanOrEqual:
          $Result = $FieldName . " >= " . $SQLText;
          break;                                
        case opBeginsWith:
          $Result = $FieldName . " like '" . $SQLValue . "%'";
          break;
        case opNotBeginsWith:
          $Result = $FieldName . " not like '" . $SQLValue . "%'";
          break;
        case opEndsWith:
          $Result = $FieldName . " like '%" . $SQLValue . "'";
          break;
        case opNotEndsWith:
          $Result = $FieldName . " not like '%" . $SQLValue . "'";
          break;
        case opContains:
          $Result = $FieldName . " like '%" . $SQLValue . "%'";
          break;
        case opNotContains:
          $Result = $FieldName . " not like '%" . $SQLValue . "%'";
          break;
        case opIsNull:
          $Result = $FieldName . " IS NULL";
          break;
        case opNotNull:
          $Result = $FieldName . " IS NOT NULL";
          break;
      }
    } 
    else if ($UseIsNull) 
    {
      switch ($Operation)
      {
        case opEqual:
        case opLessThan:
        case opLessThanOrEqual:
        case opGreaterThan:
        case opGreaterThanOrEqual:
        case opBeginsWith:
        case opEndsWith:
        case opContains:
        case opIsNull:
          $Result = $FieldName . " IS NULL";
          break;
        case opNotEqual:
        case opNotEndsWith:
        case opNotBeginsWith:
        case opNotContains:
        case opNotNull:
          $Result = $FieldName . " IS NOT NULL";
          break;
      }

    }

    return $Result;
  }
}
//End clsSQLParameters Class

//clsSQLParameter Class @0-4C84CF4E
class clsSQLParameter
{
  var $Errors;
  var $DataType;
  var $Format;
  var $DBFormat;
  var $Link;
  var $Caption;
  var $ErrorBlock;
  var $UseIsNull;

  var $Value;
  var $DBValue;
  var $Text;
  

  function clsSQLParameter($ParameterSource, $DataType, $Format, $DBFormat, $InitValue, $DefaultValue, $UseIsNull = false, $ErrorBlock = "")
  {
    $this->Errors = new clsErrors();
    $this->ErrorBlock = $ErrorBlock;
    $this->UseIsNull = $UseIsNull;

    $this->Caption = $ParameterSource;
    $this->DataType = $DataType;
    $this->Format = $Format;
    $this->DBFormat = $DBFormat;
    if(is_array($InitValue) && $DataType != ccsDate)
      $this->SetText(join(",", $InitValue));
    else if(is_array($InitValue) || strlen($InitValue))
      $this->SetText($InitValue);
    else
      $this->SetText($DefaultValue);
  }

  function GetParsedValue($ParsingValue, $Format)
  {
    global $Tpl;
    $varResult = "";

    if (strlen($ParsingValue))
    {
      switch ($this->DataType)
      {
        case ccsDate:
          $DateValidation = true;
          if (CCValidateDateMask($ParsingValue, $Format)) {
            $varResult = CCParseDate($ParsingValue, $Format);
            if(!CCValidateDate($varResult))
            {
              $DateValidation = false;
              $varResult = "";
            }
          } else {
            $DateValidation = false;
          }
          if(!$DateValidation) {
            if (is_array($Format)) {
              $FormatString = join("", $Format);
              $this->Errors->addError("The value in field " . $this->Caption . " is not valid. Use the following format: " . $FormatString . ". (" . htmlspecialchars($ParsingValue) .")");
            } else {
              $this->Errors->addError("The value in field " . $this->Caption . " is not valid. (" . htmlspecialchars($ParsingValue) . ")");
            }
          }
          break;
        case ccsBoolean:
          if (CCValidateBoolean($ParsingValue, $Format))
            $varResult = CCParseBoolean($ParsingValue, $Format);
          else
          {
            if (is_array($Format)) {
              $FormatString = CCGetBooleanFormat($Format);
              $this->Errors->addError("The value in field " . $this->Caption . " is not valid. Use the following format: " . $FormatString . ". (" . htmlspecialchars($ParsingValue) .")");
            } else {
              $this->Errors->addError("The value in field " . $this->Caption . " is not valid. (" . htmlspecialchars($ParsingValue) .")");
            }
          }
          break;
        case ccsInteger:
          if (CCValidateNumber($ParsingValue, $Format))
            $varResult = CCParseInteger($ParsingValue, $Format);
          else
          {
            $this->Errors->addError("The value in field " . $this->Caption . " is not valid. (" . htmlspecialchars($ParsingValue) . ")");
          }
          break;
        case ccsFloat:
          if (CCValidateNumber($ParsingValue, $Format) )
            $varResult = CCParseFloat($ParsingValue, $Format);
          else 
          {
            $this->Errors->addError("The value in field " . $this->Caption . " is not valid. (" . htmlspecialchars($ParsingValue) . ")");
          }
          break;
        case ccsText:
        case ccsMemo:
          $varResult = strval($ParsingValue);
          break;
      }
      if($this->Errors->Count() > 0)
      {
        if(strlen($this->ErrorBlock))
          $Tpl->replaceblock($this->ErrorBlock, $this->Errors->ToString());
        else
          echo $this->Errors->ToString();
      }
    }

    return $varResult;
  }

  function GetFormattedValue($Format)
  {
    $strResult = "";
    switch($this->DataType)
    {
      case ccsDate:
        $strResult = CCFormatDate($this->Value, $Format);
        break;
      case ccsBoolean:
        $strResult = CCFormatBoolean($this->Value, $Format);
        break;
      case ccsInteger:
      case ccsFloat:
        $strResult = CCFormatNumber($this->Value, $Format);
        break;
      case ccsText:
      case ccsMemo:
        $strResult = strval($this->Value);
        break;
    }
    return $strResult;
  }

  function SetValue($Value)
  {
    $this->Value = $Value;
    $this->Text = $this->GetFormattedValue($this->Format);
    $this->DBValue = $this->GetFormattedValue($this->DBFormat);
  }

  function SetText($Text)
  {
    if(CCCheckValue($Text, $this->DataType)) {
      $this->SetValue($Text);
    } else {
      $this->Text = $Text;
      $this->Value = $this->GetParsedValue($this->Text, $this->Format);
      $this->DBValue = $this->GetFormattedValue($this->DBFormat);
    }
  }

  function SetDBValue($DBValue)
  {
    $this->DBValue = $DBValue;
    $this->Value = $this->GetParsedValue($this->DBValue, $this->DBFormat);
    $this->Text = $this->GetFormattedValue($this->Format);
  }

  function GetValue()
  {
    return $this->Value;
  }

  function GetText()
  {
    return $this->Text;
  }

  function GetDBValue()
  {
    return $this->DBValue;
  }

}

//End clsSQLParameter Class

//clsControl Class @0-BB039C33
class clsControl
{
  var $Errors;
  var $DataType;
  var $DSType;
  var $Format;
  var $DBFormat;
  var $Caption;
  var $ControlType;
  var $ControlTypeName;
  var $Name;
  var $BlockName;
  var $HTML;
  var $Required;
  var $CheckedValue;
  var $UncheckedValue;
  var $State;
  var $BoundColumn;
  var $TextColumn;
  var $Multiple;
  var $Visible;

  var $Page;
  var $Parameters;

  var $Value;
  var $Text;
  var $Values;

  var $CCSEvents;
  var $CCSEventResult;

  function clsControl($ControlType, $Name, $Caption, $DataType, $Format, $InitValue = "")
  {
    global $ControlTypes;

    $this->Value = "";
    $this->Text = "";
    $this->Page = "";
    $this->Parameters = "";
    $this->CCSEvents = "";
    $this->Values = "";
    $this->BoundColumn = "";
    $this->TextColumn = "";
    $this->Visible = true;

    $this->Required = false;
    $this->HTML = false;
    $this->Multiple = false;

    $this->Errors = new clsErrors;

    $this->Name = $Name;
    $this->BlockName = $ControlTypes[$ControlType] . " " . $Name;
    $this->ControlType = $ControlType;
    $this->DataType = $DataType;
    $this->DSType = dsEmpty;
    $this->Format = $Format;
    $this->Caption = $Caption;
    if(is_array($InitValue))
      $this->Value = $InitValue;
    else if(strlen($InitValue))
      $this->SetText($InitValue);
  }

  function Validate()
  {
    $validation = true;
    if($this->Required && $this->Value === "" && $this->Errors->Count() == 0)
    {
      $FieldName = strlen($this->Caption) ? $this->Caption : $this->Name;
      $this->Errors->addError("The value in field " . $FieldName . " is required.");
    }
    $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate");
    return ($this->Errors->Count() == 0);
  }

  function GetParsedValue($ParsingValue)
  {
    $varResult = "";
    if($this->Multiple && is_array($ParsingValue)) {
      $ParsingValue = $ParsingValue[0];
    }
    if(CCCheckValue($ParsingValue, $this->DataType))
      $varResult = $ParsingValue;
    else if(strlen($ParsingValue))
    {
      switch ($this->DataType)
      {
        case ccsDate:
          $DateValidation = true;
          if (CCValidateDateMask($ParsingValue, $this->Format)) {
            $varResult = CCParseDate($ParsingValue, $this->Format);
            if(!CCValidateDate($varResult))
            {
              $DateValidation = false;
              $varResult = "";
            }
          } else {
            $DateValidation = false;
          }
          if(!$DateValidation && $this->Errors->Count() == 0)
          {
            if (is_array($this->Format)) {
              $FormatString = join("", $this->Format);
              $this->Errors->addError("The value in field " . $this->Caption . " is not valid. Use the following format: " . $FormatString . ".");
            } else {
              $this->Errors->addError("The value in field " . $this->Caption . " is not valid.");
            }
          }
          break;
        case ccsBoolean:
          if (CCValidateBoolean($ParsingValue, $this->Format))
            $varResult = CCParseBoolean($ParsingValue, $this->Format);
          else if($this->Errors->Count() == 0) {
            if (is_array($this->Format)) {
              $FormatString = CCGetBooleanFormat($this->Format);
              $this->Errors->addError("The value in field " . $this->Caption . " is not valid. Use the following format: " . $FormatString . ".");
            } else {
              $this->Errors->addError("The value in field " . $this->Caption . " is not valid.");
            }
          }
          break;
        case ccsInteger:
          if (CCValidateNumber($ParsingValue, $this->Format))
            $varResult = CCParseInteger($ParsingValue, $this->Format);
          else if($this->Errors->Count() == 0)
            $this->Errors->addError("The value in field " . $this->Caption . " is not valid.");
          break;
        case ccsFloat:
          if (CCValidateNumber($ParsingValue, $this->Format))
            $varResult = CCParseFloat($ParsingValue, $this->Format);
          else if($this->Errors->Count() == 0)
            $this->Errors->addError("The value in field " . $this->Caption . " is not valid.");
          break;
        case ccsText:
        case ccsMemo:
          $varResult = strval($ParsingValue);
          break;
      }
    }

    return $varResult;
  }

  function GetFormattedValue()
  {
    $strResult = "";
    switch($this->DataType)
    {
      case ccsDate:
        $strResult = CCFormatDate($this->Value, $this->Format);
        break;
      case ccsBoolean:
        $strResult = CCFormatBoolean($this->Value, $this->Format);
        break;
      case ccsInteger:
      case ccsFloat:
        $strResult = CCFormatNumber($this->Value, $this->Format);
        break;
      case ccsText:
      case ccsMemo:
        $strResult = strval($this->Value);
        break;
    }
    return $strResult;
  }

  function Prepare()
  {
    if($this->DSType == dsTable || $this->DSType == dsSQL || $this->DSType == dsProcedure)
    {
      if(!isset($this->ds->CCSEvents)) $this->ds->CCSEvents = "";
      if(!strlen($this->BoundColumn)) $this->BoundColumn = 0;
      if(!strlen($this->TextColumn)) $this->TextColumn = 1;
      $this->EventResult = CCGetEvent($this->ds->CCSEvents, "BeforeBuildSelect");
      $this->EventResult = CCGetEvent($this->ds->CCSEvents, "BeforeExecuteSelect");
      $FieldName = strlen($this->Caption) ? $this->Caption : $this->Name;
      list($this->Values, $this->Errors) = CCGetListValues($this->ds, $this->ds->SQL, $this->ds->Where, $this->ds->Order, $this->BoundColumn, $this->TextColumn, $this->DBFormat, $this->DataType, $this->Errors, $FieldName);
      $this->ds->close();
      $this->EventResult = CCGetEvent($this->ds->CCSEvents, "AfterExecuteSelect");
    }
  }

  function Show($RowNumber = "")
  {
    global $Tpl;
    $this->EventResult = CCGetEvent($this->CCSEvents, "BeforeShow");

    $ControlName = ($RowNumber === "") ? $this->Name : $this->Name . "_" . $RowNumber;
    if($this->Multiple) $ControlName = $ControlName . "[]";

    if(!$this->Visible) {
      $Tpl->SetVar($this->Name . "_Name", $ControlName);
      $Tpl->SetVar($this->Name, "");
      if($Tpl->BlockExists($this->BlockName))
        $Tpl->setblockvar($this->BlockName, "");
      return;
    }

    $Tpl->SetVar($this->Name . "_Name", $ControlName);
    switch($this->ControlType)
    {
      case ccsLabel:
        if ($this->HTML)
          $Tpl->SetVar($this->Name, $this->GetText());
        else {
          $value = htmlspecialchars($this->GetText());
          $value = str_replace("\n", "<BR>", $value);
          $Tpl->SetVar($this->Name, $value);
        }
        $Tpl->ParseSafe($this->BlockName, false);
        break;
      case ccsTextBox:
      case ccsTextArea:
      case ccsImage:
      case ccsHidden:
        $Tpl->SetVar($this->Name, htmlspecialchars($this->GetText()));
        $Tpl->ParseSafe($this->BlockName, false);
        break;
      case ccsLink:
        if ($this->HTML)
          $Tpl->SetVar($this->Name, $this->GetText());
        else {
          $value = htmlspecialchars($this->GetText());
          $value = str_replace("\n", "<BR>", $value);
          $Tpl->SetVar($this->Name, $value);
        }
        $Tpl->SetVar($this->Name . "_Src", $this->GetLink());
        $Tpl->ParseSafe($this->BlockName, false);
        break;
      case ccsImageLink:
        $Tpl->SetVar($this->Name . "_Src", htmlspecialchars($this->GetText()));
        $Tpl->SetVar($this->Name, $this->GetLink());
        $Tpl->ParseSafe($this->BlockName, false);
        break;
      case ccsCheckBox:
        if($this->Value)
          $Tpl->SetVar($this->Name, "CHECKED");
        else
          $Tpl->SetVar($this->Name, "");
        $Tpl->ParseSafe($this->BlockName, false);
        break;
      case ccsRadioButton:
        $BlockToParse = "RadioButton " . $this->Name;
        $Tpl->SetBlockVar($BlockToParse, "");
        if(is_array($this->Values))
        {
          for($i = 0; $i < sizeof($this->Values); $i++)
          {
            $Value = $this->Values[$i][0];
            $Text = $this->HTML ? $this->Values[$i][1] : htmlspecialchars($this->Values[$i][1]);
            $Selected = (CCCompareValues($Value,$this->Value, $this->DataType, $this->Format) == 0) ? " CHECKED" : "";
            $TextValue = htmlspecialchars(CCFormatValue($Value, $this->Format, $this->DataType, $this->Format));
            $Tpl->SetVar("Value", $TextValue);
            $Tpl->SetVar("Check", $Selected);
            $Tpl->SetVar("Description", $Text);
            $Tpl->Parse($BlockToParse, true);
          }
        }
        break;
      case ccsCheckBoxList:
        $BlockToParse = "CheckBoxList " . $this->Name;
        $Tpl->SetBlockVar($BlockToParse, "");
        if(is_array($this->Values))
        {
          for($i = 0; $i < sizeof($this->Values); $i++)
          {
            $Value = $this->Values[$i][0];
            $TextValue = htmlspecialchars(CCFormatValue($Value, $this->Format, $this->DataType));
            $Text = $this->HTML ? $this->Values[$i][1] : htmlspecialchars($this->Values[$i][1]);
	    if ($this->Multiple && is_array($this->Value)) {
              $Selected = "";
              foreach ($this->Value as $Val) {
                if (CCCompareValues($Value,$Val, $this->DataType, $this->Format) == 0) {
                  $Selected = " CHECKED";
                  break;  
                }
              }
	    } else {
              $Selected = (CCCompareValues($Value,$this->Value, $this->DataType, $this->Format) == 0) ? " CHECKED" : "";
            }
            $Tpl->SetVar("Value", $TextValue);
            $Tpl->SetVar("Check", $Selected);
            $Tpl->SetVar("Description", $Text);
            $Tpl->Parse($BlockToParse, true);
          }
        }
        break;
      case ccsListBox:
        $Options = "";
        if(is_array($this->Values))
        {
          for($i = 0; $i < sizeof($this->Values); $i++)
          {
            $Value = $this->Values[$i][0];
            $TextValue = htmlspecialchars(CCFormatValue($Value, $this->Format, $this->DataType));
            $Text = htmlspecialchars($this->Values[$i][1]);
	    if ($this->Multiple && is_array($this->Value)) {
              $Selected = "";
              foreach ($this->Value as $Val) {
                if (CCCompareValues($Value,$Val, $this->DataType, $this->Format) == 0) {
                  $Selected = " SELECTED";
                  break;  
                }
              }
	    } else {
              $Selected = (CCCompareValues($Value,$this->Value, $this->DataType, $this->Format) == 0) ? " SELECTED" : "";
            }
            $Options .= "<OPTION VALUE=\"" . $TextValue . "\"" . $Selected . ">" . $Text . "</OPTION>\n";
          }
        }
        $Tpl->SetVar($this->Name . "_Options", $Options);
        $Tpl->ParseSafe($this->BlockName, false);
        break;
    }
  }

  function SetValue($Value)
  {
    if($this->ControlType == ccsCheckBox)
      $this->Value = (CCCompareValues($Value,$this->CheckedValue, $this->DataType, $this->Format) == 0) ? true : false;
    else
      $this->Value = $Value;
    $this->Text = $this->GetFormattedValue();
  }

  function SetText($Text, $RowNumber = "")
  {
    $ControlName = ($RowNumber === "") ? $this->Name : $this->Name . "_" . $RowNumber;
    if(CCCheckValue($Text, $this->DataType)) {
      $this->SetValue($Text);
    } else {
      $this->Text = $Text;
      if($this->ControlType == ccsCheckBox) {
        $RequestParameter = CCGetParam($ControlName);
        if (strlen($Text) && strlen($RequestParameter) && $Text == $RequestParameter) {
          $this->Value = true;
        } else {
          $Value = $this->GetParsedValue($this->Text);
          $this->SetValue($Value);
        }

      } else {
        $this->Value = $this->GetParsedValue($this->Text);
      }
    }
  }

  function GetValue()
  {
    if($this->ControlType == ccsCheckBox)
      $value = ($this->Value) ? $this->CheckedValue : $this->UncheckedValue;
    else if($this->Multiple && is_array($this->Value))
      $value = $this->Value[0];
    else
      $value = $this->Value;

    return $value;
  }

  function GetText()
  {
    if(!strlen($this->Text))
      $this->Text = $this->GetFormattedValue();
    return $this->Text;
  }

  function GetLink()
  {
    if(substr($this->Page, 0, 2) == "./")
      $this->Page = substr($this->Page, 2);
    if($this->Parameters == "")
      return $this->Page;
    else
      return $this->Page . "?" . $this->Parameters;
  }

  function SetLink($Link)
  {
    if(!strlen($Link))
    {
      $this->Page = "";
      $this->Parameters = "";
    }
    else
    {
      $LinkParts = explode("?", $Link);
      $this->Page = $LinkParts[0];
      $this->Parameters = (sizeof($LinkParts) == 2) ? $LinkParts[1] : "";
    }
  }

}

//End clsControl Class

//clsField Class @0-E1C7DA34
class clsField
{
  var $DataType;
  var $DBFormat;
  var $Name;
  var $Errors;

  var $Value;
  var $DBValue;

  function clsField($Name, $DataType, $DBFormat)
  {
    $this->Value = "";
    $this->DBValue = "";

    $this->Name = $Name;
    $this->DataType = $DataType;
    $this->DBFormat = $DBFormat;

    $this->Errors = new clsErrors;
  }

  function GetParsedValue()
  {
    $varResult = "";

    if (strlen($this->DBValue))
    {
      switch ($this->DataType)
      {
        case ccsDate:
          $DateValidation = true;
          if (CCValidateDateMask($this->DBValue, $this->DBFormat)) {
            $varResult = CCParseDate($this->DBValue, $this->DBFormat);
            if(!CCValidateDate($varResult)) {
              $DateValidation = false;
              $varResult = "";
            }
          } else {
            $DateValidation = false;
          }
          if (!$DateValidation)
          {
            if (is_array($this->DBFormat)) {
              $FormatString = join("", $this->DBFormat);
              $this->Errors->addError("The value in field " . $this->Name . " is not valid. Use the following format: " . $FormatString . ".");
            } else {
              $this->Errors->addError("The value in field " . $this->Name . " is not valid.");
            }
          }
          break;
        case ccsBoolean:
          if (CCValidateBoolean($this->DBValue, $this->DBFormat)) {
            $varResult = CCParseBoolean($this->DBValue, $this->DBFormat);
          } else {
            if (is_array($this->DBFormat)) {
              $FormatString = CCGetBooleanFormat($this->DBFormat);
              $this->Errors->addError("The value in field " . $this->Name . " is not valid. Use the following format: " . $FormatString . ".");
            } else {
              $this->Errors->addError("The value in field " . $this->Name . " is not valid.");
            }
          }
          break;
        case ccsInteger:
          if (CCValidateNumber($this->DBValue, $this->DBFormat))
            $varResult = CCParseInteger($this->DBValue, $this->DBFormat);
          else 
            $this->Errors->addError("The value in field " . $this->Name . " is not valid.");
          break;
        case ccsFloat:
          if (CCValidateNumber($this->DBValue, $this->DBFormat) )
            $varResult = CCParseFloat($this->DBValue, $this->DBFormat);
          else 
            $this->Errors->addError("The value in field " . $this->Name . " is not valid.");
          break;
        case ccsText:
        case ccsMemo:
          $varResult = strval($this->DBValue);
          break;
      }
    }

    return $varResult;
  }

  function GetFormattedValue()
  {
    $strResult = "";
    switch($this->DataType)
    {
      case ccsDate:
        $strResult = CCFormatDate($this->Value, $this->DBFormat);
        break;
      case ccsBoolean:
        $strResult = CCFormatBoolean($this->Value, $this->DBFormat);
        break;
      case ccsInteger:
      case  ccsFloat:
        $strResult = CCFormatNumber($this->Value, $this->DBFormat);
        break;
      case ccsText:
      case ccsMemo:
        $strResult = strval($this->Value);
        break;
    }
    return $strResult;
  }

  function SetDBValue($DBValue)
  {
    $this->DBValue = $DBValue;
    $this->Value = $this->GetParsedValue();
  }

  function SetValue($Value)
  {
    $this->Value = $Value;
    $this->DBValue = $this->GetFormattedValue();
  }

  function GetValue()
  {
    return $this->Value;
  }

  function GetDBValue()
  {
    return $this->DBValue;
  }
}

//End clsField Class

//clsButton Class @0-4C1E7C58
class clsButton
{
  var $Name;
  var $Visible;

  var $CCSEvents = "";
  var $CCSEventResult;

  function clsButton($Name)
  {
    $this->Name = $Name;
    $this->Visible = true;
  }

  function Show($RowNumber = "")
  {
    global $Tpl;
    $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow");
    if($this->Visible)
    {
      $ControlName = ($RowNumber === "") ? $this->Name : $this->Name . "_" . $RowNumber;
      $Tpl->SetVar("Button_Name", $ControlName);
      $Tpl->Parse("Button " . $this->Name, false);
    }
    else
    {
      $Tpl->setblockvar("Button " . $this->Name, "");
    }
  }

}

//End clsButton Class

//clsFileUpload Class @0-178CC9D1
class clsFileUpload
{
  var $Name;
  var $Caption;
  var $Visible;
  var $Required;

  var $TemporaryFolder;
  var $FileFolder;
  var $AllowedMask; // @deprecated , use AllowedFileMasks property
  var $AllowedFileMasks;
  var $DisallowedFileMasks;
  var $FileSizeLimit;
  var $Value;
  var $Text;
  var $State;

  var $CCSEvents = "";
  var $CCSEventResult;

  function clsFileUpload($Name, $Caption, $TemporaryFolder, $FileFolder, $AllowedFileMasks, $DisallowedFileMasks, $FileSizeLimit)
  {

    $this->Errors = new clsErrors;

    $this->Name            = $Name;
    $this->Visible         = true;
    $this->Caption         = $Caption;
    if(substr($TemporaryFolder, 0, 1) == "%") {
      $TemporaryFolder = substr($TemporaryFolder, 1);
        $TemporaryFolder = isset($_ENV[$TemporaryFolder]) ? $_ENV[$TemporaryFolder] : "";
    }
    $this->TemporaryFolder = $TemporaryFolder;
    if(substr($FileFolder, 0, 1) == "%") {
      $FileFolder = substr($FileFolder, 1);
        $FileFolder = isset($_ENV[$FileFolder]) ? $_ENV[$FileFolder] : "";
    }
    $this->FileFolder          = $FileFolder;
    $this->AllowedFileMasks    = $AllowedFileMasks;
    $this->AllowedMask         = & $this->AllowedFileMasks; 
    $this->DisallowedFileMasks = $DisallowedFileMasks;
    $this->FileSizeLimit       = $FileSizeLimit;
    $this->Value               = "";
    $this->Text                = "";
    $this->Required            = false;

    $FileName = "";
    $FieldName = $this->Caption;
    if( !is_dir($TemporaryFolder) ) {
      $this->Errors->addError("Unable to upload the file specified in " . $FieldName . " - temporary upload folder doesn't exist.");
    } else if( !is_writable($TemporaryFolder) ) {
      $this->Errors->addError("Insufficient filesystem permissions to upload the file specified in " . $FieldName . " into temporary folder.");
    } else if( !is_dir($FileFolder) ) {
      $this->Errors->addError("Unable to upload the file specified in " . $FieldName . " - upload folder doesn't exist.");
    } else if( !is_writable($FileFolder) ) {
      $this->Errors->addError("Insufficient filesystem permissions to upload the file specified in " . $FieldName . ".");
    } 

  }

  function Validate()
  {
    $validation = true;
    if($this->Required && $this->Value === "" && $this->Errors->Count() == 0)
    {
      $FieldName = $this->Caption;
      $this->Errors->addError("The file attachment in field " . $FieldName . " is required.");
    }
    $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate");
    return ($this->Errors->Count() == 0);
  }


  function Upload($RowNumber = "")
  {
    global $HTTP_POST_FILES;
    $FieldName = $this->Caption;
    if(strlen($RowNumber)) {
      $ControlName = $this->Name . "_" . $RowNumber;
      $FileControl = $this->Name . "_File_" . $RowNumber;
      $DeleteControl = $this->Name . "_Delete_" . $RowNumber;
    } else {
      $ControlName = $this->Name;
      $FileControl = $this->Name . "_File";
      $DeleteControl = $this->Name . "_Delete";
    }

    $SessionName = CCGetParam($ControlName);
    $this->State = CCGetSession($SessionName);

    if (strlen(CCGetParam($DeleteControl))) { 
      // delete file from folder
      $ActualFileName = $this->State[0];
      if( file_exists($this->FileFolder . $ActualFileName) ) {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeDeleteFile");
        unlink($this->FileFolder . $ActualFileName);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterDeleteFile");
      } else if ( file_exists($this->TemporaryFolder . $ActualFileName) ) {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeDeleteFile");
        unlink($this->TemporaryFolder . $ActualFileName);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterDeleteFile");
      }
      $this->Value = ""; $this->Text = "";
      $this->State[0] = "";
    } else if (isset ($_FILES[$FileControl]) 
        && $_FILES[$FileControl]["tmp_name"] != "none" 
        && strlen ($_FILES[$FileControl]["tmp_name"])) {
      $this->Value = ""; $this->Text = "";
      $FileName = $_FILES[$FileControl]["name"];
      $GoodFileMask = 1;
      $meta_characters = array("*" => ".+", "?" => ".", "\\" => "\\\\", "^" => "\\^", "\$" => "\\\$", "." => "\\.", "[" => "\\[", "]" => "\\]", "|" => "\\|", "(" => "\\(", ")" => "\\)", "{" => "\\{", "}" => "\\}", "+" => "\\+", "-" => "\\-");
      if ($this->AllowedFileMasks != "") {
        $GoodFileMask = 0;
        $FileMasks=split(';', $this->AllowedFileMasks);
        foreach ($FileMasks as $FileMask) {
          $FileMask = preg_replace("/(\\*|\\?|\\\\|\\^|\\\$|\\.|\\[|\\]|\\||\\(|\\)|\\{|\\}|\\+|\\-)/ei", "\$meta_characters['\\1']", $FileMask);
          if (preg_match("/^$FileMask$/i", $FileName)) {
            $GoodFileMask = 1;
            break;
          }
        }
      }
      if ($GoodFileMask && $this->DisallowedFileMasks != "") {
        $FileMasks=split(';', $this->DisallowedFileMasks);
        foreach ($FileMasks as $FileMask) {
          $FileMask = preg_replace("/(\\*|\\?|\\\\|\\^|\\\$|\\.|\\[|\\]|\\||\\(|\\)|\\{|\\}|\\+|\\-)/ei", "\$meta_characters['\\1']", $FileMask);
          if (preg_match("/^$FileMask$/i", $FileName)) {
            $GoodFileMask = 0;
            break;
          }
        }
      }


      if($_FILES[$FileControl]["size"] > $this->FileSizeLimit) {
        $this->Errors->addError("The file size in field " . $FieldName . " is too large.");
      } else if (!$GoodFileMask) {
        $this->Errors->addError("The file type specified in field " . $FieldName . " is not allowed.");
      } else {
        // move uploaded file to temporary folder
        $file_exists = true;
        $index = 0;
        while($file_exists) {
          $ActualFileName = date("YmdHis") . $index . "." . $FileName;
          $file_exists = file_exists($ActualFileName);
          $index++;
        }
        if( copy($_FILES[$FileControl]["tmp_name"], $this->TemporaryFolder . $ActualFileName) ) {
          $this->Value = $ActualFileName;
          $this->Text = $ActualFileName;
          if(strlen($this->State[0])) {
            if(file_exists($this->TemporaryFolder . $this->State[0])) {
              $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeDeleteFile");
              unlink($this->TemporaryFolder . $this->State[0]);
              $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterDeleteFile");
              $this->State[0] = $ActualFileName;
            } else {
              if(!is_dir($this->TemporaryFolder . $this->State[1]) && file_exists($this->TemporaryFolder . $this->State[1])) {
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeDeleteFile");
                unlink($this->TemporaryFolder . $this->State[1]);
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterDeleteFile");
              }
              $this->State[1] = $ActualFileName;
            }
          } else {
            $this->State[0] = $ActualFileName;
          }
        } else {
          $this->Errors->addError("Insufficient filesystem permissions to upload the file specified in " . $FieldName . " into temporary folder.");
        }
      }
    } else {
      $this->SetValue(strlen($this->State[1]) ? $this->State[1] : $this->State[0]);
    }
  }

  function Move()
  {
    if (strlen($this->Value) && !file_exists($this->FileFolder . $this->Value)) {
      $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeProcessFile");
      $FileName = $this->GetFileName();
      $FieldName = $this->Caption;
      if (!file_exists($this->TemporaryFolder . $this->Value)) {
        $this->Errors->addError("The file " . $FileName . " specified in " . $FieldName . " was not found.");
      } else if (!@copy($this->TemporaryFolder . $this->Value, $this->FileFolder . $this->Value)) {
        $this->Errors->addError("Insufficient filesystem permissions to upload the file specified in " . $FieldName . ".");
      } else if (strlen($this->State[1])) {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeDeleteFile");
        unlink($this->FileFolder . $this->State[0]);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterDeleteFile");
      }
      if($this->Errors->Count() == 0 && file_exists($this->TemporaryFolder . $this->Value)) {
        unlink($this->TemporaryFolder . $this->Value);
      }
      $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterProcessFile");
    }
  }

  function Delete()
  {
    if( !is_dir($this->FileFolder . $this->State[0]) && file_exists($this->FileFolder . $this->State[0]) ) {
      $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeDeleteFile");
      unlink($this->FileFolder . $this->State[0]);
      $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterDeleteFile");
    } else if ( !is_dir($this->TemporaryFolder . $this->State[0]) && file_exists($this->TemporaryFolder . $this->State[0]) ) {
      $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeDeleteFile");
      unlink($this->TemporaryFolder . $this->State[0]);
      $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterDeleteFile");
    }
    if( !is_dir($this->FileFolder . $this->State[1]) && file_exists($this->FileFolder . $this->State[1]) ) {
      $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeDeleteFile");
      unlink($this->FileFolder . $this->State[1]);
      $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterDeleteFile");
    } else if ( !is_dir($this->TemporaryFolder . $this->State[1]) && file_exists($this->TemporaryFolder . $this->State[1]) ) {
      $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeDeleteFile");
      unlink($this->TemporaryFolder . $this->State[1]);
      $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterDeleteFile");
    }
  }

  function Show($RowNumber = "")
  {
    global $Tpl;
    if($this->Visible)
    {
      $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow");

      if(!$this->Visible) {
        $Tpl->setblockvar("FileUpload " . $this->Name, "");
        return;
      }

      if(strlen($RowNumber)) {
        $ControlName = $this->Name . "_" . $RowNumber;
        $FileControl = $this->Name . "_File_" . $RowNumber;
        $DeleteControl = $this->Name . "_Delete_" . $RowNumber;
      } else {
        $ControlName = $this->Name;
        $FileControl = $this->Name . "_File";
        $DeleteControl = $this->Name . "_Delete";
      }

      $SessionName = CCGetParam($ControlName);
      if(!strlen($SessionName)) {
        srand ((double) microtime() * 1000000);
        $random_value = rand();
        $SessionName = "FileUpload" . $random_value . date("dHis");
        $this->State = array($this->Value, "");
      } 

      CCSetSession($SessionName, $this->State);

      $Tpl->SetVar("State", $SessionName);
      $Tpl->SetVar("ControlName", $ControlName);
      $Tpl->SetVar("FileControl", $FileControl);
      $Tpl->SetVar("DeleteControl", $DeleteControl);
      if (strlen($this->Value) ) {
        $Tpl->SetVar("ActualFileName", $this->Value);
        $Tpl->SetVar("FileName", $this->GetFileName());
        $Tpl->SetVar("FileSize", $this->GetFileSize());
        $Tpl->parse("FileUpload " . $this->Name . "/Info", false);
        if($this->Required) {
          $Tpl->parse("FileUpload " . $this->Name . "/Upload", false);
          $Tpl->setblockvar("FileUpload " . $this->Name . "/DeleteControl", "");
        } else {
          $Tpl->setblockvar("FileUpload " . $this->Name . "/Upload", "");
          $Tpl->parse("FileUpload " . $this->Name . "/DeleteControl", false);
        }
      } else {
        $Tpl->parse("FileUpload " . $this->Name . "/Upload", false);
        $Tpl->setblockvar("FileUpload " . $this->Name . "/Info", "");
        $Tpl->setblockvar("FileUpload " . $this->Name . "/DeleteControl", "");
      }

      $Tpl->Parse("FileUpload " . $this->Name, false);
    }
    else
    {
      $Tpl->setblockvar("FileUpload " . $this->Name, "");
    }
  }

  function SetValue($Value) {
    $this->Text = $Value;
    $this->Value = $Value;
    $this->State[0] = $Value;
    if(strlen($Value) 
      && !file_exists($this->TemporaryFolder . $Value) 
      && !file_exists($this->FileFolder . $Value)) {
        $FileName = $this->GetFileName();
        $FieldName = $this->Caption;
        $this->Errors->addError("The file " . $FileName . " specified in " . $FieldName . " was not found.");
    }
  }

  function SetText($Text) {
    $this->SetValue($Text);
  }

  function GetValue() {
    return $this->Value;
  }

  function GetText() {
    return $this->Text;
  }

  function GetFileName() {
    return preg_match("/^\d{14,}\./", $this->Value) ? substr($this->Value, strpos($this->Value, ".") + 1) : $this->Value;
  }

  function GetFileSize() {
    $filesize = 0;
    if( file_exists($this->FileFolder . $this->Value) ) {
      $filesize = filesize($this->FileFolder . $this->Value);
    } else if ( file_exists($this->TemporaryFolder . $this->Value) ) {
      $filesize = filesize($this->TemporaryFolder . $this->Value);
    }
    return $filesize;
  }

}

//End clsFileUpload Class

//clsDatePicker Class @0-DFF66198
class clsDatePicker
{
  var $Name;
  var $DateFormat;
  var $Style;
  var $FormName;
  var $ControlName;
  var $Visible;
  var $Errors;

  var $CCSEvents = "";
  var $CCSEventResult;

  function clsDatePicker($Name, $FormName, $ControlName)
  {
    $this->Name        = $Name;
    $this->FormName    = $FormName;
    $this->ControlName = $ControlName;
    $this->Visible     = true;

    $this->Errors = new clsErrors;
  }

  function Show($RowNumber = "")
  {
    global $Tpl;
    if($this->Visible)
    {
      $ControlName = ($RowNumber === "") ? $this->ControlName : $this->ControlName . "_" . $RowNumber;
      $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow");
      $Tpl->SetVar("Name",        $this->FormName . "_" . $this->Name);
      $Tpl->SetVar("FormName",    $this->FormName);
      $Tpl->SetVar("DateControl", $ControlName);

      $Tpl->Parse("DatePicker " . $this->Name, false);
    }
    else
    {
      $Tpl->setblockvar("DatePicker " . $this->Name, "");
    }
  }

}

//End clsDatePicker Class

//clsErrors Class @0-32F63B82
class clsErrors
{
  var $Errors;
  var $ErrorsCount;
  var $ErrorDelimiter;

  function clsErrors()
  {
    $this->Errors = array();
    $this->ErrorsCount = 0;
    $this->ErrorDelimiter = "<br>";
  }

  function addError($Description)
  {
    if (strlen($Description))
    {
      $this->Errors[$this->ErrorsCount] = $Description; 
      $this->ErrorsCount++;
    }
  }

  function AddErrors($Errors)
  {
    for($i = 0; $i < $Errors->Count(); $i++)
      $this->addError($Errors[$i]);
  }

  function Clear()
  {
    $this->Errors = array();
    $this->ErrorsCount = 0;
  }

  function Count()
  {
    return $this->ErrorsCount;
  }

  function ToString()
  {

    if(sizeof($this->Errors) > 0)
      return join($this->ErrorDelimiter, $this->Errors) . $this->ErrorDelimiter;
    else
      return "";
  }

}
//End clsErrors Class


?>
