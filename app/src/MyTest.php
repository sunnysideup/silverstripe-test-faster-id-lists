<?php

namespace Sunnysideup\ABSpeedTesting;

use SilverStripe\Dev\BuildTask;
use SilverStripe\Core\Injector\Injector;
use Sunnysideup\FasterIdLists\FasterIDLists;


class MyTest extends AbstractTestClass
{
    private static $segment = 'testfasterlookups';

    public const NUMBER_OF_STEPS = 100;

    protected const MIN = 0;

    public const MAX = 9999;

    protected $title = 'Comparison of time taken to retrieve dataobjects - comparing simple and "smart" ID select statements';

    protected $description = 'Compare simple and computed ID lookups';


    public function run($request)
    {
        $tableRows[0] = [
            0 => 'limit',
            1 => $this->testATitle(),
            2 => $this->testBTitle(),
        ];
        $idListAll = [];
        for($i = 1; $i <= self::MAX; $i++) {
            $idListAll[$i] = $i;
        }
        $interval = round((self::MAX - self::MIN) / self::NUMBER_OF_STEPS);
        for($limit = $interval + self::MIN; $limit <= self::MAX; $limit += $interval ) {
            $rowCount = ($limit - self::MIN )/ $interval;
            $tableRows[$rowCount] = [];
            $tableRows[$rowCount][0] = round(($limit/self::MAX)*100, 1).'%';
            $idListSelected = array_rand($idListAll, $limit);
            shuffle($idListSelected);
            if($rowCount % 2) {
                $tableRows[$rowCount][1] = $this->testA($idListSelected);
                $tableRows[$rowCount][2] = $this->testB($idListSelected);
            } else {
                $outcomeB = $this->testB($idListSelected);
                $tableRows[$rowCount][1] = $this->testA($idListSelected);
                $tableRows[$rowCount][2] = $outcomeB;
            }
        }

        return $this->output($tableRows);
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

    protected function xAxisTitle() : string
    {
        return 'percentage of random IDs in select statement';
    }

    protected function yAxisTitle() : string {
        return 'seconds per request';
    }

    protected function testATitle() : string
    {
        return 'Simple select statement';
    }

    protected function testBTitle() : string
    {
        return 'Computed selected statement';
    }

}
