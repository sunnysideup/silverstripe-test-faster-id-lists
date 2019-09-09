<?php

use SilverStripe\Dev\BuildTask;
use SilverStripe\Core\Injector\Injector;
use Sunnysideup\FasterIdLists\FasterIDLists;


class MyTest extends BuildTask
{
    public const NUMBER_OF_STEPS = 10;

    protected const MIN = 9399;

    public const MAX = 9999;

    protected $title = 'Test faster lookups';

    public function run($request)
    {
        $tableRows[0] = [
            0 => 'No. IDs',
            1 => 'STANDARD ID LIST',
            2 => 'FAST ID LISTS'
        ];
        $idListAll = [];
        for($i = 1; $i <= self::MAX; $i++) {
            $idListAll[$i] = $i;
        }
        $interval = round((self::MAX - self::MIN) / self::NUMBER_OF_STEPS);
        for($limit = $interval + self::MIN; $limit <= self::MAX; $limit += $interval ) {
            $rowCount = ($limit - self::MIN )/ $interval;
            $tableRows[$rowCount] = [];
            $tableRows[$rowCount][0] = $limit;
            $idListSelected = array_rand($idListAll, $limit);
            shuffle($idListSelected);
            if($rowCount % 2) {
                $tableRows[$rowCount][1] = $this->testA($idListSelected);
                $tableRows[$rowCount][2] = $this->testB($idListSelected);
            } else {
                $outcomeB = $this->testB($idListSelected);
                $tableRows[$rowCount][1] = $this->testA($idListSelected);
                $tableRows[$rowCount][2] = $outcomeB;
                $tableRows[$rowCount];
            }
        }

        return $this->output($tableRows);
    }

    protected function output($tableRows)
    {
        echo '
           <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
           <script type="text/javascript">
             google.charts.load(\'current\', {\'packages\':[\'corechart\']});
             google.charts.setOnLoadCallback(drawChart);

             function drawChart() {
               var data = google.visualization.arrayToDataTable(
                 '.json_encode($tableRows, JSON_PRETTY_PRINT).'
               );

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
        $myDataList = MyDataObject::get()
            ->filter(['ID' => $idListSelected])
            ->sort('Title');
        if($myDataList->count() !== count($idListSelected)) {
            user_error('Could not find IDs');
        }
        $endA = microtime(true);
        // echo '<h1>A</h1>';
        // echo $myDataList->sql();
        return round($endA - $startA, 9);
    }

    protected function testB($idListSelected)
    {
        $startB = microtime(true);
        $obj = new FasterIDLists(
            MyDataObject::class,
            $idListSelected,
            'ID'
        );
        $myDataList = $obj->filteredDatalist();
        $myDataList = $myDataList->sort('Title');
        if($myDataList->count() !== count($idListSelected)) {
            user_error('Could not find IDs');
        }
        $endB = microtime(true);
        // echo '<h1>B</h1>';
        // echo $myDataList->sql();

        return  round($endB - $startB, 9);
    }

}
