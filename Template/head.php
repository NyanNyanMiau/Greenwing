<?php
global $GreenwingConfig;

if (isset($GreenwingConfig['bg'])): ?>
<style>
body {
    background-image: url(<?= $GreenwingConfig['bg'] ?>);
    <?php if (isset($GreenwingConfig['bg-blend'])): ?>
        background-blend-mode: <?= $GreenwingConfig['bg-blend'] ?>;
    <?php endif; ?>
}
</style>
<?php endif; ?>
