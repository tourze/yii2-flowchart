<?php

namespace tourze\yii2\flowchart;

use yii\web\AssetBundle;

class FlowChartAsset extends AssetBundle
{

    /**
     * {@inheritdoc}
     */
    public $depends = [
        'tourze\yii2\flowchart\RaphaelAsset',
    ];

    /**
     * {@inheritdoc}
     */
    public $sourcePath = '@bower/flowchart/release';

    /**
     * {@inheritdoc}
     */
    public $js = [
        'flowchart.min.js',
    ];
}
