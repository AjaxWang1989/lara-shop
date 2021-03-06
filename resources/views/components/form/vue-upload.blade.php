<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>

    <div class="{{$viewClass['field']}}">

        @include('components.form.error')
        <vue-upload class="{{$class}}" name="{{$name}}" {{$value ? "xthumb =".$value : ""}} {!! $attributes !!}></vue-upload>
        {{--<input type="file" class="{{$class}}" name="{{$name}}" {!! $attributes !!} />--}}

        @include('components.form.help-block')

    </div>
</div>
