# what is this?

A simple A / B speed test for retrieving data using the Silverstripe ORM.

With A we provide a simple list of IDs to the ORM.
With B we provide a "smart" list of IDs to the ORM.

You can use this module as a basis to create your own speed tests.

# Installation

1.  clone this repo
2.  put in your web root
3.  run `composer install`
4.  add .env file
5.  create public/assets owned by www-data
6.  browse to: `/dev/build/?flush=all`
7.  browse to: `/dev/tasks/testfasterlookups/`
8.  edit values in `app/src/MyTest.php` as you see fit.
9.  see `vendor/sunnysideup/faster-id-lists/src/FasterIDLists.php` for inner workings.
10. browse to: `/dev/tasks/testfasterlookups/showqueries=1` to see actual queries being executed.
