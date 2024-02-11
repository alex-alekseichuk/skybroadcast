<?php
//Include Common Files @1-5471E0F2
define("RelativePath", ".");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
  
//End Include Common Files

//Include Page implementation @11-294DAD93
include_once(RelativePath . "/menu.php");
//End Include Page implementation

class clsRecordservices { //services Class @2-805AFD35

//Variables @2-B2F7A83E

    // Public variables
    var $ComponentName;
    var $HTMLFormAction;
    var $PressedButton;
    var $Errors;
    var $ErrorBlock;
    var $FormSubmitted;
    var $FormEnctype;
    var $Visible;
    var $Recordset;

    var $CCSEvents = "";
    var $CCSEventResult;

    var $InsertAllowed = false;
    var $UpdateAllowed = false;
    var $DeleteAllowed = false;
    var $ReadAllowed   = false;
    var $EditMode      = false;
    var $ds;
    var $ValidatingControls;
    var $Controls;

    // Class variables
//End Variables

//Class_Initialize Event @2-68FE443B
    function clsRecordservices()
    {

        global $FileName;
        $this->Visible = true;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Record services/Error";
        $this->ds = new clsservicesDataSource();
        $this->InsertAllowed = true;
        $this->UpdateAllowed = true;
        $this->DeleteAllowed = true;
        $this->ReadAllowed = true;
        if($this->Visible)
        {
            $this->ComponentName = "services";
            $CCSForm = split(":", CCGetFromGet("ccsForm", ""), 2);
            if(sizeof($CCSForm) == 1)
                $CCSForm[1] = "";
            list($FormName, $FormMethod) = $CCSForm;
            $this->EditMode = ($FormMethod == "Edit");
            $this->FormEnctype = "application/x-www-form-urlencoded";
            $this->FormSubmitted = ($FormName == $this->ComponentName);
            $Method = $this->FormSubmitted ? ccsPost : ccsGet;
            $this->service_header = new clsControl(ccsTextBox, "service_header", "Header", ccsText, "", CCGetRequestParam("service_header", $Method));
            $this->service_desc = new clsControl(ccsTextArea, "service_desc", "Desc", ccsText, "", CCGetRequestParam("service_desc", $Method));
            $this->service_price = new clsControl(ccsTextBox, "service_price", "Price", ccsFloat, "", CCGetRequestParam("service_price", $Method));
            $this->service_price->Required = true;
            $this->service_speed = new clsControl(ccsTextBox, "service_speed", "Speed", ccsFloat, "", CCGetRequestParam("service_speed", $Method));
            $this->service_speed->Required = true;
            $this->Button_Insert = new clsButton("Button_Insert");
            $this->Button_Update = new clsButton("Button_Update");
            $this->Button_Delete = new clsButton("Button_Delete");
        }
    }
//End Class_Initialize Event

//Initialize Method @2-6ACA0FB8
    function Initialize()
    {

        if(!$this->Visible)
            return;

        $this->ds->Parameters["urlservice_id"] = CCGetFromGet("service_id", "");
    }
//End Initialize Method

//Validate Method @2-659F8753
    function Validate()
    {
        $Validation = true;
        $Where = "";
        $Validation = ($this->service_header->Validate() && $Validation);
        $Validation = ($this->service_desc->Validate() && $Validation);
        $Validation = ($this->service_price->Validate() && $Validation);
        $Validation = ($this->service_speed->Validate() && $Validation);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate");
        $Validation =  $Validation && ($this->service_header->Errors->Count() == 0);
        $Validation =  $Validation && ($this->service_desc->Errors->Count() == 0);
        $Validation =  $Validation && ($this->service_price->Errors->Count() == 0);
        $Validation =  $Validation && ($this->service_speed->Errors->Count() == 0);
        return (($this->Errors->Count() == 0) && $Validation);
    }
//End Validate Method

//CheckErrors Method @2-B023F78A
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->service_header->Errors->Count());
        $errors = ($errors || $this->service_desc->Errors->Count());
        $errors = ($errors || $this->service_price->Errors->Count());
        $errors = ($errors || $this->service_speed->Errors->Count());
        $errors = ($errors || $this->Errors->Count());
        $errors = ($errors || $this->ds->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//Operation Method @2-B50387A0
    function Operation()
    {
        if(!$this->Visible)
            return;

        global $Redirect;
        global $FileName;

        $this->ds->Prepare();
        if(!$this->FormSubmitted) {
            $this->EditMode = $this->ds->AllParametersSet;
            return;
        }

        if($this->FormSubmitted) {
            $this->PressedButton = $this->EditMode ? "Button_Update" : "Button_Insert";
            if(strlen(CCGetParam("Button_Insert", ""))) {
                $this->PressedButton = "Button_Insert";
            } else if(strlen(CCGetParam("Button_Update", ""))) {
                $this->PressedButton = "Button_Update";
            } else if(strlen(CCGetParam("Button_Delete", ""))) {
                $this->PressedButton = "Button_Delete";
            }
        }
        $Redirect = "services_list.php" . "?" . CCGetQueryString("QueryString", Array("ccsForm"));
        if($this->PressedButton == "Button_Delete") {
            if(!CCGetEvent($this->Button_Delete->CCSEvents, "OnClick") || !$this->DeleteRow()) {
                $Redirect = "";
            }
        } else if($this->Validate()) {
            if($this->PressedButton == "Button_Insert") {
                if(!CCGetEvent($this->Button_Insert->CCSEvents, "OnClick") || !$this->InsertRow()) {
                    $Redirect = "";
                }
            } else if($this->PressedButton == "Button_Update") {
                if(!CCGetEvent($this->Button_Update->CCSEvents, "OnClick") || !$this->UpdateRow()) {
                    $Redirect = "";
                }
            }
        } else {
            $Redirect = "";
        }
    }
//End Operation Method

//InsertRow Method @2-5D73687D
    function InsertRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeInsert");
        if(!$this->InsertAllowed) return false;
        $this->ds->service_header->SetValue($this->service_header->GetValue());
        $this->ds->service_desc->SetValue($this->service_desc->GetValue());
        $this->ds->service_price->SetValue($this->service_price->GetValue());
        $this->ds->service_speed->SetValue($this->service_speed->GetValue());
        $this->ds->Insert();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterInsert");
        return (!$this->CheckErrors());
    }
//End InsertRow Method

//UpdateRow Method @2-F58F9A46
    function UpdateRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeUpdate");
        if(!$this->UpdateAllowed) return false;
        $this->ds->service_header->SetValue($this->service_header->GetValue());
        $this->ds->service_desc->SetValue($this->service_desc->GetValue());
        $this->ds->service_price->SetValue($this->service_price->GetValue());
        $this->ds->service_speed->SetValue($this->service_speed->GetValue());
        $this->ds->Update();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterUpdate");
        return (!$this->CheckErrors());
    }
//End UpdateRow Method

//DeleteRow Method @2-91867A4A
    function DeleteRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeDelete");
        if(!$this->DeleteAllowed) return false;
        $this->ds->Delete();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterDelete");
        return (!$this->CheckErrors());
    }
//End DeleteRow Method

//Show Method @2-F13D0EB9
    function Show()
    {
        global $Tpl;
        global $FileName;
        $Error = "";

        if(!$this->Visible)
            return;

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSelect");


        $RecordBlock = "Record " . $this->ComponentName;
        $ParentPath = $Tpl->block_path;
        $Tpl->block_path = $ParentPath . "/" . $RecordBlock;
        $this->EditMode = $this->EditMode && $this->ReadAllowed;
        if($this->EditMode)
        {
            $this->ds->open();
            if($this->Errors->Count() == 0)
            {
                if($this->ds->Errors->Count() > 0)
                {
                    echo "Error in Record services";
                }
                else if($this->ds->next_record())
                {
                    $this->ds->SetValues();
                    if(!$this->FormSubmitted)
                    {
                        $this->service_header->SetValue($this->ds->service_header->GetValue());
                        $this->service_desc->SetValue($this->ds->service_desc->GetValue());
                        $this->service_price->SetValue($this->ds->service_price->GetValue());
                        $this->service_speed->SetValue($this->ds->service_speed->GetValue());
                    }
                }
                else
                {
                    $this->EditMode = false;
                }
            }
        }
        if(!$this->FormSubmitted)
        {
        }

        if($this->FormSubmitted || $this->CheckErrors()) {
            $Error .= $this->service_header->Errors->ToString();
            $Error .= $this->service_desc->Errors->ToString();
            $Error .= $this->service_price->Errors->ToString();
            $Error .= $this->service_speed->Errors->ToString();
            $Error .= $this->Errors->ToString();
            $Error .= $this->ds->Errors->ToString();
            $Tpl->SetVar("Error", $Error);
            $Tpl->Parse("Error", false);
        }
        $CCSForm = $this->EditMode ? $this->ComponentName . ":" . "Edit" : $this->ComponentName;
        $this->HTMLFormAction = $FileName . "?" . CCAddParam(CCGetQueryString("QueryString", ""), "ccsForm", $CCSForm);
        $Tpl->SetVar("Action", $this->HTMLFormAction);
        $Tpl->SetVar("HTMLFormName", $this->ComponentName);
        $Tpl->SetVar("HTMLFormEnctype", $this->FormEnctype);
        $this->Button_Insert->Visible = !$this->EditMode && $this->InsertAllowed;
        $this->Button_Update->Visible = $this->EditMode && $this->UpdateAllowed;
        $this->Button_Delete->Visible = $this->EditMode && $this->DeleteAllowed;

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow");
        if(!$this->Visible) {
            $Tpl->block_path = $ParentPath;
            return;
        }

        $this->service_header->Show();
        $this->service_desc->Show();
        $this->service_price->Show();
        $this->service_speed->Show();
        $this->Button_Insert->Show();
        $this->Button_Update->Show();
        $this->Button_Delete->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->ds->close();
    }
//End Show Method

} //End services Class @2-FCB6E20C

class clsservicesDataSource extends clsDBskybroadcast {  //servicesDataSource Class @2-CF0139A5

//DataSource Variables @2-0E81E1FF
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $InsertParameters;
    var $UpdateParameters;
    var $DeleteParameters;
    var $wp;
    var $AllParametersSet;


    // Datasource fields
    var $service_header;
    var $service_desc;
    var $service_price;
    var $service_speed;
//End DataSource Variables

//DataSourceClass_Initialize Event @2-FEC26952
    function clsservicesDataSource()
    {
        $this->ErrorBlock = "Record services/Error";
        $this->Initialize();
        $this->service_header = new clsField("service_header", ccsText, "");
        $this->service_desc = new clsField("service_desc", ccsText, "");
        $this->service_price = new clsField("service_price", ccsFloat, "");
        $this->service_speed = new clsField("service_speed", ccsFloat, "");

    }
//End DataSourceClass_Initialize Event

//Prepare Method @2-BFD77763
    function Prepare()
    {
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urlservice_id", ccsInteger, "", "", $this->Parameters["urlservice_id"], "", false);
        $this->AllParametersSet = $this->wp->AllParamsSet();
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "service_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsInteger),false);
        $this->Where = 
             $this->wp->Criterion[1];
    }
//End Prepare Method

//Open Method @2-033DB4AB
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect");
        $this->SQL = "SELECT *  " .
        "FROM services";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect");
        $this->PageSize = 1;
        $this->query($this->OptimizeSQL(CCBuildSQL($this->SQL, $this->Where, $this->Order)));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect");
    }
//End Open Method

//SetValues Method @2-B63538B3
    function SetValues()
    {
        $this->service_header->SetDBValue($this->f("service_header_a"));
        $this->service_desc->SetDBValue($this->f("service_desc_a"));
        $this->service_price->SetDBValue(trim($this->f("service_price")));
        $this->service_speed->SetDBValue(trim($this->f("service_speed")));
    }
//End SetValues Method

//Insert Method @2-B1F9F708
    function Insert()
    {
        $this->CmdExecution = true;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildInsert");
        $this->SQL = "INSERT INTO services ("
             . "service_header_a, "
             . "service_desc_a, "
             . "service_price,"
             . "service_speed"
             . ") VALUES ("
             . $this->ToSQL($this->service_header->GetDBValue(), $this->service_header->DataType) . ", "
             . $this->ToSQL($this->service_desc->GetDBValue(), $this->service_desc->DataType) . ", "
             . $this->ToSQL($this->service_price->GetDBValue(), $this->service_price->DataType) . ", "
             . $this->ToSQL($this->service_speed->GetDBValue(), $this->service_speed->DataType)
             . ")";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteInsert");
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteInsert");
        }
        $this->close();
    }
//End Insert Method

//Update Method @2-4AD61686
    function Update()
    {
        $this->CmdExecution = true;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildUpdate");
        $this->SQL = "UPDATE services SET "
             . "service_header_a=" . $this->ToSQL($this->service_header->GetDBValue(), $this->service_header->DataType) . ", "
             . "service_desc_a=" . $this->ToSQL($this->service_desc->GetDBValue(), $this->service_desc->DataType) . ", "
             . "service_price=" . $this->ToSQL($this->service_price->GetDBValue(), $this->service_price->DataType) . ", "
             . "service_speed=" . $this->ToSQL($this->service_speed->GetDBValue(), $this->service_speed->DataType);
        $this->SQL = CCBuildSQL($this->SQL, $this->Where, "");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteUpdate");
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteUpdate");
        }
        $this->close();
    }
//End Update Method

//Delete Method @2-FF22260D
    function Delete()
    {
        $this->CmdExecution = true;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildDelete");
        $this->SQL = "DELETE FROM services";
        $this->SQL = CCBuildSQL($this->SQL, $this->Where, "");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteDelete");
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteDelete");
        }
        $this->close();
    }
//End Delete Method

} //End servicesDataSource Class @2-FCB6E20C

//Initialize Page @1-47ECFB6C
// Variables
$FileName = "";
$Redirect = "";
$Tpl = "";
$TemplateFileName = "";
$BlockToParse = "";
$ComponentName = "";

// Events;
$CCSEvents = "";
$CCSEventResult = "";

$FileName = "services_maint_ar.php";
$Redirect = "";
$TemplateFileName = "services_maint_ar.html";
$BlockToParse = "main";
$TemplateEncoding = "";
$FileEncoding = "";
$PathToRoot = "./";
//End Initialize Page

//Authenticate User @1-5C253EF0
CCSecurityRedirect("admin", "");
//End Authenticate User

//Initialize Objects @1-79290D92
$DBskybroadcast = new clsDBskybroadcast();

// Controls
$menu = new clsmenu("");
$menu->BindEvents();
$menu->Initialize();
$services = new clsRecordservices();
$services->Initialize();

$CCSEventResult = CCGetEvent($CCSEvents, "AfterInitialize");

$Charset = $Charset ? $Charset : $TemplateEncoding;
if ($Charset)
    header("Content-Type: text/html; charset=" . $Charset);
//End Initialize Objects

//Initialize HTML Template @1-BA4209C8
$CCSEventResult = CCGetEvent($CCSEvents, "OnInitializeView");
$Tpl = new clsTemplate($FileEncoding, $TemplateEncoding);
$Tpl->LoadTemplate(TemplatePath . $TemplateFileName, "main", $TemplateEncoding);
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeShow");
//End Initialize HTML Template

//Execute Components @1-1A7645E7
$menu->Operations();
$services->Operation();
//End Execute Components

//Go to destination page @1-DB7C248E
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload");
    $DBskybroadcast->close();
    header("Location: " . $Redirect);
    $menu->Class_Terminate();
    unset($menu);
    unset($services);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-9CD78F02
$menu->Show("menu");
$services->Show();
$Tpl->PParse("main", false);
//End Show Page

//Unload Page @1-25FDC5F3
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload");
$DBskybroadcast->close();
$menu->Class_Terminate();
unset($menu);
unset($services);
unset($Tpl);
//End Unload Page


?>
