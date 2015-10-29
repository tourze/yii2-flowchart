<?php

namespace tourze\yii2\flowchart;

use yii\web\AssetBundle;

class RaphaelAsset extends AssetBundle
{

    /**
     * {@inheritdoc}
     */
    public $sourcePath = '@bower/raphael';

    /**
     * {@inheritdoc}
     */
    public $js = [
        'raphael-min.js',
    ];
}
