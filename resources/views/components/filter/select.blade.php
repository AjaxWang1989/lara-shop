<div class="input-group">
    <div class="input-group-addon">
        {{$label}}
    </div>
    <select class="form-control {{ $class }}" name="{{$name}}" style="width: 100%;">
        <option></option>
        @foreach($options as $select => $option)
            <option value="{{$select}}" {{ (string)$select === request($name, $value) ?'selected':'' }}>{{$option}}</option>
        @endforeach
    </select>
</div>