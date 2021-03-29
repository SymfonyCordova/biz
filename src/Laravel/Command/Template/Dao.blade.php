{!! $phpStart !!}
namespace Biz\{{$moduleName}}\Dao;

use Zler\Biz\Dao\AdvancedDaoInterface;

interface {{$bigName}}Dao extends AdvancedDaoInterface
{
    public function findByIds($ids);
}

