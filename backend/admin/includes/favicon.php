<?php
/**
 * Favicon include for admin pages
 * Outputs favicon link tags based on website settings
 */
require_once __DIR__ . '/../../config/config.php';

$faviconUrl = getFaviconUrl();
if ($faviconUrl): ?>
<link rel="icon" type="image/png" href="<?php echo $faviconUrl; ?>">
<link rel="shortcut icon" type="image/png" href="<?php echo $faviconUrl; ?>">
<?php else: ?>
<link rel="icon" type="image/png" href="/favicon.ico">
<?php endif; ?>









