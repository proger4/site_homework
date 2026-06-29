<?php

declare(strict_types=1);

use yii\helpers\Html;

$this->title = 'Admin Login';
?>
<section class="page-head">
    <div>
        <h1>Admin Login</h1>
        <p class="muted">Use the local demo admin to manage imports, cleanup review, previews, and exports.</p>
    </div>
</section>

<section class="panel panel--narrow">
    <?php if ($error !== null): ?>
        <div class="flash flash--error"><?= Html::encode($error) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= Html::encode(Yii::$app->urlManager->createUrl(['/login'])) ?>">
        <label for="username">Username</label>
        <input id="username" name="username" type="text" autocomplete="username" value="<?= Html::encode($defaultLogin) ?>">

        <label for="password">Password</label>
        <input id="password" name="password" type="password" autocomplete="current-password">

        <button class="button" type="submit">Login</button>
    </form>
</section>
