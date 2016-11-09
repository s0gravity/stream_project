<?php
// --- GOLD MEDIA --- //

$document = '' . $_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['SCRIPT_NAME']);
if(substr($document, -1)=="/")
$document = '' . $_SERVER['DOCUMENT_ROOT'];

class GOLD_PLUGINS extends SkinFunctions {
    var $pluginPath='';
    var $pluginName='';
    public function setPluginPath($pluginName)
    {
		$this->pluginPath=$document.'/gold-app/gold-plugins/'.$pluginName.'/';
        $this->pluginName=$pluginName;
    }
    public function getPluginPath()
    {
        return $this->pluginPath;
    }
    public function display()
    {
        echo 'Plugins';
    }
    public function run($pluginName)// this function will be called by template function class to display widget
    {
		$this->setPluginPath($pluginName);
        $this->display();
    }
}
?>