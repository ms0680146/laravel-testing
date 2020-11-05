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

## 善用 Factory & Faker 建立假資料

## 針對 model 做單元測試

## 針對 repository 做單元測試

## 針對 service 做單元測試

## 善用 mockery 生成假物件做測試

## Laravel 內建的 Fake Facade

## 針對 API 做功能測試

## 撰寫自動化測試腳本
