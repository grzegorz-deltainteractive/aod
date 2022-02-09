@if($data)
    @php
        // need to recreate object because policy might depend on record data
        $class = get_class($action);
        $action = new $class($dataType, $data);
        if ($action->getTitle() == "Pokaż") {
            return;
        }
        $arrayMethods = ['Pokaż', 'Usuń', 'Edytuj'];
        $showTitle = false;
        if (!in_array($action->getTitle(),$arrayMethods)) {
            $showTitle = true;
        }
    @endphp
    @can ($action->getPolicy(), $data)
        @if ($action->shouldActionDisplayOnRow($data))
            <a href="{{ $action->getRoute($dataType->name) }}" title="{{ $action->getTitle() }}" {!! $action->convertAttributesToHtml() !!}>
                <i class="{{ $action->getIcon() }}"></i>
                <span class="hidden-xs hidden-sm">
                    @if($showTitle)
                        {{ $action->getTitle() }}
                    @endif
                </span> 
            </a>
        @endif
    @endcan
@elseif (method_exists($action, 'massAction'))
    <form method="post" action="{{ route('voyager.'.$dataType->slug.'.action') }}" style="display:inline">
        {{ csrf_field() }}
        <button type="submit" {!! $action->convertAttributesToHtml() !!}><i class="{{ $action->getIcon() }}"></i>  {{ $action->getTitle() }}</button>
        <input type="hidden" name="action" value="{{ get_class($action) }}">
        <input type="hidden" name="ids" value="" class="selected_ids">
    </form>
@endif
