
function scJQGeneralAdd() {
  scLoadScInput('input:text.sc-js-input');
  scLoadScInput('input:password.sc-js-input');
  scLoadScInput('input:checkbox.sc-js-input');
  scLoadScInput('input:radio.sc-js-input');
  scLoadScInput('select.sc-js-input');
  scLoadScInput('textarea.sc-js-input');

} // scJQGeneralAdd

function scFocusField(sField) {
  var $oField = $('#id_sc_field_' + sField);

  if (0 == $oField.length) {
    $oField = $('input[name=' + sField + ']');
  }

  if (0 == $oField.length && document.F1.elements[sField]) {
    $oField = $(document.F1.elements[sField]);
  }

  if ($("#id_ac_" + sField).length > 0) {
    if ($oField.hasClass("select2-hidden-accessible")) {
      if (false == scSetFocusOnField($oField)) {
        setTimeout(function() { scSetFocusOnField($oField); }, 500);
      }
    }
    else {
      if (false == scSetFocusOnField($oField)) {
        if (false == scSetFocusOnField($("#id_ac_" + sField))) {
          setTimeout(function() { scSetFocusOnField($("#id_ac_" + sField)); }, 500);
        }
      }
      else {
        setTimeout(function() { scSetFocusOnField($oField); }, 500);
      }
    }
  }
  else {
    setTimeout(function() { scSetFocusOnField($oField); }, 500);
  }
} // scFocusField

function scSetFocusOnField($oField) {
  if ($oField.length > 0 && $oField[0].offsetHeight > 0 && $oField[0].offsetWidth > 0 && !$oField[0].disabled) {
    $oField[0].focus();
    return true;
  }
  return false;
} // scSetFocusOnField

function scEventControl_init(iSeqRow) {
  scEventControl_data["login" + iSeqRow] = {"blur": false, "change": false, "autocomp": false, "original": "", "calculated": ""};
  scEventControl_data["pswd" + iSeqRow] = {"blur": false, "change": false, "autocomp": false, "original": "", "calculated": ""};
  scEventControl_data["confirm_pswd" + iSeqRow] = {"blur": false, "change": false, "autocomp": false, "original": "", "calculated": ""};
  scEventControl_data["name" + iSeqRow] = {"blur": false, "change": false, "autocomp": false, "original": "", "calculated": ""};
  scEventControl_data["email" + iSeqRow] = {"blur": false, "change": false, "autocomp": false, "original": "", "calculated": ""};
  scEventControl_data["active" + iSeqRow] = {"blur": false, "change": false, "autocomp": false, "original": "", "calculated": ""};
  scEventControl_data["groups" + iSeqRow] = {"blur": false, "change": false, "autocomp": false, "original": "", "calculated": ""};
  scEventControl_data["idyarda" + iSeqRow] = {"blur": false, "change": false, "autocomp": false, "original": "", "calculated": ""};
  scEventControl_data["idnaviera" + iSeqRow] = {"blur": false, "change": false, "autocomp": false, "original": "", "calculated": ""};
  scEventControl_data["idtipo_usuario" + iSeqRow] = {"blur": false, "change": false, "autocomp": false, "original": "", "calculated": ""};
}

function scEventControl_active(iSeqRow) {
  if (scEventControl_data["login" + iSeqRow]["blur"]) {
    return true;
  }
  if (scEventControl_data["login" + iSeqRow]["change"]) {
    return true;
  }
  if (scEventControl_data["pswd" + iSeqRow]["blur"]) {
    return true;
  }
  if (scEventControl_data["pswd" + iSeqRow]["change"]) {
    return true;
  }
  if (scEventControl_data["confirm_pswd" + iSeqRow]["blur"]) {
    return true;
  }
  if (scEventControl_data["confirm_pswd" + iSeqRow]["change"]) {
    return true;
  }
  if (scEventControl_data["name" + iSeqRow]["blur"]) {
    return true;
  }
  if (scEventControl_data["name" + iSeqRow]["change"]) {
    return true;
  }
  if (scEventControl_data["email" + iSeqRow]["blur"]) {
    return true;
  }
  if (scEventControl_data["email" + iSeqRow]["change"]) {
    return true;
  }
  if (scEventControl_data["active" + iSeqRow]["blur"]) {
    return true;
  }
  if (scEventControl_data["active" + iSeqRow]["change"]) {
    return true;
  }
  if (scEventControl_data["groups" + iSeqRow]["blur"]) {
    return true;
  }
  if (scEventControl_data["groups" + iSeqRow]["change"]) {
    return true;
  }
  if (scEventControl_data["idyarda" + iSeqRow]["blur"]) {
    return true;
  }
  if (scEventControl_data["idyarda" + iSeqRow]["change"]) {
    return true;
  }
  if (scEventControl_data["idnaviera" + iSeqRow]["blur"]) {
    return true;
  }
  if (scEventControl_data["idnaviera" + iSeqRow]["change"]) {
    return true;
  }
  if (scEventControl_data["idtipo_usuario" + iSeqRow]["blur"]) {
    return true;
  }
  if (scEventControl_data["idtipo_usuario" + iSeqRow]["change"]) {
    return true;
  }
  return false;
} // scEventControl_active

function scEventControl_onFocus(oField, iSeq) {
  var fieldId, fieldName;
  fieldId = $(oField).attr("id");
  fieldName = fieldId.substr(12);
  scEventControl_data[fieldName]["blur"] = true;
  if ("idyarda" + iSeq == fieldName) {
    scEventControl_data[fieldName]["blur"] = false;
  }
  if ("idnaviera" + iSeq == fieldName) {
    scEventControl_data[fieldName]["blur"] = false;
  }
  if ("idtipo_usuario" + iSeq == fieldName) {
    scEventControl_data[fieldName]["blur"] = false;
  }
  scEventControl_data[fieldName]["change"] = false;
} // scEventControl_onFocus

function scEventControl_onBlur(sFieldName) {
  scEventControl_data[sFieldName]["blur"] = false;
  if (scEventControl_data[sFieldName]["change"]) {
        if (scEventControl_data[sFieldName]["original"] == $("#id_sc_field_" + sFieldName).val() || scEventControl_data[sFieldName]["calculated"] == $("#id_sc_field_" + sFieldName).val()) {
          scEventControl_data[sFieldName]["change"] = false;
        }
  }
} // scEventControl_onBlur

function scEventControl_onChange(sFieldName) {
  scEventControl_data[sFieldName]["change"] = false;
} // scEventControl_onChange

function scEventControl_onAutocomp(sFieldName) {
  scEventControl_data[sFieldName]["autocomp"] = false;
} // scEventControl_onChange

var scEventControl_data = {};

function scJQEventsAdd(iSeqRow) {
  $('#id_sc_field_login' + iSeqRow).bind('blur', function() { sc_sec_form_edit_users_login_onblur(this, iSeqRow) })
                                   .bind('focus', function() { sc_sec_form_edit_users_login_onfocus(this, iSeqRow) });
  $('#id_sc_field_pswd' + iSeqRow).bind('blur', function() { sc_sec_form_edit_users_pswd_onblur(this, iSeqRow) })
                                  .bind('focus', function() { sc_sec_form_edit_users_pswd_onfocus(this, iSeqRow) });
  $('#id_sc_field_name' + iSeqRow).bind('blur', function() { sc_sec_form_edit_users_name_onblur(this, iSeqRow) })
                                  .bind('focus', function() { sc_sec_form_edit_users_name_onfocus(this, iSeqRow) });
  $('#id_sc_field_email' + iSeqRow).bind('blur', function() { sc_sec_form_edit_users_email_onblur(this, iSeqRow) })
                                   .bind('focus', function() { sc_sec_form_edit_users_email_onfocus(this, iSeqRow) });
  $('#id_sc_field_active' + iSeqRow).bind('blur', function() { sc_sec_form_edit_users_active_onblur(this, iSeqRow) })
                                    .bind('focus', function() { sc_sec_form_edit_users_active_onfocus(this, iSeqRow) });
  $('#id_sc_field_idyarda' + iSeqRow).bind('blur', function() { sc_sec_form_edit_users_idyarda_onblur(this, iSeqRow) })
                                     .bind('focus', function() { sc_sec_form_edit_users_idyarda_onfocus(this, iSeqRow) });
  $('#id_sc_field_idnaviera' + iSeqRow).bind('blur', function() { sc_sec_form_edit_users_idnaviera_onblur(this, iSeqRow) })
                                       .bind('focus', function() { sc_sec_form_edit_users_idnaviera_onfocus(this, iSeqRow) });
  $('#id_sc_field_idtipo_usuario' + iSeqRow).bind('blur', function() { sc_sec_form_edit_users_idtipo_usuario_onblur(this, iSeqRow) })
                                            .bind('focus', function() { sc_sec_form_edit_users_idtipo_usuario_onfocus(this, iSeqRow) });
  $('#id_sc_field_groups' + iSeqRow).bind('blur', function() { sc_sec_form_edit_users_groups_onblur(this, iSeqRow) })
                                    .bind('focus', function() { sc_sec_form_edit_users_groups_onfocus(this, iSeqRow) });
  $('#id_sc_field_confirm_pswd' + iSeqRow).bind('blur', function() { sc_sec_form_edit_users_confirm_pswd_onblur(this, iSeqRow) })
                                          .bind('focus', function() { sc_sec_form_edit_users_confirm_pswd_onfocus(this, iSeqRow) });
} // scJQEventsAdd

function sc_sec_form_edit_users_login_onblur(oThis, iSeqRow) {
  do_ajax_sec_form_edit_users_validate_login();
  scCssBlur(oThis);
}

function sc_sec_form_edit_users_login_onfocus(oThis, iSeqRow) {
  scEventControl_onFocus(oThis, iSeqRow);
  scCssFocus(oThis);
}

function sc_sec_form_edit_users_pswd_onblur(oThis, iSeqRow) {
  do_ajax_sec_form_edit_users_validate_pswd();
  scCssBlur(oThis);
}

function sc_sec_form_edit_users_pswd_onfocus(oThis, iSeqRow) {
  scEventControl_onFocus(oThis, iSeqRow);
  scCssFocus(oThis);
}

function sc_sec_form_edit_users_name_onblur(oThis, iSeqRow) {
  do_ajax_sec_form_edit_users_validate_name();
  scCssBlur(oThis);
}

function sc_sec_form_edit_users_name_onfocus(oThis, iSeqRow) {
  scEventControl_onFocus(oThis, iSeqRow);
  scCssFocus(oThis);
}

function sc_sec_form_edit_users_email_onblur(oThis, iSeqRow) {
  do_ajax_sec_form_edit_users_validate_email();
  scCssBlur(oThis);
}

function sc_sec_form_edit_users_email_onfocus(oThis, iSeqRow) {
  scEventControl_onFocus(oThis, iSeqRow);
  scCssFocus(oThis);
}

function sc_sec_form_edit_users_active_onblur(oThis, iSeqRow) {
  do_ajax_sec_form_edit_users_validate_active();
  scCssBlur(oThis);
}

function sc_sec_form_edit_users_active_onfocus(oThis, iSeqRow) {
  scEventControl_onFocus(oThis, iSeqRow);
  scCssFocus(oThis);
}

function sc_sec_form_edit_users_idyarda_onblur(oThis, iSeqRow) {
  do_ajax_sec_form_edit_users_validate_idyarda();
  scCssBlur(oThis);
}

function sc_sec_form_edit_users_idyarda_onfocus(oThis, iSeqRow) {
  scEventControl_onFocus(oThis, iSeqRow);
  scCssFocus(oThis);
}

function sc_sec_form_edit_users_idnaviera_onblur(oThis, iSeqRow) {
  do_ajax_sec_form_edit_users_validate_idnaviera();
  scCssBlur(oThis);
}

function sc_sec_form_edit_users_idnaviera_onfocus(oThis, iSeqRow) {
  scEventControl_onFocus(oThis, iSeqRow);
  scCssFocus(oThis);
}

function sc_sec_form_edit_users_idtipo_usuario_onblur(oThis, iSeqRow) {
  do_ajax_sec_form_edit_users_validate_idtipo_usuario();
  scCssBlur(oThis);
}

function sc_sec_form_edit_users_idtipo_usuario_onfocus(oThis, iSeqRow) {
  scEventControl_onFocus(oThis, iSeqRow);
  scCssFocus(oThis);
}

function sc_sec_form_edit_users_groups_onblur(oThis, iSeqRow) {
  do_ajax_sec_form_edit_users_validate_groups();
  scCssBlur(oThis);
}

function sc_sec_form_edit_users_groups_onfocus(oThis, iSeqRow) {
  scEventControl_onFocus(oThis, iSeqRow);
  scCssFocus(oThis);
}

function sc_sec_form_edit_users_confirm_pswd_onblur(oThis, iSeqRow) {
  do_ajax_sec_form_edit_users_validate_confirm_pswd();
  scCssBlur(oThis);
}

function sc_sec_form_edit_users_confirm_pswd_onfocus(oThis, iSeqRow) {
  scEventControl_onFocus(oThis, iSeqRow);
  scCssFocus(oThis);
}

function displayChange_block(block, status) {
	if ("0" == block) {
		displayChange_block_0(status);
	}
}

function displayChange_block_0(status) {
	displayChange_field("login", "", status);
	displayChange_field("pswd", "", status);
	displayChange_field("confirm_pswd", "", status);
	displayChange_field("name", "", status);
	displayChange_field("email", "", status);
	displayChange_field("active", "", status);
	displayChange_field("groups", "", status);
	displayChange_field("idyarda", "", status);
	displayChange_field("idnaviera", "", status);
	displayChange_field("idtipo_usuario", "", status);
}

function displayChange_row(row, status) {
	displayChange_field_login(row, status);
	displayChange_field_pswd(row, status);
	displayChange_field_confirm_pswd(row, status);
	displayChange_field_name(row, status);
	displayChange_field_email(row, status);
	displayChange_field_active(row, status);
	displayChange_field_groups(row, status);
	displayChange_field_idyarda(row, status);
	displayChange_field_idnaviera(row, status);
	displayChange_field_idtipo_usuario(row, status);
}

function displayChange_field(field, row, status) {
	if ("login" == field) {
		displayChange_field_login(row, status);
	}
	if ("pswd" == field) {
		displayChange_field_pswd(row, status);
	}
	if ("confirm_pswd" == field) {
		displayChange_field_confirm_pswd(row, status);
	}
	if ("name" == field) {
		displayChange_field_name(row, status);
	}
	if ("email" == field) {
		displayChange_field_email(row, status);
	}
	if ("active" == field) {
		displayChange_field_active(row, status);
	}
	if ("groups" == field) {
		displayChange_field_groups(row, status);
	}
	if ("idyarda" == field) {
		displayChange_field_idyarda(row, status);
	}
	if ("idnaviera" == field) {
		displayChange_field_idnaviera(row, status);
	}
	if ("idtipo_usuario" == field) {
		displayChange_field_idtipo_usuario(row, status);
	}
}

function displayChange_field_login(row, status) {
}

function displayChange_field_pswd(row, status) {
}

function displayChange_field_confirm_pswd(row, status) {
}

function displayChange_field_name(row, status) {
}

function displayChange_field_email(row, status) {
}

function displayChange_field_active(row, status) {
}

function displayChange_field_groups(row, status) {
}

function displayChange_field_idyarda(row, status) {
}

function displayChange_field_idnaviera(row, status) {
}

function displayChange_field_idtipo_usuario(row, status) {
}

function scRecreateSelect2() {
}
function scResetPagesDisplay() {
	$(".sc-form-page").show();
}

function scHidePage(pageNo) {
	$("#id_sec_form_edit_users_form" + pageNo).hide();
}

function scCheckNoPageSelected() {
	if (!$(".sc-form-page").filter(".scTabActive").filter(":visible").length) {
		var inactiveTabs = $(".sc-form-page").filter(".scTabInactive").filter(":visible");
		if (inactiveTabs.length) {
			var tabNo = $(inactiveTabs[0]).attr("id").substr(27);
		}
	}
}
function scJQUploadAdd(iSeqRow) {
} // scJQUploadAdd


function scJQElementsAdd(iLine) {
  scJQEventsAdd(iLine);
  scEventControl_init(iLine);
  scJQUploadAdd(iLine);
} // scJQElementsAdd

