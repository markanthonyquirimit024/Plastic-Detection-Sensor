@if ($errors->any())
    <div {{ $attributes }}>
        <!-- <div style="font-size: 12px; color: #e3342f; font-weight: bold;">
            {{ __('Whoops! Something went wrong.') }}
        </div> -->

        <ul style="color: red;
            font-size: 14px;
            margin-bottom: 10px;
            background: rgba(255, 0, 0, 0.2);
            padding: 10px;
            border-radius: 5px;
            border: 1px solid red;
            text-align: center;
            width: 90%;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
