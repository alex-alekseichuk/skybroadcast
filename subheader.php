<?php
class clssubheader { //subheader class @1-323C7B98

//Variables @1-D089C8BE
    var $FileName = "";
    var $Redirect = "";
    var $Tpl = "";
    var $TemplateFileName = "";
    var $BlockToParse = "";
    var $ComponentName = "";

    // Events;
    var $CCSEvents = "";
    var $CCSEventResult = "";
    var $RelativePath;
    var $Visible;
//End Variables

//Class_Initialize Event @1-C9FEE82E
    function clssubheader($RelativePath)
    {
        $this->RelativePath = $RelativePath;
        $this->Visible = true;
        $this->FileName = "subheader.php";
        $this->Redirect = "";
        $this->TemplateFileName = "subheader.html";
        $this->BlockToParse = "main";
        $this->TemplateEncoding = "";
        if($this->Visible)
        {
        }
    }
//End Class_Initialize Event

//Class_Terminate Event @1-A3749DF6
    function Class_Terminate()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeUnload");
    }
//End Class_Terminate Event

//BindEvents Method @1-236CCD5D
    function BindEvents()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterInitialize");
    }
//End BindEvents Method

//Operations Method @1-7E2A14CF
    function Operations()
    {
        global $Redirect;
        if(!$this->Visible)
            return "";
    }
//End Operations Method

//Initialize Method @1-EDD74DD5
    function Initialize()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnInitializeView");
        if(!$this->Visible)
            return "";
    }
//End Initialize Method

//Show Method @1-47CC16FB
    function Show($Name)
    {
        global $Tpl;
        $block_path = $Tpl->block_path;
        $Tpl->LoadTemplate("/" . $this->TemplateFileName, $Name, $this->TemplateEncoding);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow");
        if(!$this->Visible)
            return "";
        $Tpl->block_path = $Tpl->block_path . "/" . $Name;
        $Tpl->Parse();
        $Tpl->block_path = $block_path;
        $Tpl->SetVar($Name, $Tpl->GetVar($Name));
    }
//End Show Method

} //End subheader Class @1-FCB6E20C


?>
