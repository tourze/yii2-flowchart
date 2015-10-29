<?php

namespace tourze\yii2\flowchart;

use common\assets\FlowChartAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\widgets\InputWidget;

class FlowChartWidget extends InputWidget
{

    /**
     * @var array 流程数据
     */
    public $process = [];

    /**
     * @var array 插件参数
     */
    public $options = [
        'x'           => 10,
        'y'           => 10,
        'yes-text'    => '成功',
        'no-text'     => '失败',
        'line-width'  => 2,
        'line-length' => 30,
        //'element-color' => 'green',
        //'line-color' => 'green',
        'text-margin' => 10,
        'flowstate'   => [
            '_current' => [
                'line-color'    => 'green',
                'fill'          => 'green',
                'element-color' => 'green',
                'font-color'    => 'white',
            ],
        ],
    ];

    /**
     * @var array
     */
    public $inputOptions = [

    ];

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        FlowChartAsset::register($this->getView());
        $this->renderProcess($this->process);
    }

    public function renderProcess($process)
    {
        $currentStatus = (string) $this->model->{$this->attribute};

        // 定义流
        $definition = '';
        // 控制流
        $control = '';

        // 开始
        $definition .= "_start=>start: 开始\n";
        $definition .= "_end=>end: 结束\n";
        // 结束

        // 循环输出
        $i = 0;
        foreach ($process['status'] as $name => $statusConfig)
        {
            if ($i == 0)
            {
                $control .= "_start->$name\n";
            }

            $op = ArrayHelper::getValue($statusConfig, 'op', 'operation');
            $label = ArrayHelper::getValue($statusConfig, 'label', $name);
            $direction = ArrayHelper::getValue($statusConfig, 'direction', 'right');
            $next = ArrayHelper::getValue($statusConfig, 'next');

            // 如果是当前状态
            if ($currentStatus === $name)
            {
                $label .= '|_current';
            }

            // 如果是数组，那么就是带条件的了
            if (is_array($next))
            {
                $op = 'condition';
                $definition .= "$name=>$op: $label\n";
                $yes = ArrayHelper::getValue($next, 'true');
                $no = ArrayHelper::getValue($next, 'false');

                if (is_array($yes))
                {
                    $control .= "$name(yes, {$yes[0]})->{$yes[1]}\n";
                }
                else
                {
                    $control .= "$name(yes, right)->$yes\n";
                }

                if (is_array($no))
                {
                    $control .= "$name(no, {$no[0]})->{$no[1]}\n";
                }
                else
                {
                    $control .= "$name(no)->$no\n";
                }
            }
            else
            {
                $definition .= "$name=>$op: $label\n";
                if ($next)
                {
                    $control .= "$name($direction)->$next\n";
                }
            }

            $i++;
        }

        $finalText = $definition . "\n" . $control;

        $flowChartDiagram = $this->attribute . '-diagram';
        $flowChartTextarea = $this->attribute . '-textarea';

        $options = new JsExpression(Json::encode($this->options));

        echo "<div id='$flowChartDiagram'></div>";
        echo "<textarea id='$flowChartTextarea' style='display: none;'>$finalText</textarea>";
        //echo "<textarea id='$flowChartTextarea' rows='30' cols='100'>$finalText</textarea>";
        $this->getView()->registerJs('jQuery("#' . $flowChartTextarea . '").on("change keyup paste", function () {
            var diagram = flowchart.parse(jQuery(this).val());
            jQuery("#' . $flowChartDiagram . '").html("");
            diagram.drawSVG("' . $flowChartDiagram . '", ' . $options . ');
        }).trigger("keyup");');

        if ($this->hasModel())
        {
            $input = Html::activeHiddenInput($this->model, $this->attribute, $this->inputOptions);
        }
        else
        {
            $input = Html::hiddenInput($this->name, $this->value, $this->inputOptions);
        }
        echo $input;
    }
}
