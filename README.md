VModel - Idiorm/Paris Extension
=====
https://github.com/rafayhingoro/vmodel-idiorm-extension

---

Built on top of [Idiorm](http://github.com/j4mie/idiorm/) & [Paris](https://github.com/j4mie/paris).

Tested on PHP 5.4.0+ - may work on earlier versions with PDO and the correct database drivers.

Released under a [MIT license](http://en.wikipedia.org/wiki/MIT_licenses).

Documentation
-------------

# Setup Guide
--------------
1. Download [release source code](https://github.com/rafayhingoro/vmodel-idiorm-extension/archive/1.0.0.zip) and move `vmodel.php` in your Models Directory 
2. on any model where you want to use vmodel simply use `extends` like this
   ```php
    <?php 
     class YourModelName extends VModel {
       //your code 
     }
   ```
3. for validating before save
    `Model File YourModelName.php`
   ```php 
   <?php 
    class YourModelName extends VModel {
        public $_isValid = true; //by default making it valid
        
        //fields which are in database
        public $_fields = array(
            'id',
            'name',
            'email',
            'contact_number',
            'description'
        );
        
        //adding rules for fields 
        protected $_rules = array(
            'category_id' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'contact_number' => 'required',
            'description' => 'required'
        );
    }
   ?>
   ```
   
   `Controller File ControllerUser.php`
   ```php
    <?php 
    class ControllerUser {
         try {
         
            // $form is array of inputs here's an example 
            // $form['username'] = 'user1234';
            // $form['password'] = 'mySecretPassword';
            // $form['email'] = 'myemail@123.com';
            // ... 
           
             $oModel = Model::factory("YourModelName")->create();
             $oModel->validateForm($form); //throws exception if form is not valid
             ...
         } catch (Exception $ex) {
            $ex->getMessage(); //validation error will be available here 
         }
     }
   ```


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
