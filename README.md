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