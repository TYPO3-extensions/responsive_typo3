<?php
    if (!defined('TYPO3_MODE')) {
        die ('Access denied.');
    }

    $TYPO3_CONF_VARS['FE']['XCLASS']['tslib/class.tslib_content.php'] = t3lib_extMgm::extPath($_EXTKEY).'Classes/Xclass/class.ux_tslib_cobj.php';

	$TYPO3_CONF_VARS['FE']['eID_include']['responsive_typo3'] = 'EXT:responsive_typo3/Classes/Eid/Image.php';
?>
