<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<form id="jtable-create-form" class="jtable-dialog-form jtable-create-form" enctype="multipart/form-data" method="POST" action="../scripts/update_document.php?action=create&source=materiel&id_materiel=5129" target="_blank">
<input id="Edit-id_source" type="hidden" name="id_source" value="5129">
<input id="Edit-source" type="hidden" name="source" value="materiel">
<div class="jtable-input-field-container">
<div class="jtable-input-label">Fichier</div>
<div class="jtable-input jtable-text-input">
<input id="Edit-fic" type="file" name="fic" style="height: 25px;">
</div>
</div>
<div class="jtable-input-field-container">
<div class="jtable-input-label">Description</div>
<div class="jtable-input jtable-textarea-input">
<textarea id="Edit-description" class="" name="description"></textarea>
</div>
</div>
<div class="jtable-input-field-container">
<div class="jtable-input-label">Accessible à</div>
<div class="jtable-input jtable-dropdown-input">
<select id="Edit-acces" class="" name="acces">
<option value="0">Tout le monde</option>
<option value="1">A mon groupe</option>
<option value="2">Moi uniquement</option>
<option value="3">Tous sauf entité</option>
</select>
</div>
</div>
<div class="jtable-input-field-container">
<div class="jtable-input-label">Fin de validité</div>
<div class="jtable-input jtable-date-input">
<input id="Edit-dateFin" class="validate[custom[datefr]] hasDatepicker" type="text" name="dateFin">
</div>
</div>
<button id="AddRecordDialogSaveButton" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="button" role="button" aria-disabled="false">
<span class="ui-button-text">Enregister</span>
</button>
<input type="submit" value="go">
</form>
