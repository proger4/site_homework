<?php

declare(strict_types=1);

namespace App\Controller;

use App\Web\KeywordRuntime;
use yii\web\Controller;

abstract class WebController extends Controller
{
    public $layout = false;

    private ?KeywordRuntime $keywordRuntime = null;

    protected function runtime(): KeywordRuntime
    {
        return $this->keywordRuntime ??= new KeywordRuntime();
    }

    protected function page(string $title, string $body): string
    {
        $safeTitle = self::e($title);

        return <<<HTML
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$safeTitle}</title>
    <style>
        body {
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #171717;
            background: #f7f7f4;
        }

        main {
            max-width: 1080px;
            margin: 48px auto;
            padding: 0 24px;
        }

        nav {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin: 0 0 28px;
        }

        h1 {
            margin: 0 0 12px;
            font-size: 32px;
            line-height: 1.15;
        }

        table {
            width: 100%;
            margin-top: 24px;
            border-collapse: collapse;
            background: #fff;
        }

        th,
        td {
            padding: 14px 16px;
            border: 1px solid #deded8;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #eeeeea;
        }

        a {
            color: #075985;
        }
    </style>
</head>
<body>
<main>
    <nav>
        <a href="/">Home</a>
        <a href="/health">Health</a>
        <a href="/upload">Upload</a>
        <a href="/admin/keywords">Keywords</a>
        <a href="/preview">Preview</a>
        <a href="/ai-preview?mode=template">AI Preview</a>
        <a href="/export">Export</a>
    </nav>
    <h1>{$safeTitle}</h1>
    {$body}
</main>
</body>
</html>
HTML;
    }

    protected static function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
