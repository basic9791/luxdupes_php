<?php exit;
$input['publishverify'] = form_checkbox('publishverify', $_group['publishverify'], lang('well_publish_verify'));
$input['commentverify'] = form_checkbox('commentverify', $_group['commentverify'], lang('well_comment_verify'));
$input['allowverify'] = form_checkbox('allowverify', $_group['allowverify'], lang('well_verify_content'));
$input['allowthumbnail'] = form_checkbox('allowthumbnail', $_group['allowthumbnail'], lang('well_publish_allowthumbnail'));
$input['allowbrief'] = form_checkbox('allowbrief', $_group['allowbrief'], lang('well_publish_allowbrief'));
$input['allow_auto_brief'] = form_checkbox('allow_auto_brief', $_group['allow_auto_brief'], lang('brief_get'));
$input['allowkeywords'] = form_checkbox('allowkeywords', $_group['allowkeywords'], lang('well_publish_allowkeywords'));
$input['allowdescription'] = form_checkbox('allowdescription', $_group['allowdescription'], lang('well_publish_allowdescription'));
?>