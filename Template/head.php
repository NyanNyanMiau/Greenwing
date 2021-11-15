<?php
global $GreenwingConfig;

if (isset($GreenwingConfig['bg'])): ?>
<style>
body {
    background-image: url(<?= $GreenwingConfig['bg'] ?>);
}
</style>
<?php endif; ?>