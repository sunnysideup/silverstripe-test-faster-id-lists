<?php

use SilverStripe\Dev\BuildTask;
use SilverStripe\Core\Injector\Injector;
use Sunnysideup\FasterIdLists\FasterIDLists;


class MyTest extends BuildTask
{
    public const STEP = 25;

    public const MAX = 9999;

    protected $title = 'Test faster lookups';

    public function run($request){
        $tableRows = [0] = [
            0 => 'No. IDs',
            1 => 'STANDARD ID LIST',
            2 => 'FAST ID LISTS']
        ];
        $idListAll = [];
        for($i = 1; $i <= 9999; $i++) {
            $idListAll[$i] = $i;
        }
        for($step = 25; $step <= self::MAX; $step += self::STEP ) {
            $rowCount = $step / self::STEP;
            $tableRows[$rowCount] = [];
            $tableRows[$rowCount][0] = $rowCount;
            $idListSelected = array_rand($idList, $step);

            if($rowCount % 2) {
                $tableRows[$rowCount][1] = $this->testA($idListSelected);
                $tableRows[$rowCount][2] = $this->testB($idListSelected);
            } else {
                $tableRows[$rowCount][2] = $this->testB($idListSelected);
                $tableRows[$rowCount][1] = $this->testA($idListSelected);
                ksort($tableRows[$rowCount]);
            }
        }
        echo '
           <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
           <script type="text/javascript">
             google.charts.load(\'current\', {\'packages\':[\'corechart\']});
             google.charts.setOnLoadCallback(drawChart);

             function drawChart() {
               var data = google.visualization.arrayToDataTable([
                 '.json_encode($tableRows).'
               ]);

               var options = {
                 title: \'Compare ID list lookups\',
                 curveType: \'function\',
                 legend: { position: \'bottom\' }
               };

               var chart = new google.visualization.LineChart(document.getElementById(\'curve_chart\'));

               chart.draw(data, options);
             }
           </script>
           <div id="curve_chart" style="width: 90vw; height: 90vh; margin-left: auto; margin-right: auto; margin-top: 5vh"></div>
        ';
    }

    protected function testA($idListSelected)
    {
        $startA = microtime(true);
        $objects = MyDataObject::get()->filter(['ID' => $idListSelected]);
        foreach($objects as $object) {
            //do nothing
        }
        $endA = microtime(true);

        return  $endA - $startA;
    }

    protected function testB($idListSelected)
    {
        $startB = microtime(true);
        $myDataList = Injector::inst()->create(
            FasterIDLists::class,
            MyDataObject::class,
            $idListSelected
        )->filteredDatalist();
        foreach($objects as $object) {
            //do nothing
        }
        $endB = microtime(true);

        return  $endB - $startB;
    }

}
