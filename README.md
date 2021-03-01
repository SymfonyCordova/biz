# zler-biz

An API extension for biz

# install

- sample install

  ```shell
  composer require zler/biz
  ```

- laravel install

  ```shell
  php artisan vendor:publish --provider="Zler\Biz\Laravel\Provider\LaravelServiceProvider"
  ```


# 概述

Biz定位为简单、易用的**业务层**框架，采用经典的 Service/Dao 模式来编写项目的业务逻辑，可以跟 Symfony、Laravel、Silex、Lumen、Phalcon 等框架搭配使用。

Biz Framework 的目标，给出一套组织业务代码的约定以及最佳实践，以让一些通用的模块的业务代码，能跨项目、跨 Web 开发框架的重用。使用 Biz  能给团队带来的好处有：

- 提高生产效率，减少重复开发。
- 能保证一些通用模块的质量，一些通用模块往往经过各个项目不断的锤炼，会有较高的质量。
- 方便团队各 Team 之间人员流动，因为大家都采用了一致的业务层框架，很容易就能上手新项目。

## 开发示例

### 目录结构

以下为含 `User`, `Article` 两个业务模块的推荐的目录结构示例：

```
src/
  Biz/
    User/
      Dao/
        Impl/
          UserDaoImpl.php
        UserDao.php
      Service/
        Impl/
          UserServiceImpl.php
        UserService.php
    Article
      Dao/
        Impl/
          ArticleDaoImpl.php
          CategoryDaoImpl.php
        ArticleDao.php
        CategoryDao.php
      Service/
        Impl/
          ArticleServiceImpl.php
        ArticleService.php
```

### 命名约定

- 约定应用级业务层的顶级命名空间为 `Biz`，命名空间的第二级为模块名；
- 约定 *Service 接口*的接口名以 Service 作为后缀，命名空间为 `Biz\模块名\Service`, 上述例子中 `UserService` 的完整类名为 `Biz\User\Service\UserService`；
- 约定 *Service 实现类*的类名以 ServiceImpl 作为后缀，命名空间为 `Biz\模块名\Service\Impl`, 上述例子中 `UserServiceImpl` 的完整类名为 `Biz\User\Service\Impl\UserServiceImpl`；
- Dao 接口、类名的命名约定，同 Sevice 接口、类名的命名约定。

### 创建数据库

在编写业务代码之前，我们首先需要创建数据库

### 编写Dao

以编写 User Dao 为例，我们首先需要创建 `UserDao接口`：

```php
<?php
namespace Biz\User\Dao;

use Zler\Biz\Dao\GeneralDaoInterface;

interface UserDao extends GeneralDaoInterface
{

}
```

这里我们直接继承了`GeneralDaoInterface`，在 `GeneralDaoInterface` 中，我们声明了常用的 Dao 接口：

```php
<?php
use Zler\Biz\Dao\GeneralDaoInterface;

interface GeneralDaoInterface extends DaoInterface
{
    public function create($fields);

    public function update($id, array $fields);

    public function delete($id);

    public function get($id);

    public function search($conditions, $orderBy, $start, $limit);

    public function count($conditions);

    public function wave(array $ids, array $diffs);
}
```

同样我们的 UserDao 实现类，也可继承自 `Zler\Biz\Dao\GeneralDaoImp`l;

```php
<?php
namespace Biz\User\Dao\Impl;

use Biz\User\Dao\UserDao;
use Zler\Biz\Dao\GeneralDaoImpl;

class UserDaoImpl extends GeneralDaoImpl implements UserDao
{
    protected $table = 'user';

    public function declares()
    {
        return array(
            'timestamps' => array('created_time', 'update_time'),
            'serializes' => array(),
            'orderbys' => array(),
            'conditions' => array(
                'username = :username',
            ),
        );
    }
}
```

这样我们就拥有了 `GeneralDaoInterface` 接口所定义的所有方法功能。关于方法 `declares()` 的详细说明参考DAO章节

### 编写Service

以编写 UserService 为例，我们首先需创建 `UserService` 接口：

```php
<?php
namespace Biz\User\Service;

interface UserService
{
    public function getUser($id);

    // ...
}
```

然后创建 User Service 的实现类：

```php
<?php
namespace Biz\User\Service\Impl;

use Zler\Biz\Service\BaseService
use Biz\User\Service\UserService;

class UserServiceImpl extends BaseService implements UserService
{
    public function getUser($id)
    {
        return $this->getUserDao()->get($id);
    }

    // ...

    protected function getUserDao()
    {
        return $this->biz->dao('User:UserDao');
    }
}
```

这里我们 `UserServiceImpl` 继承了 `Zler\Biz\Service\BaseService` ，使得 `UserServiceImpl` 可以自动被注入`Biz`容器对象。

在 `getUserDao()` 方法中，我们通过 `$this->biz->dao('User:UserDao')`，获得了 User Dao 对象实例`Biz\User\Dao\Impl\UserDaoImpl`，具体参见 [获取 Dao / Service 的实例对象](http://developer.edusoho.com/biz-framework/biz-framework-container.html)。

### 使用Service

以实现*显示用户的个人主页*为例，我们的 `Controller` 代码大致为：

```php
<?php

class UserController
{
    public function show($id)
    {
        $user = $this->getUserService()->getUser($id);
        // ...
        return $this->render('user', array('user' => $user));
    }
    // ...

    protected function getUserService()
    {
        return $this->biz->service('User:UserService');
    }
}
```

其中 `getUserService()` 同上个例子中的 `getUserDao()` 原理类似，通过调用 `getUserService()` 我们获得了`Biz\User\Service\Impl\UserServiceImpl` 对象实例。

# DAO

DAO(Data Access Object) 即数据访问对象，封装所有对数据源进行操作的 API。以数据库驱动的应用程序为例，通常数据库中的一张表，对应的会有一个 Dao 类去操作这个数据库表。

## 通用的DAO基类

框架中定义了基础 DAO 接口 `GeneralDaoInterface`，以及对应的实现：`GeneralDaoImpl`。`GeneralDaoInterface` 接口如下，定义了常用的增删改查的数据操作方法。

```php
<?php
namespace Zler\Biz\Dao;

interface GeneralDaoInterface
{
    public function create($fields);

    public function update($id, array $fields);

    public function delete($id);

    public function get($id);

    public function search($conditions, $orderBy, $start, $limit);

    public function count($conditions);

    public function wave(array $ids, array $diffs);
}
```

我们在声明自己的DAO接口时，可以继承 `GeneralDaoInterface`，例如：

```php
use Zler\Biz\Dao\GeneralDaoInterface;

interface ExampleDao extends GeneralDaoInterface
{
	 public function getByName($name);
}
```

同样，在编写DAO实现类时，我们继承 `GeneralDaoImpl` 类，例如：

```php
<?php
namespace Biz\Dao\Impl;

use Zler\Biz\Dao\GeneralDaoImpl;

use Biz\Dao\ExampleDao;


class ExampleDaoImpl extends GeneralDaoImpl implements ExampleDao
{
    protected $table = 'example';

    public function getByName($name)
    {
        return $this->getByField('name', $name);
    }

    public function declares()
    {
        return array(
            'timestamps' => array('created_time', 'update_time'),
            'serializes' => array(),
            'orderbys' => array(),
            'conditions' => array(
                'type = :type',
            ),
        );
    }
}
```

其中属性 `$table` 表明此 Dao 类所对应的数据库表名。每个 DAO 类，须申明 `declares()` 方法，该方法返回一个 array 类型的配置：

**`timestamps`**

类型: `array` 必须: 否

定义调用 `create`、`update` 方法时自动更新的时间戳字段。调用 `create` 方法时会自动更新 `timestamps` 数组中声明的第1、2个字段的值；调用`update` 方法时会自动更新第2个字段的值。

**`serializes`**

类型: `array` 必须: 否

定义需序列化的字段，序列化方式有 `json`, `delimiter`, `php`。

**`orderbys`**

类型：`array` 必须: 否

定义可排序的字段。

**`conditions`**

类型：`array` 必须: 否

定义可被 search 方法调用的查询条件。例如:

```php
public function declares()
{
    return array(
        'conditions' => array(
            'id = :id',
            'id IN (:ids)',
            'id != :not_id',
            'id NOT IN (:not_ids)',
            'updatedTime >= :updatedTime_GTE',
            'updatedTime > :updatedTime_GT',
            'updatedTime <= :updatedTime_LTE',
            'updatedTime < :updatedTime_LT',
            'title LIKE :titleLike', //模糊查询, 相当于%titleLike%
            'title PRE_LIKE :titlePrefixLike', //前缀模糊查询, 相当于titlePrefixLike%
            'title SUF_LIKE :titleSuffixLike', //后缀模糊查询, 相当于%titleSuffixLike
        ),
    );
}
```

## 命名约定

通常 Dao 类中，只会存在一种数据对象，所以方法名命名的时候我们约定省略名词，即由原先的`动词+名词`的方式，变为只有`动词`的方式。其他约定有：

- 数据库如选用 MySQL ，请使用 Innodb 引擎；每个数据表都应定义递增型的 `id` 主键字段；
- 查询单行数据，方法名以 `get` 开头；
- 查询多行数据，方法名以 `find` 开头；
- 查询一列数据的，方法名以 `pick` 开头；
- 根据指定条件查询用 `By` 字段；如根据主键 `id` 查询，则省略 `ById`；多个条件用 `And` 连接；方法参数的顺序跟方法名的查询条件一致；
- 分页查询多行数据，以 `$start`, `$limit` 作为分页参数，加在方法参数的末尾；`$start` 表示要查询的起始行数，`$limit` 表示要查询的行数。

**命名示例**

| 方法                              | 说明                                   |
| --------------------------------- | -------------------------------------- |
| get($id)                          | 根据主键 `id` 查询单行数据             |
| getByEmail($email)                | 根据 `Email` 字段查询单行数据          |
| getByNameAndType($name, $type)    | 根据 `Name` 和 `Type` 字段查询单行数据 |
| pickNamesByIds                    | 根据多个 `id` 获取 `name` 列数据       |
| findByIds(array $ids)             | 根据多个 `id` 查询多行数据             |
| findByType($type, $start, $limit) | 根据 `Type` 分页查询多行数据           |

以上文档摘自 developer.edusoho.com/biz-framework/overview.html



# 使用ScaffoldCommand快速生成Biz代码

## 基本用法

```bash
php artisan biz:scaffold <tableName> <moduleName> <mode>

参数说明
  tableName                table_name, example: user, user_profile (小写)
  moduleName               module_name, example: User（首字母大写）
  mode                     DSC: D=dao,S=service,C=Controller (大写)
```

示例

```bash
# 新建了一张user_token表后，希望在src/Biz/User目录下生成Dao、Service代码
php artisan biz:scaffold user_token User DS
```

## 调整生成的代码

由于自动生成的代码比较呆板，使用之前我们需要进行微调

1. 调整DaoImpl文件中的conditions字段数组，包括字段缩进、删除不必要的条件
2. 调整ServiceImpl文件中的filterCreateUserTokenFields方法，按照实际情况修改$requiredFields和$default信息
3. 调整ServiceImpl文件中的filterUpdateUserTokenFields方法，按照实际情况修改允许更新的$fields信息 

# 事件派遣的使用

- 自定义自己的MyEventSubscriber

```php
namespace Biz\Service\Event
  
use Zler\Biz\Event\EventSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MyEventSubscriber extends EventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'user.create' => 'onReviewChange',
            'user.update' => 'onReviewChange',
            'user.delete' => 'onReviewChange',
        ];
    }

    public function onReviewChange(Event $event)
    {
        $review = $event->getSubject();

        if ('course' == $review['targetType']) {
            $course = $this->getCourseService()->getCourse($review['targetId']);
            $this->getCourseSetService()->updateCourseSetStatistics($course['courseSetId'], [
                'ratingNum',
            ]);
        }
    }

    /**
     * @return CourseService
     */
    protected function getCourseService()
    {
        return $this->getBiz()->service('Course:CourseService');
    }

    /**
     * @return CourseSetService
     */
    protected function getCourseSetService()
    {
        return $this->getBiz()->service('Course:CourseSetService');
    }
}
```

- 对与symfony 需要注册打上标签

  ```yaml
  user_user_event_subscriber:
  	class: Biz\User\Event\UserEventSubscriber
  	arguments: ['@biz']
  	public: true
  	tags:
  		- { name: zler.event.subscriber }
  ```

- 对与laravel需要注册打上标签

  ```php
  $this->app->bind('SpeedReportEventSubscriber', function ($app) {
      return new SpeedReportEventSubscriber($app['biz']);
  });
  
  $this->app->bind('MemoryReportEventSubscriber', function () {
      return new MemoryReportEventSubscriber($app['biz']);
  });
  
  $this->app->tag(['SpeedReportEventSubscriber', 'MemoryReportEventSubscriber'], 'zler.event.subscriber');
  ```

- 在service中使用

  ```php
  $created = $this->getUserService()->get($id);
  $this->dispatchEvent('user.create', new Event($created));
  ```

  