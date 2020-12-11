# laravel 從零到有的測試流程
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

database/factories/GoogleReviewJobFactory.php
- Faker 可以產生非常多樣化的假資料，可以到 [fzaninotto/Faker](https://github.com/fzaninotto/Faker) 看看各種假資料。
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
- 可以建起一個 GoogleReviewJob 後，把關聯的 GoogleReviews 建起來([Laravel: Factory Callback](https://laravel.com/docs/6.x/database-testing#factory-callbacks))。
```bash
$factory->afterCreating(GoogleReviewJob::class, function (GoogleReviewJob $googleReviewJob, Faker $faker) {
    $googleReviewJob->reviews()->saveMany(
        factory(GoogleReview::class, 3)->make([
            'google_review_job_id' => $googleReviewJob['id'],
        ])
    );
});
```
- 另外也可以把一些狀態相關的東西抽成[Laravel: Factory State](https://laravel.com/docs/6.x/database-testing#factory-states)
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
- 由於 google_reviews 會用 google_review_job_id 關聯回 google_review_jobs，因此這邊可以先創建一筆 GoogleReviewJob.
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
1. 測試 model 中的 relationship 是否正確.
2. 測試的 function 名稱不用駝峰式命名法是因為想把測試的完整內容記錄下來, 看code的人才可以一目瞭然知道這在測什麼.   
3. [RefreshDatabase](https://laravel.com/docs/6.x/database-testing#resetting-the-database-after-each-test) 這個 trait 會在跑每個測試 function 時執行 migrate:fresh 重置資料庫.
4. 這邊有個小坑，若你是用指令 php artisan make:test UserTest --unit 產生 testcase 的話，會遇到 unit test 中抓不到 factory 的 bug，[目前官方文件是解釋說 unit test 本來就不應該用 factory 產生假資料](https://github.com/laravel/framework/issues/30879#issuecomment-567456608)。這邊我是[參考此篇文章](https://github.com/laravel/framework/issues/28378)將 use PHPUnit\Framework\TestCase; 改為 use Tests\TestCase; 。

tests/Unit/GoogleReviewJobTest  
- 測試 google_review_job 是否包含許多 google_reviews.
```bash
class GoogleReviewJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_review_job_has_many_google_reviews()
    {
        // Arrange(create 1 google_review_job & 3 google_reviews)
        factory(GoogleReviewJob::class)->create();

        // Act
        $googleReviewJob = GoogleReviewJob::first();
        $googleReviews = GoogleReview::all();

        // Assert
        // Method 1: Count that a googleReviewJob googleReviews collection exists.
        $this->assertEquals(3, $googleReviewJob->reviews->count());
        // Method 2: googleReviews are related to googleReviewJob and is a collection instance.
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $googleReviewJob->reviews);
    }
}
```

tests/Unit/GoogleReviewTest  
- 測試 google_reviews 是否屬於 google_review_job.
```bash
class GoogleReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_reviews_belongs_to_google_review_job()
    {
        // Arrange(create 1 google_review_job & 3 google_reviews)
        factory(GoogleReviewJob::class)->create();

        // Act
        $googleReview = GoogleReview::all()->random(1)->first();

        // Assert
        // Method 1: Test by count that a GoogleReview has a parent relationship with GoogleReviewJob
        $this->assertEquals(1, $googleReview->googleReviewJob()->count());
        // Method 2: GoogleReview has a parent GoogleReviewJob and is a GoogleReviewJob instance.
        $this->assertInstanceOf(GoogleReviewJob::class, $googleReview->googleReviewJob);
    }
}
```

## 針對 repository 做單元測試
1. Repository Pattern 將 Model 取資料的邏輯拆到 Repository 中，讓 Model 變成真正的 Pure-Model，只留 relation，盡量不要有取資料的邏輯。
2. 實作一個 BaseRepo 來放一般的 CRUD，讓之後的 Repository 都可以繼承。
3. 採用 laravel app 來實例化 GoogleReviewJobRepo。
4. 分別測試 GoogleReviewJobRepo 包含的 function: create, find, update, delete, findByGooglePlaceId。

tests/Unit/GoogleReviewJobRepoTest  
- 測試 findByGooglePlaceId
```bash
public function test_find_google_review_job_by_google_place_id()
{
    // Arrange(create 1 google_review_job & 3 google_reviews)
    $googlePlaceId = 'test123';
    factory(GoogleReviewJob::class)->create([
        'google_place_id' => $googlePlaceId
    ]);
        
    // Act(find googleReviewJob by googlePlaceId
    $googleReviewJob = $this->googleReviewJobRepo->findByGooglePlaceId($googlePlaceId);
       
    // Assert(check googleReviewJob is instance of GoogleReviewJob)
    $this->assertInstanceOf(GoogleReviewJob::class, $googleReviewJob);
}
```

tests/Unit/GoogleReviewJobRepoTest  
- 測試 create
```bash
public function test_create_google_review_job()
{
    // Arrange
    $data = [
        'job_id' => 'test_job_id',
        'google_place_id' => 'test_google_place_id', 
        'review_count' => 10,
        'average_rating' => 3.5, 
        'crawl_status' => 'complete', 
        'credits_used' => 10
     ];

    // Act(find googleReviewJob by id)
    $googleReviewJob = $this->googleReviewJobRepo->create($data);
        
    // Assert
    $this->assertInstanceOf(GoogleReviewJob::class, $googleReviewJob);
    $this->assertDatabaseHas('google_review_jobs', ['job_id' => 'test_job_id']);
}
```

tests/Unit/GoogleReviewJobRepoTest  
- 測試 update，assertDatabaseHas檢查google_review_jobs table是否包含某筆資料; assertDatabaseMissing檢查google_review_jobs table是否不包含某筆資料
```bash
public function test_update_google_review_job()
{
    // Arrange
    factory(GoogleReviewJob::class)->create([
      'crawl_status' => 'pending',
    ]);

    // Act(update googleReviewJob crawl_status as complete)
    $status = $this->googleReviewJobRepo->update(1, ['crawl_status' => 'complete']);
        
    // Assert
    $this->assertTrue($status);
    $this->assertDatabaseHas('google_review_jobs', ['crawl_status' => 'complete']);
    $this->assertDatabaseMissing('google_review_jobs', ['crawl_status' => 'pending']);
}
```

tests/Unit/GoogleReviewJobRepoTest  
- 測試 delete，assertDatabaseMissing檢查google_review_jobs table是否不包含某筆資料
```bash
public function test_delete_google_review_job()
{
    // Arrange
    factory(GoogleReviewJob::class)->create([
        'job_id' => 'should_delete'
    ]);

    // Act(delete googleReviewJob)
    $status = $this->googleReviewJobRepo->delete(1);
        
    // Assert
    $this->assertTrue($status);
    $this->assertDatabaseMissing('google_review_jobs', ['job_id' => 'should_delete']);
}
```


## 針對 service 及 mockery 做單元測試
1. DataShakeService: 發送請求到 DataShake 服務的 [add_review_profile](https://api.datashake.com/#reviews) 這隻 API。 
2. GoogleReviewService: 處理發送請求到 Datashake 服務及後續資料儲存進 Database。
3. 透過 Dependency Injection 注入 DataShakeService 及 GoogleReviewJobRepo。
4. 由於測試會涉及到第三方的DataShake API，因為DataShake收費頗貴，所以我們不希望真的打API過去測試，因此採用 Mockery 來『模仿/代替』發出去的 DataShake API。

tests/TestCase.php 
- 在TestCase內新增一個initMock的方法，這樣所有繼承的測試都可以直接使用這個方法。
- Mockery::mock 可以利用 Reflection 機制幫我們建立假物件。
- Service Container 的 instance 方法可以讓我們用假物件取代原來的物件。
```bash
protected function initMock($class)
{
    $mock = Mockery::mock($class);
    $this->app->instance($class, $mock);
    return $mock;
}
```

tests/Unit/GoogleReviewServiceTest.php @ setUp
- 透過 initMock 將假的 DataShakeService Class 取代真的 DataShakeService Class。
- DataShakeService Mock 要在 GoogleReviewService application 建立起來之前先做。
```bash
private $dataShakeServiceMock = null;
private $googleReviewService = null;

public function setUp() : void
{
    parent::setUp();
    $this->dataShakeServiceMock = $this->initMock(DataShakeService::class);
    $this->googleReviewService = $this->app->make(GoogleReviewService::class);
}
```

tests/Unit/GoogleReviewServiceTest.php @ test_add_review_profile
- 把 Class Mock 起來後，我們可以對他做一些設定。相關的方法可以參閱vendor內的/mockery/mockery/library/Mockery/Expectation。這邊舉幾個-常用的方法：
- shouldReceive(): 應該被呼叫的方法，假設你要呼叫 DataShakeService 內的 callAddReviewProfile 方法，就可以寫成 shouldReceive('callAddReviewProfile')。
- once(): 只呼叫一次。
- with(): 應該被傳入的參數。建議用with取代withAnyArgs()，可以當做是一種assert來確認傳入方法的參數是否正確，尤其當執行的物件要執行到Mock的方法前還有經過很多方法時，用 with() 可以確保資料傳入的正確性。
- andReturn(): 回傳的參數。
- andThrow(new Execption('xxx')): 拋出例外
```bash
public function test_add_review_profile()
{
    // Arrange (25sprout google place id: ChIJg8wV-cmrQjQR7o27A1TgiBs)
    $googlePalceId = 'ChIJg8wV-cmrQjQR7o27A1TgiBs';
    $params = [
        'place_id' => $googlePalceId,
    ];

    $dataShakeResponse = [
        'success' => true,
        'job_id' => 10000,
        'status' => 200,
        'message' => 'add review into queue...'
    ];

    // Act
    $this->dataShakeServiceMock
        ->shouldReceive('callAddReviewProfile')
        ->once()
        ->with($params)
        ->andReturn($dataShakeResponse);

    $result = $this->googleReviewService->addReviewProfile($googlePalceId);

    // Assert
    $this->assertTrue($result);
    $googleReviewJob = GoogleReviewJob::where('job_id', $dataShakeResponse['job_id'])->first();
    $this->assertNotNull($googleReviewJob);
}
```


## 善用 mockery 做測試
1. Mocking Hard Dependencies: 舊有的程式碼可能不是用 Dependency Injection 方式注入，而是直接在程式碼中 new 物件，此時在測試 mock 物件的時候需要 [overload] (http://docs.mockery.io/en/latest/cookbook/mocking_hard_dependencies.html)。

Service.php
```bash
<?php
namespace App;
class Service
{
    public function callExternalService($param)
    {
        $externalService = new Service\External($version = 5);
        $externalService->sendSomething($param);
        return $externalService->getSomething();
    }
}
```

ServiceTest.php
```bash
public function testCallingExternalService()
{
    $param = 'Testing';

    $externalMock = Mockery::mock('overload:App\Service\External');
    $externalMock->shouldReceive('sendSomething')
            ->once()
            ->with($param);
    $externalMock->shouldReceive('getSomething')
            ->once()
            ->andReturn('Tested!');

    $service = new \App\Service();
    $result = $service->callExternalService($param);
    $this->assertSame('Tested!', $result);
}
```

2. Mock partial: 被測試的 function 內有用到相同 class 的 function 而需要 mock 掉時，可以用 [makePartial](http://docs.mockery.io/en/latest/reference/partial_mocks.html?highlight=makePartial#runtime-partial-test-doubles)

UserService.php
```bash
public function clear($user){
   $status = $this->removeUser($user->admID); 
   if ($status) {
       return true;
   } else {
       return 'clean_failed';
   }
}

protect function removeUser($id){
   return User::delete($id);
}
```

UserServiceTest.php @ testClear
```bash
public function testClear(){
   $service = \Mockery::mock(UserService::class)
        ->shouldAllowMockingProtectedMethods()
        ->makePartial()
        ->allows([
            'removeUser' => true
        ]);

   // 需要用上面mock的object去call function
   $result = $service->clear($user);
   $this->assertTrue($result);
}
```


## Laravel 內建的 Fake Facade
Laravel 提供許多 Fake 的 Facade，例如常用的: Event, Mail, Queue, Storage。    
可參考[文件](https://laravel.com/docs/6.x/mocking)  

這邊來實作一個寄送多語系信件的範例，情景如下：  
1. 寄信之前檢查使用者的語系  
2. 去S3上查找有沒有該語系的信件範本  
3. 若有找到，則使用該語系的範本，若沒找到，會使用預設的英文範本  

tests/Unit/AlertMailTest.php
- 測試的時候不可能真的到S3去找，所以我們透過 Laravel 提供的 Storage Fake Facade 來模擬 S3 的情境  
- 測試到S3上找得到該信件範本(@test_alert_mail_from_s3_fr_json)  
- 測試到s3上找不到該語系的信件範本(@test_alert_survey_mail_from_default_en_json)  

## 針對 API 做功能測試
新增兩隻API，分別為呈現 Google Review 單筆及多筆資料。  
可參考：GoogleReviewController  

針對API的部分，撰寫Feature Test，檢查回傳狀態及回傳結構。  
GoogleReviewApiTest.php @ test_google_reviews_api
```bash
public function test_google_reviews_api()
{
    // Arrange(create 1 google_review_job & 1 google_reviews)
    factory(GoogleReview::class)->create();
    // Act
    $response = $this->json('GET', 'api/google_reviews');
    // Assert
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'status',
        'message',
        'data' => [
            '*' => [
                'id',
                'review_name',
                'review_date'
                'rating_value',
                'review_text'
            ]
        ]
    ]);
}
```

GoogleReviewApiTest.php @ test_google_review_api
```bash
public function test_google_review_api()
{
    // Arrange(create 1 google_review_job & 1 google_reviews)
    factory(GoogleReview::class)->create();
    // Act
    $response = $this->json('GET', 'api/google_reviews/1');
    // Assert
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'status',
        'message',
        'data' => [
            'id',
            'review_name',
            'review_date',
            'rating_value',
            'review_text'
        ]
    ]);
}
```

## 撰寫自動化測試腳本
可參考 .gitlab-ci.yml  
各參數介紹：  
- tags: gitlab runnner tags.  
- stage: pipeline stages.  
- only: trigger branch.
- cache: store cache file.
- artifacts: store & manage file.
- services: create a container that is linked to job container.
- variables: some services variables.

```bash
stages:
  - test

test-mysql:
  tags:
    - php7.2
    - unit-test
    - utc0 
  stage: test
  only:
    - main
  cache:
    key: "$CI_COMMIT_REF_NAME-$CI_JOB_NAME"
    paths:
      - vendor
  artifacts:
    expire_in: 2 hours
    paths:
      - coverage
      - .env
  # image: mysql5.7 is used to create a container that is linked to the job container.
  services:
    - mysql:5.7
  variables:
    # mysql services
    MYSQL_ROOT_PASSWORD: root_password
    MYSQL_DATABASE: homestead
    MYSQL_USER: homestead
    MYSQL_PASSWORD: secret
  before_script:
    - echo 'install php-pcov'
    - apt update && apt install -y php-pcov
  script:
    - composer global require hirak/prestissimo
    - composer install --no-scripts --no-interaction --ignore-platform-reqs
    - echo "${_TESTING_MYSQL_ENV}" > .env
    - php artisan key:generate
    - php artisan migrate:refresh --seed
    - ./vendor/bin/phpunit --coverage-text --coverage-html=coverage
```
