<?php

declare(strict_types=1);

namespace App\Asset;

use yii\web\AssetBundle;

final class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    /**
     * @var array<int, string>
     */
    public $css = [
        'css/app.css',
    ];
}
