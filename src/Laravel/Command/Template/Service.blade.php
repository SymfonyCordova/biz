{!! $phpStart !!}
namespace Biz\{{$moduleName}}\Service;

interface {{$bigName}}Service {
    public function get{{$bigName}}($id);

    public function create{{$bigName}}(array ${{$smallName}});

    public function update{{$bigName}}($id, array $fields);

    public function find{{$bigPluralName}}ByIds($ids);

    public function search{{$bigPluralName}}($conditions, $orderBy, $start = 0, $limit = 15);

    public function count{{$bigPluralName}}($conditions);

    public function delete{{$bigName}}($id);
}