VModal - Idiorm/Paris Extension
=====
https://github.com/rafayhingoro/vmodal-idiorm-extension

---

Built on top of [Idiorm](http://github.com/j4mie/idiorm/) & [Paris](https://github.com/j4mie/paris).

Tested on PHP 5.4.0+ - may work on earlier versions with PDO and the correct database drivers.

Released under a [MIT license](http://en.wikipedia.org/wiki/MIT_licenses).

Documentation
-------------

Validation Modal
-------------------
```php
class Item extends VModel {
    public $_fields = array(
        'id',
        'shop_id',
        'cat_id',
        'name',
        'description',
        'price',
        'created_on',
        'created_by',
        'updated_by',
        'updated_on'
    );
    public $_rules = array(
        'shop_id' => 'required',
        'cat_id'     => 'required|int',
        'name'       => 'required',
        'description' => 'required|limit<500',
        'price'       => 'required|int'
    );
}
$form = array(
    'shop_id' => 23,
    'cat_id' => 12,
    'name' => 'XYZ product',
    'description' => 'some description ...',
    'price' => '12.00'
);

$item = Model::factory('Item')
         ->saveForm($form);
//or
$item = Model::factory('Item')
         ->validateForm($form);
 $item->shop_id = $form['shop_id'];
 $item->cat_id = $form['cat_id'];
 $item->name = $form['name'];
 $item->description = $form['description'];
 $item->price = $form['price'];
 $item->save();
```

#### 1.0.0 - released 2017-03-19

* Initial release
