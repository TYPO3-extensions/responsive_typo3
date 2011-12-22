<?php

$extPath = t3lib_extMgm::extPath('responsive_typo3');

return array(
    'tx_responsivetypo3_hooks_cimage' => $extPath . 'Classes/Hooks/Cimage.php',
    'tx_responsivetypo3_domain_image' => $extPath . 'Classes/Domain/Image.php',
    'tx_responsivetypo3_exception_filetype' => $extPath . 'Classes/Exception/Filetype.php',
    'tx_responsivetypo3_exception_path' => $extPath . 'Classes/Exception/Path.php',
    'tx_responsivetypo3_exception_resizemethod' => $extPath . 'Classes/Exception/Resizemethod.php',
);
?>