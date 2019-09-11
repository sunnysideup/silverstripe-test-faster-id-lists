<?php

namespace Sunnysideup\ABSpeedTesting;

use SilverStripe\Dev\BuildTask;
use SilverStripe\Core\Injector\Injector;
use Sunnysideup\FasterIdLists\FasterIDLists;


abstract class AbstractTestClass extends BuildTask
{

    protected $title = 'To be completed';

    protected $description = 'To be completed';

    public function run($request)
    {
        user_error('extend in your own class.');
    }

    protected abstract function xAxisTitle() : string;

    protected abstract function yAxisTitle() : string;

    protected abstract function testATitle() : string;

    protected abstract function testBTitle() : string;

    protected function output($tableRows)
    {
        echo '
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
            google.charts.load(\'current\', {\'packages\':[\'corechart\']});
            google.charts.setOnLoadCallback(drawChart);

            function drawChart() {
                var data = google.visualization.arrayToDataTable('.json_encode($tableRows, JSON_PRETTY_PRINT).');
                var options = {
                    title: \''.$this->title.'\',
                    curveType: \'function\',
                    legend: { position: \'bottom\' },
                    hAxis: {
                        title: \''.$this->xAxisTitle().'\',
                    },
                    vAxis: {
                        title: \''.$this->yAxisTitle().'\',
                    }

                };

                var chart = new google.visualization.LineChart(document.getElementById(\'curve_chart\'));

                chart.draw(data, options);
            }
        </script>
        <div id="curve_chart" style="width: 90vw; height: 90vh; margin-left: auto; margin-right: auto; margin-top: 5vh"></div>
        ';
    }
}
