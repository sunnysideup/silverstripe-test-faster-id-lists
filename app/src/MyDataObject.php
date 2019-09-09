<?php

use SilverStripe\ORM\DataObject;

class MyDataObject extends DataObject
{

    private static $db = [
        'Title' => 'Varchar',
        'OtherField' => 'Varchar',
    ];

    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();
        if(MyDataObject::get()->count() === 0) {
            for($i = 0; $i < 99999; $i++) {
                $obj = new MyDataObject();
                $obj->Title = $this->randomString(40);
                $obj->OtherField = $this->randomString(40);
                DB::alteration_message('creating object '.$i.' '.$obj->Title);
                $obj->write();
            }
        }
    }

    private function randomString($length) {
       $result = null;
       $replace = array('/', '+', '=');
       while(!isset($result[$length-1])) {
          $result.= str_replace($replace, NULL, base64_encode(random_bytes($length)));
       }
       return substr($result, 0, $length);
    }

}
