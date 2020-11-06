# laravel 從零到有的測試介紹
## 測試目的：
- 避免改了A壞了B
- 為了不要讓未來的自己和後人踩坑

## 目標:
- 核心功能的API/Service/Repository撰寫測試。
- 當有Call到外部API，必須要Mock起來。
- 自動化測試流程。

 ## 前置作業:
 - 若你是用VSCODE開發的話，可以安裝PHPUnit Test Exploer，圖形化介面讓我們可以直接點選要跑的測試，推推！  
 ![](https://i.imgur.com/oT80sxo.png)
 - 了解 Laravel 測試方法: [Laravel Testing: Getting Started](https://laravel.com/docs/8.x/testing)

 ## 基本知識:
- 測試資料庫存取時，要儘可能不動到正式資料庫，因此看各位的需求，可以開一台測試用Database或者使用sqlite :memory:來測試
- 測試分為兩種:
  * 單元測試(Unit Test): 著重在單一功能的邏輯測試（Service/Respository）
  * 功能測試(Feature): 著重在商業邏輯的測試 (API/Controller)
- 撰寫Test Case的流程(3A原則)：
  * 建立初始資料(Arrange)
  * 執行要測試的功能(Act)
  * 比對結果(Assert)

## 安裝 Laravel 並建立相關檔案與環境:
- 安裝Laravel
```bash
$ composer create-project laravel/laravel laravel-testing
$ cd laravel-testing
```
- 安裝 Mockery ：
```bash
$ composer require mockery/mockery --dev
```
- 新增.env.testing及建立測試Database(laravel-testing)
```bash
APP_ENV=testing
# 和.env一模一樣，只是把DB換成測試DB
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel-mock-testing
DB_USERNAME=root
DB_PASSWORD=root
```
- 查看phpunit.xml
```bash
# 確認APP_ENV是testing，再跑測試時才會去吃.env.testing
 <php>
     <env name="APP_ENV" value="testing"/>
     <env name="CACHE_DRIVER" value="array"/>
     <env name="SESSION_DRIVER" value="array"/>
     <env name="QUEUE_DRIVER" value="sync"/>
 </php>
```

## 需求
透過第三方服務來蒐集某些地點的評論  
1. [DataShake 服務](https://www.datashake.com/)，給定該地點的Google Place ID後，DataShake會丟進他們的Queue去處理，處理完成後即可拿到評論。
2. 建立 google_review_jobs，用來存 DataShake 處理的Job.
3. 建立 google_reviews，用來存每則評論.
4. google_review_jobs, google_reviews 為一對多關聯，一個google_review_jobs包含多個google_reviews.


## 善用 Factory & Faker 建立假資料
建立 google_review_jobs, google_reviews table, model和factory.    
搭配 [Faker](https://github.com/fzaninotto/Faker) 建立假資料.  

database/factories/GoogleReviewJobFactory.php
```bash
$factory->define(GoogleReviewJob::class, function (Faker $faker) {
    return [
        'job_id' => $faker->randomDigit,
        'google_place_id' => $faker->uuid,
        'review_count' => $faker->numberBetween($min = 20, $max = 100), 
        'average_rating' => $faker->randomElement(array(1,2,3,4,5)),
        'crawl_status' => $faker->randomElement(array('complete', 'maintenance', 'pending')),
        'credits_used' => $faker->randomElement(array(1,2,3,4,5)),
    ];
});
```
可以建起一個 GoogleReviewJob 後，把關聯的 GoogleReviews 建起來([Laravel: Factory Callback](https://laravel.com/docs/6.x/database-testing#factory-callbacks))。
```bash
$factory->afterCreating(GoogleReviewJob::class, function (GoogleReviewJob $googleReviewJob, Faker $faker) {
    $googleReviewJob->reviews()->saveMany(
        factory(GoogleReview::class, 3)->make([
            'google_review_job_id' => $googleReviewJob['id'],
        ])
    );
});
```
另外也可以把一些狀態相關的東西抽成[Laravel: Factory State](https://laravel.com/docs/6.x/database-testing#factory-states)
```bash
$factory->state(GoogleReviewJob::class, 'complete', function (Faker $faker) {
    return [
        'crawl_status' => 'complete'
    ];
});
$factory->state(GoogleReviewJob::class, 'maintenance', function (Faker $faker) {
    return [
        'crawl_status' => 'maintenance'
    ];
});
$factory->state(GoogleReviewJob::class, 'pending', function (Faker $faker) {
    return [
        'crawl_status' => 'pending'
    ];
});
```

database/factories/GoogleReviewFactory.php  
由於 google_reviews 會用 google_review_job_id 關聯回 google_review_jobs，因此這邊可以先創建一筆 GoogleReviewJob.
```bash
$factory->define(GoogleReview::class, function (Faker $faker) {
    return [
        'google_review_job_id' => factory(GoogleReviewJob::class),
        'review_id' => $faker->randomDigit,
        'review_name' => $faker->name, 
        'review_date' => $faker->date('Y-m-d'),
        'rating_value' => $faker->randomElement(array(1,2,3,4,5)),
        'review_text' => $faker->text,
    ];
});
```

## 針對 model 做單元測試

## 針對 repository 做單元測試

## 針對 service 做單元測試

## 善用 mockery 生成假物件做測試

## Laravel 內建的 Fake Facade

## 針對 API 做功能測試

## 撰寫自動化測試腳本
