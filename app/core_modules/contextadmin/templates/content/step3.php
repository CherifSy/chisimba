<?php

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('label', 'htmlelements');


$objIcon = $this->newObject('geticon', 'htmlelements');
$objIcon->setIcon('loader');

$formAction = 'savestep3';
$headerTitle = $contextTitle.' - '.ucwords($this->objLanguage->code2Txt('mod_context_contextplugins', 'context', NULL, '[-context-] Plugins'));
$formButton = ' '.$this->objLanguage->languageText('mod_contextadmin_next', 'contextadmin', 'Next').' ';


$objStepMenu = $this->newObject('stepmenu', 'navigation');
if ($mode == 'edit') {
    $objStepMenu->addStep(str_replace('[-num-]', 1, $this->objLanguage->code2Txt('mod_contextadmin_stepnumber', 'contextadmin', NULL, 'Step [-num-]')).' - '.ucwords($this->objLanguage->code2Txt('mod_context_contextsettings', 'context', NULL, '[-context-] Settings')), ucwords($this->objLanguage->code2Txt('mod_contextadmin_updatecontextitlesettings', 'contextadmin', NULL, 'Update [-context-] Title and Settings')));
} else {
    $objStepMenu->addStep(str_replace('[-num-]', 1, $this->objLanguage->code2Txt('mod_contextadmin_stepnumber', 'contextadmin', NULL, 'Step [-num-]')).' - '.ucwords($this->objLanguage->code2Txt('mod_context_contextsettings', 'context', NULL, '[-context-] Settings')), $this->objLanguage->code2Txt('mod_contextadmin_checkcontextcodeavailable', 'contextadmin', NULL, 'Enter [-context-] settings and check whether [-context-] code is available'));
}
$objStepMenu->addStep(str_replace('[-num-]', 2, $this->objLanguage->code2Txt('mod_contextadmin_stepnumber', 'contextadmin', NULL, 'Step [-num-]')).' - '.ucwords($this->objLanguage->code2Txt('mod_contextadmin_contextinformation', 'contextadmin', NULL, '[-context-] Information')), $this->objLanguage->code2Txt('mod_contextadmin_enterinfoaboutcontext', 'contextadmin', NULL, 'Enter more information about your [-context-] and select a [-context-] image'));
$objStepMenu->addStep(str_replace('[-num-]', 3, $this->objLanguage->code2Txt('mod_contextadmin_stepnumber', 'contextadmin', NULL, 'Step [-num-]')).' - '.ucwords($this->objLanguage->code2Txt('mod_context_contextplugins', 'context', NULL, '[-context-] Plugins')), $this->objLanguage->code2Txt('mod_contextadmin_selectpluginsforcontext', 'contextadmin', NULL, 'Select the plugins you would like to use in this [-context-]'));

$objStepMenu->current = 3;
echo $objStepMenu->show();


$header = new htmlheading();
$header->type = 1;
$header->str = $headerTitle;

echo '<br />'.$header->show();

echo '<p>'.$this->objLanguage->code2Txt('mod_context_selectcontextplugins', 'context', NULL, 'Select the plugins you would like to use in your [-context-]').':</p>';

$form = new form('updateplugins', $this->uri(array('action'=>'savestep3')));

$table = $this->newObject('htmltable', 'htmlelements');

$objIcon = $this->newObject('geticon', 'htmlelements');

$counter = 0;

$newPlugins = array();

foreach ($plugins as $plugin)
{
    $newPlugins[$plugin['title']] = $plugin;
}

ksort($newPlugins);

foreach ($newPlugins as $plugin)
{
    $counter++;
    
    $checkbox = new checkbox('plugins[]');
    $checkbox->setValue($plugin['module_id']);
    $checkbox->setId('module_'.$plugin['module_id']);
    
    if (in_array($plugin['module_id'], $contextModules)) {
        $checkbox->setChecked(TRUE);
    }
    
    $objIcon->setModuleIcon($plugin['module_id']);
    
    if ($counter%2 == 1) {
        $table->startRow();
    }
    $table->addCell($checkbox->show(), 20);
    
    $label = new label ($objIcon->show(), 'module_'.$plugin['module_id']);
    
    $table->addCell($label->show(), 30);
    
    $label = new label ('<strong>'.$plugin['title'].'</strong><br />'.$plugin['description'], 'module_'.$plugin['module_id']);
    $table->addCell($label->show().'<br /><br />');
    
    if ($counter%2 == 2) {
        $table->endRow();
    }
}

if ($counter%2 == 1) {
    $table->addCell('&nbsp;');
    $table->addCell('&nbsp;');
    $table->addCell('&nbsp;');
    $table->endRow();
}

$form->addToForm($table->show());

$button =  new button ('submitform', $this->objLanguage->languageText('mod_context_saveplugins', 'context', 'Save Plugins'));
$button->setToSubmit();

$form->addToForm($button->show());

$hiddenInput = new hiddeninput('mode', $mode);
$form->addToForm($hiddenInput->show());

$hiddenInput = new hiddeninput('contextCode', $contextCode);
$form->addToForm($hiddenInput->show());

echo $form->show();

?>