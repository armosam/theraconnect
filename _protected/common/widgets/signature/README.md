Signature armos &copy; 2020
===============
Renders a Signature Pad using jSignature

Author
======
Armen Bablanyan

Installation
=============
Use migration file from migrations folder to create user_signature table
Use existing models in your models or extend from them.
In the widget it uses standard models location.

Usage
-----
Usage example to same image in the database.
http://stackoverflow.com/questions/26694675/store-jsignature-output-to-mysql-to-be-redrawn-on-separate-page

The js files are from brinley.github.io See [demos here](http://brinley.github.io/jSignature/ "Signature Capture Demos").


Use Active form to add signature for your form element. 
Without `allowed` boolean property it will show only existing signature. We can use access checking result as the boolean value of allowed property. 
```php
<?php
use common\widgets\signature\Signature;
?>
// Change is not allowed
<?= $form->field($model, 'signature')->widget(Signature::class) ?>
// Change is allowed
<?= $form->field($model, 'signature')->widget(Signature::class, ['allowed' => true]) ?>
```

If you prefer to save signature and use it later, then add hidden input and specify a name of that input in the signature element:
```php
<?php
use common\widgets\signature\Signature;
?>
// Change and saving is allowed
<?= $form->field($model, 'therapist_signature')->widget(Signature::class, ['save_signature_attribute' => 'save_signature', 'allowed' => true]) ?>
<?= $form->field($model, 'save_signature')->hiddenInput()->label(false) ?>

```
This will show additional button `Use Existing` when signature saved and field allowed for saving like above example.

There are some functionality in the box as ``SignatureService`` class to convert `base30 to native`, `native to SVG` and `base30 to SVG` formats.

You can get SignatureService instance and use it like:
```php
<?php

$base30_string = 'image/jsignature;base30,3E13Z5Y5_1O24Z66_1O1Z3_3E2Z4';

$native_array = Signature::getSignatureService()->base30ToNative($base30_string);

$svg = Signature::getSignatureService()->nativeToSVG($native_array);

$svg = Signature::getSignatureService()->base30ToSVG($base30_string, 'darkblue', 'white', 2);
?>
```
