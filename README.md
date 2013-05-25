UglySqlSchema
=============

ZF2 Module that lets other modules provide sql schema files for Zend\Db

Usage
=====

### Module Owners

Tell UglySqlSchema that you provide SQL schema files by adding the following to the root of your module.config.php:

```
'schema' => array(
    'zfcuser' => __DIR__ . '/../data',
),
```

### Module Users

Install the module in the usual ways (clone to vendor dir, or composer it, add to application.config.php yadda yadda yadda)

Run using the following command from your application root:

`php public/index.php uglysqlschema merge <platform>`

You'll need a configured `Zend\Db\Adapter\Adapter\` key in your service manager.
