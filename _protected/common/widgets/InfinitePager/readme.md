Yii2 Infinite Scroll
====================
LinkPager with infinite scroll support

Installation
------------

```
php composer.phar require --prefer-dist "darkcs/yii2-infinite-scroll" "*"
```

Options
-------
##### $autoStart `true`;
##### $containerSelector `.list-view`;
##### $itemSelector `.item`;
##### $paginationSelector `.pagination`;
##### $nextSelector `.pagination .next a:first`;
##### $bufferPx `40`;
##### $pjaxContainer `null`;

Usage example
-------------

```php
$pjax = \yii\widgets\Pjax::begin();

echo \yii\widgets\ListView::widget([
    'dataProvider' => $dataProvider,
    'layout' => '{items}<div class="pagination-wrap">{pager}</div>',
    'options' => ['class' => 'list-view',],
    'itemOptions' => ['class' => 'item'],
    'itemView' => '_item',
    'summary' => false,
    'pager' => [
        'class' => common\widgets\InfinitePager\InfiniteScrollPager::class,
        'paginationSelector' => '.pagination-wrap',
        'containerSelector' => '.list-view',
        'pjaxContainer' => $pjax->id,
    ],
]);
\yii\widgets\Pjax::end();
```

JS usage
--------

```javascript
// init
$('.list-view').infinitescroll();
// enable, paused by default
$('.list-view').infinitescroll('start');
// disable
$('.list-view').infinitescroll('stop');
```

Events
------
```javascript
$('.list-view').on('infinitescroll:afterRetrieve', function(){
    console.log('infinitescroll:afterRetrieve');
});

$('.list-view').on('infinitescroll:afterStart', function(){
    console.log('infinitescroll:afterStart');
});

$('.list-view').on('infinitescroll:afterStop', function(){
    console.log('infinitescroll:afterStop');
});
```