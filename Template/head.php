<?php
global $GreenwingConfig;

if (isset($GreenwingConfig['bg'])): ?>
<style>
body {
    background-image: url(<?= $GreenwingConfig['bg'] ?>);
    background-size: cover;
}
</style>
<?php endif; ?>
