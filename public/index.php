<?php

require __DIR__ . '/../vendor/autoload.php';

ob_start();

?>

<div class="text-center">
    <h1>Bem-vindo!</h1>
    <p>Escolha uma opção no menu acima.</p>
</div>

<?php

$content = ob_get_clean();

require "layout.php";