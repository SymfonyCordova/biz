{!! $phpStart !!}
namespace Biz\{{$moduleName}}\Dao;

use Zler\Biz\Dao\GeneralDaoInterface;

interface {{$bigName}}Dao extends GeneralDaoInterface
{
    public function findByIds($ids);
}

