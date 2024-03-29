namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
@if(!empty($data['child_tables']))
@foreach($data['child_tables'] as $childTable)
@if($childTable['model_count'] == 1)
use App\Http\Resources\{{ $childTable['model'] }}Resource;
@endif
@endforeach
@endif

class {{ $data['pascal_singular'] }}Resource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
@foreach($data['fields'] as $field)
@if($field['type'] == 'date')
            '{{$field['name']}}' => \App\Models\AppModel::parseDate($this->{{$field['name']}}, 'd/m/Y'),
@elseif($field['type'] == 'datetime')
            '{{$field['name']}}' => \App\Models\AppModel::parseDate($this->{{$field['name']}}, 'd/m/Y H:i:s'),
@elseif($field['type'] == 'boolean' || $field['type'] == 'tinyint')
            '{{$field['name']}}' => $this->when($this->{{$field['name']}} === 1, true, false),
@else
            '{{$field['name']}}' => $this->{{$field['name']}},
@endif
@if($field['name'] == 'active' || $field['name'] == 'status')
            '{{$field['name']}}_str' => $this->{{$field['name']}}_str,
@endif
@endforeach
@if(!empty($data['related_tables']))
@foreach($data['related_tables'] as $related_table)
@if($related_table['model_count'] == 1)
            '{{$related_table['relationship_name']}}' => $this->whenLoaded('{{$related_table['relationship_name']}}'),
@endif
@endforeach
@endif
@if(!empty($data['child_tables']))
@foreach($data['child_tables'] as $childTable)
@if($childTable['model_count'] == 1)
            '{{$childTable['table']}}' => {{ $childTable['model'] }}Resource::collection($this->whenLoaded('{{$childTable['table']}}')),
@endif
@endforeach
@endif
        ];
    }
}
